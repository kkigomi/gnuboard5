<?php
include_once('./_common.php');

// 열람관련함수
include_once($board_skin_path.'/read.lib.php');

function print_result($msg='') {
	echo '{ "msg": "' . $msg . '" }';
	exit;
}

if (!$is_member) {
	print_result('회원만 가능합니다.');
}

if (!($bo_table && $wr_id)) {
	print_result('값이 제대로 넘어오지 않았습니다.');
}

$ss_name = 'ss_view_'.$bo_table.'_'.$wr_id;
if (!get_session($ss_name)) {
	print_result('해당 게시물에서만 열람하실 수 있습니다.');
}

$row = sql_fetch(" select count(*) as cnt from $write_table ", FALSE);
if (!isset($row['cnt']) || !$row['cnt']) {
	print_result('존재하는 게시판이 아닙니다.');
}

$point = isset($write['as_view']) ? (int)$write['as_view'] : 0;
$is_reading_term = isset($boset['rterm']) ? (int)$boset['rterm'] : 0;
$is_reading = ($write['as_view']) ? na_check_reading($bo_table, $wr_id, $write['mb_id'], $member['mb_id'], $point, $is_reading_term) : 1;

if ($is_reading) {
	;
} else {

	//음수전환
	$point = $point * (-1);

	// 회원의 포인트가 0 이거나 작다면
	if ($member['mb_point'] + $point < 0) {
		$msg = '보유하신 포인트('.number_format($member['mb_point']).')가 없거나 모자라서 열람('.number_format($point).')이 불가합니다.\\n\\n포인트를 적립하신 후 다시 열람해 주십시오.';
		print_result($msg);
	}

	// 사용자 포인트 차감
	$is_repeat = ($is_reading_term > 0) ? 1 : 0;
	$is_read_point = na_insert_point($member['mb_id'], $point, "{$board['bo_subject']} $wr_id 열람", $bo_table, $wr_id, "열람", 0, $is_repeat);

	// 등록자 포인트 적립
	$is_repeat = ($is_read_point > 0) ? 1 : 0;
	$return_per = isset($boset['rrp']) ? (int)$boset['rrp'] : 0;
	if($write['mb_id'] && $return_per > 0) {
		$return_point = $point * (-1);
		$return_per = $return_per / 100;
		$return_point = round($return_point * $return_per);
		na_insert_point($write['mb_id'], $return_point, "{$board['bo_subject']} $wr_id 열람 적립", $bo_table, $wr_id, "열람적립", 0, $is_repeat);
	}
}

print_result();