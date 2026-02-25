# Frontend ↔ Backend ↔ Database connection checklist

Use this to ensure courses (and the rest of the app) load correctly, and that you can manage courses from the backend.

---

## Backend connected to frontend

- **How it works:** The React frontend (port 8080) sends all API requests to `/api/...`. Vite’s proxy forwards those to the Laravel backend at **http://localhost:8000**. The backend uses the same app for both the JSON API (used by the frontend) and the **admin panel** (where you manage courses).
- **Required:** Backend must be running on **http://localhost:8000**. Frontend `.env` has `VITE_API_URL=` (empty) so the proxy target is `http://localhost:8000` (see `frontend/vite.config.ts`). `VITE_API_KEY=1234` must match `API_KEY` in `backend/.env`.

**Verify connection:**
1. Start backend: `cd backend && php artisan serve`
2. Start frontend: `cd frontend && npm run dev`
3. Open **http://localhost:8080/programs** — you should see the courses list.
4. In browser DevTools → Network, you should see a request to `/api/development/courses` with status **200** and JSON containing a `data` array of courses.

---

## 1. Database
- **Status:** Connected (MySQL, database `kfm_lms`).
- **Data:** If you see no courses, import `Databases/KFM.sql` into MySQL database `kfm_lms`.
- **Config:** `backend/.env` — `DB_*` (host, port, database, username, password).

## 2. Backend (Laravel – API + Admin)
- **URL:** **http://localhost:8000**
- **Start:** `cd backend && php artisan serve`
- **API key:** `backend/.env` → `API_KEY=1234`. Frontend sends this as header `x-api-key` via `VITE_API_KEY`.

## 3. Frontend (Vite + React)
- **URL:** **http://localhost:8080**
- **Start:** `cd frontend && npm run dev`
- **Config:** `frontend/.env`:
  - `VITE_API_URL` — Leave **empty** in development (proxy to backend).
  - `VITE_API_KEY` — Must match `API_KEY` in `backend/.env` (e.g. `1234`).

---

## Manage courses from the backend

Course management is done in the **Laravel admin panel** (same backend as the API).

1. **Open the admin panel:**  
   **http://localhost:8000/admin**  
   (If the project uses a custom admin URL, use the one set in admin security settings.)

2. **Log in** with an admin user. If you imported `Databases/KFM.sql`, use:
   - **Admin panel:** http://localhost:8000/admin  
   - **Email:** `admin@demo.com`  
   - **Password:** The SQL dump only contains a hashed password (no plain text in the repo). To use a known password, set it once from the backend folder:
     ```bash
     cd backend && php artisan tinker
     ```
     Then in tinker:
     ```php
     $u = \App\Models\User::where('email', 'admin@demo.com')->first();
     $u->password = \Illuminate\Support\Facades\Hash::make('YourPassword123');
     $u->save();
     exit
     ```
     After that you can log in with **admin@demo.com** / **YourPassword123** (or whatever you chose).

3. **Go to Courses (Webinars):**  
   In the admin sidebar, open **Webinars** (or **Courses**). You’ll be at:  
   **http://localhost:8000/admin/webinars**

4. **What you can do there:**
   - List, create, edit, delete courses (webinars)
   - Approve / reject / unpublish
   - Manage students, content (chapters, files, sessions, quizzes), notices, featured courses, etc.

Changes you make in the admin panel are stored in the database. The **frontend** (http://localhost:8080/programs) reads the same data via the API, so updates appear there after you refresh or when the app refetches.

---

## Quick verification
- **Backend API:**  
  `curl -s -H "x-api-key: 1234" http://127.0.0.1:8000/api/development/courses`  
  → JSON with `"success":true` and a `data` array of courses.
- **Frontend:** Open http://localhost:8080/programs → courses list.
- **Admin:** Open http://localhost:8000/admin → login → Webinars to manage courses.

---

## Mail (optional – for email notifications)
- **Publishing courses:** If `MAIL_HOST` is empty in `backend/.env`, the app does **not** try to send email when you publish/approve a course. The course still publishes; only the email notification is skipped. No error.
- **If you later set up SMTP:** Set `MAIL_HOST`, `MAIL_PORT` (integer, e.g. `587`), `MAIL_USERNAME`, `MAIL_PASSWORD`, etc. `MAIL_PORT` is normalized to an integer in `config/mail.php` so empty or string values don’t cause a TypeError.

---

## Summary of changes made
- **Frontend:** `VITE_API_KEY=1234` in `.env` and `x-api-key` header in `src/lib/api.ts` so the backend accepts API requests.
- **Backend:** `app/Helpers/ApiHelper.php` — `nicePrice` and `nicePriceWithTax` handle null/empty prices so the courses API doesn’t error.
- **Backend (publish error fix):** `config/mail.php` — `MAIL_PORT` is cast to integer (default 587) so Symfony Mailer doesn’t get a string. `app/Helpers/helper.php` — `sendNotification` and `sendNotificationToEmail` only attempt to send email when `MAIL_HOST` is set, and catch `\Throwable` so invalid mail config doesn’t break course publish/approve.
