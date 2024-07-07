<?php

namespace Damoang\Tests\Unit\Helper;

use Damoang\Lib\Helper\StringHelper;

class StringHelperTest extends \Codeception\Test\Unit
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

    public function testIsCrawlerNotBot()
    {
        $this->assertFalse(StringHelper::isCrawler('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.0 Safari/605.1.15'));
    }

    public function testIsCrawlerBot()
    {
        $this->assertTrue(StringHelper::isCrawler('bot'));
        $this->assertTrue(StringHelper::isCrawler('bot'));

        $this->assertTrue(StringHelper::isCrawler('MSNBOT/0.1 (http://search.msn.com/msnbot.htm)'));
        $this->assertTrue(StringHelper::isCrawler('Mozilla/5.0 (compatible; Yahoo! Slurp/3.0; http://help.yahoo.com/help/us/ysearch/slurp))'));
        $this->assertTrue(StringHelper::isCrawler('LinkedInBot/1.0 (compatible; Mozilla/5.0; Jakarta Commons-HttpClient/3.1 +http://www.linkedin.com)'));
        $this->assertTrue(StringHelper::isCrawler('Python-urllib/1.17'));
        $this->assertTrue(StringHelper::isCrawler('python-requests/2.9.2'));
        $this->assertTrue(StringHelper::isCrawler('Python/3.9 aiohttp/3.7.3'));
        $this->assertTrue(StringHelper::isCrawler('python-httpx/0.16.1'));
        $this->assertTrue(StringHelper::isCrawler('WGETbot/1.0 (+http://wget.alanreed.org)'));
        $this->assertTrue(StringHelper::isCrawler('Wget/1.14 (linux-gnu)'));
        $this->assertTrue(StringHelper::isCrawler('adidxbot/1.1 (+http://search.msn.com/msnbot.htm)'));
        $this->assertTrue(StringHelper::isCrawler('FAST-WebCrawler/3.6/FirstPage (atw-crawler at fast dot no;http://fast.no/support/crawler.asp)'));

        // bing
        // ref: https://www.bing.com/webmasters/help/which-crawlers-does-bing-use-8c184ec0
        $this->assertTrue(StringHelper::isCrawler('Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/'));
        $this->assertTrue(StringHelper::isCrawler('Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/W.X.Y.Z Mobile Safari/537.36  (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'));
        $this->assertTrue(StringHelper::isCrawler('Mozilla/5.0 (compatible; adidxbot/2.0; +http://www.bing.com/bingbot.htm)'));
        $this->assertTrue(StringHelper::isCrawler('Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/W.X.Y.Z Safari/537.36)'));
        $this->assertTrue(StringHelper::isCrawler('Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/W.X.Y.Z Mobile Safari/537.36  (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'));
        $this->assertTrue(StringHelper::isCrawler('Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; MicrosoftPreview/2.0; +https://aka.ms/MicrosoftPreview) Chrome/W.X.Y.Z Safari/537.36'));

        // naver
        // ref: https://searchadvisor.naver.com/guide/seo-basic-firewall
        $this->assertTrue(StringHelper::isCrawler('Mozilla/5.0 (compatible; Yeti/1.1; +https://naver.me/spd)'));
        $this->assertTrue(StringHelper::isCrawler('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko; compatible; Yeti/1.1; +https://naver.me/spd) Chrome/122.0.0.0 Safari/537.36'));
        // naver 요약정보 수집봇. opengraph
        // ref: https://help.naver.com/service/5626/contents/19008?lang=ko
        $this->assertTrue(StringHelper::isCrawler('facebookexternalhit/1.1 (compatible; Blueno/1.0; +http://naver.me/scrap)'));
    }

    public function testIsCrawlerGoogleBot()
    {
        // google
        // ref: https://developers.google.com/search/docs/crawling-indexing/overview-google-crawlers?hl=ko
        $this->assertTrue(StringHelper::isCrawler('Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/W.X.Y.Z Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'));
        $this->assertTrue(StringHelper::isCrawler('Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Chrome/W.X.Y.Z Safari/537.36'));
        $this->assertTrue(StringHelper::isCrawler('Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'));
        $this->assertTrue(StringHelper::isCrawler('Googlebot/2.1 (+http://www.google.com/bot.html)'));
        $this->assertTrue(StringHelper::isCrawler('Googlebot-Image/1.0'));
        $this->assertTrue(StringHelper::isCrawler('Mozilla/5.0 (X11; Linux x86_64; Storebot-Google/1.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/W.X.Y.Z Safari/537.36'));
        $this->assertTrue(StringHelper::isCrawler('Mozilla/5.0 (Linux; Android 8.0; Pixel 2 Build/OPD3.170816.012; Storebot-Google/1.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/W.X.Y.Z Mobile Safari/537.36'));
        $this->assertTrue(StringHelper::isCrawler('Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/W.X.Y.Z Mobile Safari/537.36 (compatible; GoogleOther)'));
        $this->assertTrue(StringHelper::isCrawler('GoogleOther-Image/1.0'));
        $this->assertTrue(StringHelper::isCrawler('GoogleOther-Video/1.0'));
        $this->assertTrue(StringHelper::isCrawler('FeedFetcher-Google; (+http://www.google.com/feedfetcher.html)'));
    }

    public function testIsCrawlerAllowGoogleService()
    {
        // ref: https://developers.google.com/search/docs/crawling-indexing/overview-google-crawlers?hl=ko
        // 구글 애드센스
        $this->assertFalse(StringHelper::isCrawler('AdsBot-Google (+http://www.google.com/adsbot.html)'));
        $this->assertFalse(StringHelper::isCrawler('Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/W.X.Y.Z Mobile Safari/537.36 (compatible; AdsBot-Google-Mobile; +http://www.google.com/mobile/adsbot.html)'));
        $this->assertFalse(StringHelper::isCrawler('Mediapartners-Google'));
        $this->assertFalse(StringHelper::isCrawler('(Various mobile device types) (compatible; Mediapartners-Google/2.1; +http://www.google.com/bot.html)'));
        // google 안전 센터
        $this->assertFalse(StringHelper::isCrawler('Google-Safety'));
        // 구글 기타
        $this->assertFalse(StringHelper::isCrawler('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36 (compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943'));
    }
}
