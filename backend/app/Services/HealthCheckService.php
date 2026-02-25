<?php

namespace App\Services;

use App\Models\HealthLog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthCheckService
{
    protected int $now;

    public function __construct()
    {
        $this->now = time();
    }

    /**
     * Run all health checks and log results.
     */
    public function runAll(): array
    {
        $results = [];
        $checks = [
            'database' => [$this, 'checkDatabase'],
            'cache' => [$this, 'checkCache'],
            'storage' => [$this, 'checkStorage'],
            'env' => [$this, 'checkEnv'],
            'queue' => [$this, 'checkQueue'],
        ];

        foreach ($checks as $name => $callable) {
            $results[] = $this->runAndLog($name, $callable);
        }

        return $results;
    }

    protected function runAndLog(string $checkName, callable $check): array
    {
        try {
            $result = $check();
            $status = $result['status'] ?? HealthLog::STATUS_OK;
            $message = $result['message'] ?? 'OK';
            $meta = $result['meta'] ?? null;
        } catch (\Throwable $e) {
            $status = HealthLog::STATUS_FAILED;
            $message = $e->getMessage();
            $meta = ['exception' => get_class($e), 'file' => $e->getFile(), 'line' => $e->getLine()];
        }

        HealthLog::create([
            'check_name' => $checkName,
            'status' => $status,
            'message' => strlen($message) > 500 ? substr($message, 0, 497) . '...' : $message,
            'meta' => $meta,
            'created_at' => $this->now,
        ]);

        return compact('checkName', 'status', 'message', 'meta');
    }

    protected function checkDatabase(): array
    {
        DB::connection()->getPdo();
        $count = DB::table('users')->count();
        return [
            'status' => HealthLog::STATUS_OK,
            'message' => 'Database connection OK',
            'meta' => ['users_count' => $count],
        ];
    }

    protected function checkCache(): array
    {
        $key = 'health_check_' . $this->now;
        Cache::put($key, 1, 60);
        $ok = Cache::get($key) === 1;
        Cache::forget($key);
        return [
            'status' => $ok ? HealthLog::STATUS_OK : HealthLog::STATUS_FAILED,
            'message' => $ok ? 'Cache read/write OK' : 'Cache failure',
            'meta' => ['driver' => config('cache.default')],
        ];
    }

    protected function checkStorage(): array
    {
        $path = 'health_check_' . $this->now . '.txt';
        $wrote = Storage::put($path, 'ok');
        $content = $wrote ? Storage::get($path) : null;
        if ($content === 'ok') {
            Storage::delete($path);
        }
        $free = @disk_free_space(storage_path());
        $total = @disk_total_space(storage_path());
        $usagePercent = ($total && $total > 0) ? round((1 - $free / $total) * 100, 1) : 0;
        $status = $usagePercent >= 95 ? HealthLog::STATUS_FAILED : ($usagePercent >= 85 ? HealthLog::STATUS_WARNING : HealthLog::STATUS_OK);
        return [
            'status' => ($content === 'ok') ? $status : HealthLog::STATUS_FAILED,
            'message' => $content === 'ok' ? "Storage OK (disk usage {$usagePercent}%)" : 'Storage write/read failed',
            'meta' => [
                'disk_usage_percent' => $usagePercent,
                'free_bytes' => $free,
                'total_bytes' => $total,
            ],
        ];
    }

    protected function checkEnv(): array
    {
        $appKey = config('app.key');
        $debug = config('app.debug');
        $env = config('app.env');
        $ok = !empty($appKey);
        return [
            'status' => $ok ? HealthLog::STATUS_OK : HealthLog::STATUS_WARNING,
            'message' => $ok ? 'APP_KEY set, env OK' : 'APP_KEY missing',
            'meta' => ['env' => $env, 'debug' => $debug],
        ];
    }

    protected function checkQueue(): array
    {
        $driver = config('queue.default');
        $connection = config('queue.connections.' . $driver);
        return [
            'status' => HealthLog::STATUS_OK,
            'message' => 'Queue config OK',
            'meta' => ['driver' => $driver],
        ];
    }
}
