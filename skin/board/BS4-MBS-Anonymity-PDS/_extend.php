<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 글내용에서 수정버튼 출력
if(isset($bo_table) && $bo_table && isset($wr_id) && $wr_id) {
	if($write['wr_8'] && $write['wr_8'] === $member['mb_id']) {
		$write['mb_id'] = $write['wr_8'];
		$is_bo_use_sideview = $board['bo_use_sideview'];
		$board['bo_use_sideview'] = '';
	}
}
