# Step-by-Step Database Import Guide

## Current File Structure

```
backend/
├── database/
│   ├── migrations/          (357 migration files)
│   ├── seeders/             (Seeder files)
│   └── theboost_lmsnew.sql  ← YOUR EXPORTED FILE (Place it here)
├── import_complete_schema.php  ← Import script (Ready)
├── create_all_missing_tables.sql
├── add_all_missing_id_columns.sql
└── .env                      (Database configuration)
```

## ✅ Step 1: Verify Your SQL File Location

**Your exported file should be at:**
```
backend/database/theboost_lmsnew.sql
```

**Check if file exists:**
- Via cPanel File Manager: Navigate to `backend/database/` and look for `theboost_lmsnew.sql`
- Via SSH: `ls -la database/theboost_lmsnew.sql`

**If file is NOT in database folder:**
- Move it there: `mv theboost_lmsnew.sql database/theboost_lmsnew.sql`
- Or upload it via cPanel File Manager to `backend/database/` folder

## ✅ Step 2: Verify SQL File Contents

**Your file should contain:**
- ✅ `CREATE TABLE` statements (structure only)
- ✅ All table definitions with columns
- ❌ Should NOT contain `INSERT` statements (no data, just structure)

**Quick verification:**
Open the file and check first few lines - should see:
```sql
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  ...
```

## ✅ Step 3: Update Security Key

**The security key is a password YOU create - it doesn't exist yet!**

**Edit `import_complete_schema.php` line 15:**
```php
// Change this line:
$SECRET_KEY = 'import_schema_2024_change_me';

// To your own key (use any password you want):
$SECRET_KEY = 'kfm_import_2024_secure';  // Example - use your own!
```

**Examples of good keys:**
- `kfm_import_2024_secure_xyz123`
- `my_lms_import_key_2024`
- `brandboostup_secure_import_2024`

**Save the file.**

**OR use the default key for testing:**
- Default key: `import_schema_2024_change_me`
- ⚠️ Change it for production!

## ✅ Step 4: Import the Schema

### Option A: Web-Based Import (Recommended)

1. **Visit this URL (via Laravel route):**
   ```
   # Use the default key (for testing):
   https://kfm.brandboostup.in/import_complete_schema?key=import_schema_2024_change_me
   
   # OR if you set IMPORT_SCHEMA_SECRET_KEY in .env, use that key:
   https://kfm.brandboostup.in/import_complete_schema?key=your_env_key_here
   ```
   
   **Important:** The route is `/import_complete_schema` (no `.php` extension) - it's now a Laravel route, not a direct PHP file.

2. **Expected Success Response:**
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
       "✅ Created table: webinars",
       ...
       "✅ Created: 150 tables",
       "⏭️  Skipped: 0 tables",
       "❌ Errors: 0"
     ]
   }
   ```

3. **If File Not Found:**
   ```json
   {
     "success": false,
     "error": "Schema file not found",
     "checked_locations": [
       "/path/database/theboost_lmsnew.sql",
       "/path/complete_database_schema.sql",
       ...
     ]
   }
   ```
   → **Solution:** Verify file is in `database/theboost_lmsnew.sql`

### Option B: phpMyAdmin Import (Alternative)

1. **Login to phpMyAdmin** on your server
2. **Select database:** `theboost_lmsnew`
3. **Click "Import" tab**
4. **Choose File:** Select `database/theboost_lmsnew.sql`
5. **Settings:**
   - ✅ Format: SQL
   - ✅ Partial import: Check if file is large (>50MB)
   - ✅ Allow interruption: Check
6. **Click "Go"**
7. **Wait for completion** (may take 1-5 minutes)

### Option C: Command Line (If SSH Available)

```bash
cd /path/to/backend
mysql -u your_db_user -p theboost_lmsnew < database/theboost_lmsnew.sql
```

## ✅ Step 5: Verify Import Success

**Run in phpMyAdmin SQL tab:**
```sql
-- Check total tables
SELECT COUNT(*) as total_tables FROM information_schema.tables 
WHERE table_schema = 'theboost_lmsnew';

-- Should show 100+ tables

-- Check critical tables exist
SHOW TABLES LIKE 'users';
SHOW TABLES LIKE 'webinars';
SHOW TABLES LIKE 'settings';
SHOW TABLES LIKE 'products';
SHOW TABLES LIKE 'bundles';
SHOW TABLES LIKE 'currencies';
SHOW TABLES LIKE 'payment_channels';
SHOW TABLES LIKE 'accounting';
SHOW TABLES LIKE 'sales';
SHOW TABLES LIKE 'supports';
```

**All should return 1 row (table exists).**

## ✅ Step 6: Run Migrations (Sync Everything)

**Visit:**
```
https://kfm.brandboostup.in/emergencyDatabaseUpdate?key=run_migrations_2024_temp_key_change_me&fix=1
```

**This will:**
- ✅ Check for any missing migrations
- ✅ Add any missing columns
- ✅ Update enum types
- ✅ Handle foreign key constraints

## ✅ Step 7: Run Seeders (Initial Data)

**Via SSH:**
```bash
php artisan db:seed
```

**Or individual seeders:**
```bash
php artisan db:seed --class=SectionsTableSeeder
php artisan db:seed --class=RolesTableSeeder
php artisan db:seed --class=PermissionsTableSeeder
php artisan db:seed --class=UsersTableSeeder
php artisan db:seed --class=PaymentChannelsTableSeeder
```

## ✅ Step 8: Clear All Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## ✅ Step 9: Test Application

1. **Test Admin Login:**
   - Go to: `https://kfm.brandboostup.in/admin/login`
   - Should work without errors

2. **Test Dashboard:**
   - After login, dashboard should load
   - No "table not found" errors

3. **Test Frontend:**
   - Visit homepage
   - Should display without errors

## Troubleshooting

### ❌ Problem: "Schema file not found"

**Solution:**
1. Verify file location: `ls -la database/theboost_lmsnew.sql`
2. Check file permissions: `chmod 644 database/theboost_lmsnew.sql`
3. Verify path in error message matches your file location
4. Try moving file to project root: `mv database/theboost_lmsnew.sql ./theboost_lmsnew.sql`

### ❌ Problem: "Table already exists" errors

**Solution:**
- This is normal if you've run migrations before
- The script automatically skips existing tables
- Check the "skipped" count in response

### ❌ Problem: "Foreign key constraint" errors

**Solution:**
- The script disables foreign key checks automatically
- If using phpMyAdmin, check "Disable foreign key checks" option
- Or manually run: `SET FOREIGN_KEY_CHECKS=0;` before import

### ❌ Problem: File too large for phpMyAdmin

**Solution:**
1. Increase PHP limits in php.ini:
   ```ini
   upload_max_filesize = 100M
   post_max_size = 100M
   max_execution_time = 300
   ```
2. Or use command line import
3. Or split SQL file into smaller chunks

### ❌ Problem: Import succeeds but tables still missing

**Solution:**
1. Check database name matches in `.env`
2. Verify you're importing to correct database
3. Check for errors in import response
4. Run migrations again to sync

## Quick Reference

**File Locations:**
- SQL File: `backend/database/theboost_lmsnew.sql`
- Import Script: `backend/import_complete_schema.php`
- Database Config: `backend/.env`

**URLs:**
- Import: `https://kfm.brandboostup.in/import_complete_schema.php?key=YOUR_KEY`
- Migrations: `https://kfm.brandboostup.in/emergencyDatabaseUpdate?key=run_migrations_2024_temp_key_change_me&fix=1`

**Expected Results:**
- ✅ 100+ tables created
- ✅ All critical tables exist
- ✅ No missing column errors
- ✅ Admin login works
- ✅ Dashboard loads

## Checklist

- [ ] SQL file is in `database/theboost_lmsnew.sql`
- [ ] File contains CREATE TABLE statements (structure only)
- [ ] Security key changed in `import_complete_schema.php`
- [ ] Imported schema successfully (150+ tables created)
- [ ] Verified critical tables exist
- [ ] Ran migrations to sync
- [ ] Ran seeders for initial data
- [ ] Cleared all caches
- [ ] Tested admin login
- [ ] Tested dashboard
- [ ] Tested frontend

## Next Steps

After successful import:
1. ✅ Create admin user if needed
2. ✅ Add sample content (courses/webinars)
3. ✅ Configure payment channels
4. ✅ Set up email settings
5. ✅ Test all features

---

**Need Help?** Check the error message in the import response for specific issues.
