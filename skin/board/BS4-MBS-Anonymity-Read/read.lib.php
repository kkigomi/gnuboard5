<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

function na_check_reading($bo_table, $wr_id, $wr_mb, $mb_id, $point, $term='') {
	global $g5, $is_admin;

	// 비회원이면
	if(!$mb_id) return 0;

	// 관리자 또는 열람포인트가 없으면
	if($is_admin || !$point || ($wr_mb === $mb_id)) 
		return 1;

	// 열람포인트 내역체크
	$sql_term = ($term) ? na_sql_term($term, 'po_datetime') : ''; // 기간(일수,today,yesterday,month,prev)
	$row = sql_fetch(" select count(*) as cnt, max(po_datetime) as datetime from {$g5['point_table']} where mb_id = '{$mb_id}' and po_rel_table = '{$bo_table}' and po_rel_id = '{$wr_id}' and po_rel_action = '열람' $sql_term ", false);

	if(isset($row['cnt']) && $row['cnt']) {
		$end = ($term) ? strtotime($row['datetime']) + $term * 86400 : 1;
		return $end;
	} else {
		return 0;
	}
}
