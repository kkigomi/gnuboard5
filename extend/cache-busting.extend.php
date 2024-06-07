<?php

declare(strict_types=1);

if (!defined('_GNUBOARD_')) {
    exit;
}

class BrowserCacheBusting
{
    private static $instance;
    public static $g5Url = \G5_URL;

    public static function cacheBusting(array $array): array
    {
        foreach ($array as $idx => $item) {
            $item[1] = trim($item[1]);
            if (!$item[1]) {
                continue;
            }

            if (
                strpos($item[1], self::$g5Url) === false
                && strpos($item[1], '?CACHEBUST') === false
            ) {
                continue;
            }

            $array[$idx][1] = preg_replace_callback(
                '/(.*?)(?<full>(?<url>' . preg_quote(self::$g5Url, '/') . '.*?\.(?:css|js))\?CACHEBUST)(.*?)/i',
                [self::class, 'replace'],
                $item[1],
                1
            );
        }

        return $array;
    }

    public static function replace(array $matches): string
    {
        if (
            !isset($matches['url'])
            || strpos($matches['url'], self::$g5Url) === false
        ) {
            return $matches[0];
        }

        $filepath = str_replace(self::$g5Url, G5_PATH, $matches['url']);

        if (!file_exists($filepath)) {
            return $matches[0];
        }

        $filemtime = filemtime($filepath);

        if (!is_int($filemtime)) {
            return $matches[0];
        }

        $matches[0] = str_replace(
            $matches['full'],
            $matches['url'] . '?' . $filemtime,
            $matches[0]
        );

        return $matches[0];
    }
}

BrowserCacheBusting::$g5Url = str_replace(['http://', 'https://'], '//', \G5_URL);

add_replace(
    'html_process_css_files',
    function ($array = []) {
        return BrowserCacheBusting::cacheBusting($array);
    },
    G5_HOOK_DEFAULT_PRIORITY,
    1
);
add_replace(
    'html_process_script_files',
    function ($array = []) {
        return BrowserCacheBusting::cacheBusting($array);
    },
    G5_HOOK_DEFAULT_PRIORITY,
    1
);
