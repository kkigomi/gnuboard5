<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 신고 댓글 수정 불가
if($w == "cu") {
	if(!$is_admin && IS_NA_BBS) {
		if(isset($boset['na_shingo']) && $boset['na_shingo'] && isset($write['as_type']) && $write['as_type'] == "-1") {
			if(isset($boset['na_crows']) && $boset['na_crows']) {
				die('신고된 댓글은 수정할 수 없습니다.');
			} else {
				alert("신고된 댓글은 수정할 수 없습니다.");
			}
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

if($is_nameless && $w != 'cu') {
	if($is_member) {
		$member['mb_name'] = $member['mb_nick'] = na_anonymity();
	} else {
		$wr_name = na_anonymity();
	}
}