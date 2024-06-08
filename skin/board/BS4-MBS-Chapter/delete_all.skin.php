<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 챕터 삭제하기
$tmp_arr = array();

for($i=0; $i < $chk_count; $i++) {

	$tmp_arr[] = $tmp_array[$i];

	//챕터 불러오기
	$row = sql_fetch(" select wr_num from $write_table where wr_id = '$tmp_array[$i]' ");
	if(isset($row['wr_num']) && $row['wr_num']) {
		$result = sql_query(" select wr_id from $write_table where wr_is_comment = '0' and wr_reply <> '' and wr_num = '{$row['wr_num']}'");
		if($result) {
			while ($row1 = sql_fetch_array($result)) {
				$tmp_arr[] = $row1['wr_id'];
			}
		}
	}
}

// wr_id 값이 낮은순으로 정렬
sort($tmp_arr);

$chk_count = count($tmp_arr);