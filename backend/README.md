# Rocket LMS Backend

Laravel-based Learning Management System backend API.

## Quick Start

### Manual Installation

1. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Run migrations**
   ```bash
   php artisan migrate
   ```

4. **Start development server**
   ```bash
   php artisan serve
   ```

## AWS EC2 Deployment (no Docker)

**Prerequisites on EC2:** PHP 8.2+, Composer, Node.js 18+, MySQL (or RDS).

1. Set up MySQL (local or RDS) and create a database.
2. Edit `backend/.env`: set `DB_HOST` (127.0.0.1 or RDS endpoint), `DB_PASSWORD`, `APP_URL`, `API_KEY`.
3. From the project root:
```bash
./deploy-aws.sh
```
This builds the frontend, copies to backend/public/spa, installs Composer deps, runs migrations, and optimizes Laravel. Configure Nginx/Apache to serve `backend/public` as document root.

## Environment Variables

Key environment variables:

- `APP_ENV`: Set to `production` for production
- `APP_DEBUG`: Set to `false` in production
- `DB_*`: Database configuration
- `ENABLE_MAINTENANCE_ROUTES`: Set to `false` in production (disables database maintenance routes)

## Maintenance Scripts

Database maintenance scripts are located in `maintenance/database-scripts/`. These are only accessible when:
- `APP_ENV` is not `production`, OR
- `ENABLE_MAINTENANCE_ROUTES=true` in `.env`

## API Documentation

API endpoints are available at `/api/*`. See `API_ENDPOINTS.md` for detailed documentation.

## License

[Your License Here]
