<?php
include_once("./_common.php");

// 알림 - https://sir.kr/g5_plugin/6259 기반으로 수정

if (!$is_member) {
    alert('회원이시라면 로그인 후 이용해 주십시오.', G5_BBS_URL."/login.php?url=".urlencode("{$_SERVER['REQUEST_URI']}"));
}

set_session('noti_delete_token', $token = uniqid(time()));

$read = isset($_REQUEST['read']) ? preg_replace('/[^a-zA-Z0-9]+/', '', $_REQUEST['read']) : '';

switch ($read) {
    case "y" :
        $g5['title'] = "읽은 알림";
		$is_read = 'y';
        break;
    case "n" :
        $g5['title'] = "안 읽은 알림";
		$is_read = 'n';
        break;
    default :
        $g5['title'] = "전체 알림";
		$is_read = 'all';
		break;
}

include_once(G5_PATH.'/head.php');

$page_rows = (G5_IS_MOBILE) ? $config['cf_mobile_page_rows'] : $config['cf_page_rows'];

if (!$page) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $page_rows; // 시작 열을 구함

list($total_count, $list) = na_noti_list($page_rows, '', $from_record, $is_read, false);

$total_page  = ceil($total_count / $page_rows);  // 전체 페이지 계산

$list = (is_array($list)) ? $list : array();

$list_cnt = count($list);

$query_string = preg_replace("/&?page\=\d+/", "", clean_query_string($_SERVER['QUERY_STRING']));
$write_pages = get_paging($page_rows, $page, $total_page, "{$_SERVER['PHP_SELF']}?$query_string&amp;page=");

$skin_file = $member_skin_path.'/noti.skin.php';
if(is_file($skin_file)) {
	include_once($skin_file);
} else {
	echo '<div class="text-center px-3 py-5">'.str_replace(G5_PATH, '', $skin_file).' 스킨 파일이 없습니다.</div>'.PHP_EOL;
}

include_once(G5_PATH.'/tail.php');