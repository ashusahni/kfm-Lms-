# Database Migration Guide for cPanel

## Problem
The error `Table 'theboost_lmsnew.users' doesn't exist` occurs because database migrations haven't been run on your production server.

## Solution: Run Database Migrations

### Method 1: Using SSH/Terminal (Recommended)

1. **Access cPanel Terminal:**
   - Log into your cPanel
   - Look for "Terminal" or "SSH Access" in the Advanced section
   - If Terminal is not available, contact your hosting provider to enable SSH access

2. **Navigate to your project directory:**
   ```bash
   cd public_html
   # or wherever your Laravel project is located
   cd backend  # if your backend is in a subdirectory
   ```

3. **Run migrations:**
   ```bash
   php artisan migrate
   ```

4. **Run seeders (to create initial admin user and roles):**
   ```bash
   php artisan db:seed --class=RolesTableSeeder
   php artisan db:seed --class=PermissionsTableSeeder
   php artisan db:seed --class=SectionsTableSeeder
   php artisan db:seed --class=UsersTableSeeder
   ```

   Or run all seeders at once:
   ```bash
   php artisan db:seed
   ```

5. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

### Method 2: Using Web Route (If SSH is not available)

If you don't have SSH access, use the built-in migration route:

1. **Upload the updated `routes/web.php` file** to your server (if you haven't already)

2. **Access the migration route via browser using the default key:**
   ```
   https://kfm.brandboostup.in/emergencyDatabaseUpdate?key=run_migrations_2024_temp_key_change_me
   ```
   
   **OR** if you've set a custom key in `.env`:
   ```
   https://kfm.brandboostup.in/emergencyDatabaseUpdate?key=YOUR_CUSTOM_KEY
   ```

3. **The route will:**
   - Run all migrations
   - Run all seeders (Roles, Permissions, Sections, Users)
   - Clear all caches
   - Return a JSON response with results

4. **IMPORTANT Security Steps:**
   - After migrations complete, either:
     - Set `MIGRATION_SECRET_KEY=your_random_string` in your `.env` file, OR
     - Edit `routes/web.php` and change the default `$SECRET_KEY` value
   - This prevents unauthorized access to the migration route

### Method 3: Using cPanel Cron Jobs

1. Go to cPanel → Cron Jobs
2. Create a new cron job
3. Set it to run once:
   - Command: `cd /home/username/public_html/backend && php artisan migrate --force`
   - Schedule: Run once (set a specific date/time)

## Default Admin Credentials (After Seeding)

After running `UsersTableSeeder`, you can login with:
- **Email:** `admin@gmail.com`
- **Password:** `123456`

**⚠️ IMPORTANT:** Change the admin password immediately after first login!

## Verification

After running migrations, verify:
1. Check if you can access the admin login page
2. Try logging in with the default credentials
3. Check your database in phpMyAdmin to confirm tables exist

## Troubleshooting

### If migrations fail:
1. Check database credentials in `.env` file
2. Ensure database `theboost_lmsnew` exists
3. Check file permissions (storage and bootstrap/cache should be writable)
4. Check PHP version (should be PHP 8.1 or higher)

### If you get permission errors:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### If you get "Class not found" errors:
```bash
composer dump-autoload
```

## Security Notes

- Never leave migration scripts in your public folder
- Always use strong passwords in production
- Keep your `.env` file secure (never commit it to version control)
- Set `APP_DEBUG=false` in production after setup
