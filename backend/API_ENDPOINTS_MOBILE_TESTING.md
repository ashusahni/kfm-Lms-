# Rocket LMS – API Endpoints for Mobile Application Testing

**Base URL:** `{{base_url}}/api/development`  
**Example:** `https://lms.rocket-soft.org/api/development` or `http://127.0.0.1:8000/api/development`

**Headers for authenticated requests:**
- `Authorization: Bearer {token}` (JWT)
- `Content-Type: application/json`
- `Accept: application/json`
- `x-api-key: {API_KEY}` (match backend `.env` `API_KEY`)

---

## 1. General

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/home` | No | Home data |
| GET | `/fit-karnataka-config` | No | Fit Karnataka feature config (enabled, terminology) |

---

## 2. Auth

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/register/step/{step}` | No | Registration step (JSON body) |
| POST | `/login` | No | Login (email, password) |
| POST | `/forget-password` | No | Send forgot password email |
| POST | `/reset-password/{token}` | No | Reset password with token |
| POST | `/verification` | No | Confirm verification code |
| GET | `/google` | No | Redirect to Google OAuth |
| GET | `/facebook` | No | Redirect to Facebook OAuth |
| POST | `/google/callback` | No | Google OAuth callback |
| POST | `/facebook/callback` | No | Facebook OAuth callback |
| POST | `/logout` | Yes | Logout (invalidate token) |

---

## 3. Config

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/config` | No | App config list |
| GET | `/config/register/{role}` | No | Register config by role (e.g. student, teacher) |

---

## 4. Guest / Public (no auth required)

### Courses
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/courses` | No | List courses |
| GET | `/courses/{id}` | No | Course detail |
| GET | `/courses/{id}/content` | No | Course content |
| GET | `/courses/{id}/quizzes` | No | Course quizzes |
| GET | `/courses/{id}/certificates` | No | Course certificates |
| GET | `/courses/reports/reasons` | No | Report reasons |
| POST | `/courses/{id}/report` | Optional | Report course |
| POST | `/courses/{webinar_id}/toggle` | Yes | Toggle learning status |

### Certificate
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/certificate_validation` | No | Validate certificate (query params) |

### Discovery
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/featured-courses` | No | Featured courses |
| GET | `/categories` | No | Categories |
| GET | `/categories/{id}/webinars` | No | Webinars by category |
| GET | `/trend-categories` | No | Trend categories |
| GET | `/search` | No | Search (query params) |

### Providers / Users (public)
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/providers/instructors` | No | Instructors list |
| GET | `/providers/dieticians` | No | Dieticians list |
| GET | `/providers/organizations` | No | Organizations |
| GET | `/providers/consultations` | No | Consultations |
| GET | `/instructors` | No | Instructors |
| GET | `/organizations` | No | Organizations |
| GET | `/users/{id}/profile` | No | User public profile |
| GET | `/users/{id}/meetings` | No | User available meeting times |
| POST | `/users/{id}/send-message` | No | Send message to user |

### Meetings (guest)
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/meetings/reserve` | Yes | Reserve a meeting (JSON) |

### Files
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/files/{file_id}/download` | No | Download file |

### Blogs
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/blogs` | No | Blog list |
| GET | `/blogs/categories` | No | Blog categories |
| GET | `/blogs/{id}` | No | Blog detail |

### Subscribe
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/subscribe` | No | Subscription plans list |

### Misc
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/newsletter` | No | Newsletter signup |
| POST | `/contact` | No | Contact form |
| GET | `/regions/countries` | No | Countries |
| GET | `/regions/provinces/{id?}` | No | Provinces |
| GET | `/regions/cities/{id?}` | No | Cities |
| GET | `/regions/districts/{id?}` | No | Districts |
| GET | `/timezones` | No | Timezones list |

### Bundles (guest)
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/bundles` | No | Bundle list |
| GET | `/bundles/{id}` | No | Bundle detail |
| GET | `/bundles/{id}/webinars` | No | Webinars in bundle |
| POST | `/bundles/{id}/free` | No | Get free bundle |

---

## 5. Panel (authenticated user) – prefix: `/panel`

All below are under **`/panel`**. Auth required unless noted.

### Summary
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/quick-info` | User summary / quick info |

### Comments
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/comments` | List comments |
| POST | `/panel/comments` | Create comment |
| DELETE | `/panel/comments/{id}` | Delete comment |
| PUT | `/panel/comments/{id}` | Update comment |
| POST | `/panel/comments/{id}/reply` | Reply to comment |
| POST | `/panel/comments/{id}/report` | Report comment |

### Subscribe
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/subscribe` | My subscriptions |
| POST | `/panel/subscribe/web_pay` | Generate web payment |
| POST | `/panel/subscribe/apply` | Apply subscription |
| POST | `/panel/subscribe/general_apply` | General apply |

### Webinars / Courses
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/panel/webinars/{id}/free` | Enroll free webinar |
| GET | `/panel/webinars/purchases` | My webinar purchases |
| GET | `/panel/webinars/organization` | Organization webinars |
| GET | `/panel/my-batches` | My batch enrollments |
| GET | `/panel/webinars/{webinarId}/intake` | Course intake form (show) |
| POST | `/panel/webinars/{webinarId}/intake` | Submit course intake |
| POST | `/panel/webinars/{webinarId}/intake/upload` | Upload intake file |
| GET | `/panel/classes` | My classes (teacher) |
| GET | `/panel/webinars/{webinar}/noticeboards` | Course noticeboards |
| GET | `/panel/webinars/{webinar}` | Webinar detail |
| GET | `/panel/webinars/{webinar}/chapters` | Webinar chapters |
| GET | `/panel/webinars/{webinar}/chapters/{chapter}` | Chapter detail |
| GET | `/panel/webinars/{id}/statistic` | Webinar stats (teacher) |
| GET | `/panel/webinars/certificates` | My webinar certificates |
| GET | `/panel/webinars/certificates/{id}` | Certificate detail |

### Batches (teacher)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/webinars/{id}/batches` | List batches |
| POST | `/panel/webinars/{id}/batches` | Create batch |
| PUT | `/panel/webinars/{id}/batches/{batch_id}` | Update batch |
| DELETE | `/panel/webinars/{id}/batches/{batch_id}` | Delete batch |

### Reviews
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/reviews` | List reviews |
| POST | `/panel/reviews` | Create review |
| POST | `/panel/reviews/{id}/reply` | Reply to review |
| DELETE | `/panel/reviews/{id}` | Delete review |
| GET | `/panel/reviews3` | List reviews (alt) |
| POST | `/panel/reviews3` | Store review (alt) |
| POST | `/panel/reviews3/{id}/reply` | Reply (alt) |
| DELETE | `/panel/reviews3/{id}` | Delete (alt) |

### Support
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/support/class_support` | Class support |
| GET | `/panel/support/my_class_support` | My class support |
| GET | `/panel/support/tickets` | Platform support tickets |
| GET | `/panel/support/departments` | Support departments |
| GET | `/panel/support/{id}` | Ticket detail |
| GET | `/panel/support/{id}/close` | Close ticket |
| POST | `/panel/support` | Create ticket |
| POST | `/panel/support/{id}/conversations` | Add conversation |

### Notifications
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/notifications` | List notifications |
| POST | `/panel/notifications/{id}/seen` | Mark as seen |

### Favorites
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/favorites` | List favorites |
| POST | `/panel/favorites/toggle/{id}` | Toggle favorite |
| POST | `/panel/favorites/toggle2` | Toggle favorite (body) |
| DELETE | `/panel/favorites/{id}` | Remove favorite |

### Meetings
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/meetings` | List meetings |
| GET | `/panel/meetings/{id}` | Meeting detail |
| GET | `/panel/meetings/reservations` | Reservations |
| GET | `/panel/meetings/requests` | Meeting requests |
| POST | `/panel/meetings/{id}/finish` | Finish meeting |

### Registration packages (teacher)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/registration-packages` | List packages |
| POST | `/panel/registration-packages/pay` | Web pay generator |

### Quizzes
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/quizzes/created` | Created quizzes (teacher) |
| GET | `/panel/quizzes/not_participated` | Not participated |
| GET | `/panel/quizzes/{quizId}/result` | Result by quiz |
| GET | `/panel/quizzes/results/my-results` | My results |
| GET | `/panel/quizzes/results/my-student-result` | My student result (teacher) |
| GET | `/panel/quizzes/results/{quizResultId}/status` | Result status |
| GET | `/panel/quizzes/results/{quizResultId}/show` | Certificate for result |
| POST | `/panel/quizzes/results/{quizResultId}/review` | Update result (teacher) |
| GET | `/panel/quizzes/{id}/start` | Start quiz |
| POST | `/panel/quizzes/{id}/store-result` | Store quiz result |

### Certificates
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/certificates/achievements` | Achievements |
| GET | `/panel/certificates/created` | Created (teacher) |
| GET | `/panel/certificates/students` | Students (teacher) |

### User / Profile
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/panel/become_instructor` | Become instructor request |
| POST | `/panel/users/{id}/follow` | Follow/unfollow user |
| GET | `/panel/profile-setting` | Profile settings |
| PUT | `/panel/profile-setting/password` | Update password |
| PUT | `/panel/profile-setting` | Update profile |
| POST | `/panel/profile-setting/images` | Update profile images |
| POST | `/panel/delete-account` | Request account deletion |

### Cart
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/cart/list` | Cart list |
| DELETE | `/panel/cart/{id}` | Remove from cart |
| POST | `/panel/cart/coupon/validate` | Validate coupon |
| POST | `/panel/cart/checkout` | Checkout |
| POST | `/panel/cart/store` | Store cart |
| POST | `/panel/cart` | Add to cart |
| POST | `/panel/cart/web_checkout` | Web checkout generator |

### Financial
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/financial/sales` | Sales list |
| POST | `/panel/financial/charge` | Charge |
| POST | `/panel/financial/web_charge` | Web charge generator |
| GET | `/panel/financial/summary` | Summary |
| GET | `/panel/financial/platform-bank-accounts` | Platform bank accounts |
| GET | `/panel/financial/accounts-type` | Account types |
| GET | `/panel/financial/payout` | Payout list |
| POST | `/panel/financial/payout` | Request payout |
| GET | `/panel/financial/offline-payments` | Offline payments list |
| PUT | `/panel/financial/offline-payments/{id}` | Update offline payment |
| DELETE | `/panel/financial/offline-payments/{id}` | Delete offline payment |
| POST | `/panel/financial/offline-payments` | Create offline payment |

### Payments
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/panel/payments/request` | Payment request |
| POST | `/panel/payments/credit` | Pay by credit |
| POST | `/panel/payments/razorpay-order` | Create Razorpay order |
| GET | `/panel/payments/verify/{gateway}` | Verify payment (GET) |
| POST | `/panel/payments/verify/{gateway}` | Verify payment (POST) |

### Assignments
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/my_assignments` | My assignments |
| GET | `/panel/my_assignments/{assignment}` | Assignment detail |
| GET | `/panel/assignments/{assignment}/messages` | Assignment messages |
| POST | `/panel/assignments/{assignment}/messages` | Send message |

### Blogs (panel, teacher)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/blogs` | List blogs |
| POST | `/panel/blogs` | Create blog |
| GET | `/panel/blogs/{id}` | Blog detail |
| PUT | `/panel/blogs/{id}` | Update blog |
| DELETE | `/panel/blogs/{id}` | Delete blog |
| GET | `/panel/blogs/{id}/comments` | Blog comments |
| POST | `/panel/blogs/{id}/comments` | Add comment |
| PUT | `/panel/blogs/{id}/comments/{comment}` | Update comment |
| DELETE | `/panel/blogs/{id}/comments/{comment}` | Delete comment |

### Content (files, sessions, lessons)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/files/{file}` | File detail |
| GET | `/panel/sessions/{session}` | Session detail |
| GET | `/panel/text-lessons/{lesson}` | Text lesson detail |
| GET | `/panel/text-lessons/{lesson}/navigation` | Text lesson navigation |
| GET | `/panel/assignments/{assignment}` | Assignment detail |
| GET | `/panel/quizzes/{quiz}` | Quiz detail |

### Bundles (panel)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/panel/bundles/{id}/buyWithPoint` | Buy with points |
| POST | `/panel/bundles/{id}/free` | Get free bundle |

### Health logs (Fit Karnataka)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/health-logs` | List health logs |
| POST | `/panel/health-logs` | Create health log |
| GET | `/panel/health-logs/summary` | Health log summary |
| GET | `/panel/health-logs/{id}` | Health log detail |
| PUT | `/panel/health-logs/{id}` | Update health log |
| DELETE | `/panel/health-logs/{id}` | Delete health log |
| GET | `/panel/course-health-log-settings/{webinar_id}` | Course health log settings |

### Students / Health care (teacher/dietician)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/panel/students` | My students (teacher) |
| GET | `/panel/students/{user_id}/health-profile` | Student health profile (teacher) |
| GET | `/panel/health-care` | Health care list (teacher) |
| GET | `/panel/health-care/sales/{saleId}/intake` | Sale intake (teacher) |
| POST | `/panel/health-care/sales/{saleId}/initial-conversation` | Mark initial conversation (teacher) |

---

## 6. Onboarding (authenticated) – prefix: `/onboarding`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/onboarding/profile` | Onboarding profile |
| GET | `/onboarding/health-profile` | Health profile (show) |
| POST | `/onboarding/health-profile` | Create health profile |
| PUT | `/onboarding/health-profile` | Update health profile |
| POST | `/onboarding/medical-data` | Submit medical data |
| POST | `/onboarding/diet-pattern` | Submit diet pattern |
| POST | `/onboarding/lifestyle` | Submit lifestyle |
| POST | `/onboarding/body-goals` | Submit body goals |
| POST | `/onboarding/upload-files` | Upload onboarding files |
| GET | `/onboarding/health-conditions` | List health conditions |
| POST | `/onboarding/health-conditions` | Store health conditions |
| GET | `/onboarding/body-goals` | List body goals |

---

## 7. Dietician – prefix: `/dietician` (teacher only)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/dietician/bundles` | List bundles |
| POST | `/dietician/bundles` | Create bundle |
| GET | `/dietician/bundles/{id}` | Bundle detail |
| PUT | `/dietician/bundles/{id}` | Update bundle |
| DELETE | `/dietician/bundles/{id}` | Delete bundle |
| GET | `/dietician/bundles/{bundle}/export` | Export bundle |
| GET | `/dietician/bundles/{id}/webinars` | Bundle webinars |
| POST | `/dietician/webinar` | Create webinar (storeAll) |
| GET | `/dietician/quizzes/list` | Quiz results list |
| POST | `/dietician/quizzes` | Create quiz |
| PUT | `/dietician/quizzes/{id}` | Update quiz |
| DELETE | `/dietician/quizzes/{id}` | Delete quiz |
| GET | `/dietician/meetings/requests` | Meeting requests |
| POST | `/dietician/meetings/create-link` | Create meeting link |
| POST | `/dietician/meetings/{id}/finish` | Finish meeting |
| GET | `/dietician/comments` | My class comments |
| POST | `/dietician/comments/{id}/reply` | Reply to comment |
| GET | `/dietician/assignments` | Assignments list |
| GET | `/dietician/assignments/students` | Assignment students |
| GET | `/dietician/assignments/{assignment}/students` | Submissions for assignment |
| POST | `/dietician/assignments/histories/{assignment_history}/rate` | Set grade |

---

## 8. Instructor (legacy) – prefix: `/instructor`

Same routes as **Dietician** (same logic). Use `/instructor/...` instead of `/dietician/...`.

---

## Quick reference: Base URL and headers

```
Base URL: {{base_url}}/api/development
Headers:
  Authorization: Bearer <jwt_token>
  Content-Type: application/json
  Accept: application/json
  x-api-key: <API_KEY from .env>
```

Use this document with Postman, Insomnia, or your mobile app integration tests. Replace `{{base_url}}` with your server (e.g. `https://lms.rocket-soft.org` or `http://127.0.0.1:8000`).
