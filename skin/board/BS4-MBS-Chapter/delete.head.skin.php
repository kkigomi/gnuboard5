<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 신고 글 삭제 불가
if(!$is_admin && IS_NA_BBS) {
	if(isset($boset['na_shingo']) && $boset['na_shingo'] && isset($write['as_type']) && $write['as_type'] == "-1") {
		alert("신고된 글은 삭제할 수 없습니다.");
	}
}

// 서문일 때 챕터글부터 삭제
if(!$write['wr_reply']) {
	$row = sql_fetch(" select count(*) as cnt from $write_table where wr_reply like '$reply%' and wr_id <> '{$write['wr_id']}' and wr_num = '{$write['wr_num']}' and wr_is_comment = 0 ");
	if (isset($row['cnt']) && $row['cnt']) {
		alert('이 글과 관련된 챕터글이 존재하므로 삭제 할 수 없습니다.\\n\\n우선 챕터글부터 삭제하여 주십시오.');
	}
}