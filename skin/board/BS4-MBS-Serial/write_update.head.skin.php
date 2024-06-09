<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(!$is_admin && IS_DEMO) {
	alert("데모 화면에서는 하실(보실) 수 없는 작업입니다.");
}

// 회원전용
if($is_guest) {
	alert('회원만 등록이 가능합니다.');
}

// 글등록 회원체크
if($w != 'u' && !$is_admin && (isset($boset['smb_list']) && $boset['smb_list']) && !$wr_1) {
	$smb_arr = na_explode(',', $boset['smb_list']);
	if(count($smb_arr) > 0) {
		if(!in_array($member['mb_id'], $smb_arr)) {
			alert('지정된 회원만 등록이 가능합니다.');
		}
	}
}

// 시리즈이면...
if($w != 'u' && $wr_1) {
	$swr = get_write($write_table, $wr_1);
	if(!isset($swr['wr_id']) || !$swr['wr_id']) {
		alert('지정한 표지는 없는 표지입니다.');
	}
	if($is_admin || (isset($swr['mb_id']) && $swr['mb_id'] == $member['mb_id']) || (isset($boset['swrite']) && $boset['swrite'])) {
		;
	} else {
		alert('자신의 표지에만 글등록이 가능합니다.');
	}
}