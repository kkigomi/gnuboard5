<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

function feed_filter($subject, $content, $filter, $cnt) {

	if(!$cnt) return 0;

	$content = trim(get_text($subject.' '.$content));

	if(!$content) return 0;

	for ($i=0; $i < $cnt; $i++) {

		$str = trim($filter[$i]);

		if(!$str) continue;

		// 필터링
		$pos = stripos($content, $str);

		if ($pos !== false) 
			return 1;
	}

    return 0;
}

// 내용 변환
function feed_conv_content($content) {
	$source = array();
	$target = array();

	$source[] = "//";
	$target[] = "";
	$source[] = "/\n/";
	$target[] = "";

	$content = preg_replace($source, $target, $content);

    return $content;
}

// 피드 등록
function feed_update_board($fcate, $fsubject, $fcontent, $flink, $mb_id, $name, $password) {
	global $g5, $board, $bo_table, $write_table, $boset;

	$flink = na_get_text($flink);
	$fsubject = na_get_text($fsubject);

	if(!$flink || !$fsubject) 
		return;

	// 서버저장
	if(isset($boset['na_save_image']) && $boset['na_save_image']) {
		list($cnt, $fcontent) = na_content_image($fcontent);
	}

	// 이미지
	$img = '';
	if(IS_NA_BBS) {
		$img = na_video_img(na_video_info(trim(strip_tags($flink))));
		if($img) {
			$img = str_replace(G5_PATH, G5_URL, $img);
		} else {
			// 본문 동영상 
			if(preg_match_all("/{(동영상|video)\:([^}]*)}/is", $fcontent, $match)) {
				$vimg = na_video_img(na_video_info(trim(strip_tags($match[2][0]))));
				if($vimg && $vimg != 'none') {
					$img = str_replace(G5_PATH, G5_URL, $vimg);
				}
			}
			// 본문 이미지
			if(!$img) {
				$matches = get_editor_image($fcontent, false);
				$img = $matches[1][0];
			}
		}

		$img = str_replace(G5_URL, "./", $img);
		$img = "as_thumb = '".addslashes($img)."',";
	}

	$fwr_num = get_next_num($write_table);

	$sql = " insert into $write_table
                set wr_num = '$fwr_num',
                    ca_name = '".addslashes($fcate)."',
                    wr_option = 'html1,,',
					wr_subject = '".addslashes($fsubject)."',
                    wr_content = '".addslashes($fcontent)."',
                    wr_link1 = '".addslashes($flink)."',
                    mb_id = '{$mb_id}',
					wr_password = '{$password}',
                    wr_name = '".addslashes($name)."',
                    $img
					wr_datetime = '".G5_TIME_YMDHIS."',
                    wr_ip = '{$_SERVER['REMOTE_ADDR']}'
                    ";
    sql_query($sql);

    $wr_id = sql_insert_id();

    // 부모 아이디에 UPDATE
    sql_query(" update $write_table set wr_parent = '$wr_id' where wr_id = '$wr_id' ");

    // 새글 등록
    if(isset($boset['db_new']) && $boset['db_new']) {
		sql_query(" insert into {$g5['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id) values ( '{$bo_table}', '{$wr_id}', '{$wr_id}', '".G5_TIME_YMDHIS."', '{$mb_id}' ) ");
	}
	
	// 포인트 등록
	if($mb_id && $board['bo_write_point']) {
        insert_point($mb_id, $board['bo_write_point'], "{$board['bo_subject']} {$wr_id} 글쓰기", $bo_table, $wr_id, '쓰기');
	}

	// 경험치
	if(IS_NA_XP && $mb_id && isset($boset['xp_write']) && $boset['xp_write']) {
		na_insert_xp($mb_id, $boset['xp_write'], "{$board['bo_subject']} {$wr_id} 글쓰기", $bo_table, $wr_id, '쓰기');
	}

}

function na_feed_json($fraw) {
	global $nariya;

	$fraw['feed'] = isset($fraw['feed']) ? $fraw['feed'] : '';

	if($fraw['feed'] == "youtube") {
		$rows = isset($fraw['rows']) ? (int)$fraw['rows'] : 10;
		$url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults='.$rows;
		$url .= isset($fraw['channel']) ? '&channelId='.$fraw['channel'] : '&type=video';
		if(isset($fraw['order']) && $fraw['order'])
			$url .= '&order='.$fraw['order'];

		if(isset($fraw['region']) && $fraw['region'])
			$url .= '&regionCode='.$fraw['region'];

		if(isset($fraw['q']) && $fraw['q']) {
			$qs = explode(",", $fraw['q']);
			$stx = '';
			for($i=0; $i < count($qs); $i++) {
				if($i > 0) $stx .= "+";
				$stx .= urlencode($qs[$i]);
			}
			$url .= '&q='.$stx;
		}

		$url .= isset($nariya['youtube_key']) ? '&key='.$nariya['youtube_key'] : '';

	} else if($fraw['feed'] == "vimeo") {

		if(!isset($fraw['user']) || !$fraw['user'])
			return;

		$url = 'http://vimeo.com/api/v2/'.$fraw['user'].'/videos.json';
	} else {
		return;
	}

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
	if($fraw['feed'] == "youtube") {
		$json = json_decode(curl_exec($ch));
	} else {
	    $json = json_decode(curl_exec($ch), true);
	}
    curl_close($ch);

    return $json;
}

// 동영상 변환
function na_conv_feed_video($url) {

	// 비디오, 오디오 체크
	$ext = array("mp4", "m4v", "f4v", "mov", "flv", "webm", "acc", "m4a", "f4a", "mp3", "ogg", "oga", "rss");

	$file = na_file_info($url);
	
	$str = '';
	if(isset($file['ext']) && $file['ext'] && in_array($file['ext'], $ext)) {
		$str = '<p>{video:'.$url.'|file=1}</p>';
	} else {
		$video = na_video_info($url);
		if(isset($video['vid']) && $video['vid']) {
			$str = '<p>{video:'.$url.'}</p>';
		}
	}

	return $str;
}

// 동영상 iframe 얻기
function na_feed_video($contents) {

	if(!$contents)
        return;

	preg_match_all("/<iframe([^>]*)>/iS", $contents, $matches);

    if(empty($matches))
        return $contents;

	$match_cnt = (isset($matches[1]) && is_array($matches[1])) ? count($matches[1]) : 0;
    for($i=0; $i<$match_cnt; $i++) {
		$video_tag = isset($matches[0][$i]) ? $matches[0][$i] : '';
		$video = isset($matches[1][$i]) ? na_query($matches[1][$i]) : '';
		if(isset($video['src']) && $video['src']) {
			$video_str = na_conv_feed_video($video['src']);
			if($video_str) {
				$contents = str_replace($video_tag, $video_str, $contents);
			}
		}
	}

    return $contents;
}