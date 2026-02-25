# Fit Karnataka Mission – Configuration & Features

This document summarizes what was implemented based on the **Fit Karnataka Module Decision** and **KFM LMS Task Breakdown** docs.

## Enable/Disable

In `.env`:

```env
FIT_KARNATAKA_ENABLED=true
```

When `true`, the LMS runs in Fit Karnataka mode (health + diet + live coaching). When `false`, the app behaves as the generic course marketplace.

## Config File

`config/fit_karnataka.php`:

- **disable** – Feature toggles: `forum`, `store_products`, `reward_points`, `gift`, `affiliate_referral`, `instructor_finder`, `organizations`, `ad_banners`, `bundles`, `instructor_blog`, `become_instructor_public`. Set any to `true` to disable that feature (gift is wired; others can be wrapped similarly).
- **home_sections_hide** – Home page sections that are hidden even if enabled in DB (e.g. latest_bundles, best_sellers, reward_program, organizations, ad banners).
- **terminology** – Labels used when Fit Karnataka is on (e.g. “Programs” instead of “Courses”, “Daily Challenges” instead of “Assignments”). See `lang/en/fit_karnataka.php`.

## Helpers (in `app/Helpers/helper.php`)

- `isFitKarnatakaEnabled()` – Whether Fit Karnataka mode is on.
- `fitKarnatakaFeatureDisabled($feature)` – Whether a feature is disabled (e.g. `gift`, `reward_points`).
- `fitKarnatakaFilterHomeSections($sections)` – Filters home sections to the allow list (used in `HomeController::getHomeData()`).
- `fitKarnatakaTerm($key)` – Returns terminology override (e.g. `fitKarnatakaTerm('course')` → “Program”) or `null`.

## Home Page

When Fit Karnataka is enabled, only these sections are shown (others in `home_sections_hide` are filtered out):

- latest_classes (Latest Programs)
- featured_classes (Featured Programs)
- instructors, testimonials, upcoming_courses
- video_or_image_section, blog
- free_classes, discount_classes (optional)

## Daily Health Challenge (Student Logs)

- **Migration:** `database/migrations/2025_02_24_100000_create_student_daily_health_logs_table.php`
- **Model:** `App\Models\StudentDailyHealthLog` (fields: user_id, webinar_id, log_date, water_ml, meals JSON, calories, protein, carbs, fat, medicines, activity_minutes, activity_notes, adherence_score, locked_at).
- **API (panel):**
  - `GET /api/development/panel/health-logs` – List logs (student: own; instructor: students in own courses; admin: all). Query: `user_id`, `webinar_id`, `from_date`, `to_date`, `per_page`.
  - `POST /api/development/panel/health-logs` – Create/update log (student only, respects lock).
  - `GET /api/development/panel/health-logs/{id}` – Get one log (with permission check).

Run migration:

```bash
cd backend && php artisan migrate
```

## Public API for Frontend

- **Config:** `GET /api/fit-karnataka-config` – Returns `{ enabled, disable, terminology }` so the React app can hide menus and use “Programs” / “Daily Challenges” etc.

## Disabled Features (when configured)

- **Gift** – Web routes under `/gift` use middleware `fit_karnataka.disable:gift` and return 404 when the feature is disabled.
- Other features (rewards, forum, store, affiliate, etc.) can be disabled the same way by adding `fit_karnataka.disable:<feature>` middleware to their route groups and ensuring the key exists in `config/fit_karnataka.php` → `disable`.

## Instructor & Admin (from Task Breakdown)

- **Instructor panel:** Course management, live class (Zoom/Agora), student enrollment, progress, and **health log visibility** (read-only) are in scope; health logs are exposed via the panel API above.
- **Admin panel:** User/instructor/course moderation, payments, reports, system config, logs – existing admin routes apply; health data is read-only via the same health-logs API with admin role.

## Next Steps (optional)

- Add “Daily Challenges” (assignments) renaming in the panel and frontend using `fit_karnataka.terminology` or `lang/en/fit_karnataka.php`.
- Add adherence scoring logic (day/week/streaks) and “at-risk” flags based on `student_daily_health_logs`.
- Add instructor dashboard widgets for health cohort metrics (adherence %, missed logs).
- Add health reports (PDF/export) and cohort analytics for admin.
