<?php

namespace App\Services;

use mysqli;
use Exception;

class AiSecurityService
{
    private $db;
    private $configFilePath;
    
    // Cache the mode to avoid repeated file reads in a single request
    private $cachedMode = null;

    public function __construct($db = null)
    {
        // Allow passing DB or resolve it globally if null. DB is still needed for logging threats.
        if ($db !== null) {
            $this->db = $db;
        } elseif (function_exists('db')) {
            $this->db = db();
        } elseif (isset($GLOBALS['conn'])) {
            $this->db = $GLOBALS['conn'];
        }

        $this->configFilePath = DOCUTRACK_ROOT . '/src/Config/security_settings.json';
        
        // Ensure config file exists with default if not found
        if (!file_exists($this->configFilePath)) {
            $this->writeConfigFile(['security_mode' => 'silent']);
        }
    }

    private function readConfigFile(): array
    {
        if (!file_exists($this->configFilePath)) {
            return ['security_mode' => 'silent']; // Default if file somehow disappears
        }
        $content = file_get_contents($this->configFilePath);
        if ($content === false) {
            error_log("AiSecurityService: Failed to read config file.");
            return ['security_mode' => 'silent'];
        }
        $config = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("AiSecurityService: Failed to decode config JSON: " . json_last_error_msg());
            return ['security_mode' => 'silent'];
        }
        return $config;
    }

    private function writeConfigFile(array $config): bool
    {
        $jsonContent = json_encode($config, JSON_PRETTY_PRINT);
        if ($jsonContent === false) {
            error_log("AiSecurityService: Failed to encode config JSON: " . json_last_error_msg());
            return false;
        }
        if (file_put_contents($this->configFilePath, $jsonContent) === false) {
            error_log("AiSecurityService: Failed to write config file. Check permissions for " . $this->configFilePath);
            return false;
        }
        return true;
    }

    /**
     * Get Current Security Mode
     * @return string 'silent', 'block', 'off'
     */
    public function getMode(): string
    {
        if ($this->cachedMode !== null) {
            return $this->cachedMode;
        }

        $config = $this->readConfigFile();
        $mode = $config['security_mode'] ?? 'silent';
        $this->cachedMode = $mode;
        return $mode;
    }

    /**
     * Set Security Mode
     * @param string $mode
     */
    public function setMode(string $mode): bool
    {
        if (!in_array($mode, ['silent', 'block', 'off'])) {
            return false;
        }
        
        $config = $this->readConfigFile();
        $config['security_mode'] = $mode;
        $success = $this->writeConfigFile($config);
        if ($success) {
            $this->cachedMode = $mode; // Update cache on successful write
        }
        return $success;
    }

    /**
     * Main Entry Point: Scans Request and Enforces Policy
     * Should be called early in the request lifecycle.
     */
    public function handleSecurity(): void
    {
        $mode = $this->getMode();
        
        if ($mode === 'off') {
            return;
        }

        // Scan GET and POST
        $threats = [];
        $resultPost = $this->scanInput($_POST);
        if ($resultPost['detected']) $threats[] = array_merge($resultPost, ['source' => 'POST']);
        
        $resultGet = $this->scanInput($_GET);
        if ($resultGet['detected']) $threats[] = array_merge($resultGet, ['source' => 'GET']);

        if (!empty($threats)) {
            // Only log if DB is available. Silent fail if not.
            if ($this->db) {
                foreach ($threats as $threat) {
                    $this->logThreat($threat);
                }
            }

            if ($mode === 'block') {
                http_response_code(403);
                die("<h1>403 Forbidden</h1><p>DocuTrack Security Shield has detected a potential threat in your request.</p>");
            }
        }
    }

    private function logThreat(array $threat)
    {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            // Sanitize payload for logging (truncate to 500 chars)
            $payload = isset($threat['payload']) ? substr($threat['payload'], 0, 500) : '';
            
            // Check if ai_security_alerts table exists before inserting
            $check = $this->db->query("SHOW TABLES LIKE 'ai_security_alerts'");
            if ($check->num_rows === 0) {
                 error_log("AiSecurityService: ai_security_alerts table not found. Cannot log threat.");
                 return; // Fail safe if table not migrated
            }

            $stmt = $this->db->prepare("INSERT INTO ai_security_alerts (ip_address, input_payload, severity, detection_type) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $ip, $payload, $threat['risk'], $threat['type']);
            $stmt->execute();
        } catch (Exception $e) {
            // Silent fail on log error
            error_log("AiSecurityService: Error logging threat: " . $e->getMessage());
        }
    }

    /**
     * Scans input data for malicious patterns (SQL Injection, XSS).
     *
     * @param array $data Input data to scan (e.g., $_POST, $_GET).
     * @return array Result with keys: detected (bool), type (string), risk (string), payload (string).
     */
    public function scanInput(array $data): array
    {
        $patterns = [
            'SQL Injection' => [
                'regex' => '/(union\s+select|drop\s+table|information_schema|--|\s+or\s+1=1)/i',
                'risk' => 'high'
            ],
            'XSS' => [
                'regex' => '/(<script|javascript:|onerror=)/i',
                'risk' => 'medium'
            ]
        ];

        foreach ($data as $key => $value) {
            // Handle recursion for nested arrays
            if (is_array($value)) {
                $result = $this->scanInput($value);
                if ($result['detected']) {
                    return $result;
                }
                continue;
            }

            // Skip non-string values
            if (!is_string($value)) {
                continue;
            }

            foreach ($patterns as $type => $config) {
                if (preg_match($config['regex'], $value)) {
                    return [
                        'detected' => true,
                        'type' => $type,
                        'risk' => $config['risk'],
                        'payload' => $value // Capture the payload for logging
                    ];
                }
            }
        }

        return [
            'detected' => false,
            'type' => '',
            'risk' => ''
        ];
    }
}
