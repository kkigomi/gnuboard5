<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 열람 포인트
$as_view = isset($as_view) ? (int)$as_view : 0;
if($as_view < 0) {
	alert("양수만 등록할 수 있습니다.");
}

if(!$is_admin) {
	if(isset($boset['nrp']) && (int)$boset['nrp'] > 0) {
		if($as_view < (int)$boset['nrp']) {
			alert(number_format($boset['nrp'])."포인트 이상 설정하셔야 합니다.");
		}
	}

	if(isset($boset['xrp']) && (int)$boset['xrp'] > 0) {
		if($as_view > (int)$boset['xrp']) {
			alert(number_format($boset['xrp'])."포인트 이하로 설정하셔야 합니다.");
		}
	}
}

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