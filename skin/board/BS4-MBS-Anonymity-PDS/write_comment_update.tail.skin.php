<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// wr_7 : 실명글
// wr_8 : 실명아이디

$name = '';
if($w != 'cu') {

	if(isset($boset['nuse']) && $boset['nuse']) {
		$is_nameless = ($wr_7) ? true : false;
	} else {
		$is_nameless = ($wr_7) ? false : true;
	}

	// 이름
	if($is_nameless) {
		$name = addslashes(na_anonymity());

		// 익명처리
		sql_query(" update $write_table set wr_name = '$name', mb_id = '', wr_email = '', wr_homepage = '', wr_8 = '{$member['mb_id']}' where wr_id = '$comment_id' ");
		sql_query(" update {$g5['board_new_table']} set mb_id = '' where bo_table = '$bo_table' and wr_id = '$comment_id' ");
	}
} else {
	sql_query(" update $write_table set wr_8 = '{$wr['wr_8']}' where wr_id = '$comment_id' ");
}

// 알림설정
if (IS_NA_NOTI && $comment_id && $w === 'c' && !$boset['noti_no']) {

	$noti = array();

	// 자신의 댓글이 아닐 경우
	$re_id = ($reply_array['mb_id']) ? $reply_array['mb_id'] : $reply_array['wr_8'];
	$is_reply_noti = ($re_id && $re_id !== $member['mb_id']) ? true : false;

	$me_id = ($wr['mb_id']) ? $wr['mb_id'] : $wr['wr_8'];
	$me_name = ($name) ? $name : $wr_name;

	// 댓글을 남긴 경우
	if(($me_id && $me_id !== $member['mb_id']) || $is_reply_noti){

		// 대댓글인 경우
		if(isset($reply_array['wr_is_comment']) && $reply_array['wr_is_comment']){
			$ph_to_case = 'comment';
			$tmp_mb_id = ($re_id) ? $re_id : $me_id;
			$noti['wr_id'] = ($reply_array['wr_id']) ? $reply_array['wr_id'] : $wr_id;
			$noti['parent_subject'] = sql_real_escape_string(na_cut_text($reply_array['wr_content'], 90));

		} else { // 댓글인 경우
			$ph_to_case = 'board';
			$tmp_mb_id = $me_id;
			$noti['wr_id'] = $wr_id;
			$noti['parent_subject'] = sql_real_escape_string(na_cut_text($wr['wr_subject'], 90));
		}

		if($tmp_mb_id !== $member['mb_id']) {
			
			$noti['bo_table'] = $noti['rel_bo_table'] = $bo_table;
			$noti['wr_parent'] = $wr['wr_parent'];
			$noti['rel_wr_id'] = $comment_id;
			$noti['rel_mb_id'] = '';
			$noti['rel_mb_nick'] = $me_name;
			$noti['rel_msg'] = sql_real_escape_string(na_cut_text($wr_content, 70));
			$noti['rel_url'] = "/".G5_BBS_DIR."/board.php?bo_table=".$bo_table."&wr_id=".$wr_id."#c_".$comment_id;

			// 알림 등록
			na_noti($ph_to_case, 'comment', $tmp_mb_id, $noti);
		}

		// 원글 알림
		if($reply_array['wr_id'] && ($me_id && $me_id != $member['mb_id'])){

			// 원글을 쓴 회원이 댓글을 써서 그 댓글에 댓글을 다는 경우가 맞다면... sql에서 insert 하지 않는다.
			$ph_readed = ($re_id && !strcmp($re_id, $me_id)) ? 'Y' : '';

			if($ph_readed !== 'Y' ) {
				if(!isset($noti['bo_table'])) {
					$noti['bo_table'] = $noti['rel_bo_table'] = $bo_table;
					$noti['wr_parent'] = $wr['wr_parent'];
					$noti['rel_wr_id'] = $comment_id;
					$noti['rel_mb_id'] = '';
					$noti['rel_mb_nick'] = $me_name;
					$noti['rel_msg'] = sql_real_escape_string(na_cut_text($wr_content, 70));
					$noti['rel_url'] = "/".G5_BBS_DIR."/board.php?bo_table=".$bo_table."&wr_id=".$wr_id."#c_".$comment_id;
				}

				// 알림 등록
				na_noti('board', 'comment', $me_id, $noti);
			}
		}
	}
}

// 알림설정 해제
$boset['noti_no'] = 1;
?>