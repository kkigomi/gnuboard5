<?php
include_once('./_common.php');

// 보드설정
if(!isset($nariya['youtube_key']) || !$nariya['youtube_key'])
	exit;

$order = isset($order) ? $order : '';
$max = isset($max) ? $max : '';
$q = isset($q) ? $q : '';

$url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&type=video';
$url .='&order='.$order.'&maxResults='.$max.'&key='.$nariya['youtube_key'].'&q='.urlencode($q);
if(isset($pg) && $pg) {
	$url .= '&pageToken='.$pg;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
$json = curl_exec($ch);
curl_close($ch);
echo $json;
exit;