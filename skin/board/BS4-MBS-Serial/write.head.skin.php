<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 회원전용
if($is_guest) {
	alert('회원만 등록이 가능합니다.');
}

// 신고 글 수정 불가
if($w == "u") {
	if(!$is_admin && IS_NA_BBS) {
		if(isset($boset['na_shingo']) && $boset['na_shingo'] && isset($write['as_type']) && $write['as_type'] == "-1") {
			alert("신고된 글은 수정할 수 없습니다.");
		}
	}
}

// 시리즈 체크
if(!isset($sid) || !$sid) 
	$sid = isset($write['wr_1']) ? $write['wr_1'] : '';

// 글등록 회원체크
if($w != 'u' && !$is_admin && isset($boset['smb_list']) && $boset['smb_list'] && !$sid) {
	$smb_arr = na_explode(',', $boset['smb_list']);
	if(count($smb_arr) > 0) {
		if(!in_array($member['mb_id'], $smb_arr)) {
			alert('지정된 회원만 등록이 가능합니다.');
		}
	}
}

$swr = array();
if($sid) {
	$swr = get_write($write_table, $sid);
}

// 시리즈이면...
if($w != 'u' && $sid) { //수정이 아니면...
	if(!isset($swr['wr_id']) || !$swr['wr_id']) {
		alert('지정한 표지는 없는 표지입니다.');
	}
	if($is_admin || $swr['mb_id'] == $member['mb_id'] || (isset($boset['swrite']) && $boset['swrite'])) {
		;
	} else {
		alert('자신의 표지에만 글등록이 가능합니다.');
	}
}

$serial_subject = isset($swr['wr_subject']) ? get_text($swr['wr_subject']) : '';