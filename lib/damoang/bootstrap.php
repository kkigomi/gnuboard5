<?php

declare(strict_types=1);

// $_ENV['CACHE_REDIS_USE'] = filter_var($_ENV['CACHE_REDIS_USE'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
$_ENV['CACHE_REDIS_USE'] = trim($_ENV['CACHE_REDIS_USE'] ?? 'false');

// Redis Cache
add_replace('get_cachemanage_instance', function () {
    static $instance = null;

    if ($instance !== null) {
        return $instance;
    }

    // 관리자 전용으로 동작
    if (
        $_ENV['CACHE_REDIS_USE'] === 'admin'
        && $GLOBALS['is_admin'] !== 'super'
    ) {
        return null;
    }

    if (in_array($_ENV['CACHE_REDIS_USE'], ['true', 'admin'])) {
        $config = [
            'host' => $_ENV['CACHE_REDIS_HOST'] ?? '127.0.0.1',
            'port' => intval($_ENV['CACHE_REDIS_PORT'] ?? 6379),
            'timeout' => intval($_ENV['CACHE_REDIS_TIMEOUT'] ?? 0)
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
