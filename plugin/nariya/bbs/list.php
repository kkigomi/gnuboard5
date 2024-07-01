<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(G5_PLUGIN_PATH.'/sphinxsearch/SphinxSearch.php');


$current_page_count = $page;
if(isset($wr_id) && !empty($wr_id)) {
    $wr_id = (int)$wr_id;
    $count_query = 'SELECT count(*) as cnt FROM '. $g5['write_prefix'].$bo_table . " WHERE wr_id > $wr_id AND wr_is_comment = 0";
    $result = sql_fetch($count_query);
    if($result) {
        $current_page_count = $result['cnt'];
    }
}

// 분류 사용 여부
$is_category = false;
$category_option = '';
if ($board['bo_use_category']) {
    $is_category = true;
    $category_href = get_pretty_url($bo_table);

    $category_option .= '<li><a href="'.$category_href.'"';
    if ($sca=='')
        $category_option .= ' id="bo_cate_on"';
    $category_option .= '>전체</a></li>';

    $categories = explode('|', $board['bo_category_list']); // 구분자가 , 로 되어 있음
    for ($i=0; $i<count($categories); $i++) {
        $category = trim($categories[$i]);
        if ($category=='') continue;
        $category_option .= '<li><a href="'.(get_pretty_url($bo_table,'','sca='.urlencode($category))).'"';
        $category_msg = '';
        if ($category==$sca) { // 현재 선택된 카테고리라면
            $category_option .= ' id="bo_cate_on"';
            $category_msg = '<span class="sound_only">열린 분류 </span>';
        }
        $category_option .= '>'.$category_msg.$category.'</a></li>';
    }
}

$sop = strtolower($sop);
if ($sop != 'and' && $sop != 'or')
    $sop = 'and';

// 분류 선택 또는 검색어가 있다면
$stx = trim($stx);
//검색인지 아닌지 구분하는 변수 초기화
$is_search_bbs = false;
$sql_search = "";

$use_sphinx = false;

if ($sca || $stx || $stx === '0') {     //검색이면

    //제목이나 본문검색인 경우에만 sphinx 검색을 사용하도록 함.
    if($stx && (strpos($sfl, "wr_subject") !== FALSE  ||  strpos($sfl, "wr_content") !== FALSE)) {
        try {
            if($_ENV['SPHINX_USE'] == 'Y') {
                $sphinx = new SphinxSearch($_ENV['SPHINX_HOST'], $_ENV['SPHINX_PORT']);
                $use_sphinx = true;
            }
        } catch (Exception $e) {
            $use_sphinx = false;
        }
    }

    $is_search_bbs = true;      //검색구분변수 true 지정

    if($use_sphinx && $sphinx->is_indexed_table($write_table)) {
        $sphinx->set_sql_search($sca, $sfl, $stx, $sop);
        $total_count = $sphinx->get_total_count($write_table);
    } else {
        $sql_search = get_sql_search($sca, $sfl, $stx, $sop);

        // 가장 작은 번호를 얻어서 변수에 저장 (하단의 페이징에서 사용)
        $sql = " select MIN(wr_num) as min_wr_num from {$write_table} ";
        $row = sql_fetch($sql);
        $min_spt = (int)$row['min_wr_num'];

        if (!$spt) $spt = $min_spt;

        $sql_search .= " and (wr_num between {$spt} and ({$spt} + {$config['cf_search_part']})) ";

        /**
         * 특정 날짜의 게시물을 검색하는 쿼리 최적화를 위한 임시 수정
         */
        $datePattern = "/INSTR\(wr_datetime, '(\d{4}-\d{2}-\d{2})'\)/";
        $numPattern = "/wr_num between (-?\d+) and \(-?\d+ \+ \d+\)/";

        if (preg_match($datePattern, $sql_search, $dateMatches) && preg_match($numPattern, $sql_search)) {
            $date = $dateMatches[1];
            $startDate = $date . " 00:00:00";
            $endDate = date('Y-m-d', strtotime($date . ' +1 day')) . " 00:00:00";

            $dateCondition = "wr_datetime >= '$startDate' AND wr_datetime < '$endDate'";

            $sql_search = $dateCondition;
            $stx = '';
        }

        // 나리야
        if($na_sql_where)
            $sql_search .= $na_sql_where;

        // 원글만 얻는다. (코멘트의 내용도 검색하기 위함)
        // 라엘님 제안 코드로 대체 http://sir.kr/g5_bug/2922
        $sql = " SELECT COUNT(DISTINCT `wr_parent`) AS `cnt` FROM {$write_table} WHERE {$sql_search} ";
        $row = sql_fetch($sql);
        $total_count = $row['cnt'];
        /*
        $sql = " select distinct wr_parent from {$write_table} where {$sql_search} ";
        $result = sql_query($sql);
        $total_count = sql_num_rows($result);
        */
    }

} else {
	if($na_sql_where) {
		$row = sql_fetch(" select count(*) as cnt from {$write_table} where wr_is_comment = 0 {$na_sql_where} ");
		$total_count = $row['cnt'];
	} else {
		$total_count = $board['bo_count_write'];
	}
}
if(G5_IS_MOBILE) {
    $page_rows = $board['bo_mobile_page_rows'];
    $list_page_rows = $board['bo_mobile_page_rows'];
} else {
    $page_rows = $board['bo_page_rows'];
    $list_page_rows = $board['bo_page_rows'];
}

if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)

// 년도 2자리
$today2 = G5_TIME_YMD;

$list = array();
$i = 0;
$notice_count = 0;
$notice_array = array();

// 공지 처리
if (!$is_search_bbs) {
    $arr_notice = explode(',', trim($board['bo_notice']));
    $from_notice_idx = ($page - 1) * $page_rows;
    if($from_notice_idx < 0)
        $from_notice_idx = 0;
    $board_notice_count = count($arr_notice);

    for ($k=0; $k<$board_notice_count; $k++) {
        if (trim($arr_notice[$k]) == '') continue;

        $row = sql_fetch(" select * from {$write_table} where wr_id = '{$arr_notice[$k]}' ");

        if (!$row['wr_id']) continue;

        $notice_array[] = $row['wr_id'];

        if($k < $from_notice_idx) continue;

        $list[$i] = get_list($row, $board, $board_skin_url, G5_IS_MOBILE ? $board['bo_mobile_subject_len'] : $board['bo_subject_len']);
        $list[$i]['is_notice'] = true;
        $list[$i]['list_content'] = $list[$i]['wr_content'];

        // 비밀글인 경우 리스트에서 내용이 출력되지 않게 글 내용을 지웁니다. 
        if (strstr($list[$i]['wr_option'], "secret")) {
            $list[$i]['wr_content'] = '';
        }

        $list[$i]['num'] = 0;
        $i++;
        $notice_count++;

        if($notice_count >= $list_page_rows)
            break;
    }
}

if ($wr_id) {
    $page = ceil(($current_page_count + 1) / $page_rows);
}

$total_page  = ceil($total_count / $page_rows);  // 전체 페이지 계산
$from_record = ($page - 1) * $page_rows; // 시작 열을 구함

// 공지글이 있으면 변수에 반영
if(!empty($notice_array)) {
    $from_record -= count($notice_array);

    if($from_record < 0)
        $from_record = 0;

    if($notice_count > 0)
        $page_rows -= $notice_count;

    if($page_rows < 0)
        $page_rows = $list_page_rows;
}

// 관리자라면 CheckBox 보임
$is_checkbox = false;
if ($is_member && ($is_admin == 'super' || $group['gr_admin'] == $member['mb_id'] || $board['bo_admin'] == $member['mb_id']))
    $is_checkbox = true;

// 정렬에 사용하는 QUERY_STRING
$qstr2 = 'bo_table='.$bo_table.'&amp;sop='.$sop;

// 0 으로 나눌시 오류를 방지하기 위하여 값이 없으면 1 로 설정
$bo_gallery_cols = $board['bo_gallery_cols'] ? $board['bo_gallery_cols'] : 1;
$td_width = (int)(100 / $bo_gallery_cols);

// 정렬
// 인덱스 필드가 아니면 정렬에 사용하지 않음
//if (!$sst || ($sst && !(strstr($sst, 'wr_id') || strstr($sst, "wr_datetime")))) {
if (!$sst) {
    if ($board['bo_sort_field']) {
        $sst = $board['bo_sort_field'];
    } else {
        $sst  = "wr_num asc, wr_reply asc";
        $sod = "";
    }
} else {
    $board_sort_fields = get_board_sort_fields($board, 1);
    if (!$sod && array_key_exists($sst, $board_sort_fields)) {
        $sst = $board_sort_fields[$sst];
    } else {
        // 게시물 리스트의 정렬 대상 필드가 아니라면 공백으로 (nasca 님 09.06.16)
        // 리스트에서 다른 필드로 정렬을 하려면 아래의 코드에 해당 필드를 추가하세요.
        // $sst = preg_match("/^(wr_subject|wr_datetime|wr_hit|wr_good|wr_nogood)$/i", $sst) ? $sst : "";
        $sst = preg_match("/^(as_down|as_view|as_choice|wr_datetime|wr_hit|wr_good|wr_nogood)$/i", $sst) ? $sst : "";
    }
}

if($sod != 'desc') {
    if ($sst != 'wr_num asc, wr_reply asc')
        $sod = 'desc';
}

if(!$sst)
    $sst  = "wr_num asc, wr_reply asc";

if ($sst) {
    $sql_order = " order by {$na_sql_orderby} {$sst} {$sod} ";
}

if ($is_search_bbs) {
    if($use_sphinx && $sphinx->is_indexed_table($write_table)) {

    } else {
        $sql = " select distinct wr_parent from {$write_table} where {$sql_search} {$sql_order} limit {$from_record}, $page_rows ";
    }

} else {
    $sql = " select wr_id from {$write_table} where wr_is_comment = 0 {$na_sql_where} ";
    if(!empty($notice_array))
        $sql .= " and wr_id not in (".implode(', ', $notice_array).") ";
    $sql .= " {$sql_order} limit {$from_record}, $page_rows ";

    // 모든 컬럼 가져오기
    $sql = " select * from {$write_table} as a
                join ($sql) as b on b.wr_id = a.wr_id ";
}

// 페이지의 공지개수가 목록수 보다 작을 때만 실행
if($page_rows > 0) {

    $wr_list = array();

    if($use_sphinx && $sphinx->is_indexed_table($write_table)) {
        $sphinx->search($write_table, $sql_order, $from_record, $page_rows);
        $wr_list = $sphinx->get_items();
    } else {
        $notice_count = !empty($notice_array) ? count($notice_array) : 0;
        $result = sql_query($sql);
        while ($row = sql_fetch_array($result)) {
            $wr_list[] = $row;
        }
    }

    $k = 0;

    foreach($wr_list as $row )
    {
        // 검색일 경우 wr_id만 얻었으므로 다시 한행을 얻는다
        if ($is_search_bbs)
            $row = sql_fetch(" select * from {$write_table} where wr_id = '{$row['wr_parent']}' ");

        $list[$i] = get_list($row, $board, $board_skin_url, G5_IS_MOBILE ? $board['bo_mobile_subject_len'] : $board['bo_subject_len']);
        if (strstr($sfl, 'subject')) {
            $list[$i]['subject'] = search_font($stx, $list[$i]['subject']);
        }
        $list[$i]['is_notice'] = false;
        $list[$i]['list_content'] = $list[$i]['wr_content'];

        // 비밀글인 경우 리스트에서 내용이 출력되지 않게 글 내용을 지웁니다. 
        if (strstr($list[$i]['wr_option'], "secret")) {
            $list[$i]['wr_content'] = '';
        }

        $list_num = $total_count - ($page - 1) * $list_page_rows - $notice_count;
        $list[$i]['num'] = $list_num - $k;

        $i++;
        $k++;
    }
}


/****** PAI 위젯: 직홍게 게시글 추가 시작: ******/
// PAI 위젯 설정 데이터 파일에서 데이터 가져오기
$widget_data_path = G5_DATA_PATH . '/nariya/widget/w-promotion-ad-insertion-pai-pc.php';
if (file_exists($widget_data_path)) {
    include $widget_data_path;
    if (isset($data)) {
        $wset_pai = $data;
    } 
} 
// 삽입 예외 게시판 ID 목록
$board_exception = isset($wset_pai['d']['board_exception']) ? explode(',', $wset_pai['d']['board_exception']) : [];

//현재 게시판이 예외 게시판 목록에 포함되지 않은 경우에만 실행
/** 랜덤 광고주의 홍보글 목록 */
$promotion_posts = array();  // 주의: 초기화를 배열로 설정하지 않으면 gellery/list.skin.php에서 에러남
if (!in_array($bo_table, $board_exception)) {
    $advertisers = isset($wset_pai['d']['advertisers']) ? $wset_pai['d']['advertisers'] : [];
    $how_many_to_display = isset($wset_pai['d']['how_many_to_display']) ? $wset_pai['d']['how_many_to_display'] : 1;
    $insert_index = isset($wset_pai['d']['insert_index']) ? (int)$wset_pai['d']['insert_index'] : 0;
    $min_cnt_for_insert_index = isset($wset_pai['d']['min_cnt_for_insert_index']) ? (int)$wset_pai['d']['min_cnt_for_insert_index'] : 5;

    if (!empty($advertisers) && $how_many_to_display > 0) {
        // 광고주 목록을 섞고, 설정된 노출갯수 만큼의 이름만 선택해서 DB 조회
        shuffle($advertisers);
        $selected_advertisers = array_slice($advertisers, 0, $how_many_to_display);
        $promotion_posts = get_promotion_posts_pai($selected_advertisers);

        // 직홍게글 삽입 인덱스 계산. 
        $notice_count = ($page == 1) ? count($notice_array) : 0; // 공지글 수 (첫 페이지에만 고려)
        $non_notice_count = count($list) - $notice_count; // 공지 제외 다른 글 수
        $positionIndex = $non_notice_count < $min_cnt_for_insert_index ? $notice_count : $notice_count + $insert_index; //다른 글이 (최소글 수)min_cnt_for_insert_index 미만일 때는 포지션을 0으로 고정.

        // 글 목록에 직홍게글 추가 (설정에 따라 1개 이상일 수 있음)
        foreach ($promotion_posts as $post) {
            $post_list = get_list($post, $board, $board_skin_url, G5_IS_MOBILE ? $board['bo_mobile_subject_len'] : $board['bo_subject_len']);
            $post_list['is_notice'] = false; // 공지글은 아닌걸로 마크
            $post_list['is_advertiser_post'] = true; // 광고주 글임을 마크
            $post_list['num'] = $positionIndex; // 지정된 위치에 삽입
            $post_list['href'] = '/promotion/'.$post['wr_id']; // 링크
            // 지정된 위치에 게시물을 삽입
            array_splice($list, $positionIndex, 0, array($post_list));
            $positionIndex++; // 다음 삽입 위치를 증가
        }
    }
}
/****** : PAI 위젯. 직홍게 게시글 추가 끝 ******/


g5_latest_cache_data($board['bo_table'], $list);

$write_pages = get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, get_pretty_url($bo_table, '', $qstr.'&amp;page='));

$list_href = '';
$prev_part_href = '';
$next_part_href = '';

if ($is_search_bbs && $spt) {
    $list_href = get_pretty_url($bo_table);

    $patterns = array('#&amp;page=[0-9]*#', '#&amp;spt=[0-9\-]*#');

    //if ($prev_spt >= $min_spt)
    $prev_spt = $spt - $config['cf_search_part'];
    if (isset($min_spt) && $prev_spt >= $min_spt) {
        $qstr1 = preg_replace($patterns, '', $qstr);
        $prev_part_href = get_pretty_url($bo_table,0,$qstr1.'&amp;spt='.$prev_spt.'&amp;page=1');
        $write_pages = page_insertbefore($write_pages, '<a href="'.$prev_part_href.'" class="pg_page pg_search pg_prev">이전검색</a>');
    }

    $next_spt = $spt + $config['cf_search_part'];
    if ($next_spt < 0) {
        $qstr1 = preg_replace($patterns, '', $qstr);
        $next_part_href = get_pretty_url($bo_table,0,$qstr1.'&amp;spt='.$next_spt.'&amp;page=1');
        $write_pages = page_insertafter($write_pages, '<a href="'.$next_part_href.'" class="pg_page pg_search pg_next">다음검색</a>');
    }
}


$write_href = '';
if ($member['mb_level'] >= $board['bo_write_level']) {
    $write_href = short_url_clean(G5_BBS_URL.'/write.php?bo_table='.$bo_table);
}

$nobr_begin = $nobr_end = "";
if (preg_match("/gecko|firefox/i", $_SERVER['HTTP_USER_AGENT'])) {
    $nobr_begin = '<nobr>';
    $nobr_end   = '</nobr>';
}

// RSS 보기 사용에 체크가 되어 있어야 RSS 보기 가능 061106
$rss_href = '';
if ($board['bo_use_rss_view']) {
    $rss_href = G5_BBS_URL.'/rss.php?bo_table='.$bo_table;
}

$stx = get_text(stripslashes($stx));
include_once($board_skin_path.'/list.skin.php');


/*********
 * 함수
 **********/
/****** PAI 위젯 직홍게 글 함수 시작:   ******/
/**
 * g5_write_promotion 테이블(직접홍보 게시판)에서 요청된 광고주들의 최신글 하나씩을 반환한다. 
 * 광고주 목록에 이름이 많아도 최대 3명의 글만 반환한다
 * @param array $advertisers 직홍게에 글을 작성한 광고주 이름 목록
 * @return array 선별된 직홍게 글 목록
 */
function get_promotion_posts_pai($advertisers) {
    $latest_posts = array();
    $tableName = get_write_table_name('promotion');

    //쿼리조회성능문제개선: db 조회가 $advertisers 만큼 반복될 수 있지만
    //애초에 이함수를 호출할때 위젯에서 설정한 갯수만큼만의 광고주만 $advertisers로 넘어오도록 하므로 필요이상으로 반복되지 않음.
    //(그렇다곤 해도 최대 3명의 글로 로직 제한)
    $iteration_count = 0;
    foreach ($advertisers as $advertiser) {
        //반복 횟수를 최대 3로 설정
        if ($iteration_count >= 3) {
            break;
        }

        $sql = "SELECT * FROM {$tableName}
                WHERE
                    wr_name = '{$advertiser}'
                    AND wr_is_comment = 0
                ORDER BY wr_datetime DESC LIMIT 1";
        $result = sql_query($sql);
        while ($row = sql_fetch_array($result)) {
            $latest_posts[] = $row;
        }

        $iteration_count++;
    }

    // 광고주 별 최신글 모음 목록을 반환
    return $latest_posts;
}
/****** : PAI 위젯 직홍게 끝   ******/
