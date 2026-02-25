# React Frontend ↔ Laravel Backend Integration

This document describes how the **React frontend**, **Laravel API (student panel)**, and **Laravel admin/student web panels** work together and how to keep them in sync.

## Architecture

| Part | Stack | Purpose |
|------|--------|--------|
| **React app** | Vite + React + TypeScript | Public site: home, courses, programs, login, register; and **student panel** (dashboard, my courses, cart, profile, etc.) |
| **Laravel API** | PHP, JWT auth | REST API under `/api/*`: auth, guest (courses, categories), **panel** (student data), config, instructor |
| **Laravel web** | PHP, session auth | Admin panel (Blade), optional Laravel-rendered student panel at `/panel` (Blade) |

- **Student experience**: Users log in via the **React** login page; they are redirected to the **React student panel** at `/panel` (dashboard, programs, meetings, cart, health log, notifications). React stores the **JWT** and calls the Laravel API for all panel data (`/api/development/panel/*`).
- **Admin experience**: Admins use the **Laravel admin panel** (Blade). When an admin clicks “Login as user” for a student, they are redirected to the **React panel** at `FRONTEND_URL/panel` when `FRONTEND_URL` is set, so the student experience is consistent in React.

## 1. Environment variables

### Backend (`backend/.env`)

```env
APP_URL=http://localhost:8000
API_KEY=1234

# Optional: frontend app URL (for redirects / CORS in production)
# FRONTEND_URL=http://localhost:8080
# Production: FRONTEND_URL=https://your-react-domain.com
```

- `API_KEY`: Must match the key the frontend sends in every API request (`x-api-key`). Required for `/api/*`.
- `FRONTEND_URL`: **Recommended when using React as the main site.** When set, all main-site links and redirects from Laravel (navbar logo, login/register button, category links, and controller redirects to `/`, `/login`, `/register`, `/verify`, etc.) point to the React app. So when you use the Laravel admin or the Laravel-rendered student panel, clicking "Home" or being redirected after login sends you to the React app instead of Laravel’s removed UI. Local dev: `http://127.0.0.1:8080` or `http://localhost:8080` (React dev server). Production: your React app URL. Also used for CORS when set.

### Frontend (`frontend/.env`)

```env
# Leave empty in development to use Vite proxy (backend must be on http://localhost:8000)
VITE_API_URL=

# Must match backend .env API_KEY
VITE_API_KEY=1234
```

- **Development**: Leave `VITE_API_URL` empty. The Vite dev server proxies `/api` to `http://localhost:8000`, so the browser still sees same-origin requests.
- **Production**: Set `VITE_API_URL` to your backend root, e.g. `https://lms.example.com` (no trailing slash). The built React app will call this URL for all API requests.

## 2. Serve React from Laravel (single URL – no separate frontend server)

If you want **one URL** (e.g. `http://127.0.0.1:8000`) to serve both the API and the React app (so you don’t see Laravel’s HTML and don’t run the React dev server for the main site):

1. **Backend `.env`:**
   ```env
   SERVE_REACT_FROM_BACKEND=true
   ```
   Leave `FRONTEND_URL` empty when using this (same origin).

2. **Build the React app for Laravel** (from repo root or `frontend/`):
   ```bash
   cd frontend
   npm install
   npm run build:laravel
   ```
   This builds with asset base `/spa/` so the app works when served from Laravel.

3. **Copy the build into Laravel’s public folder:**
   - Copy **all contents** of `frontend/dist/` into `backend/public/spa/`.
   - So you get: `backend/public/spa/index.html`, `backend/public/spa/assets/...`, etc.
   - On Windows (PowerShell): `xcopy /E /I frontend\dist backend\public\spa`
   - On Mac/Linux: `cp -R frontend/dist/* backend/public/spa/`
   - Create `backend/public/spa` first if it doesn’t exist.

4. **Run only Laravel:**
   ```bash
   cd backend
   php artisan serve
   ```
   Open `http://127.0.0.1:8000` – you should see the React app (home, login, panel, etc.). Use `http://127.0.0.1:8000/admin` for the admin panel.

When `SERVE_REACT_FROM_BACKEND=true`, Laravel does **not** register its own `/panel` routes, so `/panel` is served by the React app. API stays at `/api/*`, admin at `/admin` (or your admin prefix).

## 3. Running locally (development – two servers)

1. **Start Laravel** (from `backend/`):
   ```bash
   php artisan serve
   ```
   Backend: `http://localhost:8000`

2. **Start React** (from `frontend/`):
   ```bash
   npm install
   npm run dev
   ```
   Frontend: `http://localhost:8080` (Vite proxy forwards `/api` to 8000).

3. **Use the React app** at `http://localhost:8080`: home, programs, login, register, and after login the student panel at `/panel`.

4. **Use the Laravel admin panel** at `http://localhost:8000/<admin_prefix>` (e.g. `/admin`) with session login. No JWT needed for admin.

## 4. Auth flow (React ↔ API)

- **Login**: `POST /api/development/login` with `{ "username": "email_or_mobile", "password": "..." }`.
- Backend returns `{ "success": true, "data": { "token": "<jwt>", "user_id": ... } }`.
- React stores the JWT in `localStorage` (key `rocket_lms_token`) and sends it as `Authorization: Bearer <token>` on every API request.
- **Logout**: `POST /api/development/logout` with the same Bearer token; then React clears the token.

All panel endpoints (e.g. `/api/development/panel/quick-info`, cart, webinars, etc.) require this JWT; the backend uses the `api.auth` middleware (JWT guard).

## 5. API path alignment

Frontend path constants live in `frontend/src/constants/api-paths.ts`. All development API routes are under `/api/development/`:

- Auth: `login`, `logout`, `register/step/{step}`, `forget-password`, `reset-password/{token}`, `verification`
- Guest: `courses`, `categories`, `config`, etc.
- Panel: `panel/quick-info`, `panel/webinars/purchases`, `panel/cart/*`, etc.

Backend routes are in `backend/routes/api.php` (prefix `development`) and `backend/routes/api/*.php`. Keep path segments in `api-paths.ts` in sync with these routes when adding or changing endpoints.

## 6. CORS

Backend `config/cors.php` uses `config('frontend.url')` when set:

- **Development**: Do not set `FRONTEND_URL` in backend `.env`; CORS allows all origins so the React dev server (and proxy) work without change.
- **Production**: Set `FRONTEND_URL=https://your-react-domain.com` in backend `.env`. CORS will then allow only that origin for `/api/*` requests.

## 7. Production deployment

- **Backend**: Deploy Laravel (e.g. `https://lms.example.com`). Set `APP_URL` and `API_KEY` in `.env`. Optionally set `FRONTEND_URL` to your React app URL.
- **Frontend**: Build with `VITE_API_URL=https://lms.example.com` and `VITE_API_KEY=<same as backend>`, then deploy the build (e.g. same domain under `/` or a subdomain).
- Ensure the same `API_KEY` is used on both sides and that the frontend build was made with the correct `VITE_API_URL` (it is baked in at build time).

## 8. Summary checklist

- [ ] Backend `.env`: `API_KEY` set; `APP_URL` correct for environment.
- [ ] Frontend `.env`: `VITE_API_KEY` matches backend; `VITE_API_URL` empty in dev, set to backend URL in prod.
- [ ] In dev: backend on 8000, frontend on 8080; proxy forwards `/api` to backend.
- [ ] React login stores JWT; all panel API calls send `Authorization: Bearer <token>` and `x-api-key`.
- [ ] Admin and optional Blade student panel stay on Laravel; student experience in production is the React app + Laravel API.
