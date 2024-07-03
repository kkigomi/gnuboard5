<?php
declare(strict_types=1);

if (!defined('_GNUBOARD_')) {
    exit;
}

// 댓글 페이징 인덱스 추가
add_replace('admin_dbupgrade', function ($is_check = false) {
    global $g5;

    $result = sql_query("SELECT bo_table FROM `{$g5['board_table']}`");

    while ($table = sql_fetch_array($result)) {
        $tableName = get_write_table_name($table['bo_table']);

        $resultIndex = sql_fetch("SHOW INDEX FROM `{$tableName}` where `Key_name` = 'idx_comment_paging'");

        if (!$resultIndex) {
            $is_check = false;

            if(sql_query(" DESC {$tableName} ", false)) {
                sql_query("ALTER TABLE `{$tableName}` ADD INDEX `idx_comment_paging` (`wr_parent`,`wr_comment`,`wr_comment_reply`);", true);
                $is_check = true;
            }
        }
    }
    return $is_check;
});
