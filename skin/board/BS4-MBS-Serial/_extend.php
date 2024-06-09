<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/* 여분필드 정의
 * - wr_1 : 글구분(값이 없으면 표지, 표지 wr_id 있으면 연재)
 * - wr_2 : 연재글수
 * - wr_3 : 업데이트
 */

// 연재글관리
$is_serial_admin = '';
if($is_admin) {
	if(isset($act) && $act) {
		if($act == "on") {
			set_session('serial_'.$bo_table, '1');
		} else {
			set_session('serial_'.$bo_table, '');
		}
	}

	$is_serial_admin = get_session('serial_'.$bo_table);
}

if(($stx && isset($boset['sstx']) && $boset['sstx']) || $is_serial_admin) {
	; // 검색 또는 글관리시 전체글 출력
} else {
	// 표지출력 쿼리
	$na_sql_where .= " and wr_1 = '' ";

	// 업데이트 순으로 정렬
	$na_sql_orderby .= ' wr_3 desc, ';

	// 분류가 아닐 경우 전체글수 계산
	if(!$sca) {
		$row = sql_fetch(" select count(*) as cnt from $write_table where wr_is_comment = 0 $na_sql_where");
		$board['bo_count_write'] = $row['cnt'];
	}
}