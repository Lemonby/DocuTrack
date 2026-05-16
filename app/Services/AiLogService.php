<?php

namespace App\Services;

use Exception;
use SplFileObject;

class AiLogService
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db;
    }

    /**
     * Smart Wrapper: Checks DB cache first, then calls API if needed.
     * 
     * @param int $limit Lines of logs to analyze
     * @param int $cacheDurationSeconds Cache validity (default 1 hour)
     * @return array Returns ['summary' => string, 'model' => string]
     */
    public function getSmartSummary(int $limit = 50, int $cacheDurationSeconds = 3600): array
    {
        $modelName = 'Gemini 3 Flash'; // Source of Truth for Model Name in Service

        // 1. Check Cache (if DB is available)
        if ($this->db) {
            $cached = $this->getLatestCachedSummary($cacheDurationSeconds);
            if ($cached) {
                return [
                    'summary' => $cached . " (Cached)",
                    'model' => $modelName
                ];
            }
        }

        // 2. Perform Analysis (Heavy Operation)
        $rawLogs = $this->analyzeLogs($limit);
        
        // Basic validation
        if (strlen($rawLogs) < 20 || strpos($rawLogs, "No logs found") !== false) {
             return [
                 'summary' => "No recent anomalies detected in system logs.",
                 'model' => $modelName
             ];
        }

        // 3. Call AI API
        $summary = $this->generateSummary($rawLogs);

        // 4. Save to Cache
        if ($this->db && strpos($summary, "AI Summary") === false && strpos($summary, "Error") === false) {
            $this->saveSummaryToDb($summary);
        }

        return [
            'summary' => $summary,
            'model' => $modelName
        ];
    }

    private function getLatestCachedSummary(int $seconds)
    {
        try {
            $query = "SELECT summary_text FROM ai_log_summaries WHERE created_at >= NOW() - INTERVAL ? SECOND ORDER BY id DESC LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $seconds);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row['summary_text'];
            }
        } catch (Exception $e) {
            // Fallback if table doesn't exist or DB error
        }
        return null;
    }

    private function saveSummaryToDb($text)
    {
        try {
            $query = "INSERT INTO ai_log_summaries (summary_text, error_count) VALUES (?, 0)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $text);
            $stmt->execute();
        } catch (Exception $e) {
            // Ignore insert errors
        }
    }

    /**
     * Reads the last N lines of the PHP error log.
     *
     * @param int $limit Number of lines to read.
     * @return string Log content or error message.
     */
    public function analyzeLogs(int $limit = 50): string
    {
        $logFile = defined('DOCUTRACK_ROOT') ? DOCUTRACK_ROOT . '/logs/php_error.log' : __DIR__ . '/../../logs/php_error.log';

        if (!file_exists($logFile)) {
            return "No logs found";
        }

        try {
            $file = new SplFileObject($logFile, 'r');
            
            // Seek to the end to get the total line count
            $file->seek(PHP_INT_MAX);
            $totalLines = $file->key();

            // Calculate starting line
            $startLine = max(0, $totalLines - $limit);
            
            // Go to the starting line
            $file->seek($startLine);

            $lines = [];
            while (!$file->eof()) {
                $lines[] = $file->current();
                $file->next();
            }

            return implode("", $lines);

        } catch (Exception $e) {
            return "Error reading logs: " . $e->getMessage();
        }
    }

    /**
     * Generates a summary of the log text using Gemini API.
     *
     * @param string $logText The raw log text to summarize.
     * @return string The AI-generated summary or a fallback message.
     */
    public function generateSummary(string $logText): string
    {
        // Try multiple sources for the API Key, prioritizing $_SERVER and $_ENV as they were found
        $apiKey = $_SERVER['GEMINI_API_KEY'] ?? $_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY') ?? null;

        if (!$apiKey) {
            // Debugging: Log what we tried
            error_log("AILogService: API Key NOT FOUND. Checked \$_SERVER, \$_ENV, and getenv().");
            return "AI Summary Inactive: API Key missing in .env";
        }

        if (empty(trim($logText)) || $logText === "No logs found") {
            return "No logs to summarize.";
        }

        try {
            // Using Gemini 1.5 Flash model (more widely available on Free Tier)
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent";
            
            $headers = [
                'Content-Type: application/json',
                'X-goog-api-key: ' . $apiKey // API Key now in header
            ];

            // Limit payload size to avoid hitting limits with massive logs
            $rawLog = substr($logText, 0, 8000);
            
            // SANITIZATION: Ensure strict UTF-8 encoding to prevent JSON errors
            $truncatedLogs = mb_convert_encoding($rawLog, 'UTF-8', 'UTF-8');

            $data = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "Analyze the following PHP error logs. Generate a professional and human-readable summary of the *most critical issues* identified, presented in 3 concise bullet points. Each point should clearly describe the error, its potential impact, and suggest a high-level area for investigation or resolution. Avoid excessive technical jargon where possible.\n\n" . $truncatedLogs]
                        ]
                    ]
                ]
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Use the new headers
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            
            // Zero Cost Strategy: Increased timeout slightly for better stability
            curl_setopt($ch, CURLOPT_TIMEOUT, 15); 

            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                throw new Exception("Request failed: " . curl_error($ch));
            }
            
            curl_close($ch);

            $result = json_decode($response, true);
            
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                return $result['candidates'][0]['content']['parts'][0]['text'];
            }
            
            // Log raw response for debugging
            error_log("AI API Error Raw Response: " . $response);
            
            // Handle specific API errors if available
            if (isset($result['error']['message'])) {
                return "AI Error: " . $result['error']['message'];
            }
            
            return "AI Summary Failed: Invalid response from API.";

        } catch (Exception $e) {
            // Ensure external API failures don't crash the app
            return "AI Summary Unavailable: " . $e->getMessage();
        }
    }
}
