<?php
/**
 * Fix Missing Relationships Script
 * 
 * This script fixes data integrity issues where:
 * - Webinars have teacher_id that don't exist in users table
 * - Tickets have user_id that don't exist in users table
 * - Comments have user_id that don't exist in users table
 * - And other similar relationship issues
 * 
 * Usage: Visit /fix_missing_relationships?key=YOUR_SECRET_KEY
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
    ]);
    exit;
}

header('Content-Type: application/json');

$results = [
    'status' => 'processing',
    'timestamp' => date('Y-m-d H:i:s'),
    'fixes_applied' => [],
    'errors' => []
];

try {
    // Try to bootstrap Laravel
    $useLaravel = false;
    $pdo = null;
    
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
                    break;
                }
            } catch (Exception $e) {
                continue;
            }
        }
    }
    
    // If Laravel bootstrap failed, use direct PDO
    if (!$useLaravel) {
        $dbHost = getenv('DB_HOST') ?: 'localhost';
        $dbPort = getenv('DB_PORT') ?: '3306';
        $dbDatabase = getenv('DB_DATABASE') ?: '';
        $dbUsername = getenv('DB_USERNAME') ?: 'root';
        $dbPassword = getenv('DB_PASSWORD') ?: '';
        
        if (empty($dbDatabase)) {
            throw new Exception("Database name not found. Please set DB_DATABASE in .env file.");
        }
        
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbDatabase};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUsername, $dbPassword, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    
    // Get all valid user IDs
    if ($useLaravel) {
        $validUserIds = \Illuminate\Support\Facades\DB::table('users')->pluck('id')->toArray();
    } else if ($pdo !== null) {
        $stmt = $pdo->query("SELECT id FROM users");
        $validUserIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        throw new Exception("No database connection available");
    }
    
    $validUserIds = array_map('intval', $validUserIds);
    $results['valid_user_count'] = count($validUserIds);
    
    // Find a default teacher (prefer teacher role, fallback to admin, then any user)
    $defaultTeacherId = null;
    if ($useLaravel) {
        // Try to find a user with teacher role (role_id = 4 or role_name = 'teacher')
        $teacher = \Illuminate\Support\Facades\DB::table('users')
            ->where(function($query) {
                $query->where('role_id', 4)
                      ->orWhere('role_name', 'teacher');
            })
            ->whereIn('id', $validUserIds)
            ->select('id')
            ->first();
        
        if ($teacher) {
            $defaultTeacherId = $teacher->id;
        } else {
            // Fallback to admin (role_id = 2 or role_name = 'admin')
            $admin = \Illuminate\Support\Facades\DB::table('users')
                ->where(function($query) {
                    $query->where('role_id', 2)
                          ->orWhere('role_name', 'admin');
                })
                ->whereIn('id', $validUserIds)
                ->select('id')
                ->first();
            
            if ($admin) {
                $defaultTeacherId = $admin->id;
            } else {
                // Fallback to first valid user
                $defaultTeacherId = $validUserIds[0] ?? null;
            }
        }
    } else if ($pdo !== null) {
        // Try to find a teacher (role_id = 4 or role_name = 'teacher')
        $placeholders = implode(',', array_fill(0, count($validUserIds), '?'));
        $stmt = $pdo->prepare("SELECT id FROM users 
            WHERE (role_id = 4 OR role_name = 'teacher') 
            AND id IN ($placeholders) 
            LIMIT 1");
        $stmt->execute($validUserIds);
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($teacher) {
            $defaultTeacherId = $teacher['id'];
        } else {
            // Fallback to admin (role_id = 2 or role_name = 'admin')
            $stmt = $pdo->prepare("SELECT id FROM users 
                WHERE (role_id = 2 OR role_name = 'admin') 
                AND id IN ($placeholders) 
                LIMIT 1");
            $stmt->execute($validUserIds);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin) {
                $defaultTeacherId = $admin['id'];
            } else {
                // Fallback to first valid user
                $defaultTeacherId = $validUserIds[0] ?? null;
            }
        }
    }
    
    if (!$defaultTeacherId) {
        throw new Exception("No valid users found to assign as default teacher!");
    }
    
    $results['default_teacher_id'] = $defaultTeacherId;
    
    // Fix webinars with invalid teacher_id (assign default teacher since teacher_id is NOT NULL)
    if ($useLaravel) {
        $invalidWebinars = \Illuminate\Support\Facades\DB::table('webinars')
            ->whereNotNull('teacher_id')
            ->whereNotIn('teacher_id', $validUserIds)
            ->count();
            
        if ($invalidWebinars > 0) {
            \Illuminate\Support\Facades\DB::table('webinars')
                ->whereNotNull('teacher_id')
                ->whereNotIn('teacher_id', $validUserIds)
                ->update(['teacher_id' => $defaultTeacherId]);
            
            $results['fixes_applied'][] = "Fixed $invalidWebinars webinars with invalid teacher_id (assigned default teacher ID: $defaultTeacherId)";
        }
    } else if ($pdo !== null) {
        $placeholders = implode(',', array_fill(0, count($validUserIds), '?'));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM webinars WHERE teacher_id IS NOT NULL AND teacher_id NOT IN ($placeholders)");
        $stmt->execute($validUserIds);
        $invalidWebinars = $stmt->fetchColumn();
        
        if ($invalidWebinars > 0) {
            $stmt = $pdo->prepare("UPDATE webinars SET teacher_id = ? WHERE teacher_id IS NOT NULL AND teacher_id NOT IN ($placeholders)");
            $params = array_merge([$defaultTeacherId], $validUserIds);
            $stmt->execute($params);
            $results['fixes_applied'][] = "Fixed $invalidWebinars webinars with invalid teacher_id (assigned default teacher ID: $defaultTeacherId)";
        }
    }
    
    // Find a default user (for tables where user_id is NOT NULL)
    $defaultUserId = $validUserIds[0] ?? null;
    if (!$defaultUserId) {
        throw new Exception("No valid users found to assign as default user!");
    }
    
    // Fix tickets with invalid user_id (try NULL first, if fails use default user)
    if ($useLaravel) {
        $invalidTickets = \Illuminate\Support\Facades\DB::table('supports')
            ->whereNotNull('user_id')
            ->whereNotIn('user_id', $validUserIds)
            ->count();
            
        if ($invalidTickets > 0) {
            try {
                // Try to set to NULL first
                \Illuminate\Support\Facades\DB::table('supports')
                    ->whereNotNull('user_id')
                    ->whereNotIn('user_id', $validUserIds)
                    ->update(['user_id' => null]);
                
                $results['fixes_applied'][] = "Fixed $invalidTickets tickets with invalid user_id (set to NULL)";
            } catch (\Exception $e) {
                // If NULL fails, assign default user
                \Illuminate\Support\Facades\DB::table('supports')
                    ->whereNotNull('user_id')
                    ->whereNotIn('user_id', $validUserIds)
                    ->update(['user_id' => $defaultUserId]);
                
                $results['fixes_applied'][] = "Fixed $invalidTickets tickets with invalid user_id (assigned default user ID: $defaultUserId)";
            }
        }
    } else if ($pdo !== null) {
        $placeholders = implode(',', array_fill(0, count($validUserIds), '?'));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM supports WHERE user_id IS NOT NULL AND user_id NOT IN ($placeholders)");
        $stmt->execute($validUserIds);
        $invalidTickets = $stmt->fetchColumn();
        
        if ($invalidTickets > 0) {
            try {
                // Try to set to NULL first
                $stmt = $pdo->prepare("UPDATE supports SET user_id = NULL WHERE user_id IS NOT NULL AND user_id NOT IN ($placeholders)");
                $stmt->execute($validUserIds);
                $results['fixes_applied'][] = "Fixed $invalidTickets tickets with invalid user_id (set to NULL)";
            } catch (\Exception $e) {
                // If NULL fails, assign default user
                $stmt = $pdo->prepare("UPDATE supports SET user_id = ? WHERE user_id IS NOT NULL AND user_id NOT IN ($placeholders)");
                $params = array_merge([$defaultUserId], $validUserIds);
                $stmt->execute($params);
                $results['fixes_applied'][] = "Fixed $invalidTickets tickets with invalid user_id (assigned default user ID: $defaultUserId)";
            }
        }
    }
    
    // Fix comments with invalid user_id (try NULL first, if fails use default user)
    if ($useLaravel) {
        $invalidComments = \Illuminate\Support\Facades\DB::table('comments')
            ->whereNotNull('user_id')
            ->whereNotIn('user_id', $validUserIds)
            ->count();
            
        if ($invalidComments > 0) {
            try {
                // Try to set to NULL first
                \Illuminate\Support\Facades\DB::table('comments')
                    ->whereNotNull('user_id')
                    ->whereNotIn('user_id', $validUserIds)
                    ->update(['user_id' => null]);
                
                $results['fixes_applied'][] = "Fixed $invalidComments comments with invalid user_id (set to NULL)";
            } catch (\Exception $e) {
                // If NULL fails, assign default user
                \Illuminate\Support\Facades\DB::table('comments')
                    ->whereNotNull('user_id')
                    ->whereNotIn('user_id', $validUserIds)
                    ->update(['user_id' => $defaultUserId]);
                
                $results['fixes_applied'][] = "Fixed $invalidComments comments with invalid user_id (assigned default user ID: $defaultUserId)";
            }
        }
    } else if ($pdo !== null) {
        $placeholders = implode(',', array_fill(0, count($validUserIds), '?'));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE user_id IS NOT NULL AND user_id NOT IN ($placeholders)");
        $stmt->execute($validUserIds);
        $invalidComments = $stmt->fetchColumn();
        
        if ($invalidComments > 0) {
            try {
                // Try to set to NULL first
                $stmt = $pdo->prepare("UPDATE comments SET user_id = NULL WHERE user_id IS NOT NULL AND user_id NOT IN ($placeholders)");
                $stmt->execute($validUserIds);
                $results['fixes_applied'][] = "Fixed $invalidComments comments with invalid user_id (set to NULL)";
            } catch (\Exception $e) {
                // If NULL fails, assign default user
                $stmt = $pdo->prepare("UPDATE comments SET user_id = ? WHERE user_id IS NOT NULL AND user_id NOT IN ($placeholders)");
                $params = array_merge([$defaultUserId], $validUserIds);
                $stmt->execute($params);
                $results['fixes_applied'][] = "Fixed $invalidComments comments with invalid user_id (assigned default user ID: $defaultUserId)";
            }
        }
    }
    
    // Fix webinars with invalid creator_id (assign default user since creator_id might be NOT NULL)
    if ($useLaravel) {
        $invalidCreatorWebinars = \Illuminate\Support\Facades\DB::table('webinars')
            ->whereNotNull('creator_id')
            ->whereNotIn('creator_id', $validUserIds)
            ->count();
            
        if ($invalidCreatorWebinars > 0) {
            try {
                // Try to set to NULL first
                \Illuminate\Support\Facades\DB::table('webinars')
                    ->whereNotNull('creator_id')
                    ->whereNotIn('creator_id', $validUserIds)
                    ->update(['creator_id' => null]);
                
                $results['fixes_applied'][] = "Fixed $invalidCreatorWebinars webinars with invalid creator_id (set to NULL)";
            } catch (\Exception $e) {
                // If NULL fails, assign default user
                \Illuminate\Support\Facades\DB::table('webinars')
                    ->whereNotNull('creator_id')
                    ->whereNotIn('creator_id', $validUserIds)
                    ->update(['creator_id' => $defaultUserId]);
                
                $results['fixes_applied'][] = "Fixed $invalidCreatorWebinars webinars with invalid creator_id (assigned default user ID: $defaultUserId)";
            }
        }
    } else if ($pdo !== null) {
        $placeholders = implode(',', array_fill(0, count($validUserIds), '?'));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM webinars WHERE creator_id IS NOT NULL AND creator_id NOT IN ($placeholders)");
        $stmt->execute($validUserIds);
        $invalidCreatorWebinars = $stmt->fetchColumn();
        
        if ($invalidCreatorWebinars > 0) {
            try {
                // Try to set to NULL first
                $stmt = $pdo->prepare("UPDATE webinars SET creator_id = NULL WHERE creator_id IS NOT NULL AND creator_id NOT IN ($placeholders)");
                $stmt->execute($validUserIds);
                $results['fixes_applied'][] = "Fixed $invalidCreatorWebinars webinars with invalid creator_id (set to NULL)";
            } catch (\Exception $e) {
                // If NULL fails, assign default user
                $stmt = $pdo->prepare("UPDATE webinars SET creator_id = ? WHERE creator_id IS NOT NULL AND creator_id NOT IN ($placeholders)");
                $params = array_merge([$defaultUserId], $validUserIds);
                $stmt->execute($params);
                $results['fixes_applied'][] = "Fixed $invalidCreatorWebinars webinars with invalid creator_id (assigned default user ID: $defaultUserId)";
            }
        }
    }
    
    $results['status'] = 'completed';
    $results['timestamp_end'] = date('Y-m-d H:i:s');
    
    if (empty($results['fixes_applied'])) {
        $results['message'] = "No data integrity issues found! All relationships are valid.";
    } else {
        $results['message'] = "Successfully fixed " . count($results['fixes_applied']) . " data integrity issue(s)!";
    }
    
} catch (Exception $e) {
    $results['status'] = 'error';
    $results['message'] = $e->getMessage();
    $results['errors'][] = $e->getMessage();
    $results['timestamp_end'] = date('Y-m-d H:i:s');
}

echo json_encode($results, JSON_PRETTY_PRINT);
