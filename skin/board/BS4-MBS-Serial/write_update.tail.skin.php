<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 표지번호
$sid = ($wr_1) ? $wr_1 : $wr_id;

// 연재글수
$cnt = sql_fetch("select count(*) as cnt from $write_table where wr_is_comment = '0' and wr_1 <> '' and wr_1 = '{$sid}' ", false);

// 표지갱신
$sql_serial = "wr_2 = '{$cnt['cnt']}'";
if($w != 'u' && $wr_1 && (isset($boset['supdate']) && $boset['supdate'])) {
	$sql_serial .= ", wr_datetime = '".G5_TIME_YMDHIS."'";
}

sql_query(" update $write_table set $sql_serial where wr_id = '{$sid}' ", false);