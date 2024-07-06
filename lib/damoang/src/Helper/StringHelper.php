<?php

declare(strict_types=1);

namespace Damoang\Lib\Helper;

class StringHelper
{
    public static function isCrawler(string $user_agent = '')
    {
        static $cache;

        $isCrawler = false;
        $user_agent = $user_agent ?: $_SERVER['HTTP_USER_AGENT'];
        if (isset($cache[sha1($user_agent)])) {
            return $cache[sha1($user_agent)];
        }

        if (preg_match('/APIs-Google|AdsBot-Google|Mediapartners-Google|Google-Safety/', $user_agent)) {
            $isCrawler = false;
        } else if (preg_match('/bot|crawl|wget|slurp|okhttp|headlesschrome|python|curl|googleother|feedfetcher|facebook|MicrosoftPreview|Yeti/i', $user_agent)) {
            $isCrawler = true;
        }

        $cache[sha1($user_agent)] = $isCrawler;

        return $isCrawler;
    }
}
