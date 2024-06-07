<?php

declare(strict_types=1);

namespace Damoang\Lib\Helper;

class DateHelper
{
    /**
     * @var array<string, string>
     */
    private static $foramtType = [
        'auto' => 'auto',
        'before' => 'before',
        'day' => 'H:i',
        'today' => 'H:i',
        'month' => 'm.d H:i',
        'year' => 'Y.m.d',
        'yday' => '어제 H:i',
        'yesterday' => '어제 H:i',
        'long' => 'Y.m.d H:i',
        'full' => 'Y.m.d H:i:s',
    ];

    /**
     * 날짜를 가능한 짧은 형태나 지정한 포맷으로 반환
     *
     * @param array{
     *     'day'?: string,
     *     'today'?: string,
     *     'month'?: string,
     *     'year'?: string,
     *     'yday'?: string,
     *     'yesterday'?: string,
     *     'long'?: string,
     *     'full'?: string,
     * } $customFormat 사용자 정의 포맷
     */
    public static function shorten(string $dateString, string $format = 'auto', $customFormat = []): string
    {
        $date = strtotime($dateString);
        $originDate = date('Y-m-d', $date);
        $output = date('Y.m.d H:i', $date);

        $format = $customFormat[$format] ?? self::$foramtType[$format] ?? $format;

        if ($format === 'before') {
            // timeago
            /** @var int */
            $diff = G5_SERVER_TIME - $date;
            if ($diff < 86400) {
                $output = null;

                $s = 60; // 1분 = 60초
                $h = $s * 60; // 1시간 = 60분
                $d = $h * 24; // 1일 = 24시간
                $y = $d * 10; // 1년 = 1일 * 10일

                if ($diff <= 10) {
                    $output = '방금';
                } else if ($diff < $s) {
                    $output = $diff . '초 전';
                } else if ($h > $diff) {
                    $output = round($diff / $s) . '분 전';
                } else {
                    $output = round($diff / $h) . '시간 전';
                }

                return $output;
            }
            $format = 'auto';
        }

        if ($format === 'auto') {
            // 짧은 형태로 반환
            if (\G5_TIME_YMD === $originDate) {
                // 오늘
                $format = $customFormat['day'] ?? self::$foramtType['day'];
            } else if (date('Y-m-d', strtotime('yesterday')) === $originDate) {
                // 어제
                $format = $customFormat['yesterday'] ?? $customFormat['yday'] ?? self::$foramtType['yday'];
            } else if (substr(\G5_TIME_YMD, 0, 4) === substr($originDate, 0, 4)) {
                // 올 해
                $format = $customFormat['month'] ?? self::$foramtType['month'];
            } else {
                $format = $customFormat['long'] ?? self::$foramtType['long'];
            }
        }

        $output = date($format, $date);

        return $output;
    }
}
