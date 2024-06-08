<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(!$is_admin && IS_DEMO) {
	alert("데모 화면에서는 하실(보실) 수 없는 작업입니다.");
}