# Deploy Rocket LMS: Frontend (Vercel) + Backend (Render)

This guide covers deploying the **frontend** to **Vercel** and the **backend** (Laravel) to **Render**, with the React app calling the API on a different domain.

---

## Do it now (short version)

**Secrets are already generated** for you. Use the paste files in this folder:

| Step | Where | What to do |
|------|--------|------------|
| 1 | **Render** | New → Web Service → connect repo, **Root Directory** = `backend`, **Environment** = Docker. |
| 2 | **Render → Environment** | Paste/edit from **`RENDER_ENV_PASTE.txt`**. Replace `YOUR-RENDER-SERVICE-NAME`, `YOUR-VERCEL-PROJECT`, and `YOUR_DB_*` with your real values. |
| 3 | **Render** | Deploy. After deploy, open **Shell** and run: `php artisan migrate --force` and `php artisan storage:link`. |
| 4 | **Vercel** | New → Project → same repo, **Root Directory** = `frontend`. |
| 5 | **Vercel → Settings → Environment Variables** | Paste from **`VERCEL_ENV_PASTE.txt`**. Set `VITE_API_URL` to your **actual** Render URL (e.g. `https://rocket-lms-backend.onrender.com`). |
| 6 | **Render → Environment** | Set `APP_URL` and `FRONTEND_URL` to your **actual** Render and Vercel URLs. Redeploy backend if needed. |
| 7 | **Vercel** | Deploy. Done. |

**Database:** You need a MySQL database (e.g. [PlanetScale](https://planetscale.com) free tier, or [Railway](https://railway.app)). Put its host, database, user, and password into the Render env vars.

---

## Overview

| Part      | Host   | URL (example)        |
|-----------|--------|----------------------|
| Frontend  | Vercel | `https://your-app.vercel.app` |
| Backend   | Render | `https://your-backend.onrender.com` |
| Database  | External (e.g. PlanetScale, Railway) or Render PostgreSQL |

**Important:** Deploy the **backend first** so you have its URL for the frontend env vars.

---

## 1. Backend on Render

### 1.1 Database

Your app uses **MySQL**. Options:

- **PlanetScale** (free tier): Create a DB, get connection string.
- **Railway** or **Aiven**: MySQL hosting.
- **Render PostgreSQL**: If you prefer, you can use PostgreSQL; set `DB_CONNECTION=pgsql` and use a PostgreSQL URL. You’d need to run migrations against PostgreSQL (schema is Laravel-standard).

Use one MySQL (or Postgres) instance and note: **host, port, database name, user, password** (or a single `DATABASE_URL` if your provider gives one).

### 1.2 Prepare the backend repo

Ensure the **backend** folder is what Render will build. Either:

- **Option A – Monorepo:** Root repo contains `backend/` and `frontend/`. In Render, set **Root Directory** to `backend`.
- **Option B – Backend-only repo:** Push only the `backend/` contents to a separate repo and connect that to Render.

The backend must include:

- `Dockerfile` (in `backend/` if using Option A, or in repo root if Option B)
- `render.yaml` (optional but useful)

So the build context for Docker is the directory that contains `composer.json` (i.e. the Laravel app root = `backend/` in your case).

### 1.3 Create Render Web Service

1. [Dashboard](https://dashboard.render.com) → **New** → **Web Service**.
2. Connect the repo (and set **Root Directory** to `backend` if monorepo).
3. **Environment:** Docker.
4. **Build Command:** Leave **empty** (Render builds via the Dockerfile, not a shell command).
5. **Start Command:** Set to **`/start.sh`** (or leave empty to use the Dockerfile’s `CMD`).
6. Render runs `docker build` in the root directory you set (e.g. `backend`).

### 1.4 Environment variables (Render)

In Render → your Web Service → **Environment**, add:

**Required:**

```env
APP_NAME=rocketlms
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
APP_URL=https://your-backend.onrender.com

# JWT (generate a long random string)
JWT_SECRET=your-long-random-jwt-secret

# Must match frontend VITE_API_KEY
API_KEY=your-api-key-string

# Frontend URL (for CORS) – your Vercel URL, no trailing slash
FRONTEND_URL=https://your-app.vercel.app

# Do not serve React from backend when frontend is on Vercel
SERVE_REACT_FROM_BACKEND=false

# Database (MySQL example; use your actual host/credentials)
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your_db
DB_USERNAME=user
DB_PASSWORD=password
```

If your provider gives a single URL:

```env
DATABASE_URL=mysql://user:password@host:3306/database_name
```

**Optional (mail, storage, auth):**

- `MAIL_*` – SMTP for emails.
- `MINIO_*` or S3 – for file storage (Render disk is ephemeral).
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `FACEBOOK_*` – if you use social login.

Generate `APP_KEY` locally:

```bash
cd backend
php artisan key:generate --show
```

Paste the `base64:...` value into `APP_KEY` on Render.

### 1.5 After first deploy

- Open: `https://your-backend.onrender.com`
- You should see Laravel (or a redirect). API: `https://your-backend.onrender.com/api/...`
- Run migrations once (Render **Shell** or a one-off job):
  - `php artisan migrate --force`
  - `php artisan storage:link` (if you use local storage)
- If you use file uploads in production, configure S3 (or similar); don’t rely on local disk on Render.

---

## 2. Frontend on Vercel

### 2.1 Repo setup

- Same repo as backend (monorepo) or a repo that contains the **frontend** folder.
- In Vercel you will set **Root Directory** to `frontend` so that `frontend/package.json` and `frontend/vite.config.ts` are used.

### 2.2 Create Vercel project

1. [Vercel](https://vercel.com) → **Add New** → **Project**.
2. Import the repo.
3. **Root Directory:** set to `frontend` (or the folder that contains the React app).
4. **Framework Preset:** Vite (or leave as auto).
5. **Build Command:** `npm run build`
6. **Output Directory:** `dist`
7. **Install Command:** `npm install`

A `frontend/vercel.json` is included so SPA routes (e.g. `/login`, `/course/xyz`) correctly serve `index.html`.

### 2.3 Environment variables (Vercel)

In Project → **Settings** → **Environment Variables**, add:

| Name           | Value                                    | Environment   |
|----------------|------------------------------------------|---------------|
| `VITE_API_URL` | `https://your-backend.onrender.com`      | Production (and Preview if you want) |
| `VITE_API_KEY` | Same value as backend `API_KEY`          | Production (and Preview if you want) |

No trailing slash on `VITE_API_URL`.  
Redeploy after changing env vars so the new values are baked into the build.

### 2.4 Build and URL behavior

- The frontend is built with **base `/`** (default in your Vite config when not using `VITE_APP_BASE=/spa/`), so it works on Vercel’s root.
- The app will call `VITE_API_URL` (your Render backend) for all `/api` requests.

---

## 3. CORS and security

- Backend **CORS** is driven by `FRONTEND_URL`. With `FRONTEND_URL=https://your-app.vercel.app`, only that origin is allowed.
- Keep `API_KEY` and `VITE_API_KEY` in sync; the frontend sends it as `x-api-key` on API requests.
- Use strong `APP_KEY` and `JWT_SECRET` in production.

---

## 4. Checklist

**Backend (Render):**

- [ ] Root directory = `backend` (if monorepo).
- [ ] Docker build/start from `backend` (Dockerfile in `backend/`).
- [ ] `APP_KEY`, `JWT_SECRET`, `API_KEY`, `FRONTEND_URL`, `SERVE_REACT_FROM_BACKEND=false` set.
- [ ] Database env vars (or `DATABASE_URL`) set and reachable from Render.
- [ ] Migrations run once after first deploy.
- [ ] `APP_URL` = your Render backend URL.

**Frontend (Vercel):**

- [ ] Root directory = `frontend`.
- [ ] `VITE_API_URL` = Render backend URL (no trailing slash).
- [ ] `VITE_API_KEY` = same as backend `API_KEY`.
- [ ] Redeploy after changing env vars.

**After deploy:**

- [ ] Open Vercel URL → React app loads.
- [ ] Login/API calls go to Render URL and succeed (check Network tab and no CORS errors).

---

## 5. Optional: Custom domain

- **Vercel:** Add your domain in Project → Settings → Domains.
- **Render:** Add custom domain in Service → Settings → Custom Domain.
- Update `APP_URL` and `FRONTEND_URL` to use the custom domains, and set `VITE_API_URL` to the backend domain.
