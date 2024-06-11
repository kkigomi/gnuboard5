<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once($board_skin_path.'/read.lib.php');

if($wr_id) {
	if($write['wr_8'] && $write['wr_8'] === $member['mb_id']) {
		$write['mb_id'] = $write['wr_8'];
		$is_bo_use_sideview = $board['bo_use_sideview'];
		$board['bo_use_sideview'] = '';
	}

	$wr_mb_id = ($write['mb_id']) ? $write['mb_id'] : $write['wr_8'];
	$write['as_view'] = isset($write['as_view']) ? (int)$write['as_view'] : 0;
	$is_reading_term = isset($boset['rterm']) ? (int)$boset['rterm'] : 0;
	$is_reading = ($write['as_view']) ? na_check_reading($bo_table, $wr_id, $wr_mb_id, $member['mb_id'], $write['as_view'], $is_reading_term) : 1;
} else {
	$is_reading = 1;
}
