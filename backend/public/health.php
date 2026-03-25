<?php
/**
 * Health check endpoint for Docker healthcheck
 * Returns JSON status for monitoring
 */

header('Content-Type: application/json');

$status = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'service' => 'Rocket LMS Backend'
];

// Check database connection if configured
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    
    if (isset($env['DB_HOST'])) {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s',
                $env['DB_HOST'] ?? '127.0.0.1',
                $env['DB_PORT'] ?? '3306'
            );
            $pdo = new PDO($dsn, $env['DB_USERNAME'] ?? 'root', $env['DB_PASSWORD'] ?? '');
            $status['database'] = 'connected';
        } catch (PDOException $e) {
            $status['database'] = 'disconnected';
            $status['status'] = 'degraded';
        }
    }
}

http_response_code($status['status'] === 'ok' ? 200 : 503);
echo json_encode($status, JSON_PRETTY_PRINT);
