<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 별점
$wr_star = isset($wr_star) ? $wr_star : '';

// 별점 등록 체크
$is_comment_star = false;
if($w == 'c' && !$tmp_comment_reply ) { // 새댓글만 반영, 대댓글 제외

	$sql = "select count(*) as cnt from $write_table where wr_is_comment = '1' and wr_parent = '$wr_id' and wr_comment_reply = '' and as_star_score > 0";
	$sql .= ($member['mb_id']) ? " and mb_id = '{$member['mb_id']}' " : " and mb_id = '' and wr_ip = '{$_SERVER['REMOTE_ADDR']}' ";

	$row = sql_fetch($sql, false);
	
	// 등록한 별점이 없으면
	if(!isset($row['cnt']) || !$row['cnt']) {
		$is_comment_star = true;
	}

} else if ($w == 'cu') { // 수정시 별점있는 댓글만 반영

	$row = sql_fetch(" select as_star_score from $write_table where wr_id = '$comment_id' ", false);

	// 등록된 별점이 있으면
	if(isset($row['as_star_score']) && $row['as_star_score']) {
		$is_comment_star = true;
	}
}

if($is_comment_star) {

	// 별점 반영
    sql_query(" update $write_table set as_star_score = '$wr_star', as_star_cnt = '1' where wr_id = '$comment_id' ", false);

	$star = sql_fetch(" select sum(as_star_score) as score, count(*) as cnt from $write_table where wr_is_comment = '1' and wr_parent = '$wr_id' and wr_comment_reply = '' and as_star_score > 0 ", false);

	$score = isset($star['score']) ? (int)$star['score'] : 0;
	$cnt = isset($star['cnt']) ? (int)$star['cnt'] : 0;

	// 원글 반영
	sql_query(" update $write_table set as_star_score = '$score', as_star_cnt = '$cnt' where wr_id = '$wr_id' ", false);
}