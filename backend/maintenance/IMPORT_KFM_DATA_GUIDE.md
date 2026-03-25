# Import Demo Data from KFM.sql

## Overview
This script imports actual demo data (users, courses, webinars, products, etc.) from your KFM.sql file into your database.

## What Gets Imported

The script imports data for these priority tables:
- ✅ **Users** - Demo user accounts
- ✅ **Roles & Permissions** - User roles and permissions
- ✅ **Categories** - Course categories
- ✅ **Webinars/Courses** - Demo courses and webinars
- ✅ **Bundles** - Course bundles
- ✅ **Products** - Store products
- ✅ **Meetings** - Meeting sessions
- ✅ **Sessions** - Course sessions
- ✅ **Files** - Course files
- ✅ **Quizzes** - Course quizzes
- ✅ **Certificates** - Certificates
- ✅ **Settings** - Application settings
- ✅ **Currencies** - Currency data
- ✅ **Payment Channels** - Payment methods
- ✅ **Groups** - User groups
- ✅ **Sales** - Sales records
- ✅ **Purchases** - Purchase records
- ✅ **Comments** - Comments and reviews
- ✅ **Favorites** - User favorites
- ✅ **Tags** - Course tags
- ✅ **FAQs** - Frequently asked questions
- ✅ **Tickets** - Support tickets
- ✅ **Notifications** - User notifications

## How to Use

### Step 1: Upload KFM.sql
Make sure `KFM.sql` is in the `database/` folder:
```
backend/database/KFM.sql
```

### Step 2: Run the Import
Visit this URL in your browser:
```
https://your-domain.com/import_kfm_data?key=auto_fix_kfm_2024_change_me
```

Or use the migration key:
```
https://your-domain.com/import_kfm_data?key=run_migrations_2024_temp_key_change_me
```

## What Happens

1. ✅ Script reads KFM.sql file
2. ✅ Extracts INSERT statements for priority tables
3. ✅ Imports data into your database
4. ✅ Skips duplicate entries (won't break existing data)
5. ✅ Shows detailed report of what was imported

## Example Output

```json
{
    "status": "completed",
    "timestamp": "2024-01-15 10:30:00",
    "kfm_file": "found",
    "tables_imported": 25,
    "rows_imported": 1500,
    "tables_detail": {
        "users": 50,
        "webinars": 100,
        "categories": 20,
        "products": 30
    },
    "details": [
        "✓ Imported 50 rows into `users`",
        "✓ Imported 100 rows into `webinars`",
        "✓ Imported 20 rows into `categories`"
    ]
}
```

## Important Notes

### ⚠️ Duplicate Data
- The script will **skip** rows that already exist (based on primary keys)
- Your existing data will **not be deleted**
- Only new data will be added

### ⚠️ Foreign Key Constraints
- Foreign key checks are temporarily disabled during import
- This prevents errors when importing related data

### ⚠️ Large Files
- If KFM.sql is very large (>50MB), the import might take a few minutes
- Be patient and wait for the JSON response

## Troubleshooting

### "KFM.sql not found"
- Make sure `KFM.sql` is in `database/` folder
- Check file name is exactly `KFM.sql` (case-sensitive)

### "Table does not exist"
- Run migrations first: `/emergencyDatabaseUpdate?key=...&fix=1`
- Or import schema first: `/import_complete_schema?key=...`

### "Duplicate entry" warnings
- This is normal! It means data already exists
- The script will skip duplicates and continue

### Still having issues?
- Check the `errors` array in the JSON output
- Make sure your database has all required tables
- Run the auto-fix script first: `/auto_fix_from_kfm?key=...`

## Recommended Order

1. **First:** Import schema structure
   ```
   /import_complete_schema?key=...
   ```

2. **Second:** Fix missing columns
   ```
   /auto_fix_from_kfm?key=...
   ```

3. **Third:** Import demo data
   ```
   /import_kfm_data?key=...
   ```

## Files Created

1. ✅ `database/import_kfm_data.php` - Main import script
2. ✅ Route added to `RouteServiceProvider.php` - `/import_kfm_data`
3. ✅ `IMPORT_KFM_DATA_GUIDE.md` - This guide

---

**Ready to import your demo data! 🎉**
