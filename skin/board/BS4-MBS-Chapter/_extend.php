<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 챕터 쿼리
$is_chapter_active = false;

if(isset($stx) && $stx) {
	; //검색시 제외
} else {

	$is_chapter_active = true;

	// 답글없는 글만 출력
	$na_sql_where .= "and wr_reply = ''";

	// 글수
	if(isset($sca) && $sca) {
		;
	} else {
		$row = sql_fetch(" select count(*) as cnt from $write_table where wr_is_comment = 0 and wr_reply = '' ");
		if(isset($row['cnt']) && $row['cnt']) {
			$board['bo_count_write'] = $row['cnt'];
		}
	}
}