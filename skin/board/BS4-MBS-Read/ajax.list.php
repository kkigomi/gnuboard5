<?php
include_once('./_common.php');

$list_style = isset($_REQUEST['list_style']) ? $_REQUEST['list_style'] : '';
$list_style = (in_array($list_style, array('list', 'gallery', 'webzine'))) ? $list_style : '';
if($list_style) {
	set_session($bo_table.'_list', $list_style);
}