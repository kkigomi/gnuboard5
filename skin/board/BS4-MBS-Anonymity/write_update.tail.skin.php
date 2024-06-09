<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// wr_7 : 실명글
// wr_8 : 실명아이디

if(isset($boset['nuse']) && $boset['nuse']) {
	$is_nameless = ($wr_7) ? true : false;
} else {
	$is_nameless = ($wr_7) ? false : true;
}

$name = '';
if(!$notice && $is_nameless) { //공지가 아닐경우 실행
	if($w == 'u') {
		// 익명처리
		sql_query(" update $write_table set mb_id = '', wr_email = '', wr_homepage = '' where wr_id = '{$wr['wr_id']}' ");
	} else {
		// 이름
		$name = addslashes(na_anonymity());

		// 익명처리
		sql_query(" update $write_table set wr_name = '$name', mb_id = '', wr_email = '', wr_homepage = '', wr_8 = '{$member['mb_id']}' where wr_id = '$wr_id' ");
		sql_query(" update {$g5['board_new_table']} set mb_id = '' where bo_table = '$bo_table' and wr_id = '$wr_id' ");
	}
}

// 답글 알림
if(IS_NA_NOTI) {

	// 답글 알림
	if($w == 'r' && !$boset['noti_no']) {

		$me_id = ($wr['mb_id']) ? $wr['mb_id'] : $wr['wr_8'];
		$me_name = ($name) ? $name : $wr_name;

		if($me_id && $member['mb_id'] !== $me_id) {

			$noti = array();

			$noti['rel_msg'] = sql_real_escape_string(na_cut_text($wr['wr_content'], 70));
			$noti['parent_subject'] = sql_real_escape_string(na_cut_text($wr['wr_subject'], 90));
			$noti['bo_table'] = $noti['rel_bo_table'] = $bo_table;
			$noti['wr_id'] = $noti['wr_parent'] = $wr['wr_id'];
			$noti['rel_wr_id'] = $wr_id;
			$noti['rel_mb_id'] = '';
			$noti['rel_mb_nick'] = $me_name;
			$noti['rel_url'] = "/".G5_BBS_DIR."/board.php?bo_table=".$bo_table."&wr_id=".$wr_id;

			// 알림 등록
			na_noti('board', 'reply', $me_id, $noti);
		}
	}
}

// 알림설정 해제
$boset['noti_no'] = 1;
