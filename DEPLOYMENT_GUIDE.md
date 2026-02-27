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
2. **Backend:** Create a **Web Service** on Render from this repo, Root Directory `backend`, Runtime **Docker**. In **Environment**, add (you do **not** upload a file — use **`backend/.env.render.example`** as the list of keys): `DB_CONNECTION=pgsql`, `DATABASE_URL=<Internal URL from step 1>`, plus `APP_KEY`, `JWT_SECRET`, `API_KEY`, `APP_URL`, `FRONTEND_URL`, `SERVE_REACT_FROM_BACKEND=false` (Part 2).
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
     **Important:** Type exactly `backend` with **no spaces** before or after — a trailing space (e.g. `backend `) will cause "Root directory does not exist" and the deploy will fail.
   - **Runtime:** **Docker**.
   - **Dockerfile path:** `Dockerfile` (relative to Root Directory, so `backend/Dockerfile`).
4. **Instance type:** Free or paid.
5. Do **not** deploy yet. Click **Advanced** and add environment variables first.

### Step 2.2 – Environment variables (Backend)

**You do not upload an env file to Render.** Add each variable in the dashboard: **Web Service → Environment → Add environment variable.**

Use **`backend/.env.render.example`** as your reference (it lists every key with placeholders). Copy the keys and paste your real values into Render.

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

## Part 3: Frontend on Vercel (React) — detailed

The frontend is a Vite + React app that talks to the backend API. Follow these steps in order.

### Step 3.1 – Create Vercel project

1. **Open Vercel**
   - Go to [https://vercel.com/dashboard](https://vercel.com/dashboard) and log in (or sign up with GitHub/GitLab/Bitbucket).

2. **Import the repository**
   - Click **Add New…** → **Project**.
   - If your repo is not connected, click **Import Git Repository** and authorize Vercel for your GitHub/GitLab.
   - Find your **rocket-lms** (or project) repo and click **Import** next to it.

3. **Configure the project (important)**
   Before clicking Deploy, set:
   - **Project Name:** e.g. `rocket-lms` or `your-app-name` (this becomes part of the URL).
   - **Root Directory:** Click **Edit** and set to **`frontend`**.  
     This tells Vercel to build only the `frontend` folder, not the whole repo.
   - **Framework Preset:** Should auto-detect as **Vite**. If not, choose **Vite**.
   - **Build Command:** Leave as `npm run build` (or set explicitly).
   - **Output Directory:** Set to **`dist`** (Vite’s default output).
   - **Install Command:** Leave as `npm install`.

4. **Do not click Deploy yet.** Add environment variables first (Step 3.2).

---

### Step 3.2 – Environment variables (Frontend)

These variables are baked into the frontend at **build time**. If you change them later, you must **redeploy** the frontend.

1. On the same **Configure Project** screen, find **Environment Variables** (or go to the project after creation → **Settings** → **Environment Variables**).

2. Add **two** variables:

   **Variable 1**
   - **Name:** `VITE_API_URL`  
   - **Value:** Your Render backend URL, **no trailing slash**.  
     Example: `https://rocket-lms-backend.onrender.com`  
     (Use the exact URL from your Render Web Service; get it from Render Dashboard → your service → top of the page.)
   - **Environment:** Check **Production** (and **Preview** if you want preview deployments to use the same API).

   **Variable 2**
   - **Name:** `VITE_API_KEY`  
   - **Value:** The **exact same** string you set as `API_KEY` on the backend (Render).  
     Example: `your-secret-api-key-123`  
     The frontend sends this as the `x-api-key` header; the backend checks it against `API_KEY`. If they differ, API calls will return 401.

3. Click **Add** (or **Save**) for each variable.

4. Then click **Deploy** to start the first build.

---

### Step 3.3 – Deploy and get the frontend URL

1. After you click **Deploy**, Vercel will:
   - Clone the repo, use the `frontend` folder, run `npm install`, then `npm run build`.
   - Build time is usually 1–3 minutes.

2. When the deploy finishes, you’ll see a **Congratulations** screen with your app URL.
   - Example: `https://rocket-lms-xxxx.vercel.app` or `https://your-project-name.vercel.app`.
   - **Copy this URL** (no trailing slash). You need it for Step 4.

3. Open the URL in a browser to confirm the app loads.  
   API calls will work only after you complete Step 4 (backend CORS).

---

### Step 3.4 – If you need to change frontend env later

1. In Vercel Dashboard → your project → **Settings** → **Environment Variables**.
2. Edit `VITE_API_URL` and/or `VITE_API_KEY` and save.
3. Go to **Deployments** → open the **⋯** menu on the latest deployment → **Redeploy**.  
   (Vite bakes env at build time, so a new deploy is required for changes to take effect.)

---

## Part 3 (continued): Step 4 – Connect frontend to backend (FRONTEND_URL + redeploy)

After the frontend is live on Vercel, the **backend** must allow requests from the frontend origin (CORS). You do that by setting `FRONTEND_URL` on Render and redeploying.

### Step 4.1 – Set FRONTEND_URL on Render

1. **Open Render**
   - Go to [https://dashboard.render.com](https://dashboard.render.com).
   - Click your **Web Service** (the backend, e.g. `rocket-lms-backend`).

2. **Open Environment**
   - In the left sidebar, click **Environment** (or the **Environment** tab).

3. **Add or edit FRONTEND_URL**
   - If `FRONTEND_URL` is already there (e.g. a placeholder), click **Edit** (pencil icon) next to it.
   - If it’s not there, click **Add Environment Variable**.
   - **Key:** `FRONTEND_URL`  
   - **Value:** Your **Vercel** app URL from Step 3.3, **no trailing slash**.  
     Example: `https://rocket-lms-xxxx.vercel.app` or `https://your-app.vercel.app`

4. **Save**
   - Click **Save Changes** (or **Add**).

### Step 4.2 – Redeploy the backend

Changing env on Render does **not** restart the service automatically. You must trigger a redeploy so the new `FRONTEND_URL` is loaded and CORS allows your Vercel origin.

1. Stay on the same backend service on Render.

2. **Manual Deploy**
   - In the top right, click **Manual Deploy** → **Deploy latest commit** (or **Clear build cache & deploy** if you had issues).
   - Wait for the deploy to finish (status **Live**).

3. **Verify**
   - Open your **Vercel** frontend URL in a browser.
   - Try logging in or loading data from the API.  
   - If you see “blocked by CORS” in the browser console, double-check:
     - `FRONTEND_URL` has **no** trailing slash.
     - `FRONTEND_URL` matches the frontend origin exactly (same protocol and domain).
     - You redeployed the backend after changing it.

---

### Quick reference: Step 3 + 4 in order

| Step | Where | What to do |
|------|--------|------------|
| 3.1 | Vercel | New Project → Import repo → **Root Directory** = `frontend`. |
| 3.2 | Vercel | Add env: `VITE_API_URL` = Render backend URL, `VITE_API_KEY` = same as backend `API_KEY`. |
| 3.3 | Vercel | Deploy → copy the Vercel URL (e.g. `https://your-app.vercel.app`). |
| 4.1 | Render | Backend service → **Environment** → set `FRONTEND_URL` = that Vercel URL (no trailing slash). |
| 4.2 | Render | **Manual Deploy** → Deploy latest commit → wait until **Live**. |

Your frontend is now served from Vercel and uses the backend on Render with CORS and API key correctly configured.

---

## How to verify the frontend is connected to the backend

The frontend does not show a visible "Connected to backend" label by default. Use one of these methods to confirm it is using your Render API:

### Method 1: Browser DevTools (Network tab)

1. Open your **Vercel frontend** URL in Chrome/Edge/Firefox (e.g. `https://your-app.vercel.app`).
2. Press **F12** (or right‑click → Inspect) to open DevTools.
3. Go to the **Network** tab.
4. Reload the page (or click **Programs**, **Login**, or any section that loads data).
5. In the list of requests, look for calls whose **URL starts with your Render backend** (e.g. `https://rocket-lms-backend.onrender.com/api/...`).
   - If you see such requests and they return status **200** with JSON, the frontend **is** connected to the backend.
   - If you see **CORS** errors in the Console tab, the backend `FRONTEND_URL` is wrong or you didn’t redeploy after setting it.
   - If you see **401**, the frontend `VITE_API_KEY` and backend `API_KEY` do not match.

### Method 2: Show API base in the app (optional)

The frontend can show which API URL it is using. Add **`?api=1`** to your frontend URL:

`https://your-app.vercel.app?api=1`

If the project includes the optional API indicator in the footer, you will see a line like **API: https://rocket-lms-backend.onrender.com** at the bottom. That confirms the frontend is configured to use that backend. (Remove `?api=1` for normal use.)

### Method 3: Try a real action

- **Login:** Use the frontend login with a user that exists in your **deployed** database. If login works, the frontend is talking to the backend.
- **Programs/courses:** If the backend DB has data, featured programs or categories should load. If the DB is empty (migrations-only deploy), those sections will be empty — but requests in the Network tab will still go to your Render URL. That confirms **connection**; empty content is a **data** issue (see next section).

---

## Why don’t I see my old data? (PostgreSQL is empty)

The guide recommended **Render PostgreSQL + migrations only**. That means:

- Laravel creates **tables** from migrations (schema).
- The database **starts empty** — no courses, users, or other content from your old setup.

Your previous content (from **KFM.sql** or your local MySQL) was **not** imported into Render’s PostgreSQL. So the frontend **is** connected to the backend, but the backend database has no rows yet.

### To get your existing data to production you have two options:

**Option A – Use MySQL elsewhere (easiest if you want KFM.sql as-is)**

1. Create a **MySQL** database on [Railway](https://railway.app), [PlanetScale](https://planetscale.com), [Aiven](https://aiven.io), or another host that allows external connections.
2. Import your data: `mysql -h HOST -u USER -p DATABASE < Databases/KFM.sql` (or use the provider’s import/restore).
3. On **Render** → your backend service → **Environment**:
   - Set `DB_CONNECTION=mysql`.
   - Set `DATABASE_URL=mysql://USER:PASSWORD@HOST:3306/DATABASE` (or set `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
   - Ensure the MySQL host allows connections from Render (allowlist IPs or use a public URL if the provider offers it).
4. **Redeploy** the backend. The frontend (Vercel) stays the same; it will now get data from the new MySQL database.

**Option B – Put your data into Render’s PostgreSQL**

1. Convert **KFM.sql** (MySQL) to PostgreSQL (e.g. with [pgloader](https://pgloader.io/) or a conversion script).
2. Import the converted data into your **Render PostgreSQL** instance (via `psql` or the provider’s import, if available).
3. Keep `DB_CONNECTION=pgsql` and `DATABASE_URL` as they are. Redeploy if needed.

After Option A or B, your frontend will show the same content you had in your original database.

---

## Part 4: Quick checklist

- [ ] PostgreSQL created on Render; **Internal Database URL** copied.  
- [ ] Backend Web Service created; **Root Directory** = `backend`, **Runtime** = Docker.  
- [ ] Backend env (add in Render **Environment** tab; use **`backend/.env.render.example`** as reference): `APP_KEY`, `JWT_SECRET`, `API_KEY`, `DATABASE_URL`, `DB_CONNECTION=pgsql`, `APP_URL`, `FRONTEND_URL`, `SERVE_REACT_FROM_BACKEND=false`.  
- [ ] Frontend project on Vercel; **Root Directory** = `frontend` (see Part 3 step-by-step).  
- [ ] Frontend env: `VITE_API_URL` = backend URL, `VITE_API_KEY` = same as backend `API_KEY`.  
- [ ] **Step 4:** After first frontend deploy, set backend `FRONTEND_URL` to the Vercel URL (no trailing slash), then **Manual Deploy** the backend so CORS works.

---

## Backend URL not working — is it because of the database?

Yes. The app was developed and tested with a **local MySQL** database that had data (e.g. from KFM.sql). On Render you switched to **PostgreSQL** and used **migrations only**, so:

1. **The database is empty** — no courses, users, or settings. The backend **does not** depend on your machine; it depends on **whatever database you point it to** (Render Postgres). So “everything was dependent on local database” really means: the **data** you had was in MySQL. On Render, that data is not there.
2. **Backend “not working”** can be two different things:
   - **Backend is down or crashing (502 / 500):** Then the **URL** itself won’t work. That’s usually env, DB connection, or a PHP error — not “empty DB”.
   - **Backend responds but frontend shows no content / errors:** Then the backend **is** working; you’re just seeing empty results or a specific route failing (e.g. MySQL-only SQL on Postgres).

### Step 1 — See what’s actually failing

1. **Check Render logs**  
   Render Dashboard → your **backend** Web Service → **Logs**.  
   Look for:
   - **Build/deploy:** errors during `docker build` or `php artisan migrate` / `config:cache`.
   - **Runtime:** PHP errors, “connection refused”, “could not connect to database”, or stack traces when you call the API.

2. **Test the backend URL directly (no frontend)**  
   In a browser or with curl:

   - **Simple test:**  
     `https://rocket-lms-backend.onrender.com/api/development/`  
     (replace with your real Render backend URL.)  
     You should see something like `"api test"` or a short JSON response.  
     - If this **does not load** or you get **502/503**: the app or web server isn’t starting correctly → use **Logs** (above) to find the error.
     - If this **loads**: the backend is running; the problem is likely CORS, API key, or a **specific route** (e.g. `/api/development/courses`).

   - **Courses (guest) API:**  
     `https://rocket-lms-backend.onrender.com/api/development/courses`  
     (Add header `x-api-key: YOUR_API_KEY` if the API requires it.)  
     - If this returns **200** with `{"data":[]}` or similar: backend and DB are fine; the list is empty because the DB has no rows.
     - If this returns **500**: check Logs for the exception; some code path may use MySQL-only SQL or assume data exists.

3. **Confirm env (no local dependency)**  
   The backend does **not** read your local `.env` on Render. It uses **only** the variables you set in Render → **Environment**.  
   - `APP_KEY`, `JWT_SECRET`, `API_KEY` must be set.  
   - `DB_CONNECTION=pgsql` and `DATABASE_URL` = **Internal** PostgreSQL URL from Render.  
   - `FRONTEND_URL` = your Vercel URL (for CORS).  

   If any of these are missing or wrong, the backend can fail to start or to connect to the DB.

### Step 2 — Fix according to what you see

| What you see | What to do |
|--------------|------------|
| **502 / 503 / “Application failed to respond”** | Logs will show the cause. Typical: missing `APP_KEY` or `JWT_SECRET`, wrong `DATABASE_URL`, or migrations failing. Fix env, then **Redeploy**. |
| **Database connection error in logs** | Fix `DATABASE_URL` (Internal URL, same region as the backend) and `DB_CONNECTION=pgsql`. Redeploy. |
| **`/api/development/` works but `/api/development/courses` returns 500** | Some controller/query may use MySQL-only syntax (e.g. raw SQL with backticks). Check Logs for the exact error; that file/query needs to be made PostgreSQL-compatible or you use MySQL elsewhere (see below). |
| **200 with empty data** | Backend and DB are fine. To see content, you must **put data in the Render database**: either use the app (create courses/users) or import data (e.g. convert KFM.sql to PostgreSQL and import, or use MySQL elsewhere and point Render to it). |

### Step 3 — Getting your old data to production

The backend **code** is not tied to your local DB; only the **data** was there. To have that data on Render you have two options:

- **Option A – Use MySQL elsewhere**  
  Create a MySQL database (e.g. Railway, PlanetScale), import **KFM.sql** there, then on Render set `DB_CONNECTION=mysql` and `DATABASE_URL` (or `DB_HOST`, etc.) to that MySQL. Redeploy. The backend will then use that MySQL and your existing data.

- **Option B – Use Render PostgreSQL**  
  Convert **KFM.sql** to PostgreSQL (e.g. pgloader or a conversion script), import into your Render PostgreSQL instance. Keep `DB_CONNECTION=pgsql` and `DATABASE_URL`. Redeploy if needed.

After that, the backend URL will still be the same; it will just read from the database you configured (MySQL or Postgres with imported data).

---

## What env to use when deploying on Render

**You do not upload any .env file.** Render expects environment variables to be set in the dashboard:

1. Open your **Web Service** on Render → **Environment**.
2. Add each variable by hand (key + value), or use **Bulk edit** and paste keys one per line.
3. Use **`backend/.env.render.example`** as the list of variable **names** and replace the placeholder values with your real ones:
   - `APP_KEY` → run `php artisan key:generate --show` locally, paste.
   - `JWT_SECRET` → run `php artisan jwt:secret --show` locally, paste.
   - `DATABASE_URL` → **Internal Database URL** from your Render PostgreSQL (Part 1).
   - `APP_URL` → your backend URL (e.g. `https://rocket-lms-backend.onrender.com`) after first deploy.
   - `FRONTEND_URL` → your Vercel URL (e.g. `https://your-app.vercel.app`) after Part 3.
   - `API_KEY` → any strong secret; same value must be set as `VITE_API_KEY` on Vercel.

---

## Part 5: Custom domains (optional)

- **Backend (Render):** Service → **Settings** → **Custom Domain** → add your domain and follow DNS instructions. Then set `APP_URL` to that domain.
- **Frontend (Vercel):** Project → **Settings** → **Domains** → add domain and configure DNS. Then set `VITE_API_URL` and backend `FRONTEND_URL` to the new frontend URL.

---

## Part 6: Troubleshooting

| Issue | What to check |
|-------|----------------|
| **502 / Backend not loading** | Render logs (Logs tab). Ensure Docker build and `start.sh` finish without errors; migrations run on boot. |
| **500 on backend URL** | The root `/` used to run middleware that queries the DB (settings, categories); if the DB is empty or unreachable, that caused 500. A lightweight root route is now used so `/` redirects to `FRONTEND_URL` or returns JSON without touching the DB. **Check:** Open `https://your-backend.onrender.com/up` — if you see `{"status":"ok"}`, the app is running; then try `/` and `/api/development/`. If `/up` also returns 500, check Render **Logs** for the exception (often missing `APP_KEY`, wrong `DATABASE_URL`, or DB not in same region). |
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
