<?php

declare(strict_types=1);

namespace Damoang\Lib\Cache;

class RedisCache
{
    /**
     * @var \Redis
     */
    protected $store;

    /**
     * @param array{
     *      'host'?: string,
     *      'port'?: int,
     *      'timeout'?: int,
     * } $config
     */
    public function __construct(array $config = [])
    {
        if (!class_exists('Redis', false)) {
            throw new \Exception('Class Redis not found');
        }

        try {
            $this->store = new \Redis();
            $this->store->connect(
                $config['host'] ?? '127.0.0.1',
                intval($config['port'] ?? 6379),
                intval($config['timeout'] ?? 0),
            );
        } catch (\Exception $e) {
            throw $e;
        }

        add_replace('g5_get_cache_replace', function ($data = false, $cache, $key, $expired_time) {
            if ($cache instanceof $this) {
                return $this->get($key);
            }
        }, \G5_HOOK_DEFAULT_PRIORITY, 4);

        add_event('g5_set_cache_event', function ($cache, $key, $save_data, $ttl) {
            if ($cache instanceof $this) {
                $this->set($key, $save_data, $ttl);
            }
        }, \G5_HOOK_DEFAULT_PRIORITY, 4);

        add_replace('g5_delete_cache_by_prefix', function ($files, $key, $cache) {
            if ($cache instanceof $this) {
                $keys = $this->keys($key . '*');
                if (is_array($keys) && !empty($keys)) {
                    $this->delete(...$keys);
                }
            }
            return $keys ?? [];
        }, \G5_HOOK_DEFAULT_PRIORITY, 3);

        add_event('adm_cache_delete', function ($board_tables) {
            $keys = $this->keys('*');
            if (is_array($keys) && !empty($keys)) {
                $this->delete(...$keys);
            }
        }, \G5_HOOK_DEFAULT_PRIORITY, 1);

        add_event('adm_cache_file_delete', function () {
            $keys = $this->keys('*');
            if (is_array($keys) && !empty($keys)) {
                $this->delete(...$keys);
            }

            if (!is_int($GLOBALS['cnt'])) {
                $GLOBALS['cnt'] = 0;
            }

            foreach ($keys as $key) {
                ++$GLOBALS['cnt'];
                echo '<li>Redis Cache : <code>' . $key . '</code></li>' . PHP_EOL;
                flush();
            }
        }, \G5_HOOK_DEFAULT_PRIORITY);
    }

    /**
     * @return false|mixed
     */
    public function get(string $key)
    {
        $key = strpos($key, 'g5cache:') === 0 ? $key : 'g5cache:' . $key;
        $value = $this->store->get($key);

        if ($value === false) {
            return false;
        }

        return unserialize($value);
    }

    /**
     * @param mixed $value
     * @param ?int $ttl
     */
    public function set(string $key, $value, $ttl = null): void
    {
        $key = strpos($key, 'g5cache:') === 0 ? $key : 'g5cache:' . $key;
        $this->store->set($key, serialize($value), $ttl);
    }

    /**
     * @param mixed $value
     * @param ?int $ttl
     */
    public function save(string $key, $value, $ttl = null): void
    {
        $this->set($key, $value, $ttl);
    }

    /**
     * @return string[]
     */
    public function keys(string $pattern): array
    {
        $keys = $this->store->keys('g5cache:' . $pattern);

        if (!is_array($keys)) {
            return [];
        }

        return $this->store->keys('g5cache:' . $pattern);
    }

    public function delete(string $key, string ...$keys): bool
    {
        $key = strpos($key, 'g5cache:') === 0 ? $key : 'g5cache:' . $key;

        if ($keys) {
            $keys = array_map(function ($key) {
                return strpos($key, 'g5cache:') === 0 ? $key : 'g5cache:' . $key;
            }, $keys);
        }

        return (bool) $this->store->del($key, ...$keys);
    }
}
