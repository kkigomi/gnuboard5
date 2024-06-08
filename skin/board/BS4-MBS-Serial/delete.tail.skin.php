<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 연재글일 때 전체 연재글수 업데이트
if($write['wr_1']) {

	$cnt = sql_fetch("select count(*) as cnt from $write_table where wr_is_comment = '0' and wr_1 <> '' and wr_1 = '{$write['wr_1']}' ", false);

	// 업데이트
	sql_query(" update $write_table set wr_2 = '{$cnt['cnt']}' where wr_id = '{$write['wr_1']}' ", false);

	$redirect_url = short_url_clean(G5_HTTP_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$write['wr_1'].'&amp;'.$qstr);
	goto_url($redirect_url);
}