<?php

declare(strict_types=1);

use Damoang\Plugin\ContentManagement\ContentTracker;

if (!defined('_GNUBOARD_')) {
    exit;
}

define('DA_PLUGIN_CONTENTMANAGEMENT_VERSION', 10000);
define('DA_PLUGIN_CONTENTMANAGEMENT_PATH', __DIR__);
define('DA_PLUGIN_CONTENTMANAGEMENT_DIR', basename(DA_PLUGIN_CONTENTMANAGEMENT_PATH));
define('DA_PLUGIN_CONTENTMANAGEMENT_URL', G5_PLUGIN_URL . '/' . DA_PLUGIN_CONTENTMANAGEMENT_DIR);

// 설치, 마이그레이션이 완료되지 았았다면 동작을 멈춤
if (!ContentTracker::installed()) {
    // 관리자 계정에서 DB 마이그레이션
    if ($is_admin === 'super') {
        $tableName = ContentTracker::tableName();

        // 테이블 생성
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS `{$tableName}` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `bo_table` VARCHAR(20) NOT NULL,
            `wr_id` INT UNSIGNED NOT NULL,
            `wr_is_comment` TINYINT NOT NULL DEFAULT 0,
            `mb_id` VARCHAR(20) NOT NULL,
            `wr_name` VARCHAR(255) NOT NULL,
            `operation` ENUM('수정', '삭제') NOT NULL,
            `operated_by` VARCHAR(20) NOT NULL,
            `operated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `previous_data` JSON,
            PRIMARY KEY (`id`),
            INDEX `idx_bo_table_wr_id` (`bo_table`, `wr_id`),
            INDEX `idx_operated_at` (`operated_at`),
            INDEX `idx_mb_id` (`mb_id`),
            INDEX `idx_operated_by` (`operated_by`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        SQL;

        sql_query($sql, true);

        // DB 마이그레이션 결과를 캐시에 저장
        // 캐시는 삭제될 수 있으므로, 마이그레이션 코드가 반복 실행될 수 있으므로 주의해야 함
        g5_set_cache('da-installed-content-history', DA_PLUGIN_CONTENTMANAGEMENT_VERSION);
    }

    return;
}

// 관리페이지에서는 동작 제한
if (defined('G5_IS_ADMIN') && \G5_IS_ADMIN) {
    return;
}