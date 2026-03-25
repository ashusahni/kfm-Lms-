# Maintenance Scripts

This directory contains database maintenance and migration scripts that are used for database schema updates and data imports.

## ⚠️ Security Notice

These scripts are **disabled by default in production**. They are only accessible when:
- `APP_ENV` is not `production`, OR
- `ENABLE_MAINTENANCE_ROUTES=true` is set in `.env`

## Available Scripts

### Database Scripts (`database-scripts/`)

- `auto_fix_from_kfm.php` - Automatically compare and fix database schema from KFM.sql
- `create_all_missing_tables_from_kfm.php` - Create all missing tables from KFM.sql
- `fix_missing_relationships.php` - Fix broken foreign key relationships
- `import_kfm_data_smart.php` - Smart import of demo data from KFM.sql
- `import_kfm_data.php` - Basic import of demo data
- `check_imported_data.php` - Check what data was imported

### Usage

These scripts are accessed via HTTP routes (when enabled):

- `/auto_fix_from_kfm?key=YOUR_SECRET_KEY`
- `/create_all_missing_tables_from_kfm?key=YOUR_SECRET_KEY`
- `/fix_missing_relationships?key=YOUR_SECRET_KEY`
- `/import_kfm_data_smart?key=YOUR_SECRET_KEY`
- `/check_imported_data?key=YOUR_SECRET_KEY`

**Important:** Always use a strong secret key in production if you need to enable these routes.

## For Production

In production, these scripts should be:
1. Disabled via `ENABLE_MAINTENANCE_ROUTES=false`
2. Or accessed only via SSH and run directly via CLI:
   ```bash
   php maintenance/database-scripts/fix_missing_relationships.php
   ```
