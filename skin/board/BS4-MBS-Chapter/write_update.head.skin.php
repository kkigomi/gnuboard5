<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(!$is_admin && IS_DEMO) {
	alert("데모 화면에서는 하실(보실) 수 없는 작업입니다.");
}

// 원글 등록자만 챕터등록 가능
if($w == "r" && isset($wr['mb_id']) && $wr['mb_id'] && $wr['mb_id'] != $member['mb_id']) {
	alert('원글 작성자만 챕터등록이 가능합니다.');
}