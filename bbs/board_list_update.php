<?php
include_once('./_common.php');

$count = (isset($_POST['chk_wr_id']) && is_array($_POST['chk_wr_id'])) ? count($_POST['chk_wr_id']) : 0;
$post_btn_submit = isset($_POST['btn_submit']) ? clean_xss_tags($_POST['btn_submit'], 1, 1) : '';

if(!$count) {
    alert(addcslashes($post_btn_submit, '"\\/').' 하실 항목을 하나 이상 선택하세요.');
}

// TODO 하드코딩이 아닌 게시판 설정 값을 읽어와서 값을 할당할 수 있도록 변경 필요
$allowed_category_name = array('기능제안','버그','완료','취소');
if($post_btn_submit === '선택삭제') {
    include './delete_all.php';
} else if($post_btn_submit === '선택복사') {
    $sw = 'copy';
    include './move.php';
} else if($post_btn_submit === '선택이동') {
    $sw = 'move';
    include './move.php';
} else if(in_array($post_btn_submit, $allowed_category_name)) {
    $sw = 'changeCategory';
    include './change_category.php';
} else {
    alert('올바른 방법으로 이용해 주세요.');
}