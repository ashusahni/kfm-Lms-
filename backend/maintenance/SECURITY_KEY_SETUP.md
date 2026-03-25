# Security Key Setup Guide

## What is the Security Key?

The security key is a **password** you create yourself to protect the import script from unauthorized access. It's NOT something that exists already - **you need to create it**.

## How to Set Your Security Key

### For `import_complete_schema.php`

**Step 1:** Open `import_complete_schema.php`

**Step 2:** Find line 15:
```php
$SECRET_KEY = 'import_schema_2024_change_me';
```

**Step 3:** Change it to your own secure key:
```php
$SECRET_KEY = 'my_secure_key_12345';  // Use any password you want
```

**Step 4:** Save the file

**Step 5:** Use your key in the URL:
```
https://kfm.brandboostup.in/import_complete_schema?key=my_secure_key_12345
```

**Note:** The route is `/import_complete_schema` (no `.php` extension) - it's a Laravel route

### For Migration Route (`emergencyDatabaseUpdate`)

**Option A: Use Default Key (Temporary)**
```
https://kfm.brandboostup.in/emergencyDatabaseUpdate?key=run_migrations_2024_temp_key_change_me
```

**Option B: Set in .env File (Recommended for Production)**

1. Open `.env` file
2. Add this line:
   ```
   MIGRATION_SECRET_KEY=your_secure_migration_key_here
   ```
3. Save the file
4. Use in URL:
   ```
   https://kfm.brandboostup.in/emergencyDatabaseUpdate?key=your_secure_migration_key_here
   ```

## Examples of Good Security Keys

✅ **Good Keys:**
- `kfm_import_2024_secure_xyz123`
- `my_lms_import_key_2024`
- `brandboostup_secure_import_2024`
- `kfm_database_import_secure_key`

❌ **Bad Keys:**
- `12345` (too simple)
- `password` (too common)
- `import_schema_2024_change_me` (default - everyone knows it)

## Quick Setup

### For Import Script:

1. **Edit `import_complete_schema.php` line 15:**
   ```php
   $SECRET_KEY = 'kfm_import_2024_secure';  // Your custom key
   ```

2. **Use in URL:**
   ```
   https://kfm.brandboostup.in/import_complete_schema?key=kfm_import_2024_secure
   ```
   
   **Note:** Use `/import_complete_schema` (no `.php` extension)

### For Migration Route:

1. **Edit `.env` file:**
   ```
   MIGRATION_SECRET_KEY=kfm_migration_2024_secure
   ```

2. **Use in URL:**
   ```
   https://kfm.brandboostup.in/emergencyDatabaseUpdate?key=kfm_migration_2024_secure
   ```

## Security Best Practices

1. ✅ **Use a long, random key** (at least 20 characters)
2. ✅ **Don't share the key** publicly
3. ✅ **Change default keys** before production
4. ✅ **Delete import scripts** after use (or move outside web root)
5. ✅ **Use different keys** for different scripts

## Current Default Keys

- **Import Script:** `import_schema_2024_change_me` (in `import_complete_schema.php`)
- **Migration Route:** `run_migrations_2024_temp_key_change_me` (in RouteServiceProvider)

⚠️ **Both should be changed for production use!**

## Troubleshooting

**Error: "Access denied. Invalid key"**
- Check the key in the URL matches the key in the file
- Make sure there are no extra spaces
- Key is case-sensitive

**Error: "Key not found"**
- Make sure you're using `?key=your_key` in the URL
- Check the key spelling matches exactly
