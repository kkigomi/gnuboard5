<?php
declare(strict_types=1);

if (!defined('_GNUBOARD_')) {
    exit;
}

$_ENV['CACHE_REDIS_USE'] = trim($_ENV['CACHE_REDIS_USE'] ?? 'false');
$_ENV['CACHE_REDIS_HOST'] = trim($_ENV['CACHE_REDIS_HOST'] ?? '127.0.0.1');
$_ENV['CACHE_REDIS_PORT'] = trim($_ENV['CACHE_REDIS_PORT'] ?? '6379');
$_ENV['CACHE_REDIS_TIMEOUT'] = trim($_ENV['CACHE_REDIS_TIMEOUT'] ?? '0');

// Redis Cache
add_replace('get_cachemanage_instance', function () {
    static $instance = null;

    if ($instance !== null) {
        return $instance;
    }

    if ($_ENV['CACHE_REDIS_USE'] === true) {
        $config = [
            'host' => $_ENV['CACHE_REDIS_HOST'],
            'port' => intval($_ENV['CACHE_REDIS_PORT']),
            'timeout' => intval($_ENV['CACHE_REDIS_TIMEOUT']),
        ];

        try {
            if (!class_exists('\Redis', false)) {
                throw new \Exception('Class Redis not found');
            }

            $instance = new Damoang\Lib\Cache\RedisCache($config);
        } catch (\Exception $e) {
            $instance = null;
            if ($GLOBALS['is_admin'] === 'super') {
                var_dump(
                    '관리자용 오류 메시지 : Redis 오류',
                    $e->getMessage()
                );
            }
        }
    }

    return $instance;
});

// 다중이
add_event('console:register', function ($application) {
    // ... register commands
    $application->register('dajoongi')
        ->setCode(function ($input, $output) {
            $dajoongi = new Damoang\Plugin\Dajoongi\Dajoongi();
            $dajoongi->run();
        });
}, \G5_HOOK_DEFAULT_PRIORITY, 1);
