# Backend API Endpoints

Base URL for API: `/api`  
Admin panel prefix: from `getAdminPanelUrlPrefix()` (typically `admin`).  
All API routes under `/api/development` require the `development` prefix unless noted.

---

## 1. Root & Health

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/up` | Health check (no DB), returns `{ status, service }` |
| GET | `/` | Root; redirects to frontend or returns API info JSON |
| GET | `/api/home` | Home controller index |
| GET | `/api/fit-karnataka-config` | Fit Karnataka config (enabled, disable, terminology) |

---

## 2. API Development (prefix: `/api/development`)

### 2.1 Auth (`/api/development` – no extra prefix)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/development/register/step/{step}` | No | Step register |
| POST | `/api/development/login` | No | Login |
| POST | `/api/development/forget-password` | No | Forgot password |
| POST | `/api/development/reset-password/{token}` | No | Reset password |
| POST | `/api/development/verification` | No | Confirm verification code |
| GET | `/api/development/google` | No | Google OAuth redirect |
| GET | `/api/development/facebook` | No | Facebook OAuth redirect |
| POST | `/api/development/google/callback` | No | Google OAuth callback |
| POST | `/api/development/facebook/callback` | No | Facebook OAuth callback |
| POST | `/api/development/logout` | Yes | Logout |

### 2.2 Config

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/development/config` | No | Config list |
| GET | `/api/development/config/register/{role}` | No | Register config by role |

### 2.3 Guest (no auth)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/development/courses` | List courses |
| GET | `/api/development/courses/{id}` | Course detail |
| GET | `/api/development/courses/{id}/content` | Course content |
| GET | `/api/development/courses/{id}/quizzes` | Course quizzes |
| GET | `/api/development/courses/{id}/certificates` | Course certificates |
| GET | `/api/development/courses/reports/reasons` | Report reasons |
| POST | `/api/development/courses/{id}/report` | Report course (auth) |
| POST | `/api/development/courses/{webinar_id}/toggle` | Learning status (auth) |
| GET | `/api/development/certificate_validation` | Validate certificate |
| GET | `/api/development/featured-courses` | Featured courses |
| GET | `/api/development/categories` | Categories |
| GET | `/api/development/categories/{id}/webinars` | Webinars by category |
| GET | `/api/development/trend-categories` | Trend categories |
| GET | `/api/development/search` | Search |
| GET | `/api/development/providers/instructors` | Instructors |
| GET | `/api/development/providers/organizations` | Organizations |
| GET | `/api/development/providers/consultations` | Consultations |
| POST | `/api/development/meetings/reserve` | Reserve meeting (auth) |
| GET | `/api/development/users/{id}/meetings` | User available times |
| GET | `/api/development/users/{id}/profile` | User profile |
| POST | `/api/development/users/{id}/send-message` | Send message |
| GET | `/api/development/files/{file_id}/download` | Download file |
| GET | `/api/development/blogs` | Blog list |
| GET | `/api/development/blogs/categories` | Blog categories |
| GET | `/api/development/blogs/{id}` | Blog post |
| GET | `/api/development/subscribe` | Subscribe list |
| GET | `/api/development/instructors` | Instructors |
| GET | `/api/development/organizations` | Organizations |
| POST | `/api/development/newsletter` | Newsletter |
| POST | `/api/development/contact` | Contact form |
| GET | `/api/development/regions/countries/` | Countries |
| GET | `/api/development/regions/provinces/{id?}` | Provinces |
| GET | `/api/development/regions/cities/{id?}` | Cities |
| GET | `/api/development/regions/districts/{id?}` | Districts |
| GET | `/api/development/timezones` | Timezones |
| GET | `/api/development/bundles` | Bundles list |
| GET | `/api/development/bundles/{id}/webinars` | Bundle webinars |
| POST | `/api/development/bundles/{id}/free` | Bundle free |
| GET | `/api/development/bundles/{id}` | Bundle detail |

### 2.4 Panel – User (`/api/development/panel`, auth required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/development/panel` | Panel test |
| GET | `/api/development/panel/quick-info` | Summary |
| GET | `/api/development/panel/comments` | Comments list |
| POST | `/api/development/panel/comments` | Create comment |
| DELETE | `/api/development/panel/comments/{id}` | Delete comment |
| PUT | `/api/development/panel/comments/{id}` | Update comment |
| POST | `/api/development/panel/comments/{id}/reply` | Reply |
| POST | `/api/development/panel/comments/{id}/report` | Report |
| POST | `/api/development/panel/webinars/{id}/free` | Webinar free |
| GET | `/api/development/panel/subscribe` | Subscribe index |
| POST | `/api/development/panel/subscribe/web_pay` | Subscribe web pay |
| POST | `/api/development/panel/subscribe/apply` | Subscribe apply |
| POST | `/api/development/panel/subscribe/general_apply` | Subscribe general apply |
| GET | `/api/development/panel/webinars/purchases` | Webinar purchases |
| GET | `/api/development/panel/webinars/organization` | Organization webinars |
| GET | `/api/development/panel/reviews` | Reviews list |
| POST | `/api/development/panel/reviews` | Store review |
| POST | `/api/development/panel/reviews/{id}/reply` | Reply to review |
| DELETE | `/api/development/panel/reviews/{id}` | Delete review |
| GET | `/api/development/panel/support/class_support` | Class support |
| GET | `/api/development/panel/support/my_class_support` | My class support |
| GET | `/api/development/panel/support/tickets` | Platform support tickets |
| GET | `/api/development/panel/support/departments` | Support departments |
| GET | `/api/development/panel/support/{id}` | Support detail |
| GET | `/api/development/panel/support/{id}/close` | Close support |
| POST | `/api/development/panel/support` | Create support |
| POST | `/api/development/panel/support/{id}/conversations` | Store conversation |
| GET | `/api/development/panel/notifications` | Notifications list |
| POST | `/api/development/panel/notifications/{id}/seen` | Mark seen |
| GET | `/api/development/panel/favorites` | Favorites list |
| POST | `/api/development/panel/favorites/toggle/{id}` | Toggle favorite |
| POST | `/api/development/panel/favorites/toggle2` | Toggle favorite 2 |
| DELETE | `/api/development/panel/favorites/{id}` | Delete favorite |
| GET | `/api/development/panel/classes` | Classes (teacher) |
| POST | `/api/development/panel/meetings/{id}/finish` | Finish meeting |
| GET | `/api/development/panel/meetings/reservations` | Reservations |
| GET | `/api/development/panel/meetings/requests` | Requests |
| GET | `/api/development/panel/meetings` | Meetings index |
| GET | `/api/development/panel/meetings/{id}` | Meeting detail |
| GET | `/api/development/panel/registration-packages` | Registration packages (teacher) |
| POST | `/api/development/panel/registration-packages/pay` | Pay registration package |
| GET | `/api/development/panel/quizzes/created` | Created quizzes (teacher) |
| GET | `/api/development/panel/quizzes/not_participated` | Not participated |
| GET | `/api/development/panel/quizzes/{quizId}/result` | Quiz result |
| GET | `/api/development/panel/quizzes/results/my-results` | My results |
| GET | `/api/development/panel/quizzes/results/my-student-result` | My student result (teacher) |
| GET | `/api/development/panel/quizzes/results/{quizResultId}/status` | Result status |
| GET | `/api/development/panel/quizzes/results/{quizResultId}/show` | Certificate |
| POST | `/api/development/panel/quizzes/results/{quizResultId}/review` | Update result (teacher) |
| GET | `/api/development/panel/quizzes/{id}/start` | Start quiz |
| POST | `/api/development/panel/quizzes/{id}/store-result` | Store quiz result |
| GET | `/api/development/panel/certificates/achievements` | Certificate achievements |
| GET | `/api/development/panel/certificates/created` | Created certificates (teacher) |
| GET | `/api/development/panel/certificates/students` | Students certificates (teacher) |
| POST | `/api/development/panel/become_instructor` | Become instructor |
| POST | `/api/development/panel/users/{id}/follow` | Follow user |
| GET | `/api/development/panel/cart/list` | Cart list |
| DELETE | `/api/development/panel/cart/{id}` | Remove from cart |
| POST | `/api/development/panel/cart/coupon/validate` | Validate coupon |
| POST | `/api/development/panel/cart/checkout` | Checkout |
| POST | `/api/development/panel/cart/store` | Store cart |
| POST | `/api/development/panel/cart` | Add to cart |
| POST | `/api/development/panel/cart/web_checkout` | Web checkout generator |
| GET | `/api/development/panel/financial/sales` | Sales |
| POST | `/api/development/panel/financial/charge` | Charge |
| POST | `/api/development/panel/financial/web_charge` | Web charge generator |
| GET | `/api/development/panel/financial/summary` | Summary |
| GET | `/api/development/panel/financial/platform-bank-accounts` | Platform bank accounts |
| GET | `/api/development/panel/financial/accounts-type` | Account types |
| GET | `/api/development/panel/financial/payout` | Payout index |
| POST | `/api/development/panel/financial/payout` | Request payout |
| GET | `/api/development/panel/financial/offline-payments` | Offline payments |
| PUT | `/api/development/panel/financial/offline-payments/{id}` | Update offline payment |
| DELETE | `/api/development/panel/financial/offline-payments/{id}` | Delete offline payment |
| POST | `/api/development/panel/financial/offline-payments` | Store offline payment |
| POST | `/api/development/panel/payments/request` | Payment request |
| POST | `/api/development/panel/payments/credit` | Pay by credit |
| GET | `/api/development/panel/payments/verify/{gateway}` | Payment verify |
| POST | `/api/development/panel/payments/verify/{gateway}` | Payment verify POST |
| GET | `/api/development/panel/profile-setting` | Profile setting |
| PUT | `/api/development/panel/profile-setting/password` | Update password |
| PUT | `/api/development/panel/profile-setting` | Update profile |
| POST | `/api/development/panel/profile-setting/images` | Update images |
| GET | `/api/development/panel/my_assignments` | My assignments |
| GET | `/api/development/panel/my_assignments/{assignment}` | Assignment show |
| GET | `/api/development/panel/assignments/{assignment}/messages` | Assignment messages |
| POST | `/api/development/panel/assignments/{assignment}/messages` | Store message |
| POST | `/api/development/panel/delete-account` | Delete account request |
| GET | `/api/development/panel/webinars/certificates` | Webinar certificates |
| GET | `/api/development/panel/webinars/certificates/{id}` | Webinar certificate show |
| GET | `/api/development/panel/webinars/{id}/statistic` | Webinar statistic (teacher) |
| POST | `/api/development/panel/bundles/{id}/buyWithPoint` | Bundle buy with point |
| POST | `/api/development/panel/bundles/{id}/free` | Bundle free |
| GET | `/api/development/panel/webinars/{webinar}/noticeboards` | Course noticeboards |
| GET | `/api/development/panel/webinars/{webinar}` | Webinar show |
| GET | `/api/development/panel/webinars/{webinar}/chapters/` | Webinar chapters |
| GET | `/api/development/panel/webinars/{webinar}/chapters/{chapter}` | Chapter show |
| GET | `/api/development/panel/files/{file}` | File show |
| GET | `/api/development/panel/sessions/{session}` | Session show |
| GET | `/api/development/panel/text-lessons/{lesson}` | Text lesson show |
| GET | `/api/development/panel/text-lessons/{lesson}/navigation` | Text lesson navigation |
| GET | `/api/development/panel/assignments/{assignment}` | Assignment show |
| GET | `/api/development/panel/quizzes/{quiz}` | Quiz show |
| GET | `/api/development/panel/health-logs` | Health logs index |
| POST | `/api/development/panel/health-logs` | Store health log |
| GET | `/api/development/panel/health-logs/summary` | Health logs summary |
| GET | `/api/development/panel/health-logs/{id}` | Health log show |
| PUT | `/api/development/panel/health-logs/{id}` | Update health log |
| DELETE | `/api/development/panel/health-logs/{id}` | Delete health log |
| GET | `/api/development/panel/course-health-log-settings/{webinar_id}` | Course health log settings |

Blogs (apiResource):  
- `GET/POST /api/development/panel/blogs`, `GET/PUT/DELETE /api/development/panel/blogs/{id}`  
- `GET/POST /api/development/panel/blogs/comments`, etc. (teacher)

### 2.5 Instructor (`/api/development/instructor`, auth + teacher)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/development/instructor/bundles/{bundle}/export` | Export bundle |
| GET | `/api/development/instructor/bundles` | Bundles index |
| POST | `/api/development/instructor/bundles` | Store bundle |
| GET | `/api/development/instructor/bundles/{id}` | Bundle show |
| PUT | `/api/development/instructor/bundles/{id}` | Update bundle |
| DELETE | `/api/development/instructor/bundles/{id}` | Delete bundle |
| GET | `/api/development/instructor/bundles/{bundle}/webinars` | Bundle webinars |
| POST | `/api/development/instructor/webinar` | Store webinar (all) |
| GET | `/api/development/instructor/quizzes/list` | Quizzes results |
| POST | `/api/development/instructor/quizzes` | Store quiz |
| PUT | `/api/development/instructor/quizzes/{id}` | Update quiz |
| DELETE | `/api/development/instructor/quizzes/{id}` | Delete quiz |
| GET | `/api/development/instructor/meetings/requests` | Meeting requests |
| POST | `/api/development/instructor/meetings/create-link` | Create link |
| POST | `/api/development/instructor/meetings/{id}/finish` | Finish meeting |
| GET | `/api/development/instructor/comments` | My class comments |
| POST | `/api/development/instructor/comments/{id}/reply` | Reply comment |
| GET | `/api/development/instructor/assignments/{assignment}/students` | Assignment submission |
| GET | `/api/development/instructor/assignments/students` | Assignment students |
| GET | `/api/development/instructor/assignments` | Assignments index |
| POST | `/api/development/instructor/assignments/histories/{assignment_history}/rate` | Set grade |

---

## 3. Web Routes (session/cookie, no `/api` prefix)

### 3.1 Public / Utility

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/my_api/checkout/{user}` | Signed checkout (signed) |
| GET | `/my_api/charge/{user}` | Signed charge (signed) |
| GET | `/my_api/subscribe/{user}/{subscribe}` | Signed subscribe pay (signed) |
| GET | `/my_api/registration_packages/{user}/{package}` | Signed registration package (signed) |
| GET | `/api_sessions/big_blue_button` | BigBlueButton |
| GET | `/api_sessions/agora` | Agora |
| GET | `/mobile-app` | Mobile app |
| GET | `/maintenance` | Maintenance |
| GET | `/emergencyDatabaseUpdate` | Emergency DB update |
| POST | `/locale` | Set locale |
| POST | `/set-currency` | Set currency |
| GET | `/getDefaultAvatar` | Default avatar |

### 3.2 Auth (web)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/login` | Login page / redirect |
| POST | `/login` | Login |
| GET | `/logout` | Logout |
| GET | `/register` | Register page |
| POST | `/register` | Register |
| POST | `/register/form-fields` | Form fields by user type |
| GET | `/verification` | Verification |
| POST | `/verification` | Confirm code |
| GET | `/verification/resend` | Resend code |
| GET | `/forget-password` | Forgot password form |
| POST | `/forget-password` | Forgot password |
| GET | `/reset-password/{token}` | Reset form |
| POST | `/reset-password` | Reset password |
| GET | `/google` | Google redirect |
| GET | `/google/callback` | Google callback |
| GET | `/facebook/redirect` | Facebook redirect |
| GET | `/facebook/callback` | Facebook callback |

### 3.3 Course & Learning (web)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/course/{slug}/file/{file_id}/download` | No | Download file |
| GET | `/course/{slug}/file/{file_id}/showHtml` | No | Show HTML file |
| GET | `/course/{slug}/lessons/{lesson_id}/read` | No | Get lesson |
| POST | `/course/getFilePath` | No | Get file path |
| GET | `/course/{slug}/file/{file_id}/play` | No | Play file |
| GET | `/course/{slug}/free` | No | Free course |
| GET | `/course/{slug}/points/apply` | No | Buy with points |
| POST | `/course/{id}/report` | No | Report webinar |
| POST | `/course/{id}/learningStatus` | No | Learning status |
| POST | `/course/learning/itemInfo` | Yes | Item info |
| GET | `/course/learning/{slug}` | Yes | Learning page |
| GET | `/course/learning/{slug}/noticeboards` | Yes | Noticeboards |
| GET | `/course/assignment/{assignmentId}/download/{id}/attach` | Yes | Download assignment |
| POST | `/course/assignment/{assignmentId}/history/{historyId}/message` | Yes | Store message |
| POST | `/course/assignment/{assignmentId}/history/{historyId}/setGrade` | Yes | Set grade |
| GET | `/course/assignment/.../message/{messageId}/downloadAttach` | Yes | Download attach |
| POST | `/course/direct-payment` | Yes | Direct payment |

### 3.4 Certificate, Cart, Reviews, Favorites, Comments (web, auth where noted)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/certificate_validation` | No | Certificate validation page |
| POST | `/certificate_validation/validate` | No | Validate certificate |
| POST | `/cart/store` | No | Add to cart |
| GET | `/cart/{id}/delete` | No | Remove from cart |
| GET | `/cart` | Yes | Cart page |
| POST | `/cart/coupon/validate` | Yes | Validate coupon |
| POST | `/cart/checkout` | Yes | Checkout |
| POST | `/cart/storeCheckout` | Yes | Store checkout (e.g. Razorpay) |
| POST | `/reviews/store` | Yes | Store review |
| POST | `/reviews/store-reply-comment` | Yes | Reply comment |
| GET | `/reviews/{id}/delete` | Yes | Delete review |
| GET | `/favorites/{slug}/toggle` | Yes | Toggle favorite |
| POST | `/favorites/{id}/update` | Yes | Update favorite |
| GET | `/favorites/{id}/delete` | Yes | Delete favorite |
| POST | `/comments/store` | Yes | Store comment |
| POST | `/comments/{id}/reply` | Yes | Reply |
| POST | `/comments/{id}/update` | Yes | Update |
| POST | `/comments/{id}/report` | Yes | Report |
| GET | `/comments/{id}/delete` | Yes | Delete |

### 3.5 Users, Become Instructor, Meetings, Payments (web)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/users/{id}/follow` | Yes | Follow toggle |
| GET | `/become-instructor` | Yes | Become instructor |
| GET | `/become-instructor/packages` | Yes | Packages |
| GET | `/become-instructor/packages/{id}/checkHasInstallment` | Yes | Check installment |
| GET | `/become-instructor/packages/{id}/installments` | Yes | Installments |
| POST | `/become-instructor` | Yes | Store |
| POST | `/become-instructor/form-fields` | Yes | Form fields |
| POST | `/meetings/reserve` | No | Reserve meeting |
| GET | `/users/{id}/profile` | No | User profile |
| POST | `/users/{id}/availableTimes` | No | Available times |
| POST | `/users/{id}/send-message` | No | Send message |
| POST | `/payments/payment-request` | No | Payment request |
| GET | `/payments/verify/{gateway}` | No | Payment verify |
| POST | `/payments/verify/{gateway}` | No | Payment verify POST |
| GET | `/payments/status` | No | Pay status |
| GET | `/payments/payku/callback/{id}` | No | Payku callback |
| GET | `/subscribes/apply/{webinarSlug}` | No | Subscribe apply |
| GET | `/subscribes/apply/bundle/{bundleSlug}` | No | Bundle apply |

### 3.6 Categories, Blog, Contact, Regions, etc. (web)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/categories/{categoryTitle}/{subCategoryTitle?}` | Categories |
| GET | `/reward-courses` | Reward courses |
| GET | `/blog` | Blog |
| GET | `/blog/categories/{category}` | Blog category |
| GET | `/blog/{slug}` | Blog post |
| GET | `/contact` | Contact page |
| POST | `/contact/store` | Contact store |
| GET | `/organizations` | Organizations |
| GET | `/load_more/{role}` | Load more instructors/orgs |
| GET | `/pages/{link}` | Page |
| POST | `/captcha/create` | Captcha create |
| GET | `/captcha/{config?}` | Captcha image |
| POST | `/newsletters` | Newsletter |
| GET | `/jobs/{methodName}` | Jobs |
| POST | `/jobs/{methodName}` | Jobs |
| GET | `/regions/provincesByCountry/{countryId}` | Provinces |
| GET | `/regions/citiesByProvince/{provinceId}` | Cities |
| GET | `/regions/districtsByCity/{cityId}` | Districts |
| GET | `/instructor-finder` | Instructor finder |
| GET | `/instructor-finder/wizard` | Wizard |
| GET | `/reward-products` | Reward products |
| GET | `/bundles/{slug}/free` | Bundle free |
| GET | `/bundles/{slug}/favorite` | Bundle favorite (auth) |
| GET | `/bundles/{slug}/points/apply` | Bundle points (auth) |
| POST | `/bundles/reviews/store` | Bundle review (auth) |
| POST | `/bundles/direct-payment` | Bundle direct payment (auth) |
| POST | `/cookie-security/all` | Cookie all |
| POST | `/cookie-security/customize` | Cookie customize |
| GET | `/upcoming_courses/{slug}/toggleFollow` | Toggle follow |
| GET | `/upcoming_courses/{slug}/favorite` | Favorite |
| POST | `/upcoming_courses/{id}/report` | Report |
| GET | `/forms/{url}` | Form |
| POST | `/forms/{url}/store` | Form store |

---

## 4. Admin Panel Routes (prefix: `{admin}` e.g. `/admin`)

All under `{admin}` and `admin` middleware unless stated. Only key groups are listed; many follow same pattern (index, create, store, edit, update, delete).

### 4.1 Admin Auth & Captcha

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/{admin}/login` | Login form |
| POST | `/{admin}/login` | Login |
| GET | `/{admin}/logout` | Logout |
| GET | `/{admin}/forget-password` | Forgot password |
| POST | `/{admin}/forget-password` | Forgot |
| GET | `/{admin}/reset-password/{token}` | Reset form |
| POST | `/{admin}/reset-password` | Reset |
| POST | `/{admin}/captcha/create` | Captcha |
| GET | `/{admin}/captcha/{config?}` | Captcha image |

### 4.2 Dashboard & Health

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/{admin}/` | Dashboard |
| GET | `/{admin}/clear-cache` | Clear cache |
| POST | `/{admin}/dashboard/getSaleStatisticsData` | Sale statistics |
| GET | `/{admin}/health-log` | Health log index |
| GET | `/{admin}/health-log/export/csv` | Export CSV |
| GET | `/{admin}/health-log/export/json` | Export JSON |
| GET | `/{admin}/health-log/{id}` | Health log show |
| GET | `/{admin}/system-health` | System health |
| POST | `/{admin}/system-health/run-check` | Run check |
| GET | `/{admin}/system-health/export/csv` | Export CSV |
| GET | `/{admin}/system-health/export/json` | Export JSON |
| GET | `/{admin}/system-health/api/list` | System health API list |
| GET | `/{admin}/system-health/api/{id}` | System health API show |
| GET | `/{admin}/system-health/{id}` | System health show |
| GET | `/{admin}/course-health-log-settings` | Course health log settings index |
| GET | `/{admin}/course-health-log-settings/{webinar_id}/edit` | Edit |
| POST | `/{admin}/course-health-log-settings/{webinar_id}` | Update |
| GET | `/{admin}/student-health-logs` | Redirect to health-log |
| GET | `/{admin}/student-health-logs/export/csv` | Export CSV |
| GET | `/{admin}/student-health-logs/export/json` | Export JSON |
| GET | `/{admin}/student-health-logs/{id}` | Show |

### 4.3 Users, Roles, Staff, Students, Instructors, Organizations

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/{admin}/roles` | Roles index |
| GET | `/{admin}/roles/create` | Create |
| POST | `/{admin}/roles/store` | Store |
| GET | `/{admin}/roles/{id}/edit` | Edit |
| POST | `/{admin}/roles/{id}/update` | Update |
| GET | `/{admin}/roles/{id}/delete` | Delete |
| GET | `/{admin}/staffs` | Staffs |
| GET | `/{admin}/students` | Students |
| GET | `/{admin}/students/excel` | Export students |
| GET | `/{admin}/instructors` | Instructors |
| GET | `/{admin}/instructors/excel` | Export instructors |
| GET | `/{admin}/organizations` | Organizations |
| GET | `/{admin}/organizations/excel` | Export organizations |
| GET | `/{admin}/users/create` | Create user |
| POST | `/{admin}/users/store` | Store user |
| POST | `/{admin}/users/search` | Search |
| GET | `/{admin}/users/{id}/edit` | Edit user |
| POST | `/{admin}/users/{id}/update` | Update user |
| POST | `/{admin}/users/{id}/updateImage` | Update image |
| POST | `/{admin}/users/{id}/updateFormFields` | Update form fields |
| POST | `/{admin}/users/{id}/financialUpdate` | Financial update |
| POST | `/{admin}/users/{id}/occupationsUpdate` | Occupations |
| POST | `/{admin}/users/{id}/badgesUpdate` | Badges |
| POST | `/{admin}/users/{id}/userRegistrationPackage` | Registration package |
| POST | `/{admin}/users/{id}/meetingSettings` | Meeting settings |
| GET | `/{admin}/users/{id}/deleteBadge/{badge_id}` | Delete badge |
| GET | `/{admin}/users/{id}/delete` | Delete user |
| GET | `/{admin}/users/{id}/acceptRequestToInstructor` | Accept instructor request |
| GET | `/{admin}/users/{user_id}/impersonate` | Impersonate |
| GET | `/{admin}/users/badges` | Badges index |
| POST | `/{admin}/users/badges/store` | Store badge |
| GET | `/{admin}/users/badges/{id}/edit` | Edit badge |
| POST | `/{admin}/users/badges/{id}/update` | Update badge |
| GET | `/{admin}/users/badges/{id}/delete` | Delete badge |
| GET | `/{admin}/users/groups` | Groups index |
| GET | `/{admin}/users/groups/create` | Create group |
| POST | `/{admin}/users/groups/store` | Store group |
| GET | `/{admin}/users/groups/{id}/edit` | Edit group |
| POST | `/{admin}/users/groups/{id}/update` | Update group |
| GET | `/{admin}/users/groups/{id}/delete` | Delete group |
| POST | `/{admin}/users/groups/{id}/groupRegistrationPackage` | Group registration package |
| GET | `/{admin}/users/become-instructors/{page}` | Become instructors |
| GET | `/{admin}/users/become-instructors/{id}/reject` | Reject |
| GET | `/{admin}/users/become-instructors/{id}/delete` | Delete |
| GET | `/{admin}/users/not-access-to-content` | Not access index |
| POST | `/{admin}/users/not-access-to-content/store` | Store |
| GET | `/{admin}/users/not-access-to-content/{id}/active` | Active |
| GET | `/{admin}/users/not-access-to-content/{id}/reject` | Reject |
| GET | `/{admin}/users/delete-account-requests` | Delete account requests |
| GET | `/{admin}/users/delete-account-requests/{id}/confirm` | Confirm |

### 4.4 Supports, Noticeboards, Notifications, Categories, Filters, Tags

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/{admin}/supports` | Supports index |
| GET | `/{admin}/supports/create` | Create |
| POST | `/{admin}/supports/store` | Store |
| GET | `/{admin}/supports/{id}/edit` | Edit |
| POST | `/{admin}/supports/{id}/update` | Update |
| GET | `/{admin}/supports/{id}/delete` | Delete |
| GET | `/{admin}/supports/{id}/close` | Close |
| GET | `/{admin}/supports/{id}/conversation` | Conversation |
| POST | `/{admin}/supports/{id}/conversation` | Store conversation |
| GET | `/{admin}/supports/departments` | Departments (CRUD) |
| GET | `/{admin}/noticeboards` | Noticeboards (CRUD) |
| GET | `/{admin}/course-noticeboards` | Course noticeboards (CRUD) |
| GET | `/{admin}/notifications` | Notifications (CRUD, templates, mark read) |
| GET | `/{admin}/categories` | Categories (CRUD, trends) |
| GET | `/{admin}/filters` | Filters (CRUD) |
| GET | `/{admin}/tags` | Tags (CRUD) |

### 4.5 Comments, Reports, Webinars, Quizzes, Content (Chapters, Sessions, Files, etc.)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/{admin}/comments/{page}` | Comments index |
| GET | `/{admin}/comments/{page}/{comment_id}/toggle` | Toggle |
| GET | `/{admin}/comments/{page}/{comment_id}/edit` | Edit |
| POST | `/{admin}/comments/{page}/{comment_id}/update` | Update |
| GET | `/{admin}/comments/{page}/{comment_id}/reply` | Reply |
| POST | `/{admin}/comments/{page}/{comment_id}/reply` | Store reply |
| GET | `/{admin}/comments/{page}/{comment_id}/delete` | Delete |
| GET | `/{admin}/comments/{page}/reports` | Reports |
| GET | `/{admin}/reports/reasons` | Report reasons |
| POST | `/{admin}/reports/reasons` | Store reasons |
| GET | `/{admin}/reports/webinars` | Webinar reports |
| GET | `/{admin}/reports/webinars/{id}/delete` | Delete report |
| GET | `/{admin}/webinars` | Webinars (CRUD, approve, reject, unpublish, search, excel, students, notification, order, statistics) |
| GET | `/{admin}/webinars/features` | Feature webinars (CRUD, toggle, excel) |
| GET | `/{admin}/webinars/course_forums` | Course forums |
| GET | `/{admin}/quizzes` | Quizzes (CRUD, results, excel, order) |
| GET | `/{admin}/quizzes-questions` | Quiz questions (store, edit, update, delete, getQuestionByLocale) |
| GET | `/{admin}/filters/get-by-category-id/{categoryId}` | Filter by category |
| POST | `/{admin}/tickets/store` | Store ticket |
| POST | `/{admin}/tickets/{id}/edit` | Edit ticket |
| POST | `/{admin}/tickets/{id}/update` | Update ticket |
| GET | `/{admin}/tickets/{id}/delete` | Delete ticket |
| GET | `/{admin}/chapters/{id}` | Get chapter |
| GET | `/{admin}/chapters/getAllByWebinarId/{webinar_id}` | Chapters by webinar |
| POST | `/{admin}/chapters/store` | Store chapter |
| POST | `/{admin}/chapters/{id}/edit` | Edit chapter |
| POST | `/{admin}/chapters/{id}/update` | Update chapter |
| GET | `/{admin}/chapters/{id}/delete` | Delete chapter |
| POST | `/{admin}/chapters/change` | Change |
| POST | `/{admin}/sessions/store` | Store session |
| POST | `/{admin}/sessions/{id}/edit` | Edit session |
| POST | `/{admin}/sessions/{id}/update` | Update session |
| GET | `/{admin}/sessions/{id}/delete` | Delete session |
| POST | `/{admin}/files/store` | Store file |
| POST | `/{admin}/files/{id}/edit` | Edit file |
| POST | `/{admin}/files/{id}/update` | Update file |
| GET | `/{admin}/files/{id}/delete` | Delete file |
| POST | `/{admin}/text-lesson/store` | Store text lesson |
| POST | `/{admin}/text-lesson/{id}/edit` | Edit text lesson |
| POST | `/{admin}/text-lesson/{id}/update` | Update text lesson |
| GET | `/{admin}/text-lesson/{id}/delete` | Delete text lesson |
| GET | `/{admin}/assignments` | Assignments (CRUD, students, conversations) |
| POST | `/{admin}/prerequisites/store` | Store prerequisite |
| POST | `/{admin}/prerequisites/{id}/edit` | Edit prerequisite |
| POST | `/{admin}/prerequisites/{id}/update` | Update prerequisite |
| GET | `/{admin}/prerequisites/{id}/delete` | Delete prerequisite |
| POST | `/{admin}/faqs/store` | Store FAQ |
| POST | `/{admin}/faqs/{id}/description` | FAQ description |
| POST | `/{admin}/faqs/{id}/edit` | Edit FAQ |
| POST | `/{admin}/faqs/{id}/update` | Update FAQ |
| GET | `/{admin}/faqs/{id}/delete` | Delete FAQ |
| POST | `/{admin}/webinar-extra-description/store` | Store extra description |
| POST | `/{admin}/webinar-extra-description/{id}/edit` | Edit |
| POST | `/{admin}/webinar-extra-description/{id}/update` | Update |
| GET | `/{admin}/webinar-extra-description/{id}/delete` | Delete |
| POST | `/{admin}/webinar-quiz/store` | Store webinar quiz |
| POST | `/{admin}/webinar-quiz/{id}/edit` | Edit |
| POST | `/{admin}/webinar-quiz/{id}/update` | Update |
| GET | `/{admin}/webinar-quiz/{id}/delete` | Delete |
| GET | `/{admin}/certificates` | Certificates (index, excel, templates CRUD, preview, download, course-competition) |
| GET | `/{admin}/reviews` | Reviews (index, toggleStatus, reply, delete) |
| GET | `/{admin}/consultants` | Consultants (index, excel) |
| GET | `/{admin}/appointments` | Appointments (index, join, getReminderDetails, sendReminder, cancel) |

### 4.6 Blog, Financial, Newsletters, Settings, Others

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/{admin}/blog` | Blog (CRUD, search) |
| GET | `/{admin}/blog/categories` | Blog categories (CRUD) |
| GET | `/{admin}/financial/sales` | Sales (index, refund, invoice, export) |
| GET | `/{admin}/financial/payouts` | Payouts (index, reject, payout, excel) |
| GET | `/{admin}/financial/offline_payments` | Offline payments (index, excel, reject, approved) |
| GET | `/{admin}/financial/discounts` | Discounts (CRUD) |
| GET | `/{admin}/financial/special_offers` | Special offers (CRUD) |
| GET | `/{admin}/financial/documents` | Documents (index, new, store, print) |
| GET | `/{admin}/financial/subscribes` | Subscribes (CRUD) |
| GET | `/{admin}/financial/promotions` | Promotions (CRUD, sales) |
| GET | `/{admin}/financial/registration-packages` | Registration packages (CRUD, settings, reports) |
| GET | `/{admin}/newsletters` | Newsletters (index, send, history, delete, excel) |
| GET | `/{admin}/additional_page/navbar_links` | Navbar links (CRUD) |
| GET | `/{admin}/additional_page/{name}` | Additional page |
| POST | `/{admin}/additional_page/{name}` | Store |
| POST | `/{admin}/additional_page/footer/store` | Footer store |
| GET | `/{admin}/settings` | Settings index |
| GET | `/{admin}/settings/personalization/navbar_button` | Navbar button (CRUD) |
| GET | `/{admin}/settings/personalization/home_sections` | Home sections (CRUD, sort) |
| GET | `/{admin}/settings/personalization/statistics` | Statistics (CRUD, sort) |
| GET | `/{admin}/settings/personalization/{name}` | Personalization page |
| GET | `/{admin}/settings/update-app` | Update app (basic, custom-update, database) |
| GET | `/{admin}/settings/reset-users-login-count` | Reset login count |
| GET | `/{admin}/settings/{page}` | Settings page |
| POST | `/{admin}/settings/{name}` | Store setting |
| POST | `/{admin}/settings/seo_metas/store` | SEO metas |
| POST | `/{admin}/settings/notifications/store` | Notifications metas |
| POST | `/{admin}/settings/financial/currency` | Currency (store, edit, delete, order) |
| GET | `/{admin}/settings/financial/offline_banks/get-form` | Offline bank form |
| POST | `/{admin}/settings/financial/offline_banks/store` | Store |
| GET | `/{admin}/settings/financial/offline_banks/{id}/edit` | Edit |
| POST | `/{admin}/settings/financial/offline_banks/{id}/update` | Update |
| GET | `/{admin}/settings/financial/offline_banks/{id}/delete` | Delete |
| GET | `/{admin}/settings/financial/user_banks/get-form` | User bank form |
| POST | `/{admin}/settings/financial/user_banks/store` | Store |
| GET | `/{admin}/settings/financial/user_banks/{id}/edit` | Edit |
| POST | `/{admin}/settings/financial/user_banks/{id}/update` | Update |
| GET | `/{admin}/settings/financial/user_banks/{id}/delete` | Delete |
| POST | `/{admin}/settings/socials/store` | Socials store |
| GET | `/{admin}/settings/socials/{key}/edit` | Edit social |
| GET | `/{admin}/settings/socials/{key}/delete` | Delete social |
| GET | `/{admin}/settings/payment_channels` | Payment channels (index, toggleStatus, edit, update) |
| POST | `/{admin}/settings/custom_css_js/store` | Custom CSS/JS |
| GET | `/{admin}/testimonials` | Testimonials (CRUD) |
| GET | `/{admin}/contacts` | Contacts (index, reply, storeReply, delete) |
| GET | `/{admin}/pages` | Pages (CRUD, toggle) |
| GET | `/{admin}/agora_history` | Agora history (index, excel) |
| GET | `/{admin}/regions` | Regions (new, store, edit, update, delete, provincesByCountry, citiesByProvince, index by pageType) |
| GET | `/{admin}/bundles` | Bundles (CRUD, search, excel, students, notification) |
| GET | `/{admin}/bundle-webinars` | Bundle webinars (store, edit, update, delete) |
| GET | `/{admin}/recommended-topics` | Recommended topics (CRUD) |
| GET | `/{admin}/enrollments/history` | Enrollments history |
| GET | `/{admin}/enrollments/add-student-to-class` | Add student to class |
| POST | `/{admin}/enrollments/store` | Store enrollment |
| GET | `/{admin}/enrollments/{sale_id}/block-access` | Block access |
| GET | `/{admin}/enrollments/{sale_id}/enable-access` | Enable access |
| GET | `/{admin}/enrollments/export` | Export excel |
| GET | `/{admin}/upcoming_courses` | Upcoming courses (CRUD, approve, reject, unpublish, followers, order, excel) |
| GET | `/{admin}/forms` | Forms (CRUD, fields, options, submissions) |
| POST | `/{admin}/forms/{form_id}/submissions/{id}/update` | Update submission |

---

## Notes

- **Auth**: “Auth” means the route uses `api.auth`, `web.auth`, or `admin` middleware.
- **Panel routes**: Laravel Blade panel routes (`routes/panel.php`) are **not** loaded in this setup; the React frontend uses the API under `/api/development/panel` instead.
- **Admin prefix**: Replace `{admin}` with your actual admin prefix (e.g. `admin` from config).
- **Signed routes**: `my_api` routes require a valid signed URL.
