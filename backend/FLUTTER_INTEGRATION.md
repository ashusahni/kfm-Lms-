# Flutter / Mobile API Integration

This document describes how to integrate the Rocket LMS backend with a Flutter (or any mobile) application.

## Base URLs

| Purpose | URL |
|--------|-----|
| **Mobile config (use this first)** | `GET {{APP_URL}}/api/mobile/v1/config` |
| **Connectivity check** | `GET {{APP_URL}}/api/mobile/v1/ping` |
| **All other API calls** | `{{APP_URL}}/api/development` |

Replace `{{APP_URL}}` with your backend URL (e.g. `https://lms.rocket-soft.org` or `http://127.0.0.1:8000`).

## Required Headers

Every request must include:

| Header         | Value                                           | Required                   |
|----------------|------------------------------------------------|----------------------------|
| `x-api-key`    | Your API key (see backend `.env` `API_KEY`)    | Yes                        |
| `Accept`       | `application/json`                             | Yes                        |
| `Content-Type` | `application/json`                             | Yes for POST/PUT/PATCH     |
| `Authorization`| `Bearer <jwt_token>`                           | Yes for protected endpoints|

## Bootstrap Flow (Flutter)

1. **On app start**, call:
   ```http
   GET /api/mobile/v1/config
   Headers: x-api-key: YOUR_API_KEY, Accept: application/json
   ```
   Response includes `data.api_base_url`, `data.auth`, and `data.endpoints`. Use `data.api_base_url` as the base for all subsequent requests.

2. **Optional**: Call `GET /api/mobile/v1/ping` to verify connectivity before login.

3. **Login** (see Auth below), then use the returned `token` in `Authorization: Bearer <token>` for all panel and protected routes.

## Response Format

All API responses use this structure:

```json
{
  "success": true,
  "status": "ok",
  "message": "Human-readable message",
  "data": { ... }
}
```

- `success`: `true` or `false`
- `status`: string code (e.g. `ok`, `login`, `validation_error`, `unauthorized`)
- `message`: translated message
- `data`: optional payload (omit on error when no data)

## Auth Endpoints (no token required)

| Method | Path | Description |
|--------|------|-------------|
| POST | `/api/development/login` | Login. Body: `username` (or `email`) + `password`, or `mobile` + `country_code` + `password` |
| POST | `/api/development/register/step/{step}` | Register (step 1, 2, …) |
| POST | `/api/development/forget-password` | Send reset email |
| POST | `/api/development/reset-password/{token}` | Reset password with token |
| POST | `/api/development/verification` | Verify email/code |
| POST | `/api/development/logout` | Logout (requires `Authorization: Bearer <token>`) |

### Login request example

```json
{
  "username": "user@example.com",
  "password": "yourpassword"
}
```

Or with mobile:

```json
{
  "mobile": "9876543210",
  "country_code": "+91",
  "password": "yourpassword"
}
```

### Login response (success)

```json
{
  "success": true,
  "status": "login",
  "message": "Login message",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user_id": 1,
    "role_name": "user",
    "full_name": "John Doe"
  }
}
```

Store `data.token` and send it as `Authorization: Bearer <token>` on every protected request.

## Guest Endpoints (no token)

| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/development/config` | App config (register method, currency, features) |
| GET | `/api/development/courses` | List courses |
| GET | `/api/development/courses/{id}` | Course detail |
| GET | `/api/development/courses/{id}/content` | Course content |
| GET | `/api/development/categories` | Categories |
| GET | `/api/development/featured-courses` | Featured courses |
| GET | `/api/development/search` | Search (query params) |
| GET | `/api/development/bundles` | Bundles |
| GET | `/api/development/providers/instructors` | Instructors |

## Panel Endpoints (require `Authorization: Bearer <token>`)

Base path: `/api/development/panel`

| Method | Path | Description |
|--------|------|-------------|
| GET | `/panel/quick-info` | Dashboard summary |
| GET | `/panel/profile-setting` | Current user profile |
| PUT | `/panel/profile-setting` | Update profile |
| GET | `/panel/webinars/purchases` | My purchases |
| GET | `/panel/cart/list` | Cart |
| POST | `/panel/cart/store` | Add to cart |
| POST | `/panel/cart/checkout` | Checkout |
| GET | `/panel/notifications` | Notifications |
| POST | `/panel/notifications/{id}/seen` | Mark notification seen |
| GET | `/panel/favorites` | Favorites |
| POST | `/panel/favorites/toggle/{id}` | Toggle favorite |
| GET | `/panel/meetings` | My meetings |
| GET | `/panel/reviews` | My reviews |
| GET | `/panel/support/tickets` | Support tickets |
| GET | `/panel/webinars/{id}/chapters/` | Webinar chapters |
| GET | `/panel/health-logs` | Health logs (Fit Karnataka) |

(Additional panel routes are in `routes/api/user.php`.)

## Payments

| Method | Path | Description |
|--------|------|-------------|
| POST | `/api/development/panel/payments/razorpay-order` | Create Razorpay order |
| GET/POST | `/api/development/panel/payments/verify/{gateway}` | Verify payment (e.g. `razorpay`) |

## Fit Karnataka

If `fit_karnataka` is enabled, the config response includes `data.fit_karnataka` (terminology, disabled features). You can also call:

- `GET /api/fit-karnataka-config` — same flags and terminology (no auth).

## Flutter Example (Dart)

```dart
// 1. Get config on app start
final configResponse = await http.get(
  Uri.parse('https://lms.rocket-soft.org/api/mobile/v1/config'),
  headers: {
    'x-api-key': '1234',
    'Accept': 'application/json',
  },
);
final json = jsonDecode(configResponse.body);
final baseUrl = json['data']['api_base_url'];

// 2. Login
final loginResponse = await http.post(
  Uri.parse('$baseUrl/login'),
  headers: {
    'x-api-key': '1234',
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: jsonEncode({
    'username': 'user@example.com',
    'password': 'password',
  }),
);
final loginData = jsonDecode(loginResponse.body);
final token = loginData['data']['token'];

// 3. Authenticated request
final profileResponse = await http.get(
  Uri.parse('$baseUrl/panel/profile-setting'),
  headers: {
    'x-api-key': '1234',
    'Accept': 'application/json',
    'Authorization': 'Bearer $token',
  },
);
```

## CORS

The backend uses Laravel CORS. For mobile apps (Flutter, React Native) same-origin does not apply; ensure your backend allows requests from your app (e.g. for web or debug builds).

## Errors

- **401 / success: false, status: "unauthorized"** — Missing or invalid token. Redirect to login.
- **success: false, status: "client_identity_error"** — Invalid or missing `x-api-key`.
- **success: false, status: "validation_error"** — Check `message` and request body.
