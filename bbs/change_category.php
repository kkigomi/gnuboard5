<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(!$is_admin)
    alert('접근 권한이 없습니다.', G5_URL);

// 4.11
//@include_once($board_skin_path.'/delete_all.head.skin.php');

$wr_id_list = '';
if ($wr_id)
    $wr_id_list = $wr_id;
else {
    $comma = '';

    $count_chk_wr_id = (isset($_POST['chk_wr_id']) && is_array($_POST['chk_wr_id'])) ? count($_POST['chk_wr_id']) : 0;

    for ($i=0; $i<$count_chk_wr_id; $i++) {
        $wr_id_val = isset($_POST['chk_wr_id'][$i]) ? preg_replace('/[^0-9]/', '', $_POST['chk_wr_id'][$i]) : 0;
        $wr_id_list .= $comma . $wr_id_val;
        $comma = ',';
    }
}

$chk_count = count($_POST['chk_wr_id']);

if($chk_count > (G5_IS_MOBILE ? $board['bo_mobile_page_rows'] : $board['bo_page_rows']))
    alert('올바른 방법으로 이용해 주십시오.');


// 댓글 작성을 위한 관리자 정보
$id = $member['mb_id'];
$name = $member['mb_nick'];
$email = $member['mb_email'];
$homepage = $member['mb_homepage'];

// 카테고리 변경된는 원글들의 정보 조회
$sql = "select wr_id, wr_num, wr_comment, ca_name
          from $write_table
         where wr_id in ({$wr_id_list})";
$result = sql_query($sql);


for ($i = 0; $row = sql_fetch_array($result); $i++) {
    // 동일 카테고리로 변경은 처리할 필요 없음
    if ($post_btn_submit === $row['ca_name']) {
        continue;
    }

    // 카테고리 변경된 게시물에 코멘트 작성
    $sql = " insert into $write_table
                set ca_name = '$post_btn_submit',
                     wr_option = '',
                     wr_num = '{$row['wr_num']}',
                     wr_reply = '',
                     wr_parent = '{$row['wr_id']}',
                     wr_is_comment = 1,
                     wr_comment = 1,
                     wr_comment_reply = '',
                     wr_subject = '',
                     wr_content = '게시판 관리자가 분류를 변경했습니다. {$row['ca_name']} > {$post_btn_submit}',
                     mb_id = '$id',
                     wr_password = '',
                     wr_name = '$name',
                     wr_email = '$email',
                     wr_homepage = '$homepage',
                     wr_datetime = '".G5_TIME_YMDHIS."',
                     wr_last = '',
                     wr_ip = '{$_SERVER['REMOTE_ADDR']}'";
    sql_query($sql);

    // 카테고리 변경
    $comment_cnt = intval($row['wr_comment']) + 1; // 댓글 개수 1 증가
    $sql = " update $write_table
                set ca_name = '$post_btn_submit',
                    wr_comment = $comment_cnt
              where wr_id = {$row['wr_id']}";
    sql_query($sql);
}

// 분류별 게시물수 캐시 생성
na_cate_cnt($bo_table, $board, 1);

// 분류별 새게시물수 캐시 생성
na_cate_new($bo_table, $board, 1);

goto_url(short_url_clean(G5_HTTP_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;page='.$page.$qstr));
