<?php
/**
 * Comprehensive script to create ALL missing tables from KFM.sql
 * 
 * This script:
 * 1. Scans KFM.sql for all CREATE TABLE statements
 * 2. Checks which tables exist in your database
 * 3. Creates all missing tables in one go
 * 
 * Usage: Visit /create_all_missing_tables_from_kfm?key=YOUR_SECRET_KEY
 */

// Security check
$secretKey = $_GET['key'] ?? '';
$requiredKey = getenv('AUTO_FIX_KFM_SECRET_KEY') ?: getenv('MIGRATION_SECRET_KEY') ?: 'auto_fix_kfm_2024_change_me';

if ($secretKey !== $requiredKey) {
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Security check failed. Please provide the correct key.',
        'hint' => 'Set AUTO_FIX_KFM_SECRET_KEY or MIGRATION_SECRET_KEY in your .env file'
    ]);
    exit;
}

// Set JSON header
header('Content-Type: application/json');

// Initialize results
$results = [
    'status' => 'processing',
    'timestamp' => date('Y-m-d H:i:s'),
    'tables_found_in_kfm' => 0,
    'tables_missing' => 0,
    'tables_created' => 0,
    'tables_existing' => 0,
    'errors' => [],
    'warnings' => [],
    'details' => [],
    'missing_tables' => [],
    'created_tables' => [],
    'existing_tables' => []
];

try {
    // Path to KFM.sql
    $kfmSqlPath = __DIR__ . '/KFM.sql';
    if (!file_exists($kfmSqlPath)) {
        // Try alternative paths
        $alternativePaths = [
            __DIR__ . '/../Databases/KFM.sql',
            __DIR__ . '/../../Databases/KFM.sql',
            dirname(__DIR__, 2) . '/Databases/KFM.sql',
        ];
        
        $found = false;
        foreach ($alternativePaths as $path) {
            if (file_exists($path)) {
                $kfmSqlPath = $path;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            throw new Exception("KFM.sql file not found. Please ensure KFM.sql is in the database folder or Databases folder.");
        }
    }
    
    $results['details'][] = "✓ Found KFM.sql at: " . $kfmSqlPath;
    
    // Read KFM.sql
    $kfmContent = file_get_contents($kfmSqlPath);
    if ($kfmContent === false) {
        throw new Exception("Failed to read KFM.sql file.");
    }
    
    $results['details'][] = "✓ Read KFM.sql (" . number_format(strlen($kfmContent)) . " bytes)";
    
    // Extract all CREATE TABLE statements
    // Pattern to match CREATE TABLE statements (handles both formats)
    $createTablePattern = '/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?(\w+)`?\s*\([^;]+\)[^;]*ENGINE[^;]+;/is';
    
    preg_match_all($createTablePattern, $kfmContent, $matches, PREG_SET_ORDER);
    
    $tablesFromKfm = [];
    $createStatements = [];
    
    foreach ($matches as $match) {
        $tableName = $match[1];
        $fullStatement = $match[0];
        
        // Clean up the statement
        $fullStatement = preg_replace('/\/\*.*?\*\//s', '', $fullStatement); // Remove comments
        $fullStatement = preg_replace('/LOCK\s+TABLES.*?UNLOCK\s+TABLES;/is', '', $fullStatement); // Remove LOCK/UNLOCK
        $fullStatement = preg_replace('/DROP\s+TABLE.*?;/is', '', $fullStatement); // Remove DROP TABLE
        $fullStatement = trim($fullStatement);
        
        // Ensure it starts with CREATE TABLE
        if (!preg_match('/^CREATE\s+TABLE/i', $fullStatement)) {
            continue;
        }
        
        // Convert to CREATE TABLE IF NOT EXISTS format
        if (!preg_match('/CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS/i', $fullStatement)) {
            $fullStatement = preg_replace(
                '/CREATE\s+TABLE\s+(?:`?(\w+)`?)/i',
                'CREATE TABLE IF NOT EXISTS `$1`',
                $fullStatement
            );
        }
        
        // Store table name and statement
        if (!isset($tablesFromKfm[$tableName])) {
            $tablesFromKfm[$tableName] = $fullStatement;
            $createStatements[$tableName] = $fullStatement;
        }
    }
    
    $results['tables_found_in_kfm'] = count($tablesFromKfm);
    $results['details'][] = "✓ Found " . count($tablesFromKfm) . " CREATE TABLE statements in KFM.sql";
    
    // Try to bootstrap Laravel
    $useLaravel = false;
    $pdo = null;
    $app = null;
    
    $bootstrapPaths = [
        __DIR__ . '/../bootstrap/app.php',
        __DIR__ . '/../../bootstrap/app.php',
        dirname(__DIR__, 2) . '/bootstrap/app.php',
    ];
    
    foreach ($bootstrapPaths as $bootstrapPath) {
        if (file_exists($bootstrapPath)) {
            try {
                $app = require_once $bootstrapPath;
                if (is_object($app) && method_exists($app, 'make')) {
                    $useLaravel = true;
                    $results['details'][] = "✓ Laravel bootstrapped successfully";
                    break;
                }
            } catch (Exception $e) {
                // Try next path
                continue;
            }
        }
    }
    
    // If Laravel bootstrap failed, use direct PDO connection
    if (!$useLaravel) {
        $results['warnings'][] = "Laravel bootstrap failed, using direct PDO connection";
        
        // Get database credentials from environment or config
        $dbHost = getenv('DB_HOST') ?: 'localhost';
        $dbPort = getenv('DB_PORT') ?: '3306';
        $dbDatabase = getenv('DB_DATABASE') ?: '';
        $dbUsername = getenv('DB_USERNAME') ?: 'root';
        $dbPassword = getenv('DB_PASSWORD') ?: '';
        
        if (empty($dbDatabase)) {
            throw new Exception("Database name not found. Please set DB_DATABASE in .env file.");
        }
        
        try {
            $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbDatabase};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbUsername, $dbPassword, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $results['details'][] = "✓ Direct PDO connection established";
        } catch (PDOException $e) {
            throw new Exception("Failed to connect to database: " . $e->getMessage());
        }
    }
    
    // Get list of existing tables
    $existingTables = [];
    
    if ($useLaravel) {
        try {
            $existingTablesList = \Illuminate\Support\Facades\DB::select("SHOW TABLES");
            $dbName = \Illuminate\Support\Facades\DB::getDatabaseName();
            $key = "Tables_in_{$dbName}";
            foreach ($existingTablesList as $row) {
                $existingTables[] = $row->$key;
            }
        } catch (Exception $e) {
            $results['warnings'][] = "Could not fetch existing tables: " . substr($e->getMessage(), 0, 100);
        }
    } else if ($pdo !== null) {
        try {
            $stmt = $pdo->query("SHOW TABLES");
            $existingTablesList = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $existingTables = $existingTablesList;
        } catch (PDOException $e) {
            $results['warnings'][] = "Could not fetch existing tables: " . substr($e->getMessage(), 0, 100);
        }
    }
    
    $results['details'][] = "✓ Found " . count($existingTables) . " existing tables in database";
    
    // Find missing tables
    $missingTables = [];
    foreach ($tablesFromKfm as $tableName => $createStatement) {
        if (!in_array($tableName, $existingTables)) {
            $missingTables[$tableName] = $createStatement;
        } else {
            $results['existing_tables'][] = $tableName;
        }
    }
    
    $results['tables_existing'] = count($results['existing_tables']);
    $results['tables_missing'] = count($missingTables);
    $results['missing_tables'] = array_keys($missingTables);
    
    if (empty($missingTables)) {
        $results['status'] = 'completed';
        $results['message'] = "All tables from KFM.sql already exist in your database!";
        $results['details'][] = "✓ No missing tables found";
        echo json_encode($results, JSON_PRETTY_PRINT);
        exit;
    }
    
    $results['details'][] = "⚠ Found " . count($missingTables) . " missing tables";
    
    // Disable foreign key checks
    if ($useLaravel) {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    } else if ($pdo !== null) {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');
    }
    
    // Create missing tables
    $createdCount = 0;
    $errorCount = 0;
    
    foreach ($missingTables as $tableName => $createStatement) {
        try {
            if ($useLaravel) {
                \Illuminate\Support\Facades\DB::statement($createStatement);
            } else if ($pdo !== null) {
                $pdo->exec($createStatement);
            } else {
                throw new Exception("No database connection available");
            }
            
            $createdCount++;
            $results['created_tables'][] = $tableName;
            $results['details'][] = "✓ Created table: `{$tableName}`";
            
        } catch (Exception $e) {
            $errorCount++;
            $errorMsg = substr($e->getMessage(), 0, 200);
            $results['errors'][] = "Failed to create `{$tableName}`: {$errorMsg}";
            $results['details'][] = "✗ Failed to create `{$tableName}`: {$errorMsg}";
        }
    }
    
    // Re-enable foreign key checks
    if ($useLaravel) {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    } else if ($pdo !== null) {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');
    }
    
    $results['tables_created'] = $createdCount;
    $results['status'] = 'completed';
    $results['timestamp_end'] = date('Y-m-d H:i:s');
    
    if ($createdCount > 0) {
        $results['message'] = "Successfully created {$createdCount} missing table(s) from KFM.sql!";
    } else {
        $results['message'] = "No tables were created. Check errors above.";
    }
    
    if ($errorCount > 0) {
        $results['message'] .= " {$errorCount} table(s) failed to create.";
    }
    
} catch (Exception $e) {
    $results['status'] = 'error';
    $results['message'] = $e->getMessage();
    $results['errors'][] = $e->getMessage();
    $results['timestamp_end'] = date('Y-m-d H:i:s');
}

echo json_encode($results, JSON_PRETTY_PRINT);
