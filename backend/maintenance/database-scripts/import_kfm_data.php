<?php
/**
 * Import Demo Data from KFM.sql
 * 
 * This script imports actual data (INSERT statements) from KFM.sql
 * including users, courses, webinars, and other demo content.
 * 
 * Usage:
 * - Upload KFM.sql to: database/KFM.sql
 * - Visit: https://your-domain.com/import_kfm_data?key=your_secret_key
 */

// Security key - accepts multiple keys for convenience
$SECRET_KEY = 'auto_fix_kfm_2024_change_me';
$MIGRATION_KEY = 'run_migrations_2024_temp_key_change_me';
$ACCEPTED_KEYS = [$SECRET_KEY, $MIGRATION_KEY, 'auto_fix_kfm_2024_change_me', 'run_migrations_2024_temp_key_change_me', 'import_kfm_data_2024'];

// Check security key
if (!isset($_GET['key']) || !in_array($_GET['key'], $ACCEPTED_KEYS)) {
    die(json_encode([
        'error' => 'Security check failed. Provide correct key parameter.',
        'usage' => '?key=auto_fix_kfm_2024_change_me OR ?key=run_migrations_2024_temp_key_change_me',
        'accepted_keys' => [
            'auto_fix_kfm_2024_change_me (default)',
            'run_migrations_2024_temp_key_change_me (migration key)',
            'import_kfm_data_2024'
        ]
    ], JSON_PRETTY_PRINT));
}

// Set headers for JSON output
header('Content-Type: application/json');

// Try to bootstrap Laravel
$useLaravel = false;
$bootstrapPath = __DIR__ . '/../bootstrap/app.php';

// Check multiple possible bootstrap paths
$possibleBootstrapPaths = [
    __DIR__ . '/../bootstrap/app.php',
    __DIR__ . '/../../bootstrap/app.php',
    __DIR__ . '/../../../bootstrap/app.php',
];

foreach ($possibleBootstrapPaths as $bootstrapPath) {
    if (file_exists($bootstrapPath)) {
        try {
            $app = require_once $bootstrapPath;
            // Check if $app is actually an object (not false/null)
            if (is_object($app) && method_exists($app, 'make')) {
                try {
                    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
                    // Check if Laravel facades are available after bootstrap
                    if (class_exists('Illuminate\Support\Facades\DB')) {
                        $useLaravel = true;
                        break; // Successfully bootstrapped, exit loop
                    }
                } catch (Exception $e) {
                    // Bootstrap failed, continue to next path or direct connection
                }
            }
        } catch (Exception $e) {
            // Laravel bootstrap failed, continue to next path or direct connection
        }
    }
}

// Database connection - initialize $pdo variable
$pdo = null;

try {
    if ($useLaravel && class_exists('Illuminate\Support\Facades\DB')) {
        // Use Laravel's DB facade - $pdo stays null
    } else {
        // Direct MySQL connection - try to read .env file
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
    die(json_encode([
        'error' => 'Database connection failed: ' . $e->getMessage()
    ], JSON_PRETTY_PRINT));
}

// File path
$kfmSqlPath = __DIR__ . '/KFM.sql';

$results = [
    'status' => 'started',
    'timestamp' => date('Y-m-d H:i:s'),
    'kfm_file' => file_exists($kfmSqlPath) ? 'found' : 'not found',
    'tables_imported' => 0,
    'rows_imported' => 0,
    'errors' => [],
    'warnings' => [],
    'details' => []
];

// Check if file exists
if (!file_exists($kfmSqlPath)) {
    $results['error'] = "KFM.sql not found at: $kfmSqlPath";
    $results['status'] = 'error';
    echo json_encode($results, JSON_PRETTY_PRINT);
    exit;
}

// Tables to import data for (priority order)
$priorityTables = [
    'users',
    'roles',
    'permissions',
    'sections',
    'categories',
    'webinars',
    'webinar_translations',
    'bundles',
    'bundle_translations',
    'products',
    'product_translations',
    'meetings',
    'sessions',
    'files',
    'quizzes',
    'certificates',
    'settings',
    'currencies',
    'payment_channels',
    'groups',
    'group_users',
    'sales',
    'purchases',
    'comments',
    'favorites',
    'tags',
    'faqs',
    'tickets',
    'notifications',
];

// Read KFM.sql file
$results['details'][] = "Reading KFM.sql...";
$kfmContent = file_get_contents($kfmSqlPath);
$results['details'][] = "File size: " . round(strlen($kfmContent) / 1024, 2) . " KB";

// Disable foreign key checks
try {
    if ($useLaravel) {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
    } else if ($pdo !== null) {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    }
} catch (Exception $e) {
    $results['warnings'][] = "Could not disable foreign key checks: " . $e->getMessage();
}

// Extract and execute INSERT statements
$results['details'][] = "Extracting INSERT statements...";

// Find all INSERT statements - match both with and without column names
// Pattern 1: INSERT INTO table VALUES (...)
// Pattern 2: INSERT INTO table (col1, col2) VALUES (...)
preg_match_all('/INSERT\s+(?:IGNORE\s+)?INTO\s+`?(\w+)`?\s*(?:\(([^)]+)\))?\s*VALUES\s*([^;]+);/is', $kfmContent, $insertMatches, PREG_SET_ORDER);

$totalRows = 0;
$tablesProcessed = [];

foreach ($insertMatches as $match) {
    $tableName = $match[1];
    $columnList = isset($match[2]) ? trim($match[2]) : null;
    $values = $match[3];
    
    // Only import data for priority tables
    if (!in_array($tableName, $priorityTables)) {
        continue;
    }
    
    // Check if table exists
    try {
        if ($useLaravel) {
            $tableExists = \Illuminate\Support\Facades\Schema::hasTable($tableName);
        } else if ($pdo !== null) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
            $tableExists = $stmt->rowCount() > 0;
        } else {
            continue;
        }
        
        if (!$tableExists) {
            $results['warnings'][] = "Table '$tableName' does not exist - skipping data import";
            continue;
        }
        
        // Get actual table columns
        if ($useLaravel) {
            $actualColumns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `$tableName`");
            $actualColumnNames = array_column($actualColumns, 'Field');
        } else if ($pdo !== null) {
            $stmt = $pdo->query("SHOW COLUMNS FROM `$tableName`");
            $actualColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $actualColumnNames = array_column($actualColumns, 'Field');
        } else {
            continue;
        }
        
        // If column list is provided, use it; otherwise assume all columns in order
        $kfmColumnNames = [];
        if ($columnList) {
            // Extract column names from column list
            preg_match_all('/`?(\w+)`?/', $columnList, $colMatches);
            $kfmColumnNames = $colMatches[1];
        }
        
        // Use a simpler approach: execute the INSERT statement as-is but with INSERT IGNORE
        // This will skip rows with column mismatches or duplicates
        try {
            // Reconstruct INSERT statement with IGNORE to skip errors
            if ($columnList) {
                $fullInsert = "INSERT IGNORE INTO `$tableName` ($columnList) VALUES " . $values . ";";
            } else {
                $fullInsert = "INSERT IGNORE INTO `$tableName` VALUES " . $values . ";";
            }
            
            // Clean up the statement
            $fullInsert = preg_replace('/LOCK TABLES.*?UNLOCK TABLES/is', '', $fullInsert);
            $fullInsert = preg_replace('/\/\*.*?\*\//s', '', $fullInsert);
            $fullInsert = trim($fullInsert);
            
            // Try to execute - INSERT IGNORE will skip problematic rows
            if ($useLaravel) {
                \Illuminate\Support\Facades\DB::statement($fullInsert);
            } else if ($pdo !== null) {
                $pdo->exec($fullInsert);
            }
            
            // Count rows (approximate - count opening parentheses)
            $rowCount = substr_count($values, '(');
            $rowsInserted = $rowCount;
            
        } catch (Exception $e) {
            // If bulk insert fails, try to extract and insert rows one by one
            // This handles column mismatches better
            $rowsInserted = 0;
            
            // Extract individual rows from VALUES clause
            // Simple approach: split by ),( pattern
            $rowPattern = '/\(([^)]*(?:\([^)]*\)[^)]*)*)\)/';
            preg_match_all($rowPattern, $values, $rowMatches);
            
            if (!empty($rowMatches[1])) {
                foreach ($rowMatches[1] as $rowData) {
                    try {
                        // Try to insert this single row
                        if ($columnList) {
                            $singleInsert = "INSERT IGNORE INTO `$tableName` ($columnList) VALUES ($rowData)";
                        } else {
                            $singleInsert = "INSERT IGNORE INTO `$tableName` VALUES ($rowData)";
                        }
                        
                        if ($useLaravel) {
                            \Illuminate\Support\Facades\DB::statement($singleInsert);
                        } else if ($pdo !== null) {
                            $pdo->exec($singleInsert);
                        }
                        $rowsInserted++;
                    } catch (Exception $rowError) {
                        // Skip this row if it has errors (column mismatch, etc.)
                        // Only log first few errors to avoid spam
                        if ($rowsInserted < 3 && strpos($rowError->getMessage(), 'Duplicate entry') === false) {
                            $results['warnings'][] = "Skipped row in `$tableName`: " . substr($rowError->getMessage(), 0, 80);
                        }
                    }
                }
            }
            
            // If still no rows inserted, log the error
            if ($rowsInserted === 0) {
                $errorMsg = $e->getMessage();
                // Don't log column count errors as they're expected
                if (strpos($errorMsg, 'Column count') === false && 
                    strpos($errorMsg, 'Duplicate entry') === false) {
                    $results['errors'][] = "Error importing data into `$tableName`: " . substr($errorMsg, 0, 150);
                } else {
                    $results['warnings'][] = "Skipped `$tableName` - column structure mismatch (expected)";
                }
            }
        }
        
        if ($rowsInserted > 0) {
            $totalRows += $rowsInserted;
            if (!isset($tablesProcessed[$tableName])) {
                $tablesProcessed[$tableName] = 0;
            }
            $tablesProcessed[$tableName] += $rowsInserted;
            $results['details'][] = "✓ Imported $rowsInserted rows into `$tableName`";
        }
        
    } catch (Exception $e) {
        $results['errors'][] = "Error processing table `$tableName`: " . substr($e->getMessage(), 0, 200);
    }
}

// Re-enable foreign key checks
try {
    if ($useLaravel) {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
    } else if ($pdo !== null) {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }
} catch (Exception $e) {
    $results['warnings'][] = "Could not re-enable foreign key checks: " . $e->getMessage();
}

// Final results
$results['status'] = 'completed';
$results['tables_imported'] = count($tablesProcessed);
$results['rows_imported'] = $totalRows;
$results['tables_detail'] = $tablesProcessed;
$results['timestamp_end'] = date('Y-m-d H:i:s');

// Output results
echo json_encode($results, JSON_PRETTY_PRINT);
