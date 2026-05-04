<?php
namespace App\Utilities;

/**
 * Base Controller
 * 
 * Provides common utility methods for all controllers,
 * such as standardized JSON responses and view rendering.
 */
class Controller {
    
    /**
     * Send a JSON response and exit.
     *
     * @param mixed $data The data to send.
     * @param int $statusCode HTTP status code (default 200).
     * @return void
     */
    protected function jsonResponse($data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Sanitize input data.
     *
     * @param string $data
     * @return string
     */
    protected function sanitize(string $data): string {
        return htmlspecialchars(strip_tags(trim($data)));
    }
}
?>
