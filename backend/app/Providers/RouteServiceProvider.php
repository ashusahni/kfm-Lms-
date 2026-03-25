<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';
    protected $api_namespace = 'App\Http\Controllers\Api';


    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        // Register emergency routes ONLY in non-production environments or when explicitly enabled
        // These routes are for database maintenance and should be disabled in production
        if (config('app.env') !== 'production' || env('ENABLE_MAINTENANCE_ROUTES', false)) {
            $this->mapEmergencyMigrationRoute();
            $this->mapEmergencyImportRoute();
            $this->mapAutoFixFromKfmRoute();
            $this->mapImportKfmDataRoute();
            $this->mapCreateAllMissingTablesRoute();
            $this->mapFixMissingRelationshipsRoute();
        }
        
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapAdminRoutes();

        // Do not register Laravel Blade panel routes: student panel is only on the React app (FRONTEND_URL).
        // Backend (8000) serves only /admin and /api (and required web hooks). Set FRONTEND_URL and use that URL for the app.
        // if (!config('frontend.serve_react', false)) {
        //     $this->mapPanelRoutes();
        // }

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->api_namespace)
            ->group(base_path('routes/api.php'));

    }

    /**
     * Define the "admin" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        Route::namespace($this->namespace)
            ->group(base_path('routes/admin.php'));
    }

    protected function mapPanelRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/panel.php'));
    }

    /**
     * Emergency migration route - must bypass ALL middleware to work when DB is empty
     */
    protected function mapEmergencyMigrationRoute()
    {
        Route::get('/emergencyDatabaseUpdate', function () {
            // Security: Check for secret key
            $SECRET_KEY = env('MIGRATION_SECRET_KEY', 'run_migrations_2024_temp_key_change_me');
            
            if (!isset($_GET['key']) || $_GET['key'] !== $SECRET_KEY) {
                return response()->json([
                    'error' => 'Security check failed.',
                    'message' => 'Add ?key=YOUR_SECRET_KEY to the URL.',
                    'instructions' => [
                        'Option 1: Use default key: ?key=run_migrations_2024_temp_key_change_me',
                        'Option 2: Set MIGRATION_SECRET_KEY in your .env file',
                    ],
                ], 403);
            }

            set_time_limit(300);
            $results = [];
            $errors = [];
            $warnings = [];

            // Check status
            if (isset($_GET['status'])) {
                try {
                    \Illuminate\Support\Facades\Artisan::call('migrate:status', ['--force' => true]);
                    $status = \Illuminate\Support\Facades\Artisan::output();
                    
                    $criticalTables = ['users', 'categories', 'roles', 'sections', 'permissions', 'settings', 'setting_translations'];
                    $existingTables = [];
                    $missingTables = [];
                    
                    try {
                        $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
                        $tableNames = array_map(function($table) {
                            return array_values((array)$table)[0];
                        }, $tables);
                        
                        foreach ($criticalTables as $table) {
                            if (in_array($table, $tableNames)) {
                                $existingTables[] = $table;
                            } else {
                                $missingTables[] = $table;
                            }
                        }
                    } catch (\Exception $e) {
                        // DB connection issue
                    }
                    
                    return response()->json([
                        'migration_status' => $status,
                        'existing_tables' => $existingTables,
                        'missing_tables' => $missingTables,
                        'all_tables_count' => count($tableNames ?? []),
                        'message' => empty($missingTables) 
                            ? 'All critical tables exist.'
                            : 'Some critical tables are missing.',
                    ], 200);
                } catch (\Exception $e) {
                    return response()->json([
                        'error' => 'Could not check status',
                        'message' => $e->getMessage(),
                    ], 500);
                }
            }

            // Fresh start option
            if (isset($_GET['fresh']) && $_GET['fresh'] == '1') {
                try {
                    // Disable foreign key checks temporarily to avoid constraint errors
                    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                    
                    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true, '--seed' => false]);
                    $results['fresh_migrate'] = \Illuminate\Support\Facades\Artisan::output();
                    
                    // Re-enable foreign key checks
                    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                } catch (\Exception $e) {
                    // Re-enable foreign key checks even on error
                    try {
                        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    } catch (\Exception $fkError) {
                        // Ignore
                    }
                    
                    // If it's a foreign key error, try to fix the meetings table issue
                    if (str_contains($e->getMessage(), 'teacher_id') && str_contains($e->getMessage(), 'meetings')) {
                        try {
                            // Fix the meetings table - remove the invalid foreign key
                            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `meetings` DROP FOREIGN KEY IF EXISTS `meetings_teacher_id_foreign`;");
                            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                            $warnings[] = 'Fixed invalid foreign key in meetings table. You may need to add teacher_id column manually if needed.';
                        } catch (\Exception $fixError) {
                            // Ignore
                        }
                    }
                    
                    return response()->json([
                        'error' => 'Fresh migration failed',
                        'message' => $e->getMessage(),
                        'suggestion' => 'Try running migrations without fresh (remove &fresh=1) or fix the migration files manually.',
                    ], 500);
                }
            }

            try {
                // Run Migrations
                if (!isset($_GET['fresh'])) {
                    // Use migrate:install first to ensure migrations table exists
                    try {
                        \Illuminate\Support\Facades\Artisan::call('migrate:install', ['--force' => true]);
                    } catch (\Exception $e) {
                        // Migrations table might already exist, ignore
                    }
                    
                    // Fix known migration issues before running
                    try {
                        // Fix meetings table - remove invalid foreign key if it exists
                        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                        try {
                            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `meetings` DROP FOREIGN KEY IF EXISTS `meetings_teacher_id_foreign`;");
                        } catch (\Exception $e) {
                            // Table or constraint might not exist, ignore
                        }
                        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    } catch (\Exception $fixError) {
                        // Ignore fix errors
                    }
                    
                    // Run all migrations with --force
                    try {
                        // Temporarily disable foreign key checks to avoid constraint errors during migration
                        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                        
                        // Run migrations multiple times to catch any that were skipped
                        // Keep running until no new migrations are executed
                        $maxRetries = 5;
                        $allOutput = '';
                        
                        for ($i = 0; $i < $maxRetries; $i++) {
                            // Get count of migrations before running
                            try {
                                $beforeCount = \Illuminate\Support\Facades\DB::table('migrations')->count();
                            } catch (\Exception $e) {
                                $beforeCount = 0;
                            }
                            
                            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                            $output = \Illuminate\Support\Facades\Artisan::output();
                            $allOutput .= ($i > 0 ? "\n--- Retry " . ($i + 1) . " ---\n" : '') . $output;
                            
                            // Get count after running
                            try {
                                $afterCount = \Illuminate\Support\Facades\DB::table('migrations')->count();
                            } catch (\Exception $e) {
                                $afterCount = $beforeCount;
                            }
                            
                            // If no new migrations were added, break
                            if ($afterCount == $beforeCount && ($i > 0 || str_contains($output, 'Nothing to migrate'))) {
                                break;
                            }
                            
                            // If output says "Nothing to migrate", break
                            if (str_contains($output, 'Nothing to migrate')) {
                                break;
                            }
                        }
                        
                        $results['migrations'] = $allOutput;
                        
                        // Re-enable foreign key checks
                        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                        
                        // If output is empty or shows "Nothing to migrate", check what's actually missing
                        if (empty(trim($allOutput)) || str_contains($allOutput, 'Nothing to migrate')) {
                            // Check which migrations haven't run by checking the migrations table
                            try {
                                $runMigrations = \Illuminate\Support\Facades\DB::table('migrations')->pluck('migration')->toArray();
                                $allMigrations = glob(base_path('database/migrations/*.php'));
                                $missingMigrations = [];
                                
                                foreach ($allMigrations as $migrationFile) {
                                    $migrationName = basename($migrationFile, '.php');
                                    $migrationName = preg_replace('/^\d+_\d+_\d+_\d+_/', '', $migrationName);
                                    
                                    if (!in_array($migrationName, $runMigrations)) {
                                        $missingMigrations[] = $migrationName;
                                    }
                                }
                                
                                if (!empty($missingMigrations)) {
                                    $warnings[] = 'Some migrations appear to be missing from migrations table. Attempting to run them...';
                                    // Try running migrations again - Laravel should pick up missing ones
                                    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                                    $results['migrations'] .= "\n" . \Illuminate\Support\Facades\Artisan::output();
                                }
                            } catch (\Exception $checkError) {
                                // Can't check, continue
                            }
                        }
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Re-enable foreign key checks on error
                        try {
                            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                        } catch (\Exception $fkError) {
                            // Ignore
                        }
                        
                        if (str_contains($e->getMessage(), 'already exists') || str_contains($e->getMessage(), 'Base table or view already exists')) {
                            $warnings[] = 'Some tables already exist. The migrations table may be out of sync.';
                            $warnings[] = 'Automatically enabling fix mode to handle existing tables...';
                            
                            // Automatically enable fix mode if not already set
                            if (!isset($_GET['fix']) || $_GET['fix'] != '1') {
                                $_GET['fix'] = '1';
                            }
                            
                            if (isset($_GET['fix']) && $_GET['fix'] == '1') {
                                $warnings[] = 'Attempting to continue with remaining migrations...';
                                
                                // Try to run migrations step by step
                                try {
                                    // First, ensure migrations table exists
                                    if (!\Illuminate\Support\Facades\Schema::hasTable('migrations')) {
                                        \Illuminate\Support\Facades\Artisan::call('migrate:install', ['--force' => true]);
                                    }
                                    
                                    // Run migrations again - Laravel will skip existing tables
                                    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                                    $results['migrations'] = \Illuminate\Support\Facades\Artisan::output();
                                    
                                    // If still having issues, try running without foreign key checks temporarily
                                    if (empty($results['migrations']) || str_contains($results['migrations'], 'Nothing to migrate')) {
                                        $warnings[] = 'Migrations table may be out of sync. Checking for missing tables...';
                                        
                                    // List of critical tables that should exist
                                    $criticalTables = [
                                        'users', 'settings', 'setting_translations', 'notifications', 
                                        'notifications_status', 'roles', 'permissions', 'sections',
                                        'categories', 'webinars', 'product_orders'
                                    ];
                                    
                                    $missingTables = [];
                                    foreach ($criticalTables as $table) {
                                        try {
                                            if (!\Illuminate\Support\Facades\Schema::hasTable($table)) {
                                                $missingTables[] = $table;
                                            }
                                        } catch (\Exception $checkTableError) {
                                            // Can't check, assume missing
                                            $missingTables[] = $table;
                                        }
                                    }
                                    
                                        if (!empty($missingTables)) {
                                            $warnings[] = 'Missing tables detected: ' . implode(', ', $missingTables);
                                            $warnings[] = 'Attempting to create missing tables directly...';
                                            
                                            // Create missing tables directly
                                            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                                            
                                            foreach ($missingTables as $table) {
                                                try {
                                                    if ($table === 'settings' && !\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                                                        \Illuminate\Support\Facades\DB::statement("
                                                            CREATE TABLE IF NOT EXISTS `settings` (
                                                                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                                                `page` enum('general','financial','personalization','notifications','seo','customization','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
                                                                `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                                `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                                                `updated_at` int(11) DEFAULT NULL,
                                                                PRIMARY KEY (`id`),
                                                                KEY `name` (`name`)
                                                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                                                        ");
                                                        $results['migrations'] .= "\n✓ Created settings table";
                                                    }
                                                    
                                                    if ($table === 'setting_translations' && !\Illuminate\Support\Facades\Schema::hasTable('setting_translations')) {
                                                        \Illuminate\Support\Facades\DB::statement("
                                                            CREATE TABLE IF NOT EXISTS `setting_translations` (
                                                                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                                                `setting_id` int(10) unsigned NOT NULL,
                                                                `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                                `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                                                PRIMARY KEY (`id`),
                                                                KEY `setting_id` (`setting_id`)
                                                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                                                        ");
                                                        $results['migrations'] .= "\n✓ Created setting_translations table";
                                                    }
                                                    
                                                    if ($table === 'notifications' && !\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                                                        \Illuminate\Support\Facades\DB::statement("
                                                            CREATE TABLE IF NOT EXISTS `notifications` (
                                                                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                                                `user_id` int(10) unsigned DEFAULT NULL,
                                                                `sender_id` int(10) unsigned DEFAULT NULL,
                                                                `group_id` int(10) unsigned DEFAULT NULL,
                                                                `webinar_id` int(10) unsigned DEFAULT NULL,
                                                                `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                                `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                                                `sender` enum('system','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'system',
                                                                `type` enum('single','all_users','students','instructors','organizations','group','course_students') COLLATE utf8mb4_unicode_ci NOT NULL,
                                                                `created_at` int(10) unsigned NOT NULL,
                                                                PRIMARY KEY (`id`),
                                                                KEY `user_id` (`user_id`),
                                                                KEY `group_id` (`group_id`),
                                                                KEY `webinar_id` (`webinar_id`)
                                                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                                                        ");
                                                        $results['migrations'] .= "\n✓ Created notifications table";
                                                    }
                                                    
                                                    if ($table === 'notifications_status' && !\Illuminate\Support\Facades\Schema::hasTable('notifications_status')) {
                                                        \Illuminate\Support\Facades\DB::statement("
                                                            CREATE TABLE IF NOT EXISTS `notifications_status` (
                                                                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                                                `user_id` int(10) unsigned NOT NULL,
                                                                `notification_id` int(10) unsigned NOT NULL,
                                                                `seen_at` int(10) unsigned NOT NULL,
                                                                PRIMARY KEY (`id`),
                                                                KEY `notification_id` (`notification_id`),
                                                                KEY `user_id` (`user_id`)
                                                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                                                        ");
                                                        $results['migrations'] .= "\n✓ Created notifications_status table";
                                                    }
                                                    
                                                    if ($table === 'product_orders' && !\Illuminate\Support\Facades\Schema::hasTable('product_orders')) {
                                                        \Illuminate\Support\Facades\DB::statement("
                                                            CREATE TABLE IF NOT EXISTS `product_orders` (
                                                                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                                                `user_id` int(10) unsigned NOT NULL,
                                                                `product_id` int(10) unsigned NOT NULL,
                                                                `sale_id` int(10) unsigned DEFAULT NULL,
                                                                `quantity` int(11) NOT NULL DEFAULT 1,
                                                                `amount` decimal(15,2) NOT NULL,
                                                                `total_amount` decimal(15,2) NOT NULL,
                                                                `discount` decimal(15,2) DEFAULT NULL,
                                                                `tax` decimal(15,2) DEFAULT NULL,
                                                                `status` enum('pending','paid','canceled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
                                                                `created_at` int(10) unsigned NOT NULL,
                                                                `updated_at` int(10) unsigned DEFAULT NULL,
                                                                PRIMARY KEY (`id`),
                                                                KEY `user_id` (`user_id`),
                                                                KEY `product_id` (`product_id`),
                                                                KEY `sale_id` (`sale_id`)
                                                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                                                        ");
                                                        $results['migrations'] .= "\n✓ Created product_orders table";
                                                    }
                                                    
                                                } catch (\Exception $createError) {
                                                    $warnings[] = "Could not create $table: " . $createError->getMessage();
                                                }
                                            }
                                            
                                            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                                            
                                            // Try running migrations one more time after creating tables
                                            try {
                                                \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                                                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                                                $results['migrations'] .= "\n\nFinal migration run: " . \Illuminate\Support\Facades\Artisan::output();
                                                \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                                            } catch (\Exception $retryError) {
                                                $warnings[] = 'Final migration run had issues: ' . $retryError->getMessage();
                                            }
                                        }
                                    }
                                } catch (\Exception $e2) {
                                    if (str_contains($e2->getMessage(), 'already exists')) {
                                        $warnings[] = 'Some migrations skipped - tables already exist.';
                                        $results['migrations'] = 'Partial migration completed.';
                                    } else {
                                        $errors['migrations'] = $e2->getMessage();
                                    }
                                }
                            } else {
                                $errors['migrations'] = $e->getMessage();
                                $results['migrations'] = 'Add ?fix=1 to continue, or ?fresh=1 to reset (WARNING: deletes all data!)';
                            }
                        } else {
                            throw $e;
                        }
                    }
                }

                // FINAL SAFETY CHECK: Always ensure critical tables exist
                $criticalTablesToCreate = [
                    'settings' => "
                        CREATE TABLE IF NOT EXISTS `settings` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `page` enum('general','financial','personalization','notifications','seo','customization','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
                            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
                            `updated_at` int(11) DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            KEY `name` (`name`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'setting_translations' => "
                        CREATE TABLE IF NOT EXISTS `setting_translations` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `setting_id` int(10) unsigned NOT NULL,
                            `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `setting_id` (`setting_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'notifications' => "
                        CREATE TABLE IF NOT EXISTS `notifications` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `user_id` int(10) unsigned DEFAULT NULL,
                            `sender_id` int(10) unsigned DEFAULT NULL,
                            `group_id` int(10) unsigned DEFAULT NULL,
                            `webinar_id` int(10) unsigned DEFAULT NULL,
                            `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
                            `sender` enum('system','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'system',
                            `type` enum('single','all_users','students','instructors','organizations','group','course_students') COLLATE utf8mb4_unicode_ci NOT NULL,
                            `created_at` int(10) unsigned NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `user_id` (`user_id`),
                            KEY `group_id` (`group_id`),
                            KEY `webinar_id` (`webinar_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'notifications_status' => "
                        CREATE TABLE IF NOT EXISTS `notifications_status` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `user_id` int(10) unsigned NOT NULL,
                            `notification_id` int(10) unsigned NOT NULL,
                            `seen_at` int(10) unsigned NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `notification_id` (`notification_id`),
                            KEY `user_id` (`user_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'product_orders' => "
                        CREATE TABLE IF NOT EXISTS `product_orders` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `user_id` int(10) unsigned NOT NULL,
                            `product_id` int(10) unsigned NOT NULL,
                            `sale_id` int(10) unsigned DEFAULT NULL,
                            `quantity` int(11) NOT NULL DEFAULT 1,
                            `amount` decimal(15,2) NOT NULL,
                            `total_amount` decimal(15,2) NOT NULL,
                            `discount` decimal(15,2) DEFAULT NULL,
                            `tax` decimal(15,2) DEFAULT NULL,
                            `status` enum('pending','paid','canceled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
                            `created_at` int(10) unsigned NOT NULL,
                            `updated_at` int(10) unsigned DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            KEY `user_id` (`user_id`),
                            KEY `product_id` (`product_id`),
                            KEY `sale_id` (`sale_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'cart' => "
                        CREATE TABLE IF NOT EXISTS `cart` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `creator_id` int(10) unsigned NOT NULL,
                            `webinar_id` int(10) unsigned DEFAULT NULL,
                            `ticket_id` int(10) unsigned DEFAULT NULL,
                            `reserve_meeting_id` int(10) unsigned DEFAULT NULL,
                            `subscribe_id` int(10) unsigned DEFAULT NULL,
                            `promotion_id` int(10) unsigned DEFAULT NULL,
                            `bundle_id` int(10) unsigned DEFAULT NULL,
                            `product_order_id` int(10) unsigned DEFAULT NULL,
                            `special_offer_id` int(10) unsigned DEFAULT NULL,
                            `installment_order_id` int(10) unsigned DEFAULT NULL,
                            `gift_id` int(10) unsigned DEFAULT NULL,
                            `batch_id` int(10) unsigned DEFAULT NULL,
                            `created_at` int(10) unsigned NOT NULL,
                            `updated_at` int(10) unsigned DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            KEY `creator_id` (`creator_id`),
                            KEY `webinar_id` (`webinar_id`),
                            KEY `bundle_id` (`bundle_id`),
                            KEY `product_order_id` (`product_order_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'products' => "
                        CREATE TABLE IF NOT EXISTS `products` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `creator_id` int(10) unsigned NOT NULL,
                            `type` enum('physical','virtual') COLLATE utf8mb4_unicode_ci NOT NULL,
                            `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `category_id` int(10) unsigned DEFAULT NULL,
                            `price` bigint(20) unsigned DEFAULT NULL,
                            `point` bigint(20) unsigned DEFAULT NULL,
                            `unlimited_inventory` tinyint(1) NOT NULL DEFAULT 0,
                            `ordering` tinyint(1) NOT NULL DEFAULT 0,
                            `inventory` int(10) unsigned DEFAULT NULL,
                            `inventory_warning` int(10) unsigned DEFAULT NULL,
                            `inventory_updated_at` bigint(20) unsigned DEFAULT NULL,
                            `delivery_fee` bigint(20) unsigned DEFAULT NULL,
                            `delivery_estimated_time` int(10) unsigned DEFAULT NULL,
                            `message_for_reviewer` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `tax` int(10) unsigned DEFAULT NULL,
                            `commission` int(10) unsigned DEFAULT NULL,
                            `status` enum('draft','pending','active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL,
                            `updated_at` bigint(20) unsigned NOT NULL,
                            `created_at` bigint(20) unsigned NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `type` (`type`),
                            KEY `slug` (`slug`),
                            KEY `creator_id` (`creator_id`),
                            KEY `category_id` (`category_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'product_translations' => "
                        CREATE TABLE IF NOT EXISTS `product_translations` (
                            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                            `product_id` int(10) unsigned NOT NULL,
                            `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `seo_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `summary` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            KEY `locale` (`locale`),
                            KEY `product_id` (`product_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'group_users' => "
                        CREATE TABLE IF NOT EXISTS `group_users` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `group_id` int(10) unsigned NOT NULL,
                            `user_id` int(10) unsigned NOT NULL,
                            `created_at` int(10) unsigned NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `group_id` (`group_id`),
                            KEY `user_id` (`user_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'bundles' => "
                        CREATE TABLE IF NOT EXISTS `bundles` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `creator_id` int(10) unsigned NOT NULL,
                            `teacher_id` int(10) unsigned NOT NULL,
                            `category_id` int(10) unsigned DEFAULT NULL,
                            `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `image_cover` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `video_demo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `video_demo_source` enum('upload','youtube','vimeo','external_link','iframe') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `price` int(10) unsigned DEFAULT NULL,
                            `points` int(10) unsigned DEFAULT NULL,
                            `subscribe` tinyint(1) NOT NULL DEFAULT 0,
                            `access_days` int(10) unsigned DEFAULT NULL,
                            `message_for_reviewer` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `status` enum('active','pending','is_draft','inactive') COLLATE utf8mb4_unicode_ci NOT NULL,
                            `created_at` bigint(20) unsigned NOT NULL,
                            `updated_at` bigint(20) unsigned DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            KEY `slug` (`slug`),
                            KEY `creator_id` (`creator_id`),
                            KEY `teacher_id` (`teacher_id`),
                            KEY `category_id` (`category_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'bundle_translations' => "
                        CREATE TABLE IF NOT EXISTS `bundle_translations` (
                            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                            `bundle_id` int(10) unsigned NOT NULL,
                            `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `seo_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            KEY `locale` (`locale`),
                            KEY `bundle_id` (`bundle_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'sales' => "
                        CREATE TABLE IF NOT EXISTS `sales` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `user_id` int(10) unsigned NOT NULL,
                            `buyer_id` int(10) unsigned DEFAULT NULL,
                            `seller_id` int(10) unsigned DEFAULT NULL,
                            `order_id` int(10) unsigned NOT NULL,
                            `webinar_id` int(10) unsigned DEFAULT NULL,
                            `meeting_id` int(10) unsigned DEFAULT NULL,
                            `meeting_time_id` int(10) unsigned DEFAULT NULL,
                            `ticket_id` int(10) unsigned DEFAULT NULL,
                            `subscribe_id` int(10) unsigned DEFAULT NULL,
                            `promotion_id` int(10) unsigned DEFAULT NULL,
                            `promotion_type` enum('fixed_amount','percentage') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `registration_package_id` int(10) unsigned DEFAULT NULL,
                            `product_id` int(10) unsigned DEFAULT NULL,
                            `bundle_id` int(10) unsigned DEFAULT NULL,
                            `gift_id` int(10) unsigned DEFAULT NULL,
                            `installment_order_id` int(10) unsigned DEFAULT NULL,
                            `type` enum('webinar','meeting','subscribe','promotion','registration_package','product','bundle') COLLATE utf8mb4_unicode_ci NOT NULL,
                            `payment_method` enum('credit','payment_channel','subscribe') COLLATE utf8mb4_unicode_ci NOT NULL,
                            `amount` decimal(15,2) unsigned NOT NULL,
                            `tax` decimal(15,2) unsigned DEFAULT NULL,
                            `commission` decimal(15,2) unsigned DEFAULT NULL,
                            `discount` decimal(15,2) unsigned DEFAULT NULL,
                            `total_amount` decimal(15,2) unsigned NOT NULL,
                            `product_delivery_fee` decimal(15,2) DEFAULT NULL,
                            `manual_added` tinyint(1) NOT NULL DEFAULT 0,
                            `batch_id` int(10) unsigned DEFAULT NULL,
                            `created_at` int(10) unsigned NOT NULL,
                            `refund_at` int(10) unsigned DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            KEY `user_id` (`user_id`),
                            KEY `buyer_id` (`buyer_id`),
                            KEY `seller_id` (`seller_id`),
                            KEY `order_id` (`order_id`),
                            KEY `webinar_id` (`webinar_id`),
                            KEY `product_id` (`product_id`),
                            KEY `bundle_id` (`bundle_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'payouts' => "
                        CREATE TABLE IF NOT EXISTS `payouts` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `user_id` int(10) unsigned NOT NULL,
                            `user_selected_bank_id` int(10) unsigned DEFAULT NULL,
                            `amount` decimal(13,2) NOT NULL,
                            `status` enum('waiting','done','reject') COLLATE utf8mb4_unicode_ci NOT NULL,
                            `created_at` int(10) unsigned NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `user_id` (`user_id`),
                            KEY `user_selected_bank_id` (`user_selected_bank_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'offline_payments' => "
                        CREATE TABLE IF NOT EXISTS `offline_payments` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `user_id` int(10) unsigned NOT NULL,
                            `offline_bank_id` int(10) unsigned DEFAULT NULL,
                            `amount` int(10) unsigned NOT NULL,
                            `bank` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `reference_number` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `pay_date` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `status` enum('waiting','approved','reject') COLLATE utf8mb4_unicode_ci NOT NULL,
                            `created_at` int(10) unsigned NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `user_id` (`user_id`),
                            KEY `offline_bank_id` (`offline_bank_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'ai_content_templates' => "
                        CREATE TABLE IF NOT EXISTS `ai_content_templates` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `type` enum('text','image') COLLATE utf8mb4_unicode_ci NOT NULL,
                            `enable_length` tinyint(1) NOT NULL DEFAULT 0,
                            `length` int(10) unsigned DEFAULT NULL,
                            `image_size` enum('256','512','1024') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `enable` tinyint(1) NOT NULL DEFAULT 0,
                            `created_at` bigint(20) unsigned NOT NULL,
                            PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'ai_content_template_translations' => "
                        CREATE TABLE IF NOT EXISTS `ai_content_template_translations` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `ai_content_template_id` int(10) unsigned NOT NULL,
                            `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `prompt` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            KEY `locale` (`locale`),
                            KEY `ai_content_template_id` (`ai_content_template_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'ai_contents' => "
                        CREATE TABLE IF NOT EXISTS `ai_contents` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `user_id` int(10) unsigned NOT NULL,
                            `service_type` enum('text','image') COLLATE utf8mb4_unicode_ci NOT NULL,
                            `service_id` int(10) unsigned DEFAULT NULL,
                            `keyword` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `language` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `prompt` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `result` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `created_at` bigint(20) unsigned NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `user_id` (`user_id`),
                            KEY `service_id` (`service_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'accounting' => "
                        CREATE TABLE IF NOT EXISTS `accounting` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `user_id` int(10) unsigned DEFAULT NULL,
                            `creator_id` int(10) unsigned DEFAULT NULL,
                            `webinar_id` int(10) unsigned DEFAULT NULL,
                            `meeting_id` int(10) unsigned DEFAULT NULL,
                            `meeting_time_id` int(10) unsigned DEFAULT NULL,
                            `bundle_id` int(10) unsigned DEFAULT NULL,
                            `installment_order_id` int(10) unsigned DEFAULT NULL,
                            `system` tinyint(1) NOT NULL DEFAULT 0,
                            `tax` tinyint(1) NOT NULL DEFAULT 0,
                            `amount` int(11) NOT NULL,
                            `type` enum('addiction','deduction') COLLATE utf8mb4_unicode_ci NOT NULL,
                            `type_account` enum('income','asset') COLLATE utf8mb4_unicode_ci NOT NULL,
                            `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `affiliate` tinyint(1) NOT NULL DEFAULT 0,
                            `created_at` int(10) unsigned NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `user_id` (`user_id`),
                            KEY `creator_id` (`creator_id`),
                            KEY `webinar_id` (`webinar_id`),
                            KEY `bundle_id` (`bundle_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'sales_log' => "
                        CREATE TABLE IF NOT EXISTS `sales_log` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `sale_id` int(10) unsigned NOT NULL,
                            `viewed_at` int(10) unsigned NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `sale_id` (`sale_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'supports' => "
                        CREATE TABLE IF NOT EXISTS `supports` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `user_id` int(10) unsigned NOT NULL,
                            `webinar_id` int(10) unsigned DEFAULT NULL,
                            `department_id` int(10) unsigned DEFAULT NULL,
                            `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `status` enum('open','close','replied') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
                            `created_at` int(10) unsigned DEFAULT NULL,
                            `updated_at` int(10) unsigned DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            KEY `user_id` (`user_id`),
                            KEY `webinar_id` (`webinar_id`),
                            KEY `department_id` (`department_id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'support_departments' => "
                        CREATE TABLE IF NOT EXISTS `support_departments` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `created_at` int(10) unsigned DEFAULT NULL,
                            PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'currencies' => "
                        CREATE TABLE IF NOT EXISTS `currencies` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `currency_position` enum('left','right','left_with_space','right_with_space') COLLATE utf8mb4_unicode_ci NOT NULL,
                            `currency_separator` enum('dot','comma') COLLATE utf8mb4_unicode_ci NOT NULL,
                            `currency_decimal` int(10) unsigned DEFAULT NULL,
                            `exchange_rate` float DEFAULT NULL,
                            `order` int(10) unsigned DEFAULT NULL,
                            `created_at` bigint(20) unsigned NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `order` (`order`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'payment_channels' => "
                        CREATE TABLE IF NOT EXISTS `payment_channels` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `class_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
                            `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `settings` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `created_at` int(10) unsigned NOT NULL,
                            PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'webinar_translations' => "
                        CREATE TABLE IF NOT EXISTS `webinar_translations` (
                            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                            `webinar_id` int(10) unsigned NOT NULL,
                            `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `seo_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            KEY `webinar_translations_webinar_id_foreign` (`webinar_id`),
                            KEY `webinar_translations_locale_index` (`locale`),
                            CONSTRAINT `webinar_translations_webinar_id_foreign` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                    'agora_history' => "
                        CREATE TABLE IF NOT EXISTS `agora_history` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `session_id` int(10) unsigned NOT NULL,
                            `start_at` int(10) unsigned NOT NULL,
                            `end_at` int(10) unsigned DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            KEY `agora_history_session_id_foreign` (`session_id`),
                            CONSTRAINT `agora_history_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ",
                ];
                
                try {
                    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                    foreach ($criticalTablesToCreate as $tableName => $createSQL) {
                        try {
                            if (!\Illuminate\Support\Facades\Schema::hasTable($tableName)) {
                                \Illuminate\Support\Facades\DB::statement($createSQL);
                                $results['safety_check'] = ($results['safety_check'] ?? '') . "\n✓ Created missing table: $tableName";
                            }
                        } catch (\Exception $createError) {
                            // Table might already exist or other error, continue
                        }
                    }
                    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                } catch (\Exception $safetyError) {
                    // Ignore safety check errors
                }
                
                // Add missing columns to existing tables
                try {
                    // Add order column to categories if missing
                    if (\Illuminate\Support\Facades\Schema::hasTable('categories')) {
                        $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `categories` LIKE 'order'");
                        if (empty($columns)) {
                            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `categories` ADD COLUMN `order` int(10) unsigned DEFAULT NULL;");
                            $results['safety_check'] = ($results['safety_check'] ?? '') . "\n✓ Added order column to categories";
                        }
                    }
                    
                    // Add type column to webinars if missing
                    if (\Illuminate\Support\Facades\Schema::hasTable('webinars')) {
                        $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `webinars` LIKE 'type'");
                        if (empty($columns)) {
                            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `webinars` ADD COLUMN `type` enum('webinar','course','text_lesson') COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `category_id`;");
                            $results['safety_check'] = ($results['safety_check'] ?? '') . "\n✓ Added type column to webinars";
                        }
                        
                        // Add duration column to webinars if missing
                        $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `webinars` LIKE 'duration'");
                        if (empty($columns)) {
                            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `webinars` ADD COLUMN `duration` int(10) unsigned NOT NULL AFTER `start_date`;");
                            $results['safety_check'] = ($results['safety_check'] ?? '') . "\n✓ Added duration column to webinars";
                        }
                    }
                    
                    // Add bundle_id column to comments if missing
                    if (\Illuminate\Support\Facades\Schema::hasTable('comments')) {
                        $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `comments` LIKE 'bundle_id'");
                        if (empty($columns)) {
                            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `comments` ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;");
                            $results['safety_check'] = ($results['safety_check'] ?? '') . "\n✓ Added bundle_id column to comments";
                        }
                        
                        // Add blog_id column to comments if missing
                        $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `comments` LIKE 'blog_id'");
                        if (empty($columns)) {
                            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `comments` ADD COLUMN `blog_id` int(10) unsigned DEFAULT NULL AFTER `bundle_id`;");
                            $results['safety_check'] = ($results['safety_check'] ?? '') . "\n✓ Added blog_id column to comments";
                        }
                        
                        // Add product_id column to comments if missing
                        $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `comments` LIKE 'product_id'");
                        if (empty($columns)) {
                            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `comments` ADD COLUMN `product_id` int(10) unsigned DEFAULT NULL AFTER `blog_id`;");
                            $results['safety_check'] = ($results['safety_check'] ?? '') . "\n✓ Added product_id column to comments";
                        }
                        
                        // Add product_review_id column to comments if missing
                        $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `comments` LIKE 'product_review_id'");
                        if (empty($columns)) {
                            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `comments` ADD COLUMN `product_review_id` int(10) unsigned DEFAULT NULL AFTER `product_id`;");
                            $results['safety_check'] = ($results['safety_check'] ?? '') . "\n✓ Added product_review_id column to comments";
                        }
                        
                        // Add upcoming_course_id column to comments if missing
                        $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `comments` LIKE 'upcoming_course_id'");
                        if (empty($columns)) {
                            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `comments` ADD COLUMN `upcoming_course_id` int(10) unsigned DEFAULT NULL AFTER `bundle_id`;");
                            $results['safety_check'] = ($results['safety_check'] ?? '') . "\n✓ Added upcoming_course_id column to comments";
                        }
                    }
                    
                    // Add bundle_id to other common tables if missing
                    $tablesNeedingBundleId = [
                        'sales' => 'webinar_id',
                        'cart' => 'webinar_id',
                        'order_items' => 'webinar_id',
                        'accounting' => 'webinar_id',
                        'favorites' => 'webinar_id',
                        'tags' => 'webinar_id',
                        'tickets' => 'webinar_id',
                        'faqs' => 'webinar_id',
                        'special_offers' => 'webinar_id',
                        'webinar_reviews' => 'webinar_id',
                        'subscribe_uses' => 'webinar_id',
                    ];
                    
                    foreach ($tablesNeedingBundleId as $table => $afterColumn) {
                        try {
                            if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                                $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `{$table}` LIKE 'bundle_id'");
                                if (empty($columns)) {
                                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `{$table}` ADD COLUMN `bundle_id` int(10) unsigned DEFAULT NULL AFTER `{$afterColumn}`;");
                                    $results['safety_check'] = ($results['safety_check'] ?? '') . "\n✓ Added bundle_id column to {$table}";
                                }
                            }
                        } catch (\Exception $e) {
                            // Table or column might not exist, continue
                        }
                    }
                    
                    // Add product_id to comments_reports if missing
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasTable('comments_reports')) {
                            $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `comments_reports` LIKE 'product_id'");
                            if (empty($columns)) {
                                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `comments_reports` ADD COLUMN `product_id` int(10) unsigned DEFAULT NULL AFTER `webinar_id`;");
                                $results['safety_check'] = ($results['safety_check'] ?? '') . "\n✓ Added product_id column to comments_reports";
                            }
                        }
                    } catch (\Exception $e) {
                        // Table might not exist
                    }
                    
                    // Update sales type enum to include bundle if needed
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasTable('sales')) {
                            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `sales` MODIFY COLUMN `type` enum('webinar','meeting','subscribe','promotion','registration_package','product','bundle') COLLATE utf8mb4_unicode_ci NOT NULL;");
                        }
                    } catch (\Exception $e) {
                        // Column might not exist or enum already correct
                    }
                    
                    // Add missing columns to groups table (from KFM.sql)
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasTable('groups')) {
                            // Add discount column if missing
                            $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `groups` LIKE 'discount'");
                            if (empty($columns)) {
                                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `groups` ADD COLUMN `discount` int DEFAULT NULL AFTER `name`;");
                                $results['safety_check'] = ($results['safety_check'] ?? '') . "\n✓ Added discount column to groups";
                            }
                            
                            // Add commission column if missing
                            $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `groups` LIKE 'commission'");
                            if (empty($columns)) {
                                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `groups` ADD COLUMN `commission` int DEFAULT NULL AFTER `discount`;");
                                $results['safety_check'] = ($results['safety_check'] ?? '') . "\n✓ Added commission column to groups";
                            }
                            
                            // Add status column if missing (CRITICAL - fixes current error)
                            $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `groups` LIKE 'status'");
                            if (empty($columns)) {
                                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `groups` ADD COLUMN `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive' AFTER `commission`;");
                                $results['safety_check'] = ($results['safety_check'] ?? '') . "\n✓ Added status column to groups";
                            }
                        }
                    } catch (\Exception $e) {
                        // Column might already exist or table doesn't exist
                    }
                    
                    // Add ALL missing columns to users table based on KFM.sql structure
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                            // List of all columns that should exist in users table (from KFM.sql)
                            $usersColumns = [
                                'organ_id' => "ALTER TABLE `users` ADD COLUMN `organ_id` int DEFAULT NULL AFTER `role_id`;",
                                'bio' => "ALTER TABLE `users` ADD COLUMN `bio` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `email`;",
                                'logged_count' => "ALTER TABLE `users` ADD COLUMN `logged_count` int(10) unsigned NOT NULL DEFAULT 0 AFTER `remember_token`;",
                                'verified' => "ALTER TABLE `users` ADD COLUMN `verified` tinyint(1) NOT NULL DEFAULT 0 AFTER `remember_token`;",
                                'financial_approval' => "ALTER TABLE `users` ADD COLUMN `financial_approval` tinyint(1) NOT NULL DEFAULT 0 AFTER `verified`;",
                                'installment_approval' => "ALTER TABLE `users` ADD COLUMN `installment_approval` tinyint(1) DEFAULT 0 AFTER `financial_approval`;",
                                'enable_installments' => "ALTER TABLE `users` ADD COLUMN `enable_installments` tinyint(1) DEFAULT 1 AFTER `installment_approval`;",
                                'disable_cashback' => "ALTER TABLE `users` ADD COLUMN `disable_cashback` tinyint(1) DEFAULT 0 AFTER `enable_installments`;",
                                'enable_registration_bonus' => "ALTER TABLE `users` ADD COLUMN `enable_registration_bonus` tinyint(1) NOT NULL DEFAULT 0 AFTER `disable_cashback`;",
                                'registration_bonus_amount' => "ALTER TABLE `users` ADD COLUMN `registration_bonus_amount` double(15,2) DEFAULT NULL AFTER `enable_registration_bonus`;",
                                'avatar_settings' => "ALTER TABLE `users` ADD COLUMN `avatar_settings` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `avatar`;",
                                'cover_img' => "ALTER TABLE `users` ADD COLUMN `cover_img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `avatar_settings`;",
                                'headline' => "ALTER TABLE `users` ADD COLUMN `headline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `cover_img`;",
                                'about' => "ALTER TABLE `users` ADD COLUMN `about` text COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `headline`;",
                                'address' => "ALTER TABLE `users` ADD COLUMN `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `about`;",
                                'country_id' => "ALTER TABLE `users` ADD COLUMN `country_id` int(10) unsigned DEFAULT NULL AFTER `address`;",
                                'province_id' => "ALTER TABLE `users` ADD COLUMN `province_id` int(10) unsigned DEFAULT NULL AFTER `country_id`;",
                                'city_id' => "ALTER TABLE `users` ADD COLUMN `city_id` int(10) unsigned DEFAULT NULL AFTER `province_id`;",
                                'district_id' => "ALTER TABLE `users` ADD COLUMN `district_id` int(10) unsigned DEFAULT NULL AFTER `city_id`;",
                                'level_of_training' => "ALTER TABLE `users` ADD COLUMN `level_of_training` bit(3) DEFAULT NULL;",
                                'meeting_type' => "ALTER TABLE `users` ADD COLUMN `meeting_type` enum('all','in_person','online') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all' AFTER `level_of_training`;",
                                'access_content' => "ALTER TABLE `users` ADD COLUMN `access_content` tinyint(1) NOT NULL DEFAULT 1 AFTER `status`;",
                                'enable_ai_content' => "ALTER TABLE `users` ADD COLUMN `enable_ai_content` tinyint(1) NOT NULL DEFAULT 0 AFTER `access_content`;",
                                'language' => "ALTER TABLE `users` ADD COLUMN `language` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `enable_ai_content`;",
                                'currency' => "ALTER TABLE `users` ADD COLUMN `currency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `language`;",
                                'timezone' => "ALTER TABLE `users` ADD COLUMN `timezone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `currency`;",
                                'newsletter' => "ALTER TABLE `users` ADD COLUMN `newsletter` tinyint(1) NOT NULL DEFAULT 0 AFTER `timezone`;",
                                'public_message' => "ALTER TABLE `users` ADD COLUMN `public_message` tinyint(1) NOT NULL DEFAULT 0 AFTER `newsletter`;",
                                'identity_scan' => "ALTER TABLE `users` ADD COLUMN `identity_scan` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `public_message`;",
                                'certificate' => "ALTER TABLE `users` ADD COLUMN `certificate` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `identity_scan`;",
                                'commission' => "ALTER TABLE `users` ADD COLUMN `commission` int(10) unsigned DEFAULT NULL AFTER `certificate`;",
                                'affiliate' => "ALTER TABLE `users` ADD COLUMN `affiliate` tinyint(1) NOT NULL DEFAULT 1 AFTER `commission`;",
                                'can_create_store' => "ALTER TABLE `users` ADD COLUMN `can_create_store` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Despite disabling the store feature in the settings, we can enable this feature for that user through the edit page of a user and turning on the store toggle.' AFTER `affiliate`;",
                                'ban' => "ALTER TABLE `users` ADD COLUMN `ban` tinyint(1) NOT NULL DEFAULT 0 AFTER `can_create_store`;",
                                'ban_start_at' => "ALTER TABLE `users` ADD COLUMN `ban_start_at` int(10) unsigned DEFAULT NULL AFTER `ban`;",
                                'ban_end_at' => "ALTER TABLE `users` ADD COLUMN `ban_end_at` int(10) unsigned DEFAULT NULL AFTER `ban_start_at`;",
                                'offline' => "ALTER TABLE `users` ADD COLUMN `offline` tinyint(1) NOT NULL DEFAULT 0 AFTER `ban_end_at`;",
                                'offline_message' => "ALTER TABLE `users` ADD COLUMN `offline_message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `offline`;",
                            ];
                            
                            foreach ($usersColumns as $columnName => $sql) {
                                try {
                                    $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `users` LIKE '{$columnName}'");
                                    if (empty($columns)) {
                                        \Illuminate\Support\Facades\DB::statement($sql);
                                        $results['safety_check'] = ($results['safety_check'] ?? '') . "\n✓ Added {$columnName} column to users";
                                    }
                                } catch (\Exception $e) {
                                    // Column might already exist or SQL syntax issue, continue
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        // Table might not exist
                    }
                } catch (\Exception $columnError) {
                    // Column might already exist or table doesn't exist
                }

                // Run Seeders in correct order
                $seeders = [
                    'SectionsTableSeeder',
                    'RolesTableSeeder',
                    'PermissionsTableSeeder',
                    'UsersTableSeeder',
                    'PaymentChannelsTableSeeder', // Add payment channels
                ];
                
                foreach ($seeders as $seeder) {
                    try {
                        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => $seeder, '--force' => true]);
                        $results['seeders'][$seeder] = \Illuminate\Support\Facades\Artisan::output();
                    } catch (\Exception $e) {
                        if (isset($_GET['fix']) && str_contains($e->getMessage(), 'foreign key constraint')) {
                            $warnings[] = "$seeder: Foreign key constraint - data may already exist";
                        } else {
                            $errors[$seeder] = $e->getMessage();
                        }
                    }
                }
                
                // Note about frontend data
                $results['frontend_data_note'] = 'Basic seeders completed. To populate frontend with sample data, you can:';
                $results['frontend_data_options'] = [
                    '1. Access admin panel and create courses/webinars manually',
                    '2. Run fake data seeders via SSH: php artisan db:seed --class=makeFakeData',
                    '3. Or run individual seeders: makeFakeCategories, makeFakeWebinars, etc.'
                ];

                // Clear Cache
                try {
                    \Illuminate\Support\Facades\Artisan::call('config:clear');
                    \Illuminate\Support\Facades\Artisan::call('cache:clear');
                    \Illuminate\Support\Facades\Artisan::call('route:clear');
                    \Illuminate\Support\Facades\Artisan::call('view:clear');
                    $results['cache'] = 'Cache cleared successfully';
                } catch (\Exception $e) {
                    $errors['cache'] = $e->getMessage();
                }

                return response()->json([
                    'success' => empty($errors),
                    'results' => $results,
                    'errors' => $errors,
                    'warnings' => $warnings,
                    'message' => empty($errors) 
                        ? 'Migrations and seeders completed successfully! Default admin: admin@gmail.com / 123456'
                        : 'Completed with some errors. Check the errors array.',
                ], empty($errors) ? 200 : 207);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'trace' => config('app.debug') ? $e->getTraceAsString() : 'Trace hidden',
                ], 500);
            }
        })->middleware([]); // Explicitly bypass ALL middleware
    }

    /**
     * Emergency import schema route - must bypass ALL middleware to work when DB is empty
     */
    protected function mapEmergencyImportRoute()
    {
        Route::get('/import_complete_schema', function () {
            // Security: Check for secret key
            $SECRET_KEY = env('IMPORT_SCHEMA_SECRET_KEY', 'import_schema_2024_change_me');
            
            if (!isset($_GET['key']) || $_GET['key'] !== $SECRET_KEY) {
                return response()->json([
                    'error' => 'Security check failed.',
                    'message' => 'Add ?key=YOUR_SECRET_KEY to the URL.',
                    'instructions' => [
                        'Option 1: Use default key: ?key=import_schema_2024_change_me',
                        'Option 2: Set IMPORT_SCHEMA_SECRET_KEY in your .env file',
                    ],
                ], 403);
            }

            set_time_limit(300);
            
            // Check multiple possible locations for the schema file
            $possibleFiles = [
                base_path('maintenance/database-scripts/theboost_lmsnew.sql'),
                base_path('database/theboost_lmsnew.sql'),
                base_path('complete_database_schema.sql'),
                base_path('database/complete_database_schema.sql'),
                base_path('theboost_lmsnew.sql'),
            ];

            $schemaFile = null;
            foreach ($possibleFiles as $file) {
                if (file_exists($file)) {
                    $schemaFile = $file;
                    break;
                }
            }

            if (!$schemaFile) {
                return response()->json([
                    'success' => false,
                    'error' => 'Schema file not found',
                    'message' => 'Please ensure your exported SQL file is in one of these locations:',
                    'checked_locations' => array_map(function($path) {
                        return str_replace(base_path(), '', $path);
                    }, $possibleFiles),
                    'instructions' => 'See EXPORT_COMPLETE_SCHEMA.md for instructions'
                ], 404);
            }

            $messages = [];
            $messages[] = "Found schema file: " . basename($schemaFile);
            $fileSizeKB = round(filesize($schemaFile) / 1024, 2);
            $messages[] = "File size: " . $fileSizeKB . " KB";
            $messages[] = "Reading schema file...";
            
            try {
                $sql = file_get_contents($schemaFile);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to read schema file',
                    'message' => $e->getMessage()
                ], 500);
            }

            // Split into individual statements
            $statements = array_filter(
                array_map('trim', explode(';', $sql)),
                function($stmt) {
                    return !empty($stmt) && !preg_match('/^--/', $stmt) && strlen($stmt) > 10;
                }
            );

            $messages[] = "Found " . count($statements) . " SQL statements";
            
            // Count CREATE TABLE statements
            $createTableCount = 0;
            foreach ($statements as $stmt) {
                if (preg_match('/CREATE TABLE/i', $stmt)) {
                    $createTableCount++;
                }
            }
            $messages[] = "Found " . $createTableCount . " CREATE TABLE statements";
            
            // Check if export seems incomplete
            $warnings = [];
            if ($fileSizeKB < 200) {
                $warnings[] = "⚠️ WARNING: File size is only " . $fileSizeKB . " KB (expected 500 KB - 2 MB+)";
                $warnings[] = "Your export appears to be incomplete.";
            }
            if ($createTableCount < 50) {
                $warnings[] = "⚠️ WARNING: Only found $createTableCount CREATE TABLE statements (expected 100+)";
                $warnings[] = "Your export may be missing many tables.";
            }
            if (!empty($warnings)) {
                $warnings[] = "📋 Recommendation: Re-export from phpMyAdmin with 'Structure' only, ALL tables selected.";
                $warnings[] = "See HOW_TO_EXPORT_COMPLETE_SCHEMA.md for detailed instructions.";
            }
            
            // Check if export seems incomplete
            $createTableCount = 0;
            foreach ($statements as $stmt) {
                if (preg_match('/CREATE TABLE/i', $stmt)) {
                    $createTableCount++;
                }
            }
            
            if ($createTableCount < 50) {
                $warnings = [];
                $warnings[] = "⚠️ WARNING: Only found $createTableCount CREATE TABLE statements.";
                $warnings[] = "Expected 100+ tables for a complete database schema.";
                $warnings[] = "Your export file may be incomplete.";
                $warnings[] = "File size: " . round(filesize($schemaFile) / 1024, 2) . " KB";
                $warnings[] = "Recommended: Re-export from phpMyAdmin with 'Structure' only, all tables selected.";
            }

            // Disable foreign key checks
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $created = 0;
            $skipped = 0;
            $errors = [];

            foreach ($statements as $index => $statement) {
                // Skip comments and empty statements
                if (empty($statement) || preg_match('/^--/', $statement)) {
                    continue;
                }
                
                // Extract table name from CREATE TABLE statement
                if (preg_match('/CREATE TABLE (?:IF NOT EXISTS )?`?(\w+)`?/i', $statement, $matches)) {
                    $tableName = $matches[1];
                    
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasTable($tableName)) {
                            $messages[] = "⏭️  Table '$tableName' already exists, skipping...";
                            $skipped++;
                            continue;
                        }
                        
                        \Illuminate\Support\Facades\DB::statement($statement);
                        $messages[] = "✅ Created table: $tableName";
                        $created++;
                    } catch (\Exception $e) {
                        $errorMsg = "❌ Error creating table '$tableName': " . $e->getMessage();
                        $messages[] = $errorMsg;
                        $errors[] = $errorMsg;
                    }
                } elseif (preg_match('/ALTER TABLE/i', $statement)) {
                    // Handle ALTER TABLE statements (adding columns, etc.)
                    try {
                        \Illuminate\Support\Facades\DB::statement($statement);
                        $messages[] = "✅ Executed ALTER statement";
                    } catch (\Exception $e) {
                        $errorMsg = "❌ Error executing ALTER: " . $e->getMessage();
                        $messages[] = $errorMsg;
                        $errors[] = $errorMsg;
                    }
                }
            }

            // Re-enable foreign key checks
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $messages[] = "\n=== Summary ===";
            $messages[] = "✅ Created: $created tables";
            $messages[] = "⏭️  Skipped: $skipped tables (already exist)";
            $messages[] = "❌ Errors: " . count($errors);

            if (!empty($errors)) {
                $messages[] = "\n=== Errors ===";
                foreach ($errors as $error) {
                    $messages[] = $error;
                }
            }

            $messages[] = "\nDone!";

            $response = [
                'success' => count($errors) === 0,
                'created' => $created,
                'skipped' => $skipped,
                'errors' => $errors,
                'messages' => $messages,
                'file_used' => basename($schemaFile),
                'file_size_kb' => $fileSizeKB ?? round(filesize($schemaFile) / 1024, 2),
                'create_table_count' => $createTableCount ?? 0,
            ];
            
            if (!empty($warnings)) {
                $response['warnings'] = $warnings;
                $response['export_incomplete'] = true;
                $response['recommendation'] = 'Re-export the complete database schema. See HOW_TO_EXPORT_COMPLETE_SCHEMA.md for detailed instructions.';
            }
            
            return response()->json($response, count($errors) === 0 ? 200 : 207);
        })->middleware([]); // Explicitly bypass ALL middleware
    }

    /**
     * Map the auto-fix from KFM.sql route
     * This route allows automatic comparison and fixing of database structure
     */
    protected function mapAutoFixFromKfmRoute()
    {
        Route::get('/auto_fix_from_kfm', function () {
            // Security: Check for secret key (accept multiple keys for convenience)
            $SECRET_KEY = env('AUTO_FIX_KFM_SECRET_KEY', 'auto_fix_kfm_2024_change_me');
            $MIGRATION_KEY = env('MIGRATION_SECRET_KEY', 'run_migrations_2024_temp_key_change_me');
            $ACCEPTED_KEYS = [$SECRET_KEY, $MIGRATION_KEY, 'auto_fix_kfm_2024_change_me', 'run_migrations_2024_temp_key_change_me'];
            
            if (!isset($_GET['key']) || !in_array($_GET['key'], $ACCEPTED_KEYS)) {
                return response()->json([
                    'error' => 'Security check failed.',
                    'message' => 'Add ?key=YOUR_SECRET_KEY to the URL.',
                    'accepted_keys' => [
                        'Default: auto_fix_kfm_2024_change_me',
                        'Or migration key: run_migrations_2024_temp_key_change_me',
                        'Or set AUTO_FIX_KFM_SECRET_KEY in .env'
                    ],
                    'instructions' => [
                        '1. Use: /auto_fix_from_kfm?key=auto_fix_kfm_2024_change_me',
                        '2. Or use migration key: /auto_fix_from_kfm?key=run_migrations_2024_temp_key_change_me',
                        '3. Or set AUTO_FIX_KFM_SECRET_KEY in your .env file'
                    ]
                ], 403);
            }

            // Include and execute the auto-fix script - check multiple possible locations
            $possiblePaths = [
                base_path('maintenance/database-scripts/auto_fix_from_kfm.php'),
                base_path('database/auto_fix_from_kfm.php'),
                base_path('../maintenance/database-scripts/auto_fix_from_kfm.php'),
                base_path('../database/auto_fix_from_kfm.php'),
            ];
            
            $scriptPath = null;
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $scriptPath = $path;
                    break;
                }
            }
            
            if (!$scriptPath) {
                return response()->json([
                    'error' => 'Auto-fix script not found',
                    'message' => 'Please upload database/auto_fix_from_kfm.php to your server',
                    'checked_paths' => array_map(function($path) {
                        return str_replace(base_path(), '[Laravel Root]', $path);
                    }, $possiblePaths),
                    'instructions' => [
                        '1. Upload auto_fix_from_kfm.php to: backend/database/ folder',
                        '2. Or upload to: public_html/database/ folder',
                        '3. Make sure the file is readable (chmod 644)',
                        '4. The file should be in the same folder as your KFM.sql file'
                    ],
                    'current_base_path' => base_path(),
                    'current_public_path' => public_path()
                ], 404);
            }

            // Capture output
            ob_start();
            try {
                include $scriptPath;
                $output = ob_get_clean();
                
                // Try to decode JSON output
                $jsonData = json_decode($output, true);
                if ($jsonData !== null) {
                    return response()->json($jsonData);
                } else {
                    // Return as text if not JSON
                    return response($output, 200)
                        ->header('Content-Type', 'application/json');
                }
            } catch (\Exception $e) {
                ob_end_clean();
                return response()->json([
                    'error' => 'Script execution failed',
                    'message' => $e->getMessage(),
                    'trace' => config('app.debug') ? $e->getTraceAsString() : null
                ], 500);
            }
        })->middleware([]); // Explicitly bypass ALL middleware
    }

    /**
     * Map the import KFM data route
     * This route imports demo data (users, courses, etc.) from KFM.sql
     */
    protected function mapImportKfmDataRoute()
    {
        // Smart import route with column mapping
        Route::get('/import_kfm_data_smart', function () {
            $SECRET_KEY = env('AUTO_FIX_KFM_SECRET_KEY', 'auto_fix_kfm_2024_change_me');
            $MIGRATION_KEY = env('MIGRATION_SECRET_KEY', 'run_migrations_2024_temp_key_change_me');
            $ACCEPTED_KEYS = [$SECRET_KEY, $MIGRATION_KEY, 'auto_fix_kfm_2024_change_me', 'run_migrations_2024_temp_key_change_me', 'import_kfm_data_2024'];
            
            if (!isset($_GET['key']) || !in_array($_GET['key'], $ACCEPTED_KEYS)) {
                return response()->json(['error' => 'Security check failed'], 403);
            }

            $scriptPath = base_path('maintenance/database-scripts/import_kfm_data_smart.php');
            if (!file_exists($scriptPath)) {
                return response()->json(['error' => 'Script not found'], 404);
            }

            ob_start();
            try {
                include $scriptPath;
                $output = ob_get_clean();
                $jsonData = json_decode($output, true);
                return response()->json($jsonData !== null ? $jsonData : ['output' => $output]);
            } catch (\Exception $e) {
                ob_end_clean();
                return response()->json(['error' => $e->getMessage()], 500);
            }
        })->middleware([]);
        
        // Check imported data route
        Route::get('/check_imported_data', function () {
            $SECRET_KEY = env('AUTO_FIX_KFM_SECRET_KEY', 'auto_fix_kfm_2024_change_me');
            $MIGRATION_KEY = env('MIGRATION_SECRET_KEY', 'run_migrations_2024_temp_key_change_me');
            $ACCEPTED_KEYS = [$SECRET_KEY, $MIGRATION_KEY, 'auto_fix_kfm_2024_change_me', 'run_migrations_2024_temp_key_change_me'];
            
            if (!isset($_GET['key']) || !in_array($_GET['key'], $ACCEPTED_KEYS)) {
                return response()->json(['error' => 'Security check failed'], 403);
            }

            $scriptPath = base_path('maintenance/database-scripts/check_imported_data.php');
            if (!file_exists($scriptPath)) {
                return response()->json(['error' => 'Script not found'], 404);
            }

            ob_start();
            try {
                include $scriptPath;
                $output = ob_get_clean();
                $jsonData = json_decode($output, true);
                return response()->json($jsonData !== null ? $jsonData : ['output' => $output]);
            } catch (\Exception $e) {
                ob_end_clean();
                return response()->json(['error' => $e->getMessage()], 500);
            }
        })->middleware([]);
        
        Route::get('/import_kfm_data', function () {
            // Security: Check for secret key (accept multiple keys for convenience)
            $SECRET_KEY = env('AUTO_FIX_KFM_SECRET_KEY', 'auto_fix_kfm_2024_change_me');
            $MIGRATION_KEY = env('MIGRATION_SECRET_KEY', 'run_migrations_2024_temp_key_change_me');
            $ACCEPTED_KEYS = [$SECRET_KEY, $MIGRATION_KEY, 'auto_fix_kfm_2024_change_me', 'run_migrations_2024_temp_key_change_me', 'import_kfm_data_2024'];
            
            if (!isset($_GET['key']) || !in_array($_GET['key'], $ACCEPTED_KEYS)) {
                return response()->json([
                    'error' => 'Security check failed.',
                    'message' => 'Add ?key=YOUR_SECRET_KEY to the URL.',
                    'accepted_keys' => [
                        'Default: auto_fix_kfm_2024_change_me',
                        'Or migration key: run_migrations_2024_temp_key_change_me',
                        'Or set AUTO_FIX_KFM_SECRET_KEY in .env'
                    ],
                    'instructions' => [
                        '1. Use: /import_kfm_data?key=auto_fix_kfm_2024_change_me',
                        '2. Or use migration key: /import_kfm_data?key=run_migrations_2024_temp_key_change_me',
                        '3. Or set AUTO_FIX_KFM_SECRET_KEY in your .env file'
                    ]
                ], 403);
            }

            // Include and execute the import script
            $scriptPath = base_path('maintenance/database-scripts/import_kfm_data.php');
            
            if (!file_exists($scriptPath)) {
                return response()->json([
                    'error' => 'Import script not found',
                    'message' => 'Please ensure database/import_kfm_data.php exists',
                    'path_checked' => $scriptPath
                ], 404);
            }

            // Capture output
            ob_start();
            try {
                include $scriptPath;
                $output = ob_get_clean();
                
                // Try to decode JSON output
                $jsonData = json_decode($output, true);
                if ($jsonData !== null) {
                    return response()->json($jsonData);
                } else {
                    // Return as text if not JSON
                    return response($output, 200)
                        ->header('Content-Type', 'application/json');
                }
            } catch (\Exception $e) {
                ob_end_clean();
                return response()->json([
                    'error' => 'Script execution failed',
                    'message' => $e->getMessage(),
                    'trace' => config('app.debug') ? $e->getTraceAsString() : null
                ], 500);
            }
        })->middleware([]); // Explicitly bypass ALL middleware
    }

    /**
     * Map the route to create all missing tables from KFM.sql
     * This route scans KFM.sql and creates all missing tables in one go
     */
    protected function mapCreateAllMissingTablesRoute()
    {
        Route::get('/create_all_missing_tables_from_kfm', function () {
            // Security: Check for secret key (accept multiple keys for convenience)
            $SECRET_KEY = env('AUTO_FIX_KFM_SECRET_KEY', 'auto_fix_kfm_2024_change_me');
            $MIGRATION_KEY = env('MIGRATION_SECRET_KEY', 'run_migrations_2024_temp_key_change_me');
            $ACCEPTED_KEYS = [$SECRET_KEY, $MIGRATION_KEY, 'auto_fix_kfm_2024_change_me', 'run_migrations_2024_temp_key_change_me'];
            
            if (!isset($_GET['key']) || !in_array($_GET['key'], $ACCEPTED_KEYS)) {
                return response()->json([
                    'error' => 'Security check failed.',
                    'message' => 'Add ?key=YOUR_SECRET_KEY to the URL.',
                    'accepted_keys' => [
                        'Default: auto_fix_kfm_2024_change_me',
                        'Or migration key: run_migrations_2024_temp_key_change_me',
                        'Or set AUTO_FIX_KFM_SECRET_KEY in .env'
                    ],
                    'instructions' => [
                        '1. Use: /create_all_missing_tables_from_kfm?key=auto_fix_kfm_2024_change_me',
                        '2. Or use migration key: /create_all_missing_tables_from_kfm?key=run_migrations_2024_temp_key_change_me',
                        '3. Or set AUTO_FIX_KFM_SECRET_KEY in your .env file'
                    ]
                ], 403);
            }

            // Include and execute the script - check multiple possible locations
            $possiblePaths = [
                base_path('maintenance/database-scripts/create_all_missing_tables_from_kfm.php'),
                base_path('database/create_all_missing_tables_from_kfm.php'),
                base_path('../maintenance/database-scripts/create_all_missing_tables_from_kfm.php'),
                base_path('../database/create_all_missing_tables_from_kfm.php'),
            ];
            
            $scriptPath = null;
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $scriptPath = $path;
                    break;
                }
            }
            
            if (!$scriptPath) {
                return response()->json([
                    'error' => 'Script not found',
                    'message' => 'Please ensure database/create_all_missing_tables_from_kfm.php exists',
                    'paths_checked' => $possiblePaths
                ], 404);
            }

            // Capture output
            ob_start();
            try {
                include $scriptPath;
                $output = ob_get_clean();
                
                // Try to decode JSON output
                $jsonData = json_decode($output, true);
                if ($jsonData !== null) {
                    return response()->json($jsonData);
                } else {
                    // Return as text if not JSON
                    return response($output, 200)
                        ->header('Content-Type', 'application/json');
                }
            } catch (\Exception $e) {
                ob_end_clean();
                return response()->json([
                    'error' => 'Script execution failed',
                    'message' => $e->getMessage(),
                    'trace' => config('app.debug') ? $e->getTraceAsString() : null
                ], 500);
            }
        })->middleware([]); // Explicitly bypass ALL middleware
    }

    /**
     * Map the route to fix missing relationships
     * This route fixes data integrity issues where foreign keys reference non-existent records
     */
    protected function mapFixMissingRelationshipsRoute()
    {
        Route::get('/fix_missing_relationships', function () {
            // Security: Check for secret key
            $SECRET_KEY = env('AUTO_FIX_KFM_SECRET_KEY', 'auto_fix_kfm_2024_change_me');
            $MIGRATION_KEY = env('MIGRATION_SECRET_KEY', 'run_migrations_2024_temp_key_change_me');
            $ACCEPTED_KEYS = [$SECRET_KEY, $MIGRATION_KEY, 'auto_fix_kfm_2024_change_me', 'run_migrations_2024_temp_key_change_me'];
            
            if (!isset($_GET['key']) || !in_array($_GET['key'], $ACCEPTED_KEYS)) {
                return response()->json([
                    'error' => 'Security check failed.',
                    'message' => 'Add ?key=YOUR_SECRET_KEY to the URL.',
                ], 403);
            }

            // Include and execute the script
            $possiblePaths = [
                base_path('maintenance/database-scripts/fix_missing_relationships.php'),
                base_path('database/fix_missing_relationships.php'),
                base_path('../maintenance/database-scripts/fix_missing_relationships.php'),
                base_path('../database/fix_missing_relationships.php'),
            ];
            
            $scriptPath = null;
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $scriptPath = $path;
                    break;
                }
            }
            
            if (!$scriptPath) {
                return response()->json([
                    'error' => 'Script not found',
                    'message' => 'Please ensure database/fix_missing_relationships.php exists',
                ], 404);
            }

            ob_start();
            try {
                include $scriptPath;
                $output = ob_get_clean();
                $jsonData = json_decode($output, true);
                return response()->json($jsonData !== null ? $jsonData : ['output' => $output]);
            } catch (\Exception $e) {
                ob_end_clean();
                return response()->json([
                    'error' => 'Script execution failed',
                    'message' => $e->getMessage(),
                ], 500);
            }
        })->middleware([]); // Explicitly bypass ALL middleware
    }
}
