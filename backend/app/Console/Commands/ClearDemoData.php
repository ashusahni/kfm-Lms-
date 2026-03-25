<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clear-demo {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all demo courses, content, sales, and related data. Keeps users, roles, permissions, sections, payment channels, and settings.';

    /**
     * Tables to keep (do not truncate). These are required for the app to run and for you to log in.
     */
    protected $tablesToKeep = [
        'users',
        'roles',
        'permissions',
        'sections',
        'role_user',
        'section_role',
        'permission_role',
        'payment_channels',
        'settings',
        'setting_translations',
        'migrations',
        'password_resets',
        'personal_access_tokens',
        'failed_jobs',
        'support_departments',
        'support_department_translations',
        'offline_banks',
        'offline_bank_specifications',
        'offline_bank_specification_translations',
        'offline_bank_translations',
        'currencies',
        'notification_templates',
        'regions',
        'become_instructors',
        'navbar_buttons',
        'navbar_button_translations',
        'home_sections',
        'home_page_statistics',
        'home_page_statistic_translations',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->option('force')) {
            if (! $this->confirm('This will PERMANENTLY delete all courses, categories, blog, sales, orders, and related data. Only users, roles, and settings are kept. Continue?')) {
                $this->info('Aborted.');
                return 0;
            }
        }

        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver !== 'mysql') {
            $this->error('This command is designed for MySQL. Your current driver is: ' . $driver);
            return 1;
        }

        $database = config("database.connections.{$connection}.database");

        $tables = $this->getTableNames($connection, $database);
        $toTruncate = array_diff($tables, $this->tablesToKeep);

        if (empty($toTruncate)) {
            $this->warn('No tables to truncate (all are in the keep list).');
            return 0;
        }

        $this->info('Tables that will be truncated: ' . count($toTruncate));
        $this->line(implode(', ', array_slice($toTruncate, 0, 15)) . (count($toTruncate) > 15 ? '...' : ''));

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $truncated = 0;
        $errors = [];

        foreach ($toTruncate as $table) {
            try {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $truncated++;
                    $this->output->write('.');
                }
            } catch (\Throwable $e) {
                $errors[$table] = $e->getMessage();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->newLine();
        $this->info("Truncated {$truncated} tables.");

        if (! empty($errors)) {
            $this->warn('Some tables could not be truncated:');
            foreach ($errors as $t => $msg) {
                $this->line("  - {$t}: {$msg}");
            }
        }

        $this->info('Demo data cleared. You can now add your own courses and content.');
        return 0;
    }

    /**
     * Get list of table names from the database (as Laravel sees them, with prefix stripped if set).
     */
    protected function getTableNames(string $connection, string $database): array
    {
        $prefix = config("database.connections.{$connection}.prefix", '');
        $result = DB::connection($connection)->select(
            "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = 'BASE TABLE'",
            [$database]
        );

        $tables = array_map(function ($row) use ($prefix) {
            $name = $row->TABLE_NAME;
            if ($prefix !== '' && substr($name, 0, strlen($prefix)) === $prefix) {
                return substr($name, strlen($prefix));
            }
            return $name;
        }, $result);

        return array_values(array_unique($tables));
    }
}
