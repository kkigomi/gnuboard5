<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 챕터 삭제시 표지로 이동
if($write['wr_reply']) {

	delete_cache_latest($bo_table);

	run_event('bbs_delete', $write, $board);

	// 표지
	$wr = sql_fetch(" select wr_id from $write_table where wr_is_comment = '0' and wr_reply = '' and wr_num = '{$write['wr_num']}' ", false);
	if(isset($wr['wr_id']) && $wr['wr_id']) {
		goto_url(short_url_clean(G5_HTTP_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr['wr_id'].'&amp;page='.$page.$qstr));
	} else {
		goto_url(short_url_clean(G5_HTTP_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;page='.$page.$qstr));
	}
}