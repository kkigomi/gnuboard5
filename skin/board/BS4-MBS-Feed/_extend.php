<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once($board_skin_path.'/feed.lib.php');

$is_feed_popup = '';
if(isset($bo_table) && $bo_table && isset($wr_id) && $wr_id) { //글내용일 때
	if(!$is_admin && isset($write['wr_1']) && $write['wr_1'] == "1") {
		include_once(NA_PATH.'/bbs/alert.php');
		alert('접근권한이 없습니다.');
	}

	if(isset($boset['move']) && $boset['move']) {
		$no_arr = explode(",", trim($board['bo_notice']));
		$no_cnt = is_array($no_arr) ? count($no_arr) : 0;
		$no_post = ($no_cnt && in_array($wr_id, $no_arr)) ? false : true;

		if($no_post) {
			ob_start();
			include_once($board_skin_path.'/_popup.php');
			$is_feed_popup = ob_get_contents();
			ob_end_clean();
		}
	}
}

// 캐시체크
$feed_time = (int)(strtotime(G5_SERVER_TIME) - strtotime($board['bo_1']));
$feed_cache = isset($boset['cache']) ? (int)$boset['cache'] * 60 : 1800;

if($feed_time >= $feed_cache) {
	// 캐시시간 업데이트
	sql_query(" update {$g5['board_table']} set bo_1 = '".G5_SERVER_TIME."' where bo_table = '$bo_table' ");

	// Load SimplePie
	include_once($board_skin_path.'/SimplePie/autoloader.php');

	// 필터링
	$fout_str = isset($boset['fout']) ? trim(stripslashes($boset['fout'])) : '';
	$fout = ($fout_str) ? na_explode(",", $fout_str) : array();
	$fout_cnt = count($fout);

	// 최고관리자 정보
	$cfa = get_member($config['cf_admin'], 'mb_id, mb_nick, mb_password');

	$fraw = array();
	$rss = array();
	$feeds = isset($boset['feed']) ? explode("\n", stripslashes($boset['feed'])) : array();
	$feeds_cnt = count($feeds);
	for($i=0; $i < $feeds_cnt; $i++) {
		$feeds[$i] = isset($feeds[$i]) ? trim($feeds[$i]) : '';

		if(!$feeds[$i]) 
			continue;

		// 피드정보
		$fraw = na_query($feeds[$i]);
		
		if(isset($fraw['name']) && $fraw['name']) {

			$arr = na_explode(",", $fraw['name']);
			$cmb_id = isset($arr[0]) ? trim($arr[0]) : '';
			$cmb_opt = isset($arr[1]) ? $arr[1] : '';

			if($cmb_opt) { //회원

				$fmb = get_member($cmb_id, 'mb_id, mb_nick, mb_password');

				if(!isset($fmb['mb_id']) || !$fmb['mb_id'])
					continue;

				$fraw['mb_id'] = $fmb['mb_id'];
				$fraw['name'] = $fmb['mb_nick'];
				$fraw['password'] = $fmb['mb_password'];
			} else {
				$fraw['mb_id'] = '';
				$fraw['name'] = $cmb_id;
				$fraw['password'] = $cfa['mb_password'];
			}
		} else {
			$fraw['mb_id'] = $cfa['mb_id'];
			$fraw['name'] = $cfa['mb_nick'];
			$fraw['password'] = $cfa['mb_password'];
		}

		// 필터링
		$fin_str = isset($fraw['filter']) ? trim($fraw['filter']) : '';
		$fin = ($fin_str) ? na_explode(",", $fin_str) : array();
		$fin_cnt = count($fin);

		$fraw['feed'] = isset($fraw['feed']) ? $fraw['feed'] : '';
		$fraw['ca_name'] = isset($fraw['ca_name']) ? $fraw['ca_name'] : '';
		$fraw['user'] = isset($fraw['user']) ? $fraw['user'] : '';
		$fraw['url'] = isset($fraw['url']) ? $fraw['url'] : '';

		if($fraw['feed'] == 'youtube') { // 유튜브

			$feed = na_feed_json($fraw);

			$count = is_array($feed->items) ? count($feed->items) : 0;

			for($j=0; $j < $count; $j++){ 

				$item = $feed->items[$j]; 

				$vid = trim($item->id->videoId);
				$pid = trim($item->id->playlistId);

				if($pid) 
					$vid = $pid;

				$fsubject = trim($item->snippet->title);

				if(!$vid || !$fsubject) 
					break;

				$permalink = ($pid) ? 'https://www.youtube.com/embed?listType=playlist&list='.$vid : 'https://youtu.be/'.$vid;

				$row = sql_fetch("select wr_id from $write_table where wr_link1 = '".addslashes($permalink)."'");

				if(isset($row['wr_id']) && $row['wr_id']) 
					break;

				//$pimg = trim($item->snippet->thumbnails->high->url);
				$fcontent = trim($item->snippet->description);

				// 날짜
				$fdate = strtotime(trim($item->snippet->publishedAt));

				if($fin_cnt) { // 수집 필터링
					if(!feed_filter($fsubject, $fcontent, $fin, $fin_cnt)) 
						continue;
				}

				if($fout_cnt) { // 제외 필터링
					if(feed_filter($fsubject, $fcontent, $fout, $fout_cnt)) 
						continue;
				}

				$fcontent = ($fcontent) ? $fcontent : $fsubject;

				$rss[] = array( 'id'=>$fdate, 
								'link'=>$permalink, 
								'subject'=>$fsubject, 
								'content'=>$fcontent, 
								'ca_name'=>$fraw['ca_name'],
								'mb_id'=>$fraw['mb_id'],
								'name'=>$fraw['name'],
								'password'=>$fraw['password']
						);
				}

		} else if($fraw['feed'] == 'vimeo') { // 비메오

			if(!$fraw['user']) 
				continue;

			$feed = na_feed_json($fraw);

			$count = count($feed);
			for($j=0; $j < $count; $j++){

				$vid = $feed[$j]['id'];

				$fsubject = trim($feed[$j]['title']);

				if(!$vid || !$fsubject) 
					break;

				$permalink = trim($feed[$j]['url']);

				$row = sql_fetch("select wr_id from $write_table where wr_link1 = '".addslashes($permalink)."'");

				if(isset($row['wr_id']) && $row['wr_id']) 
					break;

				$fcontent = trim($feed[$j]['description']);

				// 날짜
				$fdate = strtotime(trim($feed[$j]['upload_date']));

				if($fin_cnt) { // 수집 필터링
					if(!feed_filter($fsubject, $fcontent, $fin, $fin_cnt)) continue;
				}

				if($fout_cnt) { // 제외 필터링
					if(feed_filter($fsubject, $fcontent, $fout, $fout_cnt)) continue;
				}

				$fcontent = ($fcontent) ? $fcontent : $fsubject;

				$rss[] = array( 'id'=>$fdate, 
								'link'=>$permalink, 
								'subject'=>$fsubject, 
								'content'=>$fcontent, 
								'ca_name'=>$fraw['ca_name'],
								'mb_id'=>$fraw['mb_id'],
								'name'=>$fraw['name'],
								'password'=>$fraw['password']
						);
			}

		} else { 
			if(!$fraw['url'])
				continue;

			// RSS & ATOM - Use the long syntax
			$feed = new SimplePie();
			$feed->set_feed_url($fraw['url']);

			// Remove these tags from the list
			$strip_htmltags = $feed->strip_htmltags;
			array_splice($strip_htmltags, array_search('iframe', $strip_htmltags), 1);
			$feed->strip_htmltags($strip_htmltags);

			$feed->init();
			$feed->handle_content_type();

			$items = $feed->get_items();
			foreach($items as $item) {

				$permalink = html_entity_decode($item->get_permalink(), ENT_QUOTES, 'UTF-8');

				if(!$permalink) 
					break;

				$row = sql_fetch("select wr_id from $write_table where wr_link1 = '".addslashes($permalink)."'");

				if(isset($row['wr_id']) && $row['wr_id']) 
					break;

				$fsubject = feed_conv_content(html_entity_decode($item->get_title(), ENT_QUOTES, 'UTF-8'));
				$fcontent = feed_conv_content(html_entity_decode($item->get_content(), ENT_QUOTES, 'UTF-8'));

				$fdate = strtotime($item->get_date());

				if($fin_cnt) { // 수집 필터링
					if(!feed_filter($fsubject, $fcontent, $fin, $fin_cnt)) 
						continue;
				}

				if($fout_cnt) { // 제외 필터링
					if(feed_filter($fsubject, $fcontent, $fout, $fout_cnt)) 
						continue;
				}

				$fcontent = ($fcontent) ? na_feed_video($fcontent) : $fsubject;

				$rss[] = array( 'id'=>$fdate, 
								'link'=>$permalink, 
								'subject'=>$fsubject, 
								'content'=>$fcontent, 
								'ca_name'=>$fraw['ca_name'],
								'mb_id'=>$fraw['mb_id'],
								'name'=>$fraw['name'],
								'password'=>$fraw['password']
						);
			}
		}

		unset($feed);
	}

	$rss_cnt = count($rss);
	if($rss_cnt) {
		// 날짜순으로 정렬
		$rss = na_sort($rss, 'id');
		for($i=0; $i < $rss_cnt; $i++) {
			feed_update_board($rss[$i]['ca_name'], $rss[$i]['subject'], $rss[$i]['content'], $rss[$i]['link'], $rss[$i]['mb_id'], $rss[$i]['name'], $rss[$i]['password']);
		}

		// 게시판의 글 수
		$row = sql_fetch(" select count(*) as cnt from $write_table where wr_is_comment = 0 ");
		$bo_count_write = $row['cnt'];

		// 업데이트
	    sql_query(" update {$g5['board_table']} set bo_count_write = '{$bo_count_write}' where bo_table = '{$bo_table}' ");

		$board['bo_count_write'] = $bo_count_write;
	}

	unset($rss);
}

// SQL 추가구문
if($is_admin) {
	$na_sql_orderby .= "wr_1 asc,"; // 수집제외글은 뒤로 보냄
} else {
	$na_sql_where .= "and wr_1 <> '1'"; // 수집제외글은 빼고 출력
	if ($sca || $stx) { // 분류 또는 검색일 때는 통과
		;
	} else {
		// 수집제외글 카운팅 후 전체글수에 반영
		$row = sql_fetch(" select count(*) as cnt from $write_table where wr_is_comment = 0 and wr_1 = '1'");
		$board['bo_count_write'] = $board['bo_count_write'] - $row['cnt'];
	}
}