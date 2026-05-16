<?php

namespace App\Services;

use App\Exceptions\ValidationException;

class ValidationService
{
    private $errors = [];
    private $data = [];

    /**
     * Sanitize and validate the given data against the rules.
     *
     * @param array $data The input data (e.g., $_POST).
     * @param array $rules The validation rules.
     * @return array The sanitized data.
     * @throws ValidationException If validation fails.
     */
    public function validate(array $data, array $rules): array
    {
        $this->errors = [];
        $this->data = $this->sanitize($data, $rules);

        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        if (!empty($this->errors)) {
            throw new ValidationException("Data tidak valid.", $this->errors);
        }

        return $this->data;
    }

    /**
     * Sanitize an array of data based on rules.
     * @param array $data
     * @param array $rules
     * @return array
     */
    public function sanitize(array $data, array $rules): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $fieldRules = isset($rules[$key]) ? explode('|', $rules[$key]) : [];

            // By default, sanitize. If 'nosanitize' rule exists, keep it raw.
            $shouldSanitize = !in_array('nosanitize', $fieldRules);

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitize($value, $rules); // Recursive sanitize
            } elseif (is_string($value) && $shouldSanitize) {
                $sanitized[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
            } else {
                // Keep the value as is (e.g., non-strings, or fields with 'nosanitize')
                $sanitized[$key] = trim($value);
            }
        }
        return $sanitized;
    }

    private function applyRule(string $field, $value, string $rule): void
    {
        $params = [];
        if (strpos($rule, ':') !== false) {
            list($rule, $paramStr) = explode(':', $rule, 2);
            $params = explode(',', $paramStr);
        }

        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, "Kolom {$field} wajib diisi.");
                }
                break;
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "Kolom {$field} harus berupa alamat email yang valid.");
                }
                break;
            case 'min':
                $minLength = (int)($params[0] ?? 0);
                if (strlen($value) < $minLength) {
                    $this->addError($field, "Kolom {$field} minimal harus {$minLength} karakter.");
                }
                break;
            case 'max':
                $maxLength = (int)($params[0] ?? PHP_INT_MAX);
                if (strlen($value) > $maxLength) {
                    $this->addError($field, "Kolom {$field} maksimal harus {$maxLength} karakter.");
                }
                break;
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "Kolom {$field} harus berupa angka.");
                }
                break;
            case 'nosanitize':
                // This is a marker rule, no action needed here.
                // It's handled during the sanitize step.
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
