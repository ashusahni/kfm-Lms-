# Databases (KFM) – Rocket LMS

This folder contains the **KFM** MySQL dump used as the main database for the Rocket LMS backend and frontend.

## Schema alignment with LMS

- **KFM.sql** – Full dump (tables + data). Table list and structure match the Laravel backend except for the additions below.
- **KFM_schema_patch.sql** – Run **once after** importing `KFM.sql` so the schema matches the current backend/frontend.

### What the patch adds

| Change | Reason |
|--------|--------|
| Table `course_batches` | Backend uses it for batch enrollment (CourseBatch model, admin course batches). |
| Column `cart.batch_id` | Cart can attach to a batch when enrolling in a program. |
| Column `order_items.batch_id` | Order line can reference a batch. |
| Column `sales.batch_id` | Sale record can reference a batch. |

If you use **Laravel migrations** (e.g. `php artisan migrate`) on a DB created from KFM, the same changes are applied by:

- `2025_03_04_100000_create_course_batches_and_add_batch_id.php`

You only need the patch when you restore from `KFM.sql` and do **not** run migrations (e.g. production restore from backup).

## Payment channels (Razorpay)

The app is configured for **Razorpay only**. The migration `2025_03_04_000000_razorpay_only_payment_channel.php` truncates `payment_channels` and inserts a single Razorpay row. If you restore from KFM.sql and then run `php artisan migrate`, payment channels will be reset to Razorpay. The `payment_channels` table structure in KFM.sql is correct.

## How to use

1. Create/restore the database from **KFM.sql** (e.g. `mysql -u user -p database < KFM.sql`).
2. Either:
   - Run **KFM_schema_patch.sql** once, **or**
   - Run Laravel migrations: `php artisan migrate` (from the `backend` directory).
3. Ensure `.env` in `backend` points to this database (DB_DATABASE, DB_USERNAME, DB_PASSWORD, etc.).

After that, table schema and usage are aligned with the LMS backend and frontend.

## Clearing all demo data

To remove all demo courses, categories, blog posts, sales, orders, and related content (so you can add your own courses and data), run from the **backend** directory:

```bash
php artisan db:clear-demo
```

You will be asked to confirm. Use `--force` to skip the confirmation:

```bash
php artisan db:clear-demo --force
```

**Kept:** users, roles, permissions, sections, payment channels, settings (so you can still log in and the app works).  
**Cleared:** webinars (courses), categories, blog, sales, orders, cart, quizzes, lessons, certificates, bundles, products, and all other content tables.
