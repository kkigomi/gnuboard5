<?php

declare(strict_types=1);

use Damoang\Lib\Cache\RedisCache;

// Redis Cache
add_replace('get_cachemanage_instance', function () {
    static $instance = null;

    if (
        ($_ENV['CACHE_REDIS_USE'] ?? 'false') === 'true' && $instance === null
    ) {
        $config = [
            'host' => $_ENV['CACHE_REDIS_HOST'] ?? '127.0.0.1',
            'port' => intval($_ENV['CACHE_REDIS_PORT'] ?? 6379),
            'timeout' => intval($_ENV['CACHE_REDIS_TIMEOUT'] ?? 0)
        ];

        try {
            if (!class_exists('Redis', false)) {
                throw new \Exception('Class Redis not found');
            }

            $instance = new RedisCache($config);
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
