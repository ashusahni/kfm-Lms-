<?php
/**
 * Automatic Database Fix from KFM.sql
 * 
 * This script:
 * 1. Reads KFM.sql and theboost_lmsnew.sql from the database folder
 * 2. Compares table structures
 * 3. Automatically adds all missing columns
 * 
 * Usage:
 * - Upload both SQL files to: database/KFM.sql and database/theboost_lmsnew.sql
 * - Visit: https://your-domain.com/database/auto_fix_from_kfm.php?key=your_secret_key
 */

// Security key - accepts multiple keys for convenience
$SECRET_KEY = 'auto_fix_kfm_2024_change_me';
$MIGRATION_KEY = 'run_migrations_2024_temp_key_change_me';
$ACCEPTED_KEYS = [$SECRET_KEY, $MIGRATION_KEY, 'auto_fix_kfm_2024_change_me', 'run_migrations_2024_temp_key_change_me'];

// Check security key
if (!isset($_GET['key']) || !in_array($_GET['key'], $ACCEPTED_KEYS)) {
    die(json_encode([
        'error' => 'Security check failed. Provide correct key parameter.',
        'usage' => '?key=auto_fix_kfm_2024_change_me OR ?key=run_migrations_2024_temp_key_change_me',
        'accepted_keys' => [
            'auto_fix_kfm_2024_change_me (default)',
            'run_migrations_2024_temp_key_change_me (migration key)'
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

// File paths
$kfmSqlPath = __DIR__ . '/KFM.sql';
$currentSqlPath = __DIR__ . '/theboost_lmsnew.sql';

$results = [
    'status' => 'started',
    'timestamp' => date('Y-m-d H:i:s'),
    'kfm_file' => file_exists($kfmSqlPath) ? 'found' : 'not found',
    'current_file' => file_exists($currentSqlPath) ? 'found' : 'not found',
    'tables_compared' => 0,
    'columns_added' => 0,
    'errors' => [],
    'warnings' => [],
    'details' => []
];

// Check if files exist
if (!file_exists($kfmSqlPath)) {
    $results['error'] = "KFM.sql not found at: $kfmSqlPath";
    $results['status'] = 'error';
    echo json_encode($results, JSON_PRETTY_PRINT);
    exit;
}

if (!file_exists($currentSqlPath)) {
    $results['warning'] = "theboost_lmsnew.sql not found, but continuing with KFM.sql only";
}

// Function to extract table structures from SQL
function extractTableStructures($sqlContent) {
    $tables = [];
    
    // Find all CREATE TABLE statements
    preg_match_all('/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?`?(\w+)`?\s*\(([^;]+)\)/is', $sqlContent, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $tableName = $match[1];
        $tableBody = $match[2];
        
        // Extract column definitions
        $columns = [];
        $lines = explode("\n", $tableBody);
        $currentColumn = '';
        $inColumn = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip empty lines, comments, and constraint definitions
            if (empty($line) || 
                strpos($line, '--') === 0 ||
                strpos($line, 'PRIMARY KEY') === 0 || 
                strpos($line, 'KEY') === 0 || 
                strpos($line, 'CONSTRAINT') === 0 ||
                strpos($line, 'UNIQUE') === 0 || 
                strpos($line, 'INDEX') === 0 ||
                strpos($line, 'FOREIGN KEY') === 0) {
                continue;
            }
            
            // Extract column name and definition
            // Match: `column_name` TYPE(parameters) [attributes] [COMMENT 'text'],
            // Stop at comma, or end of line, or before CONSTRAINT/KEY/PRIMARY/UNIQUE/FOREIGN
            if (preg_match('/^`?(\w+)`?\s+([^,]+?)(?:,\s*$|$)/', $line, $colMatch)) {
                $colName = $colMatch[1];
                $colDef = trim($colMatch[2]);
                
                // Stop at constraint/keywords that shouldn't be part of column definition
                $colDef = preg_replace('/\s+(CONSTRAINT|KEY|PRIMARY|UNIQUE|INDEX|FOREIGN|REFERENCES).*$/i', '', $colDef);
                // Remove any "to table_name" patterns (from malformed regex matches)
                $colDef = preg_replace('/\s+to\s+`?\w+`?.*$/i', '', $colDef);
                // Remove trailing comma if present
                $colDef = rtrim($colDef, ',');
                // Remove any trailing comments
                $colDef = preg_replace('/\s+COMMENT\s+[\'"][^\'"]*[\'"].*$/i', '', $colDef);
                // Clean up any extra whitespace
                $colDef = preg_replace('/\s+/', ' ', $colDef);
                $colDef = trim($colDef);
                
                // Only store if we have a valid definition that starts with a data type
                if (!empty($colDef) && preg_match('/^(int|varchar|text|tinyint|enum|decimal|double|bigint|date|datetime|timestamp|char|blob|point|bit|float|mediumint|smallint)/i', $colDef)) {
                    $columns[$colName] = $colDef;
                }
            }
        }
        
        if (!empty($columns)) {
            $tables[$tableName] = $columns;
        }
    }
    
    return $tables;
}

// Read and parse SQL files
$results['details'][] = "Reading KFM.sql...";
$kfmContent = file_get_contents($kfmSqlPath);
$kfmTables = extractTableStructures($kfmContent);
$results['details'][] = "Found " . count($kfmTables) . " tables in KFM.sql";

if (file_exists($currentSqlPath)) {
    $results['details'][] = "Reading theboost_lmsnew.sql...";
    $currentContent = file_get_contents($currentSqlPath);
    $currentTables = extractTableStructures($currentContent);
    $results['details'][] = "Found " . count($currentTables) . " tables in theboost_lmsnew.sql";
} else {
    $currentTables = [];
}

// Disable foreign key checks
try {
    if ($useLaravel) {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
    } else {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    }
} catch (Exception $e) {
    $results['warnings'][] = "Could not disable foreign key checks: " . $e->getMessage();
}

// Compare and add missing columns
$totalAdded = 0;
$tablesProcessed = 0;

foreach ($kfmTables as $tableName => $kfmColumns) {
        // Check if table exists in database
        try {
            if ($useLaravel) {
                $tableExists = \Illuminate\Support\Facades\Schema::hasTable($tableName);
            } else if ($pdo !== null) {
                $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
                $tableExists = $stmt->rowCount() > 0;
            } else {
                $results['errors'][] = "Database connection not available for table $tableName";
                continue;
            }
        
        if (!$tableExists) {
            $results['warnings'][] = "Table '$tableName' does not exist in database - skipping";
            continue;
        }
        
        // Get existing columns
        if ($useLaravel) {
            $existingColumns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `$tableName`");
            $existingColumnNames = array_column($existingColumns, 'Field');
        } else if ($pdo !== null) {
            $stmt = $pdo->query("SHOW COLUMNS FROM `$tableName`");
            $existingColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $existingColumnNames = array_column($existingColumns, 'Field');
        } else {
            $results['errors'][] = "Database connection not available for table $tableName";
            continue;
        }
        
        $missingColumns = [];
        foreach ($kfmColumns as $colName => $colDef) {
            if (!in_array($colName, $existingColumnNames)) {
                $missingColumns[$colName] = $colDef;
            }
        }
        
        if (!empty($missingColumns)) {
            $tablesProcessed++;
            $tableAdded = 0;
            
            // Determine column position (after which column)
            $kfmColNames = array_keys($kfmColumns);
            
            foreach ($missingColumns as $colName => $colDef) {
                $colIndex = array_search($colName, $kfmColNames);
                $afterCol = null;
                
                if ($colIndex > 0) {
                    // Find the previous column that exists in database
                    for ($i = $colIndex - 1; $i >= 0; $i--) {
                        $prevCol = $kfmColNames[$i];
                        if (in_array($prevCol, $existingColumnNames)) {
                            $afterCol = $prevCol;
                            break;
                        }
                    }
                }
                
                // Build ALTER TABLE statement
                // Clean the column definition - remove any invalid parts that might have been captured
                $cleanColDef = trim($colDef);
                // Remove any "to table_name" or similar invalid suffixes that regex might have captured
                $cleanColDef = preg_replace('/\s+to\s+`?\w+`?.*$/i', '', $cleanColDef);
                // Remove any trailing invalid SQL keywords that shouldn't be in column definition
                $cleanColDef = preg_replace('/\s+(REFERENCES|CONSTRAINT|FOREIGN\s+KEY).*$/i', '', $cleanColDef);
                // Remove any trailing table references
                $cleanColDef = preg_replace('/\s+`?\w+`?\s*`?\w+`?.*$/', '', $cleanColDef);
                $cleanColDef = trim($cleanColDef);
                
                // Validate column definition has at least a type
                if (empty($cleanColDef) || !preg_match('/^(int|varchar|text|tinyint|enum|decimal|double|bigint|date|datetime|timestamp|char|blob|point|bit)/i', $cleanColDef)) {
                    $results['warnings'][] = "Skipping column `$colName` in `$tableName` - invalid column definition extracted: $colDef";
                    continue;
                }
                
                $alterStmt = "ALTER TABLE `$tableName` ADD COLUMN `$colName` $cleanColDef";
                if ($afterCol) {
                    $alterStmt .= " AFTER `$afterCol`";
                }
                
                try {
                    if ($useLaravel) {
                        \Illuminate\Support\Facades\DB::statement($alterStmt);
                    } else if ($pdo !== null) {
                        $pdo->exec($alterStmt);
                    } else {
                        $results['errors'][] = "Database connection not available for ALTER TABLE";
                        continue;
                    }
                    
                    $tableAdded++;
                    $totalAdded++;
                    $results['details'][] = "✓ Added column `$colName` to table `$tableName`";
                } catch (PDOException $e) {
                    // Check if it's a duplicate column error (column might have been added)
                    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                        $results['warnings'][] = "Column `$colName` in `$tableName` already exists (skipped)";
                    } else {
                        $results['errors'][] = "Error adding `$colName` to `$tableName`: " . $e->getMessage();
                    }
                } catch (Exception $e) {
                    $results['errors'][] = "Error adding `$colName` to `$tableName`: " . $e->getMessage();
                }
            }
            
            if ($tableAdded > 0) {
                $results['details'][] = "Table `$tableName`: Added $tableAdded missing columns";
            }
        }
    } catch (Exception $e) {
        $results['errors'][] = "Error processing table `$tableName`: " . $e->getMessage();
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
$results['tables_compared'] = count($kfmTables);
$results['tables_processed'] = $tablesProcessed;
$results['columns_added'] = $totalAdded;
$results['timestamp_end'] = date('Y-m-d H:i:s');

// Output results
echo json_encode($results, JSON_PRETTY_PRINT);
