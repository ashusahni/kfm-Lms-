# Import All Data from KFM.sql - Complete Guide

## Overview

The `import_kfm_data_smart.php` script has been updated to import **ALL data** from `KFM.sql`, not just priority tables. This includes:
- ✅ Users
- ✅ Courses/Webinars
- ✅ Categories
- ✅ Sections, Files, Sessions
- ✅ Products, Bundles
- ✅ Sales, Orders
- ✅ Comments, Reviews
- ✅ Settings
- ✅ And **ALL other tables** with data

## What Changed

The script now:
1. **Imports ALL tables** from KFM.sql (not just priority ones)
2. **Smart column mapping** - handles column mismatches automatically
3. **Skips system tables** - migrations, password_resets, failed_jobs
4. **Detailed reporting** - shows exactly what was imported

## Usage

### Step 1: Ensure KFM.sql is Available

Make sure `KFM.sql` is in one of these locations:
- `database/KFM.sql` (recommended)
- `Databases/KFM.sql`

### Step 2: Run the Import

Visit this URL in your browser:
```
https://kfm.brandboostup.in/import_kfm_data_smart?key=auto_fix_kfm_2024_change_me
```

Or if you set a custom key:
```
https://kfm.brandboostup.in/import_kfm_data_smart?key=your_custom_secret_key
```

## Expected Output

The script will return JSON showing:
- Total tables imported
- Total rows imported
- Breakdown per table
- Any errors or warnings

Example:
```json
{
    "status": "completed",
    "timestamp": "2026-03-06 22:45:00",
    "tables_imported": 150,
    "rows_imported": 50000,
    "errors": [],
    "warnings": [],
    "details": [
        "✓ Imported 100 rows into `users`",
        "✓ Imported 50 rows into `webinars`",
        "✓ Imported 200 rows into `categories`",
        "..."
    ],
    "tables_detail": {
        "users": 100,
        "webinars": 50,
        "categories": 200,
        "..."
    },
    "message": "Successfully imported 50000 rows from 150 table(s)!",
    "timestamp_end": "2026-03-06 22:50:00"
}
```

## What Gets Imported

### Core Data
- ✅ **Users** - All user accounts (admin, teachers, students, organizations)
- ✅ **Roles** - User roles and permissions
- ✅ **Categories** - Course categories
- ✅ **Webinars** - All courses/webinars
- ✅ **Webinar Translations** - Course titles and descriptions in multiple languages

### Course Content
- ✅ **Sections** - Course sections/chapters
- ✅ **Files** - Course files and resources
- ✅ **Sessions** - Live sessions and meetings
- ✅ **Quizzes** - Course quizzes
- ✅ **Assignments** - Course assignments

### E-commerce
- ✅ **Products** - Store products
- ✅ **Bundles** - Course bundles
- ✅ **Sales** - Purchase records
- ✅ **Orders** - Order details
- ✅ **Cart** - Shopping cart items

### Content & Engagement
- ✅ **Comments** - User comments
- ✅ **Reviews** - Course reviews
- ✅ **Blog** - Blog posts
- ✅ **Pages** - Static pages
- ✅ **FAQs** - Frequently asked questions

### Settings & Configuration
- ✅ **Settings** - System settings
- ✅ **Currencies** - Currency configurations
- ✅ **Payment Channels** - Payment gateway settings
- ✅ **Support Departments** - Support ticket departments

### And Much More!
- ✅ All other tables with data in KFM.sql

## Processing Time

**Note:** Importing all data can take several minutes depending on:
- Size of KFM.sql file
- Number of rows to import
- Server performance

**Be patient!** The script will show progress as it processes each table.

## Troubleshooting

### Error: "Table not found"

**Solution:**
- Run the create tables script first:
  ```
  https://kfm.brandboostup.in/create_all_missing_tables_from_kfm?key=auto_fix_kfm_2024_change_me
  ```

### Warning: "Skipped row" or "Duplicate entry"

**This is normal!** The script:
- Skips duplicate entries (won't overwrite existing data)
- Handles column mismatches gracefully
- Continues even if some rows fail

### Import Takes Too Long

**Solution:**
- This is expected for large datasets
- Check the browser console - it should show progress
- The script processes tables one by one
- You can refresh to see current status (but don't interrupt the process)

### Some Tables Show 0 Rows

**Possible reasons:**
- Table exists but has no data in KFM.sql
- Column structure mismatch (script will try to map columns)
- Data format issues

**Solution:**
- Check the `warnings` array in the JSON output
- Verify the table has data in KFM.sql
- Some tables may need manual import if structure differs significantly

## After Import

### 1. Verify Data

Check what was imported:
```
https://kfm.brandboostup.in/check_imported_data?key=auto_fix_kfm_2024_change_me
```

### 2. Test Your Application

- ✅ Login with demo accounts
- ✅ Browse courses
- ✅ Check admin dashboard
- ✅ Verify all features work

### 3. Default Login Credentials

From KFM.sql, you should have:
- **Admin:** Check KFM.sql for admin email/password
- **Teacher:** Check KFM.sql for teacher accounts
- **Student:** Check KFM.sql for student accounts

## Customization

### Import Only Specific Tables

If you want to import only specific tables, edit `database/import_kfm_data_smart.php`:

```php
// Change this line:
$priorityTables = [];  // Empty = import all tables

// To this:
$priorityTables = [
    'users',
    'webinars',
    'categories',
    // Add more table names here
];
```

### Skip Specific Tables

To skip certain tables, add them to `$skipTables`:

```php
$skipTables = [
    'migrations',
    'password_resets',
    'failed_jobs',
    'your_table_to_skip',  // Add here
];
```

## Security Notes

⚠️ **Important:**
- This script bypasses all Laravel middleware
- It has direct database access
- **Change the default secret key** in production
- **Remove or disable this route** after use if possible
- Or restrict access by IP address

## Next Steps

After importing all data:

1. ✅ **Verify imports** - Check the JSON output
2. ✅ **Test login** - Try logging in with demo accounts
3. ✅ **Browse courses** - Check if courses display correctly
4. ✅ **Check admin panel** - Verify dashboard works
5. ✅ **Test features** - Ensure all functionality works

---

**Ready to import?** Visit the URL above and wait for the import to complete!
