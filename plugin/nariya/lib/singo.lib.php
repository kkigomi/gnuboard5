<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 신고글 구분
// sg_flag = 0 : 게시물(글, 댓글)
// sg_flag = 1 : 상품후기
// sg_flag = 2 : 상품후기

// 신고 타입
//$singo_type = array();
//$singo_type[1] = '스팸홍보/도배글';
//$singo_type[2] = '영리목적/홍보성';
//$singo_type[3] = '불법정보/불법촬영물';
//$singo_type[4] = '음란성/선정성';
//$singo_type[5] = '욕설/혐오/인신공격';
//$singo_type[6] = '명예훼손/저작권침해';
//$singo_type[7] = '개인정보노출';
//$singo_type[9] = '기타';

// 신고 타입
$singo_type = array();
$singo_type[1] = '스팸홍보/도배글';
$singo_type[2] = '영리목적/홍보성';
$singo_type[3] = '불법정보/불법촬영물';
$singo_type[4] = '음란성/선정성';
$singo_type[5] = '욕설/혐오/인신공격';
$singo_type[6] = '명예훼손/저작권침해';
$singo_type[7] = '개인정보노출';
$singo_type[8] = '반말/욕설/비아냥';
$singo_type[9] = '갈등조장/회원분란/회원기만';
$singo_type[10] = '게시글반복/도배';
$singo_type[11] = '스팸/바이럴';
$singo_type[12] = '자유게시판내 질문글';
$singo_type[13] = '구걸/금전거래/중고거래';
$singo_type[14] = '차별 - 인종/성/남녀';
$singo_type[15] = '정치게시판 분리요청';
$singo_type[16] = '커뮤니티 비방';
$singo_type[17] = '범죄모의기도/실정법위반';
$singo_type[100] = '기타';

// 신고 내역
function na_singo_array($singo, $sg_table=''){
	global $g5, $member, $is_guest;

	$list = array();

	if($is_guest)
		return $list;

	switch($singo) {
		case 'write' : $sg_flag = 0; break;
		case 'iuse'  : $sg_flag = 1; break;
		case 'iqa'   : $sg_flag = 2; break;
		default		 : return $list; break;

	}

	$sql_sg = ($sg_table) ? "and sg_table = '".$sg_table."'" : "";
	$result = sql_query(" select sg_id from {$g5['na_singo']} where mb_id = '{$member['mb_id']}' and sg_flag = '{$sg_flag}' $sql_sg ");
	while ($row = sql_fetch_array($result)) {

		if(!isset($row['sg_id']) || !$row['sg_id'])
			continue;

		$list[] = $row['sg_id'];
	}

	if(count($list) > 0)
		$list = array_unique($list);

	return $list;
}