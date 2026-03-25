# Complete Database Setup Guide - Step by Step

## Current Status

✅ **Import Script:** `import_complete_schema.php` - Ready to use
✅ **Script Location Check:** Automatically finds SQL file in multiple locations
✅ **Your Exported File:** Should be `database/theboost_lmsnew.sql`

## Step-by-Step Instructions

### Step 1: Verify Your Exported SQL File Location

Your exported file should be in one of these locations:
- ✅ `database/theboost_lmsnew.sql` (recommended - you mentioned this)
- `complete_database_schema.sql` (project root)
- `database/complete_database_schema.sql`
- `theboost_lmsnew.sql` (project root)

**Check if file exists:**
```bash
# If you have SSH access
ls -la database/theboost_lmsnew.sql

# Or check via file manager in cPanel
```

### Step 2: Verify File Contents

Your exported SQL file should contain:
- ✅ `CREATE TABLE` statements
- ✅ Table structures with all columns
- ✅ Indexes and constraints
- ❌ Should NOT contain `INSERT` statements (structure only, no data)

**Quick check:**
```bash
# Check first few lines
head -20 database/theboost_lmsnew.sql

# Should see something like:
# CREATE TABLE IF NOT EXISTS `users` (
#   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
#   ...
```

### Step 3: Import the Schema

**Option A: Web-Based Import (Recommended)**

1. **Change the security key** in `import_complete_schema.php`:
   ```php
   $SECRET_KEY = 'your_secure_key_here'; // Change this!
   ```

2. **Visit the import URL:**
   ```
   https://kfm.brandboostup.in/import_complete_schema.php?key=your_secure_key_here
   ```

3. **Expected Response:**
   ```json
   {
     "success": true,
     "created": 150,
     "skipped": 0,
     "errors": [],
     "messages": [
       "Found schema file: theboost_lmsnew.sql",
       "Reading schema file...",
       "Found 500 SQL statements",
       "✅ Created table: users",
       ...
     ]
   }
   ```

**Option B: phpMyAdmin Import (Alternative)**

1. Login to phpMyAdmin on your new server
2. Select your database (`theboost_lmsnew`)
3. Click **"Import"** tab
4. Click **"Choose File"** and select `database/theboost_lmsnew.sql`
5. **Important Settings:**
   - ✅ Check "Partial import" if file is large
   - ✅ Check "Allow interruption of import"
   - ✅ Set "Skip this number of queries" to 0
6. Click **"Go"**
7. Wait for import to complete

**Option C: Command Line (If you have SSH)**

```bash
cd /path/to/your/project/backend
mysql -u your_username -p theboost_lmsnew < database/theboost_lmsnew.sql
```

### Step 4: Verify Import Success

**Check if tables were created:**
```sql
-- Run in phpMyAdmin SQL tab
SHOW TABLES;

-- Should show 100+ tables including:
-- users, webinars, sales, products, bundles, etc.
```

**Check specific critical tables:**
```sql
SHOW TABLES LIKE 'settings';
SHOW TABLES LIKE 'webinars';
SHOW TABLES LIKE 'products';
SHOW TABLES LIKE 'bundles';
SHOW TABLES LIKE 'currencies';
SHOW TABLES LIKE 'payment_channels';
```

### Step 5: Run Migrations (To Sync Everything)

After importing the schema, run migrations to ensure everything is in sync:

```
https://kfm.brandboostup.in/emergencyDatabaseUpdate?key=run_migrations_2024_temp_key_change_me&fix=1
```

This will:
- ✅ Check for any missing migrations
- ✅ Add any missing columns
- ✅ Update enum types if needed
- ✅ Handle foreign key constraints

### Step 6: Run Seeders (For Initial Data)

```bash
# Via SSH
php artisan db:seed

# Or via web route (if you add it)
```

**Seeders to run:**
- `SectionsTableSeeder`
- `RolesTableSeeder`
- `PermissionsTableSeeder`
- `UsersTableSeeder`
- `PaymentChannelsTableSeeder`

### Step 7: Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Troubleshooting

### Problem: File Not Found

**Error:** `Schema file not found`

**Solution:**
1. Verify file exists: `ls -la database/theboost_lmsnew.sql`
2. Check file permissions: `chmod 644 database/theboost_lmsnew.sql`
3. Verify file path is correct
4. Try moving file to project root: `mv database/theboost_lmsnew.sql ./theboost_lmsnew.sql`

### Problem: Import Fails with "Table Already Exists"

**Error:** `Table 'users' already exists`

**Solution:**
- This is normal if you've run migrations before
- The script will skip existing tables automatically
- Or drop existing tables first (be careful!):
  ```sql
  SET FOREIGN_KEY_CHECKS=0;
  DROP TABLE IF EXISTS users;
  -- Repeat for other tables
  SET FOREIGN_KEY_CHECKS=1;
  ```

### Problem: Import Fails with Foreign Key Errors

**Error:** `Cannot add foreign key constraint`

**Solution:**
- The script disables foreign key checks automatically
- If using phpMyAdmin, check "Disable foreign key checks" option
- Or run: `SET FOREIGN_KEY_CHECKS=0;` before import

### Problem: File Too Large

**Error:** `Maximum upload size exceeded`

**Solution:**
1. Increase PHP upload limits in php.ini:
   ```ini
   upload_max_filesize = 100M
   post_max_size = 100M
   max_execution_time = 300
   ```
2. Or use command line import
3. Or split the SQL file into smaller chunks

## File Structure Summary

```
backend/
├── database/
│   ├── migrations/          (357 migration files)
│   ├── seeders/             (Seeder files)
│   └── theboost_lmsnew.sql  ← YOUR EXPORTED FILE HERE
├── import_complete_schema.php  ← Import script
├── USE_COMPLETE_SCHEMA_INSTRUCTIONS.md
└── EXPORT_COMPLETE_SCHEMA.md
```

## Quick Checklist

- [ ] Exported SQL file is in `database/theboost_lmsnew.sql`
- [ ] File contains CREATE TABLE statements (structure only)
- [ ] Changed security key in `import_complete_schema.php`
- [ ] Imported schema via web script or phpMyAdmin
- [ ] Verified tables were created (100+ tables)
- [ ] Ran migrations to sync everything
- [ ] Ran seeders for initial data
- [ ] Cleared all caches
- [ ] Tested admin login
- [ ] Tested frontend

## Next Steps After Import

1. ✅ **Test Admin Login** - Should work now
2. ✅ **Check Dashboard** - Should load without errors
3. ✅ **Create Test Content** - Add a course/webinar
4. ✅ **Test Frontend** - Verify data displays correctly

## Support

If you encounter issues:
1. Check the error message in the import response
2. Verify file location and permissions
3. Check database connection in `.env`
4. Review Laravel logs: `storage/logs/laravel.log`
