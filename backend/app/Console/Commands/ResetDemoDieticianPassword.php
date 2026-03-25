<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetDemoDieticianPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rocket:reset-demo-dietician-password
                            {--password=123456 : New password for the demo dietician account}
                            {--email=instructor@demo.com : Email of the demo dietician account}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the demo dietician/instructor account password so you can log in (e.g. instructor@demo.com / 123456)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');

        if (strlen($password) < 6) {
            $this->error('Password must be at least 6 characters.');
            return 1;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No user found with email: {$email}");
            $this->line('Run: php artisan db:seed --class=UsersTableSeeder  to create the demo dietician account.');
            return 1;
        }

        $user->password = Hash::make($password);
        $user->status = User::$active;
        $user->save();

        $this->info("Password reset for: {$user->full_name} ({$email})");
        $this->line("You can now sign in with:");
        $this->line("  Email: {$email}");
        $this->line("  Password: {$password}");
        return 0;
    }
}
