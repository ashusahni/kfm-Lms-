<?php
/**
 * Check What Data Was Actually Imported
 * This script shows what data exists in your database
 */

// Security key
$SECRET_KEY = 'auto_fix_kfm_2024_change_me';
$MIGRATION_KEY = 'run_migrations_2024_temp_key_change_me';
$ACCEPTED_KEYS = [$SECRET_KEY, $MIGRATION_KEY, 'auto_fix_kfm_2024_change_me', 'run_migrations_2024_temp_key_change_me'];

if (!isset($_GET['key']) || !in_array($_GET['key'], $ACCEPTED_KEYS)) {
    die(json_encode(['error' => 'Security check failed'], JSON_PRETTY_PRINT));
}

header('Content-Type: application/json');

// Bootstrap Laravel
$useLaravel = false;
$bootstrapPath = __DIR__ . '/../bootstrap/app.php';
if (file_exists($bootstrapPath)) {
    try {
        $app = require_once $bootstrapPath;
        if (is_object($app) && method_exists($app, 'make')) {
            try {
                $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
                if (class_exists('Illuminate\Support\Facades\DB')) {
                    $useLaravel = true;
                }
            } catch (Exception $e) {}
        }
    } catch (Exception $e) {}
}

// Database connection
$pdo = null;
try {
    if (!$useLaravel) {
        $envFile = __DIR__ . '/../.env';
        $host = 'localhost';
        $database = 'theboost_lmsnew';
        $username = 'root';
        $password = '';
        
        if (file_exists($envFile)) {
            $envContent = file_get_contents($envFile);
            preg_match('/DB_HOST=(.+)/', $envContent, $hostMatch);
            preg_match('/DB_DATABASE=(.+)/', $envContent, $dbMatch);
            preg_match('/DB_USERNAME=(.+)/', $envContent, $userMatch);
            preg_match('/DB_PASSWORD=(.+)/', $envContent, $passMatch);
            
            if (!empty($hostMatch[1])) $host = trim($hostMatch[1]);
            if (!empty($dbMatch[1])) $database = trim($dbMatch[1]);
            if (!empty($userMatch[1])) $username = trim($userMatch[1]);
            if (!empty($passMatch[1])) $password = trim($passMatch[1]);
        }
        
        $pdo = new PDO(
            "mysql:host=$host;dbname=$database;charset=utf8mb4",
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
} catch (Exception $e) {
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()], JSON_PRETTY_PRINT));
}

$results = [
    'status' => 'completed',
    'timestamp' => date('Y-m-d H:i:s'),
    'table_counts' => [],
    'details' => []
];

// Check important tables
$importantTables = [
    'users',
    'webinars',  // This is where courses are stored
    'categories',
    'sections',
    'files',
    'sessions',
    'bundles',
    'products',
    'sales',
    'purchases',
];

foreach ($importantTables as $tableName) {
    try {
        if ($useLaravel) {
            $tableExists = \Illuminate\Support\Facades\Schema::hasTable($tableName);
            if ($tableExists) {
                $count = \Illuminate\Support\Facades\DB::table($tableName)->count();
            } else {
                $count = 0;
            }
        } else if ($pdo !== null) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
            $tableExists = $stmt->rowCount() > 0;
            if ($tableExists) {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$tableName`");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $count = $result['count'] ?? 0;
            } else {
                $count = 0;
            }
        } else {
            $count = 0;
        }
        
        $results['table_counts'][$tableName] = $count;
        $results['details'][] = "Table `$tableName`: $count rows";
        
        // For webinars, show sample data
        if ($tableName === 'webinars' && $count > 0) {
            if ($useLaravel) {
                $sample = \Illuminate\Support\Facades\DB::table('webinars')
                    ->select('id', 'title', 'type', 'status', 'teacher_id')
                    ->limit(5)
                    ->get();
            } else if ($pdo !== null) {
                $stmt = $pdo->query("SELECT id, title, type, status, teacher_id FROM `webinars` LIMIT 5");
                $sample = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            $results['webinars_sample'] = $sample ?? [];
        }
        
    } catch (Exception $e) {
        $results['table_counts'][$tableName] = 'error: ' . substr($e->getMessage(), 0, 100);
    }
}

echo json_encode($results, JSON_PRETTY_PRINT);
