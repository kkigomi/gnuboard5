<?php
include_once('./_common.php');

// 게시판 관리자 이상 복사, 이동 가능
if (!$is_admin)
    alert_close("관리자만 접근이 가능합니다.");

if(isset($mode) && $mode) {
	$pwrid = (isset($pwrid) && $pwrid) ? (int)$pwrid : '';
	$wr_id_list = preg_replace('/[^0-9\,]/', '', $_POST['wr_id_list']);
	$wr_arr = explode(",", $wr_id_list);
	for($i=0; $i < count($wr_arr); $i++) {
		sql_query(" update $write_table set wr_1 = '{$pwrid}' where wr_id = '{$wr_arr[$i]}' ", false);
	}
    alert_close("표지글 지정을 완료했습니다.");
}

$g5['title'] = '표지글 지정하지';
include_once(G5_PATH.'/head.sub.php');

$wr_id_list = '';
if (isset($wr_id) && $wr_id)
    $wr_id_list = $wr_id;
else {
    $comma = '';
    for ($i=0; $i<count($_POST['chk_wr_id']); $i++) {
        $wr_id_list .= $comma . $_POST['chk_wr_id'][$i];
        $comma = ',';
    }
}

?>

<div id="copymove" class="new_win">
    <h1 id="win_title"><?php echo $g5['title'] ?></h1>

    <form name="fboardmoveall" method="post" onsubmit="return fboardmoveall_submit(this);">
    <input type="hidden" name="mode" value="1">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id_list" value="<?php echo $wr_id_list ?>">

    <div class="tbl_head01 tbl_wrap">
        <table>
        <thead>
        <tr>
            <th scope="col" align="right">표지글 아이디(wr_id)</th>
            <th scope="col"><input type="text" value="" id="pwrid" name="pwrid" class="frm_input"></th>
        </tr>
        </thead>
		<tbody>
        <tr><td colspan="2" align="center">표지글 글아이디(wr_id)를 입력해 주십시오.</td></tr>
		</tbody>
		</table>
    </div>

    <div class="win_btn">
        <input type="submit" value="확인" id="btn_submit" class="btn_submit">
    </div>
    </form>

</div>

<script>
$(function() {
    $(".win_btn").append("<button type=\"button\" class=\"btn_cancel\">닫기</button>");

    $(".win_btn button").click(function() {
        window.close();
    });
});
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');