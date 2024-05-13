<?php

declare(strict_types=1);

include_once __DIR__ . '/src/DamoangEditorImages.php';


define('DA_PLUGIN_EDITOR_IMAGES_VERSION', 10000);

// DB 테이블 생성 등 설치가 완료되지 않았다면 동작 중지
// super 관리자만 설치를 실행할
if (!installed_d58350ab()) {
    if ($GLOBALS['is_admin'] === 'super') {
        install_d58350ab();
    } else {
        return;
    }
}

// 그누보드의 공용 이벤트를 이용한 중복 이미지 처리
// 에디터 교체를 대비하여 그누보드와 호환되는 이벤트를 활용한 처리
add_replace('get_editor_upload_url', function ($file_url = '', $filepath = null, $fileInfo = null) {
    $filesize = $fileInfo->size ?? 0;
    $existsFile = \DamoangEditorImages::existsImage($filepath, $filesize);

    // 중복 이미지가 있으면 이번 파일은 삭제
    if ($existsFile !== false) {
        unlink($filepath);
        $file_url = $existsFile['url'];
        $GLOBALS['upload'] = basename($existsFile['path']);
    } else {
        $filehash = sha1_file($filepath);
        \DamoangEditorImages::logImage($filepath, $filehash, $filesize);
    }

    return $file_url;
}, 100, 3);

/**
 * DB 테이블 생성 확인 등 설치 여부 확인
 */
function installed_d58350ab(): bool
{
    // cache에서 업데이트 기록이 있으면 패스
    $cacheData = g5_get_cache('da-editor-images-updated') ?: 0;
    if ($cacheData >= DA_PLUGIN_EDITOR_IMAGES_VERSION) {
        return true;
    }

    if ($GLOBALS['is_admin'] !== 'super') {
        return false;
    }

    $tableName = \G5_TABLE_PREFIX . 'da_editor_images';

    $existsTable = sql_query("SHOW TABLES LIKE '{$tableName}'", true);
    if (!$existsTable || !sql_num_rows($existsTable)) {
        return false;
    }

    g5_set_cache('da-editor-images-updated', DA_PLUGIN_EDITOR_IMAGES_VERSION);

    return true;
}

/**
 * 테이블 생성
 */
function install_d58350ab(): void
{
    if (installed_d58350ab()) {
        return;
    }

    $tableName = G5_TABLE_PREFIX . 'da_editor_images';
    sql_query("CREATE TABLE IF NOT EXISTS `{$tableName}` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `filepath` varchar(255) CHARACTER SET ascii NOT NULL,
        `filesize` int(10) unsigned NOT NULL,
        `filehash` varchar(100) CHARACTER SET ascii NOT NULL,
        `uploaded_count` int(11) DEFAULT '1',
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `filehash` (`filehash`,`filesize`)
    );", true);

    g5_set_cache('da-editor-images-updated', \DA_PLUGIN_EDITOR_IMAGES_VERSION);
}