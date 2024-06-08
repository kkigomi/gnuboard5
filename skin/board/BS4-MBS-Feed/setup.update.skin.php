<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 목록세션 초기화
set_session($bo_table.'_list', '');

// 피드 재수집
if(isset($re_feed) && $re_feed) {
	$board['bo_1'] = 0;
	$boset = na_skin_config('board', $bo_table);
	@include_once($board_skin_path.'/_extend.php');
}