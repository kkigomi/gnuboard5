<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 등록자 아이디 체크
$write['mb_id'] = ($write['mb_id']) ? $write['mb_id'] : $write['wr_8'];

// 이미 다운로드 받은 파일인지를 검사한 후 포인트를 차감하도록 수정
$ss_name = 'ss_down_'.$bo_table.'_'.$wr_id;
if (!get_session($ss_name)) {

	// 자신의 글, 관리자인 경우 통과
    if (($write['mb_id'] && $write['mb_id'] == $member['mb_id']) || $is_admin) {
		;
	} else if ($board['bo_download_level'] >= 1) {

		// 다운 유지
		$down_term = isset($boset['down_term']) ? (int)$boset['down_term'] : 0;

		$is_download = false;
		if($down_term) {
			$sql_term = na_sql_term($down_term, 'po_datetime'); // 기간(일수,today,yesterday,month,prev)
			$row = sql_fetch(" select count(*) as cnt from {$g5['point_table']} where mb_id = '{$member['mb_id']}' and po_rel_table = '$bo_table' and po_rel_id = '$wr_id' and po_rel_action = '다운로드' $sql_term ");
			$is_download = (isset($row['cnt']) && $row['cnt']) ? true : false;
		}

		if(!$is_download) {

			// 다운로드 포인트가 음수이고 회원의 포인트가 0 이거나 작다면
		    if ($member['mb_point'] + $board['bo_download_point'] < 0)
			    alert('보유하신 포인트('.number_format($member['mb_point']).')가 없거나 모자라서 다운로드('.number_format($board['bo_download_point']).')가 불가합니다.\\n\\n포인트를 적립하신 후 다시 다운로드 해 주십시오.');

	        // 포인트 차감하도록 수정
			na_insert_point($member['mb_id'], $board['bo_download_point'], "{$board['bo_subject']} $wr_id 파일 다운로드", $bo_table, $wr_id, "다운로드", 0, $down_term);

			// 등록자에게 포인트 적립
			if($write['mb_id']) {

				// 적립율
				$save_rate = isset($boset['save_rate']) ? (int)$boset['save_rate'] / 100 : 1;

				if($save_rate) {

					// 다운적립 포인트
					$save_point = round((int)$write['wr_1'] * $save_rate);

					// 등록자에게 적립
					na_insert_point($write['mb_id'], $save_point, "{$board['bo_subject']} $wr_id 파일 다운로드 적립", $bo_table, $wr_id, "다운적립(".$member['mb_id'].")", 0, $down_term);
				}
			}
		}
	}

    set_session($ss_name, TRUE);
}