<?php
declare(strict_types=1);

namespace Damoang\Lib\G5\Board;

use Damoang\Lib\G5\G5CommonObject;

class BoardConfig extends G5CommonObject
{
    /**
     * @var ?string
     * @readonly
     */
    protected $boardId = null;

    protected $defaults = [
        // 주요 설정
        'bo_table' => '',
        'bo_skin' => '',
        'bo_mobile_skin' => '',
        'bo_subject' => '',
        'bo_mobile_subject' => '',

        'bo_image_width' => 0,
        'bo_upload_count' => 0,
        'bo_use_dhtml_editor' => '',
    ];

    protected $casts = [
        'bo_image_width' => 'int',
        'bo_upload_count' => 'int',
    ];

    /**
     * @param ?mixed[] $data
     */
    function __construct($data = [])
    {
        $data['bo_table'] = $data['bo_table'] ?? null;

        parent::__construct($data);

        $this->boardId = $data['bo_table'];
    }

    /**
     * 게시판 아이디
     */
    public function id(): ?string
    {
        return $this->boardId;
    }

    public function title(): string
    {
        return $this->data['bo_subject'] ?? '';
    }

    /**
     * Alias of self::title()
     */
    public function titleMobile(): string
    {
        $title = $this->data['bo_mobile_subject'];

        if (!$this->data['bo_mobile_subject']) {
            $title = $this->title();
        }

        return $title;
    }

    public function skin(): string
    {
        return $this->data['bo_skin'] ?? '';
    }

    public function skinMobile(): string
    {
        $skin = $this->data['bo_mobile_skin'] ?? $this->skin();

        if ($skin === 'theme/PC-Skin') {
            $skin = $this->skin();
        }

        return $skin;
    }

    /**
     * 스킨 설정 반환
     *
     * 나리야 빌더의 스킨 설정.
     */
    public function getSkinConfig(): SkinConfig
    {
        global $config;

        include_once \G5_PLUGIN_PATH . '/nariya/lib/core.lib.php';

        $themeName = ucfirst($config['cf_theme'] ?? '');

        if (!$themeName) {
            return new SkinConfig();
        }

        $skin = $this->data['bo_skin'];
        if (strpos($skin, '/') !== false) {
            $skin = ltrim(strstr($this->data['bo_skin'], '/'), '/');
        }
        $skinName = ucfirst($skin);

        $className = "Damoang\\Theme\\{$themeName}\\Skin\\Board\\{$skinName}\\SkinConfig";

        if (!class_exists($className)) {
            return new SkinConfig();
        }

        $skinConfig = \na_skin_config('board', $this->id());

        return new $className($skinConfig ?? []);
    }
}
