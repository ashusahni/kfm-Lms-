# Backend–Frontend Connection

This document describes how the Rocket LMS frontend talks to the backend so you can verify and debug the connection.

## Overview

- **Backend**: Laravel API under `/api` (and under `/api/development` for app routes).
- **Frontend**: React app that calls the backend using paths from `src/constants/api-paths.ts` and `src/lib/api.ts`.

## Base URL and proxy

- **Development (Vite dev server)**  
  - If `VITE_API_URL` is empty, the frontend uses **relative** paths (e.g. `/api/development/...`).  
  - Vite’s proxy (see `vite.config.ts`) forwards `/api` to `http://localhost:8000` (or to `VITE_API_URL` if set).  
  - So: run backend on port 8000, frontend on 8080; requests to `http://localhost:8080/api/...` are proxied to the backend.

- **Production**  
  - Set `VITE_API_URL` to your backend URL (e.g. `https://api.example.com`) before building.  
  - The built app will call that base URL + path (e.g. `https://api.example.com/api/development/panel/...`).

## Auth

- **Login**: `POST /api/development/login` with `{ username, password }` (or `{ mobile, country_code, password }`).  
  Response includes `data.token` and `data.role_name`. The frontend stores the token and sends it as `Authorization: Bearer <token>` on all panel/dietician requests.

- **Panel and dietician routes** use middleware `api.auth` (and dietician uses `api.level-access:teacher`).  
  So the frontend **must** send the JWT for those routes; `src/lib/api.ts` adds the header from `localStorage` when present.

## Key path mapping

| Frontend path constant              | Backend route                          | Method | Purpose |
|-------------------------------------|----------------------------------------|--------|--------|
| `paths.auth.login`                  | `/api/development/login`               | POST   | Login  |
| `paths.panel.quickInfo`             | `/api/development/panel/quick-info`    | GET    | Dashboard summary (badges, balance, etc.) |
| `paths.panel.classes`               | `/api/development/panel/classes`       | GET    | Dietician’s programs (teacher only) |
| `paths.panel.students`              | `/api/development/panel/students`      | GET    | Dietician’s student list (teacher only) |
| `paths.panel.studentHealthProfile(userId)` | `/api/development/panel/students/{id}/health-profile` | GET | Student health profile (teacher only) |
| `paths.panel.meetings`              | `/api/development/panel/meetings`      | GET    | Reservations + requests |
| `paths.panel.meetingFinish(id)`     | `/api/development/panel/meetings/{id}/finish` | POST | Mark meeting finished |
| `paths.panel.healthLogs`            | `/api/development/panel/health-logs`  | GET    | Health logs (list/summary) |
| `paths.panel.batches(webinarId)`    | `/api/development/panel/webinars/{id}/batches` | GET  | Batches for a program |
| `paths.dieticianApi.assignments`    | `/api/development/dietician/assignments` | GET  | Assignments list |
| `paths.dieticianApi.assignmentStudents(id)` | `/api/development/dietician/assignments/{id}/students` | GET | Submissions for an assignment |
| `paths.dieticianApi.setAssignmentGrade(historyId)` | `/api/development/dietician/assignments/histories/{id}/rate` | POST | Grade submission |
| `paths.dieticianApi.certificatesCreated` | `/api/development/panel/certificates/created` | GET | Certificates (teacher) |
| `paths.dieticianApi.certificatesStudents` | `/api/development/panel/certificates/students` | GET | Students who earned certificates |

## Response format

The backend uses `apiResponse2(success, status, message, data)`, which returns JSON like:

```json
{ "success": 1, "status": "retrieved", "message": "...", "data": { ... } }
```

The frontend `api.get` / `api.post` in `src/lib/api.ts` **unwrap** this and return only `data`. So in your code you always work with the payload (e.g. `{ students: [...] }`, `{ reservations: {...}, requests: {...} }`). On `success === 0`, the frontend throws an error with `message`.

## Checklist for “connected” backend + frontend

1. **Backend**  
   - Run on the URL you use in proxy / `VITE_API_URL` (e.g. `http://localhost:8000`).  
   - Routes under `routes/api.php` (development group) and `routes/api/user.php` (panel) and `routes/api/dietician.php` are loaded.

2. **Frontend**  
   - In dev, run Vite (e.g. `npm run dev`) so `/api` is proxied to the backend.  
   - Or set `VITE_API_URL` to the backend URL (for dev without proxy or for production build).  
   - After login, the token is stored and sent on every request.

3. **CORS**  
   - If the frontend runs on a different origin (e.g. port 8080) and you do **not** use the Vite proxy, the backend must allow that origin (e.g. Laravel CORS config or middleware).

4. **Dietician panel**  
   - User must have role `teacher` or `organization` so `api.level-access:teacher` allows access to panel/students, health-profile, dietician/assignments, etc.
