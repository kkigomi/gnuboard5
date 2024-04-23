<?php
include_once('./_common.php');

if (!$is_member)
    alert_close('회원만 조회하실 수 있습니다.');

if (isset($_REQUEST['bo_id']) && ! is_array($_REQUEST['bo_id'])) {
    $bo_id = preg_replace('/[^a-z0-9_]/i', '', trim($_REQUEST['bo_id']));
    $bo_id = substr($bo_id, 0, 20);
} else {
    $bo_id = '';
}

$bo_qstr = ($bo_id) ? 'bo_id='.$bo_id.'&amp;' : '';

// 삭제하기
if (isset($_REQUEST['del']) && $_REQUEST['del'] == '1') {

	$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;

	if($id) {
		sql_query(" delete from {$g5['na_singo']} where id = '$id' and mb_id = '{$member['mb_id']}' ", false);
	}

	goto_url('./singo.php?'.$bo_qstr.'page='.$page);
}

$g5['title'] = get_text($member['mb_nick']).'님의 신고글';
include_once(G5_PATH.'/head.sub.php');

// 게시판 정리
$bo_list = array();
$result = sql_query(" select sq_table from {$g5['na_singo']} where mb_id = '{$member['mb_id']}' and sg_flag = '0' group by sg_table ");
$z = 0;
for ($i=0; $row=sql_fetch_array($result); $i++) {

	if(!isset($row['sg_table']) || !$row['sg_table'])
		continue;

    $row2 = sql_fetch(" select bo_subject from {$g5['board_table']} where bo_table = '{$row['sg_table']}' ");

	$bo_list[$z]['bo_table'] = $row['sg_table'];
	$bo_list[$z]['bo_subject'] = $row2['bo_subject'];
	$z++;
}

$bo_sql = ($bo_id) ? " and sg_table = '{$bo_id}' and sg_flag = '0' " : "";
$sql_common = " from {$g5['na_singo']} where mb_id = '{$member['mb_id']}' $bo_sql ";
$sql_order = " order by id desc ";

$sql = " select count(*) as cnt $sql_common ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = G5_IS_MOBILE ? $config['cf_mobile_page_rows'] : $config['cf_new_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$list = array();

$sql = " select *
            $sql_common
            $sql_order
            limit $from_record, $rows ";
$result = sql_query($sql);

$num = $total_count - ($page - 1) * $rows;

for ($i=0; $row=sql_fetch_array($result); $i++) {

	$list[$i] = $row;

	if($row['sg_flag'] == "1") {
		$row1 = sql_fetch(" select * from `{$g5['g5_shop_item_use_table']}` where it_id = '{$row['sg_table']}' and is_id = '{$row['sg_id']}' ");
		if(isset($row1['is_id']) && $row1['is_id']) {
			$imgs = get_editor_image($row1['is_content'], false);
			$img = isset($imgs[1][0]) ? $imgs[1][0] : '';
			$content = cut_str(na_get_text('[상품후기] 작성자 : '.$row1['is_name'].', 후기 내용 : '.$row1['is_subject'].' '.$row1['is_content']), 100, '…');
		} else {
			$img = '';
			$content = '상품후기 글이 존재하지 않습니다.';
		}
	} else if($row['sg_flag'] == "2") {



	} else {
		
		// 게시판 제목
		$row1 = sql_fetch(" select bo_subject from {$g5['board_table']} where bo_table = '{$row['sg_table']}' ");
		if (!isset($row1['bo_subject']) || !$row1['bo_subject']) 
			$row1['bo_subject'] = '게시판 없음';
	    
		// 게시물
		$tmp_write_table = $g5['write_prefix'] . $row['sg_table'];
		$row2 = sql_fetch(" select wr_subject, wr_is_comment, wr_content, wr_name from $tmp_write_table where wr_id = '{$row['sg_id']}' ");

		$img = '';
	    if (!isset($row2['wr_subject'])) {
			$content = '글이 존재하지 않습니다.';
		} else if (strstr($row2['wr_option'], 'secret')) {
			$content = '비밀글로 내용을 확인할 수 없습니다.';
		} else if ($row2['wr_is_comment']) {
			$content = '작성자 : '.$row2['wr_name'].', 댓글 : '.$row2['wr_content'];
		} else {
			$content = '작성자 : '.$row2['wr_name'].', 게시물 : '.$row2['wr_subject'].' '.$row2['wr_content'];
			$img = na_wr_img($row['sg_table'], $row2);
		}

		$content = cut_str(na_get_text('['.$row1['bo_subject'].'] '.$content), 100, '…');
	}

	$sg_type = $row['sg_type'];
    $list[$i]['reason'] = $singo_type[$sg_type];

	$num = $num - $i;
	$list[$i]['num'] = $num;
	$list[$i]['img'] = $img;
	$list[$i]['content'] = $content;
    $list[$i]['del_href'] = './singo.php?'.$bo_qstr.'del=1&amp;id='.$row['id'].'&amp;page='.$page;
}

$skin_file = $member_skin_path.'/singo.skin.php';
if(is_file($skin_file)) {
	include_once($skin_file);
} else {
	echo '<div class="text-center px-3 py-5">'.str_replace(G5_PATH, '', $skin_file).' 스킨 파일이 없습니다.</div>'.PHP_EOL;
}

include_once(G5_PATH.'/tail.sub.php');