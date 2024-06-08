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

if(!$is_admin && IS_DEMO) {
	alert("데모 화면에서는 하실(보실) 수 없는 작업입니다.");
}