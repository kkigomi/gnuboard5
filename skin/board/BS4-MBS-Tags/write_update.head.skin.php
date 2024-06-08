<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(!$is_admin && IS_DEMO) {
	alert("데모 화면에서는 하실(보실) 수 없는 작업입니다.");
}

// 추가태그 정리
$as_tag = isset($as_tag) ? $as_tag : '';
$wr_10 = isset($wr_10) ? $wr_10 : '';
if($as_tag && $wr_10) {
	$as_tag = $as_tag.','.$wr_10;
} else if(!$as_tag && $wr_10) {
	$as_tag = $wr_10;
}