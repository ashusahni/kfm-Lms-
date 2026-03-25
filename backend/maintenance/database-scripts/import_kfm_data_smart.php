<?php
/**
 * Smart Import Demo Data from KFM.sql with Column Mapping
 * 
 * This script intelligently maps KFM.sql columns to your database columns
 * and imports data even when column structures differ.
 */

// Security key
$SECRET_KEY = 'auto_fix_kfm_2024_change_me';
$MIGRATION_KEY = 'run_migrations_2024_temp_key_change_me';
$ACCEPTED_KEYS = [$SECRET_KEY, $MIGRATION_KEY, 'auto_fix_kfm_2024_change_me', 'run_migrations_2024_temp_key_change_me', 'import_kfm_data_2024'];

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

$kfmSqlPath = __DIR__ . '/KFM.sql';
$results = [
    'status' => 'started',
    'timestamp' => date('Y-m-d H:i:s'),
    'tables_imported' => 0,
    'rows_imported' => 0,
    'errors' => [],
    'warnings' => [],
    'details' => [],
    'tables_detail' => []  // Track rows per table
];

if (!file_exists($kfmSqlPath)) {
    $results['error'] = "KFM.sql not found";
    $results['status'] = 'error';
    echo json_encode($results, JSON_PRETTY_PRINT);
    exit;
}

// Tables to skip (system tables or tables that shouldn't be imported)
$skipTables = [
    'migrations',  // Don't import migration records
    'password_resets',  // Don't import password reset tokens
    'failed_jobs',  // Don't import failed job records
];

// If you want to import only specific tables, add them here
// Leave empty to import ALL tables from KFM.sql
$priorityTables = [];  // Empty = import all tables

$kfmContent = file_get_contents($kfmSqlPath);

// Extract column order from CREATE TABLE statements for tables without column lists in INSERT
function extractKfmTableColumns($sqlContent, $tableName) {
    // Find CREATE TABLE statement for this table
    $pattern = '/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?`?' . preg_quote($tableName, '/') . '`?\s*\(([^;]+)\)/is';
    if (preg_match($pattern, $sqlContent, $matches)) {
        $tableBody = $matches[1];
        $columns = [];
        $lines = explode("\n", $tableBody);
        
        foreach ($lines as $line) {
            $line = trim($line);
            // Skip constraints, keys, etc.
            if (empty($line) || 
                strpos($line, 'PRIMARY KEY') === 0 || 
                strpos($line, 'KEY') === 0 || 
                strpos($line, 'CONSTRAINT') === 0 ||
                strpos($line, 'UNIQUE') === 0 || 
                strpos($line, 'INDEX') === 0 ||
                strpos($line, 'FOREIGN KEY') === 0 ||
                strpos($line, '--') === 0) {
                continue;
            }
            
            // Extract column name (first identifier)
            if (preg_match('/^`?(\w+)`?\s+/', $line, $colMatch)) {
                $colName = $colMatch[1];
                $columns[] = $colName;
            }
        }
        
        return $columns;
    }
    return [];
}

// Cache for KFM column orders
$kfmTableColumns = [];

// Disable foreign key checks
try {
    if ($useLaravel) {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
    } else if ($pdo !== null) {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    }
} catch (Exception $e) {}

// Find INSERT statements
preg_match_all('/INSERT\s+(?:IGNORE\s+)?INTO\s+`?(\w+)`?\s*(?:\(([^)]+)\))?\s*VALUES\s*([^;]+);/is', $kfmContent, $insertMatches, PREG_SET_ORDER);

$totalRows = 0;
$tablesProcessed = [];

foreach ($insertMatches as $match) {
    $tableName = $match[1];
    $columnList = isset($match[2]) ? trim($match[2]) : null;
    $values = $match[3];
    
    // Skip system tables
    if (in_array($tableName, $skipTables)) {
        continue;
    }
    
    // If priority tables are specified, only import those
    // If empty, import all tables
    if (!empty($priorityTables) && !in_array($tableName, $priorityTables)) {
        continue;
    }
    
    try {
        // Check if table exists
        if ($useLaravel) {
            $tableExists = \Illuminate\Support\Facades\Schema::hasTable($tableName);
        } else if ($pdo !== null) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$tableName'");
            $tableExists = $stmt->rowCount() > 0;
        } else {
            continue;
        }
        
        if (!$tableExists) {
            // Try to create the table from KFM.sql if it's a critical table
            if ($tableName === 'webinar_translations') {
                // Extract CREATE TABLE statement from KFM.sql
                $createPattern = '/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?`?' . preg_quote($tableName, '/') . '`?\s*\(([^;]+)\)[^;]*ENGINE[^;]+;/is';
                if (preg_match($createPattern, $kfmContent, $createMatch)) {
                    $fullCreate = $createMatch[0];
                    // Clean up the CREATE statement
                    $fullCreate = preg_replace('/DROP TABLE.*?;/is', '', $fullCreate);
                    $fullCreate = preg_replace('/\/\*.*?\*\//s', '', $fullCreate);
                    $fullCreate = trim($fullCreate);
                    
                    try {
                        if ($useLaravel) {
                            \Illuminate\Support\Facades\DB::statement($fullCreate);
                        } else if ($pdo !== null) {
                            $pdo->exec($fullCreate);
                        }
                        $results['details'][] = "✓ Created table `$tableName` from KFM.sql";
                        $tableExists = true;
                    } catch (Exception $createError) {
                        $results['warnings'][] = "Could not create table `$tableName`: " . substr($createError->getMessage(), 0, 100);
                        continue;
                    }
                } else {
                    $results['warnings'][] = "Table '$tableName' does not exist and CREATE TABLE not found in KFM.sql";
                    continue;
                }
            } else {
                $results['warnings'][] = "Table '$tableName' does not exist";
                continue;
            }
        }
        
        // Get actual database columns
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
        
        // Extract KFM column names
        $kfmColumnNames = [];
        if ($columnList) {
            preg_match_all('/`?(\w+)`?/', $columnList, $colMatches);
            $kfmColumnNames = $colMatches[1];
        } else {
            // No column list in INSERT - extract from CREATE TABLE in KFM.sql
            if (!isset($kfmTableColumns[$tableName])) {
                $kfmTableColumns[$tableName] = extractKfmTableColumns($kfmContent, $tableName);
            }
            $kfmColumnNames = $kfmTableColumns[$tableName];
        }
        
        // Parse rows from VALUES
        // Split by ),( but handle nested parentheses and quotes
        $rows = [];
        $currentRow = '';
        $depth = 0;
        $inQuotes = false;
        $quoteChar = null;
        
        $chars = str_split($values);
        foreach ($chars as $char) {
            if (($char === '"' || $char === "'") && (empty($currentRow) || substr($currentRow, -1) !== '\\')) {
                if (!$inQuotes) {
                    $inQuotes = true;
                    $quoteChar = $char;
                } elseif ($char === $quoteChar) {
                    $inQuotes = false;
                    $quoteChar = null;
                }
            }
            
            if (!$inQuotes) {
                if ($char === '(') {
                    $depth++;
                    if ($depth === 1) {
                        $currentRow = '';
                        continue;
                    }
                } elseif ($char === ')') {
                    $depth--;
                    if ($depth === 0) {
                        $rows[] = $currentRow;
                        $currentRow = '';
                        continue;
                    }
                }
            }
            
            if ($depth > 0) {
                $currentRow .= $char;
            }
        }
        
        // Process each row with column mapping
        $rowsInserted = 0;
        foreach ($rows as $rowData) {
            if (empty(trim($rowData))) continue;
            
            try {
                // Parse values from row
                $rowValues = [];
                $currentValue = '';
                $inQuotes = false;
                $quoteChar = null;
                
                $rowChars = str_split($rowData);
                foreach ($rowChars as $char) {
                    if (($char === '"' || $char === "'") && (empty($currentValue) || substr($currentValue, -1) !== '\\')) {
                        if (!$inQuotes) {
                            $inQuotes = true;
                            $quoteChar = $char;
                        } elseif ($char === $quoteChar) {
                            $inQuotes = false;
                            $quoteChar = null;
                        }
                        $currentValue .= $char;
                    } elseif ($char === ',' && !$inQuotes) {
                        $rowValues[] = trim($currentValue);
                        $currentValue = '';
                    } else {
                        $currentValue .= $char;
                    }
                }
                if (!empty($currentValue)) {
                    $rowValues[] = trim($currentValue);
                }
                
                // Map KFM columns to actual columns
                if (!empty($kfmColumnNames) && count($kfmColumnNames) <= count($rowValues)) {
                    // We have KFM column names - map them to actual database columns
                    $insertColumns = [];
                    $insertValues = [];
                    
                    foreach ($kfmColumnNames as $index => $kfmCol) {
                        if ($index < count($rowValues) && in_array($kfmCol, $actualColumnNames)) {
                            $insertColumns[] = "`$kfmCol`";
                            $insertValues[] = $rowValues[$index] ?? 'NULL';
                        }
                    }
                    
                    if (!empty($insertColumns) && count($insertColumns) > 0) {
                        $insertStmt = "INSERT IGNORE INTO `$tableName` (" . implode(', ', $insertColumns) . ") VALUES (" . implode(', ', $insertValues) . ")";
                        
                        try {
                            if ($useLaravel) {
                                \Illuminate\Support\Facades\DB::statement($insertStmt);
                            } else if ($pdo !== null) {
                                $pdo->exec($insertStmt);
                            }
                            $rowsInserted++;
                        } catch (Exception $insertError) {
                            // Skip rows with errors
                            if (strpos($insertError->getMessage(), 'Duplicate entry') === false && $rowsInserted < 3) {
                                $results['warnings'][] = "Skipped row in `$tableName`: " . substr($insertError->getMessage(), 0, 80);
                            }
                        }
                    }
                } elseif (empty($kfmColumnNames) && count($rowValues) === count($actualColumnNames)) {
                    // No column list and exact match - insert directly
                    $insertStmt = "INSERT IGNORE INTO `$tableName` VALUES (" . implode(', ', $rowValues) . ")";
                    
                    try {
                        if ($useLaravel) {
                            \Illuminate\Support\Facades\DB::statement($insertStmt);
                        } else if ($pdo !== null) {
                            $pdo->exec($insertStmt);
                        }
                        $rowsInserted++;
                    } catch (Exception $insertError) {
                        // Skip rows with errors
                        if (strpos($insertError->getMessage(), 'Duplicate entry') === false && $rowsInserted < 3) {
                            $results['warnings'][] = "Skipped row in `$tableName`: " . substr($insertError->getMessage(), 0, 80);
                        }
                    }
                }
            } catch (Exception $e) {
                // Skip problematic rows
                if (strpos($e->getMessage(), 'Duplicate entry') === false && $rowsInserted < 3) {
                    $results['warnings'][] = "Skipped row in `$tableName`: " . substr($e->getMessage(), 0, 80);
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
        $results['errors'][] = "Error processing `$tableName`: " . substr($e->getMessage(), 0, 150);
    }
}

// Re-enable foreign key checks
try {
    if ($useLaravel) {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
    } else if ($pdo !== null) {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }
} catch (Exception $e) {}

$results['status'] = 'completed';
$results['tables_imported'] = count($tablesProcessed);
$results['rows_imported'] = $totalRows;
$results['tables_detail'] = $tablesProcessed;
$results['timestamp_end'] = date('Y-m-d H:i:s');

// Add summary message
if ($totalRows > 0) {
    $results['message'] = "Successfully imported {$totalRows} rows from " . count($tablesProcessed) . " table(s)!";
} else {
    $results['message'] = "No data was imported. Check if tables exist and contain data in KFM.sql.";
}

echo json_encode($results, JSON_PRETTY_PRINT);
