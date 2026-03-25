# Rocket LMS Backend – API Features Reference

This document lists **all current backend API features** for sign-up, sign-in, courses, and health monitor, and notes applied fixes for mobile app connectivity.

**Base path:** All endpoints below are under `/api/development/` unless noted.  
**Auth:** Send `x-api-key`, `Content-Type: application/json`, `Accept: application/json`, and (for panel) `Authorization: Bearer <token>`.

---

## 1. Sign-in & sign-up (auth)

### 1.1 Config (required for app)

| Method | Endpoint | Auth | Description |
|--------|----------|------|--------------|
| GET | `config` | No (API key only) | App config: `register_method`, `show_google_login_button`, `show_facebook_login_button`, `showOtherRegisterMethod`, `selectRolesDuringRegistration`, `currency`, `referralSettings`, etc. **Response:** `{ "data": { ... } }`. |
| GET | `config/register/{role}` | No (API key only) | Register config per role (`user`, `teacher`, `organization`). Returns `{ "data": { "role", "fields" } }`. Use for custom registration fields. |

### 1.2 Login

| Method | Endpoint | Auth | Body | Description |
|--------|----------|------|------|--------------|
| POST | `login` | No | `username`, `password` | Login by email or phone. `username` = email or full number (e.g. `11234567890`). **Success:** `{ "success": true, "data": { "token", "user_id" } }`. |

### 1.3 Register (multi-step)

| Method | Endpoint | Auth | Body | Description |
|--------|----------|------|------|--------------|
| POST | `register/step/1` | No | **Email:** `register_method`, `email`, `password`, `password_confirmation`. **Phone:** `register_method`, `country_code`, `mobile`, `password`, `password_confirmation`. Optional: `fields`. | Step 1 – create account. **Response:** `success: true`, `status`: `go_step_2` (verify code) or `go_step_3` (name/referral), `data.user_id`. |
| POST | `register/step/2` | No | `user_id`, `code` | Step 2 – verify code. **Success:** `{ "success": true }`. |
| POST | `register/step/3` | No | `user_id`, `full_name`, `referral_code` (optional) | Step 3 – name & referral. **Success:** `{ "success": true, "data": { "token", "user_id" } }`. |

### 1.4 Forgot password & verification

| Method | Endpoint | Auth | Body | Description |
|--------|----------|------|------|--------------|
| POST | `forget-password` | No | **Email:** `email`. **Phone:** `country_code`, `mobile`. | Sends reset email (or looks up email from mobile). **Success:** `{ "success": true }`. |
| POST | `reset-password/{token}` | No | — | Reset password with token from email. |
| POST | `verification` | No | `code`, `email` or `mobile` | Confirm verification code. |

### 1.5 Social login

| Method | Endpoint | Auth | Body | Description |
|--------|----------|------|------|--------------|
| POST | `google/callback` | No | `email`, `name`, `id` (Google token) | Google login. **Success:** `{ "data": { "token" } }`. |
| POST | `facebook/callback` | No | `id`, `name`, `email` | Facebook login. **Success:** `{ "success": true, "data": { "token" } }`. |

### 1.6 Logout

| Method | Endpoint | Auth | Description |
|--------|----------|------|--------------|
| POST | `logout` | Yes (Bearer) | Invalidates token. **Success:** `{ "success": true }`. |

---

## 2. Guest & public (no login or optional token)

### 2.1 Courses & content

| Method | Endpoint | Auth | Description |
|--------|----------|------|--------------|
| GET | `courses` | No | List active courses (brief). |
| GET | `courses/{id}` | No | Course details. |
| GET | `courses/{id}/content` | Optional token | Course content (chapters, files, sessions, text lessons). **Fixed:** content items returned as arrays for app. |
| GET | `courses/{id}/quizzes` | No | Quizzes for course. |
| GET | `courses/{id}/certificates` | No | Certificates for course. |
| GET | `courses/reports/reasons` | No | Report reasons. |
| POST | `courses/{id}/report` | Yes | Report course. |
| POST | `courses/{id}/toggle` | Yes | Toggle learning status. |

### 2.2 Featured, categories, search

| Method | Endpoint | Auth | Description |
|--------|----------|------|--------------|
| GET | `featured-courses` | No | Featured courses. |
| GET | `categories` | No | Categories list. |
| GET | `categories/{id}/webinars` | No | Courses in category. |
| GET | `trend-categories` | No | Trend categories. |
| GET | `search` | No | Search (query param). |

### 2.3 Bundles

| Method | Endpoint | Auth | Description |
|--------|----------|------|--------------|
| GET | `bundles` | No | List bundles. |
| GET | `bundles/{id}` | No | Bundle details. |
| GET | `bundles/{id}/webinars` | No | Webinars in bundle. |
| POST | `bundles/{id}/free` | Yes | Enroll in free bundle. |

### 2.4 Providers, regions, other

| Method | Endpoint | Auth | Description |
|--------|----------|------|--------------|
| GET | `providers/instructors` | No | Instructors. |
| GET | `providers/organizations` | No | Organizations. |
| GET | `providers/consultations` | No | Consultations. |
| GET | `regions/countries` | No | Countries. |
| GET | `regions/provinces/{id}` | No | Provinces. |
| GET | `regions/cities/{id}` | No | Cities. |
| GET | `regions/districts/{id}` | No | Districts. |
| GET | `timezones` | No | Timezones. |
| GET | `blogs` | No | Blogs. |
| GET | `blogs/categories` | No | Blog categories. |
| GET | `blogs/{id}` | No | Blog post. |
| GET | `subscribe` | No | Subscribe plans. |
| GET | `users/{id}/profile` | No | User/profile. |
| GET | `users/{id}/meetings` | No | Available meeting times. |
| POST | `meetings/reserve` | Yes | Reserve meeting. |
| POST | `users/{id}/send-message` | Yes | Send message. |

---

## 3. Panel API (requires login)

**Prefix:** `/api/development/panel/`. All require `Authorization: Bearer <token>`.

### 3.1 Profile & account

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `profile-setting` | Profile settings. |
| PUT | `profile-setting` | Update profile. |
| PUT | `profile-setting/password` | Change password. |
| POST | `profile-setting/images` | Update images. |
| GET | `quick-info` | Quick info. |
| POST | `delete-account` | Delete account request. |

### 3.2 Courses & learning

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `webinars/purchases` | My purchased courses. |
| GET | `webinars/organization` | Organization courses. |
| POST | `webinars/{id}/free` | Enroll in free course. |
| GET | `webinars/{id}` | Course details. |
| GET | `webinars/{id}/chapters` | Chapters. |
| GET | `webinars/{id}/chapters/{chapter}` | Chapter detail. |
| GET | `webinars/{id}/noticeboards` | Noticeboards. |
| GET | `webinars/certificates` | My certificates. |
| GET | `webinars/{id}/statistic` | Teacher: course statistic. |
| GET | `classes` | Teacher: my classes. |

### 3.3 Favorites, reviews, comments

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `favorites` | Favorites list. |
| POST | `favorites/toggle2` | Toggle favorite. |
| DELETE | `favorites/{id}` | Remove favorite. |
| GET | `reviews` | Reviews. |
| POST | `reviews` | Create review. |
| GET | `comments` | Comments. |
| POST | `comments` | Create comment. |

### 3.4 Cart, payments, financial

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `cart/list` | Cart list. |
| POST | `cart/store` | Add to cart. |
| POST | `cart` | Add to cart (alternate). |
| DELETE | `cart/{id}` | Remove from cart. |
| POST | `cart/checkout` | Checkout. |
| POST | `cart/coupon/validate` | Validate coupon. |
| POST | `cart/web_checkout` | Web checkout. |
| POST | `payments/request` | Payment request. |
| POST | `payments/credit` | Pay by credit. |
| GET | `financial/summary` | Financial summary. |
| GET | `financial/sales` | Sales. |
| GET | `financial/platform-bank-accounts` | Bank accounts. |
| GET | `financial/payout` | Payout. |
| POST | `financial/payout` | Request payout. |

### 3.5 Quizzes, certificates, assignments

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `quizzes/not_participated` | Quizzes not participated. |
| GET | `quizzes/{id}/start` | Start quiz. |
| GET | `quizzes/{id}/result` | Quiz result. |
| POST | `quizzes/{id}/store-result` | Store result. |
| GET | `quizzes/results/my-results` | My results. |
| GET | `certificates/achievements` | Certificate achievements. |
| GET | `certificates/created` | Teacher: created certificates. |
| GET | `my_assignments` | My assignments. |
| GET | `assignments/{id}/messages` | Assignment messages. |
| POST | `assignments/{id}/messages` | Post message. |

### 3.6 Support, notifications, meetings

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `support/tickets` | Support tickets. |
| GET | `support/departments` | Departments. |
| GET | `support/{id}` | Ticket detail. |
| POST | `support` | Create ticket. |
| POST | `support/{id}/conversations` | Reply. |
| GET | `notifications` | Notifications. |
| POST | `notifications/{id}/seen` | Mark seen. |
| GET | `meetings` | My meetings. |
| GET | `meetings/{id}` | Meeting detail. |
| POST | `meetings/{id}/finish` | Finish meeting. |

### 3.7 Subscribe & bundles

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `subscribe` | Subscribe plans. |
| POST | `subscribe/web_pay` | Web pay. |
| POST | `subscribe/apply` | Apply. |
| POST | `bundles/{id}/free` | Enroll free bundle. |

### 3.8 Follow & other

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `users/{id}/follow` | Toggle follow. |
| POST | `become_instructor` | Become instructor. |

---

## 4. Health monitor (Fit Karnataka)

**Public config (no auth):**

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/fit-karnataka-config` | **Outside** `/development`. Returns `enabled`, `disable`, `terminology`. |

**Panel (auth required):** Prefix `/api/development/panel/`.

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `health-logs` | List health logs (student: own; teacher: course students). Query: `user_id`, `webinar_id`, `from_date`, `to_date`, `per_page`. |
| GET | `health-logs/summary` | Summary stats (same filters). |
| GET | `health-logs/{id}` | Single log. |
| POST | `health-logs` | Create/upsert log (body: `log_date`, `webinar_id`, `water_ml`, `meals`, `calories`, etc.). |
| PUT | `health-logs/{id}` | Update log. |
| DELETE | `health-logs/{id}` | Delete log. |
| GET | `course-health-log-settings/{webinar_id}` | Course health log settings (enabled, tracking_notes, custom_fields). |

See **HEALTH_LOG_API.md** for full request/response and locking rules.

---

## 5. Fixes applied for app connectivity

| Area | Fix |
|------|-----|
| **Register** | Step 1: when existing user needs verification or step 3, response now uses `success: 1` and `status: "go_step_2"` or `"go_step_3"` with `data.user_id` so the app can proceed. New user creation also returns `status: "go_step_2"` or `"go_step_3"` from `checkConfirmed`. |
| **Config** | `GET config` response wrapped in `data`. Added app-expected keys: `show_google_login_button`, `show_facebook_login_button`, `showOtherRegisterMethod`, `selectRolesDuringRegistration`, `currency_position`, `currency_decimal`, `referralSettings.status`. Safe defaults if settings missing. |
| **Config register** | Added `GET config/register/{role}` (`user`, `teacher`, `organization`) returning `{ "data": { "role", "fields" } }` so the app does not 404. |
| **Course content** | `GET courses/{id}/content`: content items (chapters, files, sessions, text lessons) now converted to arrays via `$item->resolve()` so the app receives consistent JSON (fixes `array_merge` / Resource type error). |
| **RequestType** | Invalid or missing `Content-Type: application/json` now returns `success: false` (was `success: true`), so the app can show an error. |

---

## 6. Quick connectivity checklist

- [ ] **Base URL** in app = backend base (e.g. ngrok) + path `/api/development/` (no trailing slash on base).
- [ ] **Headers:** `x-api-key: 1234` (or your `API_KEY`), `Content-Type: application/json`, `Accept: application/json`, `x-locale: en`.
- [ ] **Login:** `POST login` with `username`, `password`; expect `data.token`.
- [ ] **Config:** `GET config` returns `data` with `register_method`, social flags, currency.
- [ ] **Register:** Step 1 returns `status` + `data.user_id`; step 3 returns `data.token`.
- [ ] **Health:** `GET /api/fit-karnataka-config` and panel `health-logs` / `course-health-log-settings/{webinar_id}` as in HEALTH_LOG_API.md.

Use **SIGNIN_CHECKLIST.md** for sign-in-only troubleshooting.
