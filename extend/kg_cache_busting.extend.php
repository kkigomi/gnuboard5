<?php
/**
 * @author kkigomi
 * @version 1.1.0
 * @license https://opensource.org/licenses/LGPL-2.1 GNU LGPL v2.1 or later
 */
declare(strict_types=1);

namespace Kkigomi\OldPlugins\CacheBusting;

if (!defined('_GNUBOARD_')) {
    exit;
}

// PHP 버전 제한. 오류 방지
if (PHP_VERSION_ID < 50400) {
    return;
}

class CacheBusting
{
    private static $instance;
    private $g5Url;

    private function __construct()
    {
        $this->g5Url = str_replace(['http://', 'https://'], '//', \G5_URL);
    }

    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new static ();
        }

        return static::$instance;
    }

    function cacheBusting($array)
    {
        foreach ($array as $idx => $item) {
            if (!trim($item[1])) {
                continue;
            }

            $array[$idx][1] = preg_replace_callback(
                '/<(?:script|link).*?(?:href|src)=(?:\'|")(?<full>(?<url>.*?\.(?:css|js))(?<param>.*?))(?:\'|")[^\<\>]*?>/i',
                [$this, 'replace'],
                $item[1],
                1
            );
        }

        return $array;
    }

    public function headCssUrl($url)
    {
        $replaced = preg_replace_callback(
            '/(?<full>(?<url>.+\.css)(?<param>.*))/i',
            [$this, 'replace'],
            $url,
            1
        );

        if ($replaced) {
            $url = $replaced;
        }

        return $url;
    }

    public function replace($matches)
    {
        if (!isset($matches['url'])) {
            return $matches[0];
        }

        if (stripos($matches['url'], $this->g5Url) === false) {
            return $matches[0];
        }

        if (isset($matches['param']) && $matches['param']) {
            if (
                $matches['param'] === '?ver=' . G5_CSS_VER
                || $matches['param'] === '?ver=' . G5_JS_VER
            ) {
                $matches['param'] = '';
            }
        }

        $filepath = str_replace(
            ['https:' . $this->g5Url, 'http:' . $this->g5Url, $this->g5Url],
            G5_PATH,
            $matches['url']
        );

        if (!file_exists($filepath)) {
            return $matches[0];
        }

        $filemtime = filemtime($filepath);

        if (!$filemtime) {
            return $matches[0];
        }

        $addParam = '?' . $filemtime;

        if ($matches['param']) {
            $addParam = str_replace(
                $addParam . '?',
                $addParam . '&amp;',
                $addParam . $matches['param']
            );
        }

        $matches[0] = str_replace(
            $matches['full'],
            $matches['url'] . $addParam,
            $matches[0]
        );

        return $matches[0];
    }
}

\add_replace(
    'head_css_url',
    ['Kkigomi\OldPlugins\CacheBusting\CacheBusting', 'headCssUrl'],
    G5_HOOK_DEFAULT_PRIORITY,
    1
);
\add_replace(
    'html_process_css_files',
    ['Kkigomi\OldPlugins\CacheBusting\CacheBusting', 'cacheBusting'],
    G5_HOOK_DEFAULT_PRIORITY,
    1
);
\add_replace(
    'html_process_script_files',
    ['Kkigomi\OldPlugins\CacheBusting\CacheBusting', 'cacheBusting'],
    G5_HOOK_DEFAULT_PRIORITY,
    1
);
