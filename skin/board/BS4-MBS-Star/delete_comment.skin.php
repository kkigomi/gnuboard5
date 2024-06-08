<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(isset($write['as_star_score']) && $write['as_star_score']) {

	// 전체 별점 체크
	$star = sql_fetch(" select sum(as_star_score) as score, count(*) as cnt from $write_table where wr_is_comment = '1' and wr_parent = '{$write['wr_parent']}' and wr_comment_reply = '' and as_star_score > 0 ", false);

	$score = isset($star['score']) ? (int)$star['score'] : 0;
	$cnt = isset($star['cnt']) ? (int)$star['cnt'] : 0;

	// 원글 반영
    sql_query(" update $write_table set as_star_score = '$score', as_star_cnt = '$cnt' where wr_id = '{$write['wr_parent']}' ", false);
}