<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if($w == 'r') { 
	// 답글
	sql_query(" update $write_table set wr_6 = '{$wr['mb_id']}' where wr_id = '$wr_id' ");
} else if($w == 'u') { 
	// 수정
	sql_query(" update $write_table set wr_6 = '{$wr['wr_6']}' where wr_id = '$wr_id' ");
}