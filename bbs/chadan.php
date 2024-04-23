<?php
include_once("./_common.php");

if (!$member['mb_id'])
    alert_close('회원만 이용하실 수 있습니다.');

$chadan_array = (isset($member['as_chadan']) && $member['as_chadan']) ? na_explode(',', $member['as_chadan']) : array();

if(isset($_POST['unlock']) && $_POST['unlock']) {

	$list = array();

	$chadans = array_diff($chadan_array, $_POST['chk_mb_id']);

	for($i=0;$i<count($chadans);$i++) {
		if(!trim($chadans[$i]))
			continue;

		$list[] = $chadans[$i];
	}

	$mb_chadna = implode(',', $list);

	$sql = " update {$g5['member_table']} set as_chadan = '".addslashes($mb_chadna)."' where mb_id = '{$member['mb_id']}' ";
	sql_query($sql);

	goto_url('./chadan.php');
}

include_once(G5_PATH.'/head.sub.php');

$list = array();
$z = 0;
for($i=0; $i < count($chadan_array); $i++) {

	if(!trim($chadan_array[$i]))
		continue;

	$list[$z] = get_member($chadan_array[$i]);
	$z++;
}

$total_count = $z;

$skin_file = $member_skin_path.'/chadan.skin.php';
if(is_file($skin_file)) {
	include_once($skin_file);
} else {
	echo '<div class="text-center px-3 py-5">'.str_replace(G5_PATH, '', $skin_file).' 스킨 파일이 없습니다.</div>'.PHP_EOL;
}

include_once(G5_PATH.'/tail.sub.php');