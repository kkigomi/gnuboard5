<?php
include_once('./_common.php');

if($is_guest) {
	alert_close('회원만 이용하실 수 있습니다.');
}

// 설정 저장-------------------------------------------------------
$mode = isset($mode) ? $mode : '';
if ($mode == "u") {
	$del_mb_img = isset($_POST['del_mb_img']) ? $del_mb_img : '';
	na_myphoto_upload($member['mb_id'], $del_mb_img, $_FILES); //Save

	goto_url(G5_BBS_URL.'/myphoto.php');
}
//--------------------------------------------------------------------

$mb_dir = substr($member['mb_id'],0,2);

$is_photo = (is_file(G5_DATA_PATH.'/member_image/'.$mb_dir.'/'.$member['mb_id'].'.gif')) ? true : false;

$photo_width = (isset($config['cf_member_img_width']) && $config['cf_member_img_width']) ? $config['cf_member_img_width'] : 80;
$photo_height = (isset($config['cf_member_img_height']) && $config['cf_member_img_height']) ? $config['cf_member_img_height'] : 80;

$g5['title'] = '내 사진 관리';
include_once(G5_PATH.'/head.sub.php');

$skin_file = $member_skin_path.'/myphoto.skin.php';
if(is_file($skin_file)) {
	include_once($skin_file);
} else {
	echo '<div class="text-center px-3 py-5">'.str_replace(G5_PATH, '', $skin_file).' 스킨 파일이 없습니다.</div>'.PHP_EOL;
}

include_once(G5_PATH.'/tail.sub.php');
