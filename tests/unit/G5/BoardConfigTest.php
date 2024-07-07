<?php

namespace Damoang\Tests\Unit\G5;

use Damoang\Lib\G5\Board\BoardConfig;

class BoardConfigTest extends \Codeception\Test\Unit
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

    public function testInstance()
    {
        $board = new BoardConfig();
        $this->assertInstanceOf(BoardConfig::class, $board);
    }

    public function testId()
    {
        $board = new BoardConfig(['bo_table' => 'free']);
        $this->assertSame('free', $board->id());

        $board = new BoardConfig();
        $this->assertNull($board->id());
    }

    public function testTitle()
    {
        $bo_subject = 'pc-title';
        $bo_mobile_subject = 'mobile-title';
        $board = new BoardConfig(compact('bo_subject'));
        $this->assertSame($bo_subject, $board->title());
        $this->assertSame($bo_subject, $board->titleMobile());

        $board = new BoardConfig(compact('bo_subject', 'bo_mobile_subject'));
        $this->assertSame($bo_subject, $board->title());
        $this->assertSame($bo_mobile_subject, $board->titleMobile());
    }

    public function testSkin()
    {
        $bo_skin = 'basic';
        $bo_mobile_skin = 'basic-mobile';
        $board = new BoardConfig(compact('bo_skin', 'bo_mobile_skin'));
        $this->assertSame($bo_skin, $board->skin());
        $this->assertSame($bo_mobile_skin, $board->skinMobile());

        $bo_skin = 'theme/basic';
        $bo_mobile_skin = 'theme/basic-mobile';
        $board = new BoardConfig(compact('bo_skin', 'bo_mobile_skin'));
        $this->assertSame($bo_skin, $board->skin());
        $this->assertSame($bo_mobile_skin, $board->skinMobile());
    }

    public function testNariyaReplacedPcSkin()
    {
        $bo_skin = 'theme/basic';
        $bo_mobile_skin = 'theme/PC-Skin';
        $board = new BoardConfig(compact('bo_skin', 'bo_mobile_skin'));
        $this->assertSame($bo_skin, $board->skin());
        $this->assertSame($bo_skin, $board->skinMobile());
    }

    public function testGetSkinConfig()
    {
        include_once \G5_PLUGIN_PATH . '/nariya/lib/core.lib.php';

        $GLOBALS['config']['cf_theme'] = 'damoang';
        $bo_skin = 'theme/basic';
        $config1 = 'value1';
        $board = new BoardConfig(compact('bo_skin', 'config1'));

        $skinConfig = $board->getSkinConfig();
        $this->assertInstanceOf(\Damoang\Theme\Damoang\Skin\Board\Basic\SkinConfig::class, $skinConfig);
    }

    public function testNotExistsSkinConfig()
    {
        include_once \G5_PLUGIN_PATH . '/nariya/lib/core.lib.php';

        $GLOBALS['config']['cf_theme'] = '';
        $bo_skin = 'basic';
        $board = new BoardConfig(compact('bo_skin'));

        $skinConfig = $board->getSkinConfig();
        $this->assertInstanceOf(\Damoang\Lib\G5\Board\SkinConfig::class, $skinConfig);
        $this->assertNotInstanceOf(\Damoang\Theme\Damoang\Skin\Board\Basic\SkinConfig::class, $skinConfig);
    }
}