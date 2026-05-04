<?php
namespace App\Utilities;

/**
 * Validator Service
 * 
 * Handles all input validation logic to keep controllers clean.
 */
class Validator {
    
    private array $errors = [];

    /**
     * Validate an email address.
     *
     * @param string $email
     * @return bool
     */
    public function validateEmail(string $email): bool {
        if (empty($email)) {
            $this->errors['email'] = "Email is required.";
            return false;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Invalid email format.";
            return false;
        }
        return true;
    }

    /**
     * Validate required fields.
     *
     * @param array $data The input data array.
     * @param array $fields The list of required field names.
     * @return bool
     */
    public function validateRequired(array $data, array $fields): bool {
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
            }
        }
        return empty($this->errors);
    }

    /**
     * Get validation errors.
     *
     * @return array
     */
    public function getErrors(): array {
        return $this->errors;
    }
    
    /**
     * Get the first error message.
     * 
     * @return string|null
     */
    public function getFirstError(): ?string {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
}
?>
