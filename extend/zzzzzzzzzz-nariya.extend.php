<?php
// 개별 페이지 접근 불가
if (!defined('_GNUBOARD_')) 
	exit;

// 나리야 빌더에서만 작동
if (!defined('_NARIYA_')) 
	return;

// 상수 선언
define('NA_DIR', 'nariya');
define('NA_URL', G5_PLUGIN_URL.'/'.NA_DIR);
define('NA_PATH', G5_PLUGIN_PATH.'/'.NA_DIR);
define('NA_DATA_URL', G5_DATA_URL.'/'.NA_DIR);
define('NA_DATA_PATH', G5_DATA_PATH.'/'.NA_DIR);
define('NA_ADMIN_URL', NA_URL.'/'.G5_ADMIN_DIR);
define('NA_ADMIN_PATH', NA_PATH.'/'.G5_ADMIN_DIR);

require_once NA_PATH.'/_nariya.php';

// 원본 교체 파일 체크
$is_bbs_replace = $is_shop_replace = $is_adm_replace = $is_file_replace = false;
if($file_id) {
	if(!empty($replace_bbs_file) && in_array($file_id, $replace_bbs_file)) {
		$is_file_replace = $is_bbs_replace = true;
	} else if(!empty($replace_shop_file) && in_array($file_id, $replace_shop_file)) {
		$is_file_replace = $is_shop_replace = true;
	} else if(!empty($replace_adm_file) && in_array($file_id, $replace_adm_file)) {
		$is_file_replace = $is_adm_replace = true;
	}
}

if($is_file_replace) {

	$replace_file = '';
	if($is_bbs_replace) {
		$replace_file = NA_PATH.'/bbs/'.$pset['file'].'.php';
	} if($is_shop_replace) {
		$replace_file = NA_PATH.'/shop/'.$pset['file'].'.php';
	} if($is_adm_replace) {
		$replace_file = NA_ADMIN_PATH.'/'.$pset['file'].'.php';
	}

	if ($replace_file && is_file($replace_file)) {
		// Extend 재실행
		foreach ($extend_file as $efile) {
			require_once G5_EXTEND_PATH.'/'.$efile;
		}		

		unset($efile);
		unset($extend_file);

		//	if($is_member && !$is_admin && (!defined("G5_CERT_IN_PROG") || !G5_CERT_IN_PROG) && $config['cf_cert_use'] <> 0 && $config['cf_cert_req']) { // 본인인증이 필수일때
		//		if (empty($member['mb_certify'])) { // 본인인증이 안된 계정일때
		//			goto_url(G5_BBS_URL."/member_cert_refresh.php");
		//		}
		//	}

		ob_start();

		// 자바스크립트에서 go(-1) 함수를 쓰면 폼값이 사라질때 해당 폼의 상단에 사용하면
		// 캐쉬의 내용을 가져옴. 완전한지는 검증되지 않음
		header('Content-Type: text/html; charset=utf-8');
		$gmnow = gmdate('D, d M Y H:i:s') . ' GMT';
		header('Expires: 0'); // rfc2616 - Section 14.21
		header('Last-Modified: ' . $gmnow);
		header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
		header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1

		run_event('common_header');

		require_once $replace_file;

		exit;		
	}
}
