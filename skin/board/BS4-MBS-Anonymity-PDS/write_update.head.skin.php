<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 익명
include_once($board_skin_path.'/board.lib.php');

if(isset($boset['nuse']) && $boset['nuse']) {
	$is_nameless = ($wr_7) ? true : false;
} else {
	$is_nameless = ($wr_7) ? false : true;
}

if(!$notice && $is_nameless && $w != 'u') { //공지가 아닐경우 실행
	if($is_member) {
		$member['mb_name'] = $member['mb_nick'] = na_anonymity();
	} else {
		$_POST['wr_name'] = na_anonymity();
	}
}

// 글수정
if($w == 'u') {
	if($wr['wr_8'] && $wr['wr_8'] === $member['mb_id']) {
		$wr['mb_id'] = $wr['wr_8'];
	}

	if($write['wr_8'] && $write['wr_8'] === $member['mb_id']) {
		$write['mb_id'] = $write['wr_8'];
	}

	if($is_nameless) {
		if($is_member) {
			$member['mb_name'] = $member['mb_nick'] = $write['wr_name'];
		} else {
			$_POST['wr_name'] = $write['wr_name'];
		}
	}

	$wr_8 = $write['wr_8'];
}