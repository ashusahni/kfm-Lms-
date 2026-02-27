# Paid-Plugin Features Removed from Backend

The following features that typically require paid add-ons or premium services have been **disabled/removed** from the backend (routes commented out or UI hidden). Controllers and models remain in the codebase; only access via routes and admin UI has been removed.

## Admin panel (`routes/admin.php`)

| Feature | What was removed |
|--------|-------------------|
| **User toggles** | `disable_cashback_toggle`, `disable_registration_bonus`, `disable_installment_approval` (user edit actions) |
| **Course forums** | `{webinar_id}/forums` routes (forum per course) |
| **Installments** | Entire `financial/installments` group (plans, orders, overdue, verification, etc.) |
| **Referrals** | `referrals` group (history, users, export) |
| **Rewards (points)** | `rewards` group (items, settings) |
| **Registration bonus** | `registration_bonus` group (history, settings) |
| **Cashback** | `cashback` group (history, transactions, rules) |
| **Waitlists** | `waitlists` group (list, export, view, disable, delete items) |
| **Gifts** | `gifts` group (list, reminders, cancel, settings) |
| **Featured / recommended topics** | `featured-topics` and `recommended-topics` (forum-related) |
| **AI Contents** | `ai-contents` group (lists, generate, settings, templates) |

## Web routes (`routes/web.php`)

| Feature | What was removed |
|--------|-------------------|
| **Referral** | `GET /reff/{code}` |
| **Course installments** | `GET /course/{slug}/installments` (auth) |
| **Installments** | `installments` group (request_submitted, request_rejected, index, store) |
| **Waitlists** | `waitlists` group (join) |
| **Gift** | `gift` group (index, store) |

## API panel routes (`routes/api/user.php`)

| Feature | What was removed |
|--------|-------------------|
| **Rewards** | `rewards` group (index, exchange, buyWithPoint, reward-courses) |

## Admin UI

| Location | Change |
|----------|--------|
| **Navbar** | AI content drawer button hidden (commented in `admin/includes/navbar.blade.php`) |
| **App layout** | AI content generator include and `ai-content-generator.min.js` script removed (`admin/layouts/app.blade.php`) |
| **Settings > Financial** | “Referral” tab and referral form content hidden (`admin/settings/financial.blade.php`) |
| **Settings > General > Features** | “Cashback” section and “Enable waitlist” section hidden (`admin/settings/general/features.blade.php`) |
| **Course create/edit** | “Enable waitlist” checkbox replaced with hidden input (waitlist disabled) (`admin/webinars/create.blade.php`) |
| **User edit > Financial tab** | “Enable installments”, “Installment approval”, “Disable cashback”, “Enable registration bonus”, and “Registration bonus amount” fields hidden; hidden inputs keep values off (`admin/users/editTabs/financial.blade.php`) |

---

## Restoring a feature

To re-enable any feature:

1. In the relevant route file, uncomment the corresponding `/* ... */` block (or restore the route line).
2. For AI content: in `navbar.blade.php` and `app.blade.php`, uncomment the Blade/script blocks marked as "AI Content (paid add-on) - Removed".
3. Clear route and config cache: `php artisan route:clear` and `php artisan config:clear`.

Controllers, models, migrations, and views for these features are **unchanged**; only routes and the AI UI were disabled.
