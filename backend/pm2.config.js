module.exports = {
  apps: [{
    name: 'rocket-lms-backend',
    script: 'php',
    args: 'artisan serve --host=0.0.0.0 --port=8000',
    cwd: '/var/www/rocket-lms/backend',
    interpreter: 'none',
    instances: 1,
    autorestart: true,
    watch: false,
    max_memory_restart: '1G',
    env: {
      APP_ENV: 'production',
      APP_DEBUG: 'false'
    },
    error_file: '/var/www/rocket-lms/backend/storage/logs/pm2-error.log',
    out_file: '/var/www/rocket-lms/backend/storage/logs/pm2-out.log',
    log_date_format: 'YYYY-MM-DD HH:mm:ss Z',
    merge_logs: true,
    time: true
  }]
};
