<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 현재 챕터로 돌아가기
if(isset($cpid) && $cpid) {
	$wr['wr_parent'] = $cpid;
}