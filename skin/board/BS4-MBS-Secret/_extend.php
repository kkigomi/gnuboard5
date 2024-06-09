<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 회원만 이용
if ($is_guest) {
	include_once(NA_PATH.'/bbs/alert.php');
	alert('로그인 후 이용할 수 있습니다.', G5_BBS_URL.'/login.php?url='.urlencode(get_pretty_url($bo_table)));
}

if(isset($wr_id) && $wr_id) { // 글내용에서

	// 공지글 체크
	$is_notice = (in_array($wr_id, explode(",",$board['bo_notice']))) ? true : false;

	// 관리자와 자기글, 공지글 통과
	if($is_admin || $member['mb_id'] == $write['mb_id'] || $member['mb_id'] == $write['wr_6'] || $is_notice) {
		;
	} else {
		include_once(NA_PATH.'/bbs/alert.php');
		alert('자신의 글만 읽을 수 있습니다.', get_pretty_url($bo_table));
	}
}

// SQL 추가구문
if(!$is_admin) {

	$na_sql_where .= "and (mb_id = '{$member['mb_id']}' or wr_6 = '{$member['mb_id']}') ";

	if ($sca || $stx) { // 분류 또는 검색일 때는 통과
		;
	} else {
		$row = sql_fetch(" select count(*) as cnt from $write_table where wr_is_comment = 0 $na_sql_where");
		if(isset($row['cnt'])) {
			$board['bo_count_write'] = $row['cnt'];
		}
	}
}