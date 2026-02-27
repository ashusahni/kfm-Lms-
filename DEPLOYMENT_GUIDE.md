# Deploy Rocket LMS: Database + Backend (Render) + Frontend (Vercel)

This guide walks you through deploying the current project with:

- **Database:** PostgreSQL on Render (or MySQL hosted elsewhere if you need to use `KFM.sql` as-is)
- **Backend:** Laravel (PHP) on Render (Docker)
- **Frontend:** React (Vite) on Vercel

**PostgreSQL is fully supported.** The backend uses Laravel’s `pgsql` driver (`config/database.php`), and the Docker image includes `pdo_pgsql`. For Render you use PostgreSQL; for local or external MySQL (e.g. to use `KFM.sql`), set `DB_CONNECTION=mysql` and provide MySQL credentials.

---

## About `Databases/KFM.sql`

**What it is:** `Databases/KFM.sql` is a **MySQL dump** (export) of an existing Rocket LMS database. It contains:

- **Schema:** Full table definitions (e.g. `accounting`, `adherence_scores`, `users`, `webinars`, etc.).
- **Data:** Rows from that database (users, courses, orders, health logs, etc.).

It was created with MySQL 8 (e.g. `mysqldump`), so the syntax is **MySQL-specific** (e.g. `ENGINE=InnoDB`, `LOCK TABLES`, backticks, some types).

**How you can use it:**

| Where you run the app | Can you use KFM.sql? | How |
|------------------------|----------------------|-----|
| **Local (MySQL)**     | Yes                  | Create a MySQL database (e.g. `rocketlms` or `kfm_lms`), then run: `mysql -u root -p your_db_name < Databases/KFM.sql`. Then point `backend/.env` to that DB. |
| **Render (PostgreSQL)** | Not directly       | Render uses **PostgreSQL**. You cannot run a raw MySQL dump on PostgreSQL. You have two options: (1) **Fresh deploy:** use only Laravel **migrations** on Render (no KFM.sql). The schema is recreated from `backend/database/migrations/` and the DB starts empty. (2) **Migrate data:** convert KFM.sql to PostgreSQL (e.g. with [pgloader](https://pgloader.io/) or a conversion tool) and import into the Render PostgreSQL DB—more involved and only needed if you must keep that exact data in the cloud. |

**Recommendation for Render:** For a clean production deploy, use **migrations only** (Part 1–2 below). If you need the **data** from KFM.sql on Render, you’ll need to convert the dump to PostgreSQL and import it separately; the deployment steps stay the same once the database has the correct schema and data.

---

## Simplest deployment (recommended)

1. **Database:** Create a **PostgreSQL** database on Render (Part 1). Do **not** import KFM.sql here — it's MySQL-only. Laravel will create tables from **migrations** on first deploy.
2. **Backend:** Create a **Web Service** on Render from this repo, Root Directory `backend`, Runtime **Docker**. Set env: `DB_CONNECTION=pgsql`, `DATABASE_URL=<Internal URL from step 1>`, plus `APP_KEY`, `JWT_SECRET`, `API_KEY`, `APP_URL`, `FRONTEND_URL`, `SERVE_REACT_FROM_BACKEND=false` (Part 2).
3. **Frontend:** Create a project on **Vercel** from the same repo, Root Directory `frontend`. Set `VITE_API_URL` = your Render backend URL and `VITE_API_KEY` = same as backend `API_KEY` (Part 3).
4. Set backend `FRONTEND_URL` to your Vercel URL and redeploy the backend.

Result: **DB and backend on Render, frontend on Vercel.** No KFM.sql; DB starts empty and is built from migrations. To use **KFM.sql data**, see the next section.

---

## Using KFM.sql "somewhere else" and connecting Render + Vercel

You can host the data from KFM.sql elsewhere and still use Render for the backend and Vercel for the frontend.

| Option | Where KFM.sql lives | Backend (Render) connects to |
|--------|----------------------|------------------------------|
| **A – MySQL elsewhere** | Import KFM.sql into a **MySQL** host (e.g. [Railway](https://railway.app) MySQL, [PlanetScale](https://planetscale.com), [Aiven](https://aiven.io), or a VPS). | Set `DB_CONNECTION=mysql` and the MySQL URL/host/credentials in Render env. Backend on Render connects to that external MySQL. |
| **B – PostgreSQL on Render** | Convert KFM.sql to PostgreSQL (e.g. with [pgloader](https://pgloader.io/) or a conversion script), then import into the **same** Render PostgreSQL DB you use for the app. | Use `DB_CONNECTION=pgsql` and `DATABASE_URL` as in Part 1–2. |

**Option A (simplest if you want KFM.sql as-is):**

1. Create a **MySQL** database on Railway, PlanetScale, or another provider that allows external connections.
2. Import: `mysql -h HOST -u USER -p DATABASE < Databases/KFM.sql` (or use the provider's import/restore).
3. On **Render** (backend): set `DB_CONNECTION=mysql`, and either `DATABASE_URL=mysql://USER:PASSWORD@HOST:3306/DATABASE` or `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`. Ensure the MySQL host allows connections from Render's IPs (or use a public URL if the provider offers it).
4. Deploy frontend on Vercel and set `FRONTEND_URL` on the backend as in Part 3.

**Option B:** Convert KFM.sql to PostgreSQL (e.g. pgloader from a MySQL source to a PostgreSQL file or direct load), then import that into your Render PostgreSQL instance. After that, use Part 1–2 as written.

---

## Prerequisites

- Code in a **Git repository** (e.g. GitHub or GitLab).  
- Accounts: [Render](https://render.com), [Vercel](https://vercel.com).  
- (Optional) Custom domains for backend and frontend.

---

## Part 1: Database on Render (PostgreSQL)

Render does **not** offer MySQL; it offers **PostgreSQL**. This project supports both.

### Step 1.1 – Create the database

1. Go to [Render Dashboard](https://dashboard.render.com) → **New +** → **PostgreSQL**.
2. **Name:** e.g. `rocket-lms-db`.
3. **Region:** Choose one (e.g. **Oregon (US West)**). Use the **same region** for the backend later.
4. **Plan:** Free or paid.
5. Click **Create Database**.
6. Wait until status is **Available**.

### Step 1.2 – Get the connection URL

1. Open your database → **Info** (or **Connect**).
2. Copy the **Internal Database URL**.  
   It looks like:
   ```text
   postgres://USER:PASSWORD@HOST/DATABASE?sslmode=require
   ```
   Use the **Internal** URL for the backend service on Render (same network).  
   Save this for **Part 2**.

---

## Part 2: Backend on Render (Laravel)

The backend runs as a **Docker** web service and uses the PostgreSQL database from Part 1.

### Step 2.1 – Create Web Service

1. In Render Dashboard → **New +** → **Web Service**.
2. **Connect** your Git repository (e.g. GitHub).
3. **Configure:**
   - **Name:** e.g. `rocket-lms-backend`
   - **Region:** Same as the database (e.g. Oregon).
   - **Branch:** e.g. `main`
   - **Root Directory:** `backend`  
     (so Render uses the `backend` folder as the project root).
   - **Runtime:** **Docker**.
   - **Dockerfile path:** `Dockerfile` (relative to Root Directory, so `backend/Dockerfile`).
4. **Instance type:** Free or paid.
5. Do **not** deploy yet. Click **Advanced** and add environment variables first.

### Step 2.2 – Environment variables (Backend)

In the same Web Service → **Environment** tab, add these. Replace placeholders with your real values.

| Key | Value | Notes |
|-----|--------|--------|
| `APP_ENV` | `production` | |
| `APP_DEBUG` | `false` | Keep false in production. |
| `APP_KEY` | `base64:...` | Generate with `php artisan key:generate --show` locally and paste. |
| `APP_URL` | `https://rocket-lms-backend.onrender.com` | Your Render backend URL (see after first deploy). |
| `JWT_SECRET` | long random string | Generate with `php artisan jwt:secret --show` locally (if you use JWT). |
| `API_KEY` | e.g. `your-secret-api-key-123` | Must match frontend `VITE_API_KEY` (see Part 3). |
| `DB_CONNECTION` | `pgsql` | Use PostgreSQL. |
| `DATABASE_URL` | (paste Internal Database URL from Part 1) | e.g. `postgres://user:pass@host/db?sslmode=require`. |
| `SERVE_REACT_FROM_BACKEND` | `false` | Frontend is on Vercel, not served by Laravel. |
| `FRONTEND_URL` | `https://your-app.vercel.app` | Your Vercel app URL (no trailing slash). Set after Part 3. |

Optional but recommended:

- **Mail:** Set `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` if you want real email.
- **Payments / OAuth:** Add `PAYPAL_*`, `RAZORPAY_*`, `GOOGLE_CLIENT_*`, etc. if you use them.

You can leave `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` unset when using `DATABASE_URL`; Laravel will parse the URL.

### Step 2.3 – Deploy backend

1. Click **Create Web Service**.  
   Render will build the Docker image and deploy. The first deploy may take a few minutes.
2. After deploy, note the service URL (e.g. `https://rocket-lms-backend.onrender.com`).
3. **Update env:**
   - Set `APP_URL` to this URL (e.g. `https://rocket-lms-backend.onrender.com`).
   - Set `FRONTEND_URL` to your Vercel URL (e.g. `https://your-app.vercel.app`) once the frontend is live.
4. **Redeploy** so the new env is applied.

On each deploy, the container runs `php artisan migrate --force` and caches config/routes (see `backend/docker/start.sh`), so the database will have tables and the app will use the correct DB and CORS (via `FRONTEND_URL`).

---

## Part 3: Frontend on Vercel (React)

The frontend is a Vite + React app that talks to the backend API.

### Step 3.1 – Create Vercel project

1. Go to [Vercel Dashboard](https://vercel.com/dashboard) → **Add New** → **Project**.
2. **Import** the same Git repository.
3. **Configure:**
   - **Root Directory:** set to `frontend` (so Vercel builds only the frontend).
   - **Framework Preset:** Vite (or leave auto-detected).
   - **Build Command:** `npm run build`
   - **Output Directory:** `dist`
   - **Install Command:** `npm install`
4. Before deploying, add environment variables (Step 3.2).

### Step 3.2 – Environment variables (Frontend)

In the Vercel project → **Settings** → **Environment Variables**, add:

| Name | Value | Environment |
|------|--------|-------------|
| `VITE_API_URL` | `https://rocket-lms-backend.onrender.com` | Production (and Preview if you want). |
| `VITE_API_KEY` | Same value as backend `API_KEY` (e.g. `your-secret-api-key-123`) | Production (and Preview if needed). |

Use your **actual** Render backend URL (no trailing slash).  
The frontend sends `VITE_API_URL` for API calls and `VITE_API_KEY` as `x-api-key`; the backend expects the same value in `API_KEY`.

### Step 3.3 – Deploy frontend

1. Click **Deploy**.  
   Vercel will run `npm run build` and deploy the `dist` output.
2. Note the Vercel URL (e.g. `https://your-app.vercel.app`).
3. **Go back to Render** → backend service → **Environment**:
   - Set `FRONTEND_URL` = `https://your-app.vercel.app` (no trailing slash).
   - Redeploy the backend so CORS allows the frontend origin.

Your frontend is now served from Vercel and uses the backend on Render.

---

## Part 4: Quick checklist

- [ ] PostgreSQL created on Render; **Internal Database URL** copied.  
- [ ] Backend Web Service created; **Root Directory** = `backend`, **Runtime** = Docker.  
- [ ] Backend env: `APP_KEY`, `JWT_SECRET`, `API_KEY`, `DATABASE_URL`, `DB_CONNECTION=pgsql`, `APP_URL`, `FRONTEND_URL`, `SERVE_REACT_FROM_BACKEND=false`.  
- [ ] Frontend project on Vercel; **Root Directory** = `frontend`.  
- [ ] Frontend env: `VITE_API_URL` = backend URL, `VITE_API_KEY` = same as backend `API_KEY`.  
- [ ] After first frontend deploy, `FRONTEND_URL` set on backend and backend redeployed.

---

## Part 5: Custom domains (optional)

- **Backend (Render):** Service → **Settings** → **Custom Domain** → add your domain and follow DNS instructions. Then set `APP_URL` to that domain.
- **Frontend (Vercel):** Project → **Settings** → **Domains** → add domain and configure DNS. Then set `VITE_API_URL` and backend `FRONTEND_URL` to the new frontend URL.

---

## Part 6: Troubleshooting

| Issue | What to check |
|-------|----------------|
| **502 / Backend not loading** | Render logs (Logs tab). Ensure Docker build and `start.sh` finish without errors; migrations run on boot. |
| **Database connection error** | **PostgreSQL:** `DATABASE_URL` is the **Internal** URL; `DB_CONNECTION=pgsql`; database is in same region and **Available**. **External MySQL:** `DB_CONNECTION=mysql`; host allows inbound from Render; `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (or `DATABASE_URL`) set correctly. |
| **CORS / “blocked by CORS”** | Backend `FRONTEND_URL` = exact frontend origin (e.g. `https://your-app.vercel.app`), no trailing slash; redeploy backend. |
| **API 401 / “wrong API key”** | `API_KEY` (backend) and `VITE_API_KEY` (frontend) must be identical. |
| **Frontend shows wrong API URL** | Rebuild frontend after changing `VITE_API_URL` (Vite bakes it at build time). Trigger a new deploy on Vercel. |
| **Admin panel / login** | Admin is served by the **backend** URL, e.g. `https://rocket-lms-backend.onrender.com/admin`. Students use the **frontend** URL (Vercel) and log in there. |

---

## Summary

1. **Render:** Create PostgreSQL → create Web Service from repo, Root Directory `backend`, Docker → set env (including `DATABASE_URL`, `APP_URL`, `FRONTEND_URL`, `API_KEY`) → deploy.  
2. **Vercel:** Import repo, Root Directory `frontend` → set `VITE_API_URL` and `VITE_API_KEY` → deploy.  
3. Set backend `FRONTEND_URL` to the Vercel URL and redeploy backend.  

After that, the database and backend run on Render and the frontend on Vercel, with API and CORS correctly configured.
