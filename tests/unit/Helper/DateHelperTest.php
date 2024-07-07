<?php

namespace Damoang\Tests\Unit\Helper;

use Damoang\Lib\Helper\DateHelper;

class DateHelperTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testShortenDefaultFormat()
    {
        // today "H:i"
        $basetime = strtotime('today 0 hours 1 minutes');
        $this->assertSame(
            date('H:i', $basetime),
            DateHelper::shorten(date('Y-m-d H:i:s', $basetime))
        );

        // yesterday "어제 H:i"
        $basetime = strtotime('yesterday 23 hours 59 minutes');
        $this->assertSame(
            date('어제 H:i', $basetime),
            DateHelper::shorten(date('Y-m-d H:i:s', $basetime))
        );

        // 올 해 "m.d H:i"
        $basetime = strtotime('first day of this year');
        $this->assertSame(
            date('m.d H:i', $basetime),
            DateHelper::shorten(date('Y-m-d H:i:s', $basetime))
        );

        // 올 해 이전 "Y.m.d H:i"
        $basetime = strtotime('last year');
        $this->assertSame(
            date('Y.m.d H:i', $basetime),
            DateHelper::shorten(date('Y-m-d H:i:s', $basetime))
        );
    }

    public function testShortenTimeago()
    {
        // 방금
        $basetime = strtotime('-8 seconds');
        $this->assertSame(
            '방금',
            DateHelper::shorten(date('Y-m-d H:i:s', $basetime), 'before')
        );

        // n초 전
        $basetime = strtotime('-15 seconds');
        $this->assertStringEndsWith(
            '초 전',
            DateHelper::shorten(date('Y-m-d H:i:s', $basetime), 'before')
        );

        // n분 전
        $basetime = strtotime('-58 minutes');
        $this->assertSame(
            '58분 전',
            DateHelper::shorten(date('Y-m-d H:i:s', $basetime), 'before')
        );

        // 시간 전
        $basetime = strtotime('-22 hours');
        $this->assertSame(
            '22시간 전',
            DateHelper::shorten(date('Y-m-d H:i:s', $basetime), 'before')
        );

        // 시간 전
        $basetime = strtotime('-28 hours');
        $this->assertStringEndsNotWith(
            '전',
            DateHelper::shorten(date('Y-m-d H:i:s', $basetime), 'before')
        );
    }

    public function testShortenSpecifyFormat()
    {
        $base = '2024-07-05 11:22:33';
        $this->assertSame('11:22', DateHelper::shorten($base, 'day'));
        $this->assertSame('11:22', DateHelper::shorten($base, 'today'));
        $this->assertSame('07.05 11:22', DateHelper::shorten($base, 'month'));
        $this->assertSame('2024.07.05', DateHelper::shorten($base, 'year'));
        $this->assertSame('어제 11:22', DateHelper::shorten($base, 'yday'));
        $this->assertSame('어제 11:22', DateHelper::shorten($base, 'yesterday'));
        $this->assertSame('2024.07.05 11:22', DateHelper::shorten($base, 'long'));
        $this->assertSame('2024.07.05 11:22:33', DateHelper::shorten($base, 'full'));

        $this->assertSame('1720146153', DateHelper::shorten($base, 'U'));
        $this->assertStringStartsWith('2024-07-05T11:22:33', DateHelper::shorten($base, \DateTime::ATOM));
        $this->assertSame('20240705', DateHelper::shorten($base, 'Ymd'));
        $this->assertSame('112233', DateHelper::shorten($base, 'His'));
    }

    public function testShortenCustomFormat()
    {
        // today "H시 i분"
        $basetime = strtotime('today 0 hours 1 minutes');
        $this->assertSame(
            date('H시 i분', $basetime),
            DateHelper::shorten(date('Y-m-d H:i:s', $basetime), 'auto',  [
                'day' => 'H시 i분',
            ])
        );
    }
}
