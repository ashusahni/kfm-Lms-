# How to Use Complete Database Schema Export

## Quick Start Guide

### Step 1: Export Schema from Working KFM Database

**Using phpMyAdmin:**
1. Go to your working KFM server phpMyAdmin
2. Select database → Export → Custom
3. Check "Structure" only (uncheck "Data")
4. Check "Add IF NOT EXISTS"
5. Download and save as `complete_database_schema.sql`

### Step 2: Upload to New Server

**Your exported file should be at:**
```
backend/database/theboost_lmsnew.sql
```

**The import script automatically checks these locations (in order):**
1. ✅ `database/theboost_lmsnew.sql` (your file - checked first)
2. `complete_database_schema.sql` (project root)
3. `database/complete_database_schema.sql`
4. `theboost_lmsnew.sql` (project root)

**To verify file location:**
- Via cPanel File Manager: Check `backend/database/` folder
- Via SSH: `ls -la database/theboost_lmsnew.sql`

### Step 3: Import the Schema

**Option A: Using the Import Route (Web-based)**
```
https://your-domain.com/import_complete_schema?key=import_schema_2024_change_me
```

**Note:** Use `/import_complete_schema` (no `.php` extension) - it's now a Laravel route

**Option B: Using phpMyAdmin**
1. Open phpMyAdmin on new server
2. Select your database
3. Click "Import" tab
4. Choose `complete_database_schema.sql`
5. Click "Go"

**Option C: Using MySQL Command Line**
```bash
mysql -u username -p database_name < complete_database_schema.sql
```

## Why This is Better

✅ **One-time export** - Get everything at once
✅ **No missing tables** - Complete database structure
✅ **No missing columns** - All fields included
✅ **Correct indexes** - All indexes and constraints
✅ **Fast** - Import takes seconds vs hours of manual work

## Security Note

⚠️ **Change the secret key** in `import_complete_schema.php`:
```php
$SECRET_KEY = 'import_schema_2024_change_me'; // CHANGE THIS!
```

## After Import

1. Run migrations to ensure everything is in sync:
   ```
   https://your-domain.com/emergencyDatabaseUpdate?key=run_migrations_2024_temp_key_change_me&fix=1
   ```

2. Run seeders to populate initial data:
   ```
   php artisan db:seed
   ```

3. Clear cache:
   ```
   php artisan cache:clear
   php artisan config:clear
   ```

## Troubleshooting

**If import fails:**
- Check file size limits in phpMyAdmin
- Use command line if file is too large
- Split into smaller files if needed

**If tables already exist:**
- The script will skip existing tables
- Or use "DROP TABLE IF EXISTS" in export options
