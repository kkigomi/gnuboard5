<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$stag = array();
$sql_stag = '';
if(isset($tag) && $tag) {
	$tmp_stag = array();
	$tag = clean_xss_tags(trim(strip_tags($tag)));
	$tmp_stag = explode(',', $tag);
	$gubun = (isset($sto) && $sto) ? ' and ' : ' or ';
	$z = 0;
    for ($i=0; $i < count($tmp_stag); $i++) {

        $str_stag = get_search_string(trim($tmp_stag[$i]));

		if(!$str_stag) 
			continue;

		if($z > 0)
			$sql_stag .= $gubun;

		$sql_stag .= "find_in_set('".$str_stag."', as_tag)";

		$stag[$z] = $str_stag;
		$z++;
	}

	if($sql_stag) {
		$na_sql_where .= "and ( $sql_stag )";

		$qstr .= '&amp;tag='.urlencode($tag).'&amp;sto='.$sto;

		if ($sca || $stx) { // 분류 또는 검색일 때는 통과
			;
		} else {
			$bo_notice_cnt = 0; // 공지갯수 초기화
			$row = sql_fetch(" select count(*) as cnt from $write_table where wr_is_comment = 0 $na_sql_where");
			$board['bo_count_write'] = $row['cnt'];
		}
	}
}