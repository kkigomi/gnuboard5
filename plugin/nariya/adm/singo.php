<?php
$sub_menu = "800400";
include_once('./_common.php');

if ($is_admin != 'super') {
    alert('최고관리자만 접근 가능합니다.');
}

$g5['title'] = '신고 관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');
include_once('../lib/singo.lib.php');
include_once('../lib/pagination.lib.php');

if (!$page) $page = 0;
if ((int) $page > 0) {
    $page = (int) $page - 1;
}

$sql_where = null;
$req_param = null;
if (isset($sg_table) && isset($sg_id)) {
    $sql_where = " WHERE sg_table = '$sg_table' AND sg_id = $sg_id";
    $req_param = "sg_table=".$sg_table."&sg_id=".$sg_id;
}

$count_sql = sql_fetch(" SELECT count(*) AS count FROM {$g5['na_singo']}".$sql_where);
$count = isset($count_sql["count"]) ? (int) $count_sql["count"] : 0;

// 한 페이지당 보여지는 항목 수
$list_count = 500;

$offset = $list_count * $page;
$sql = " SELECT * FROM {$g5['na_singo']}" .$sql_where."
         ORDER BY `id` DESC LIMIT {$offset}, {$list_count}";
$result = sql_query($sql);
$page_url = G5_URL.'/plugin/nariya/adm/singo.php';

// 목록 페이징
$pg = new CommonPagination();
$pg->list_count = $list_count;
$pg->page = $page + 1;
$pg->count = $count;
$pg->one_section = G5_IS_MOBILE ? (int) $config['cf_mobile_pages'] : (int) $config['cf_write_pages'];

$pagination = $pg->getPagination();
?>

<style>
    .sg_desc {
        position: absolute;
        left: 10px;
        display: none;
        width: 200px;
        border-radius: 4px;
        border: 1px solid #d6dce7;
        background-color: #fff;
        padding: 4px 10px;
        text-align: left;
        z-index: 100;
    }
</style>

<div class="local_ov01 local_ov">
    <a class="ov_listall" href="/plugin/nariya/adm/singo.php">전체목록</a>
    <?php if (isset($sg_id)) { ?>
        <span class="btn_ov01">
            <span class="ov_txt">
                신고 횟수
            </span>
            <span class="ov_num"> <?=$count?>회</span>
        </span>
    <?php } ?>
</div>
<?php if (isset($sg_id)) { ?>
<h2><?=$sg_table?>의 [<?=$sg_id?>] 게시물 신고 목록</h2>
<?php } ?>

<!-- 신고 목록 -->
<form name="fboardlist" id="fboardlist" action="./singo_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <div class="tbl_head01 tbl_wrap">
        <table>
            <colgroup>
                <col style="width:2%">
                <col style="width:8%">
                <col style="width:8%">
                <col style="width:5%">
                <col>
                <col style="width:10%">
                <col style="width:10%">
                <col style="width:6%">
                <col style="width:10%">
                <col style="width:6%">
                <col style="width:6%"> <!-- Add width for the new date column -->
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">
                        <label for="chkall" class="sound_only">게시판 전체</label>
                        <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
                    </th>
                    <th scope="col">날짜</th> <!-- New date column header -->
                    <th scope="col">게시판</th>
                    <th scope="col">유형</th>
                    <th scope="col">제목(내용)</th>
                    <th scope="col">작성자</th>
                    <th scope="col">신고 사유</th>
                    <th scope="col">추가 내용</th>
                    <th scope="col">신고자</th>
                    <th scope="col">게시물 필터</th>
                    <th scope="col">게시물 보기</th>
                </tr>
            </thead>
            <tbody>
                <?php
                for ($i=0; $row=sql_fetch_array($result); $i++) {
                    $tmp_write_table = $g5['write_prefix'].$row['sg_table'];
                    $row_board = sql_fetch(" SELECT * from {$g5['board_table']} where bo_table = '{$row['sg_table']}' ");
                    $row2 = sql_fetch(" SELECT * from {$tmp_write_table} where wr_id = '{$row['sg_id']}' ");

                    if (!$row2) {
                ?>
                <tr>
                    <td class="td_chk">
                        <label for="chk_<?php echo $row['id']; ?>" class="sound_only"><?php echo $row['id']; ?>번 항목 체크</label>
                        <input type="checkbox" name="chk[]" value="<?php echo $row['id']; ?>" id="chk_<?php echo $row['id']; ?>">
                    </td>
                    <td colspan="10" class="td_left">삭제 또는 이동된 게시물입니다.</td>
                </tr>
                <?php continue; }

                    $name = get_sideview($row2['mb_id'], get_text(
                        cut_str($row2['wr_name'], $config['cf_cut_name'])),
                        $row2['wr_email'], $row2['wr_homepage']);

                    $sg_member = get_member($row['mb_id']);
                    $singo_name = get_sideview($sg_member['mb_id'], $sg_member['mb_nick']);

                    if ($row['sg_id'] == $row['sg_parent']) {
                        // 게시물이라면
                        $post_type = '게시물';
                        $sel_comment = '';
                        $content = $row2['wr_subject'];

                    } else {
                        $post_type = '댓글';
                        $sel_comment = '#c_'.$row['sg_id'];
                        $content = $row2['wr_content'];
                    }

                    $view_url = short_url_clean(
                        G5_HTTP_BBS_URL.'/board.php?bo_table='.$row['sg_table'].'&amp;wr_id='.$row['sg_parent'].$sel_comment);
                    $filter_url = $page_url.'?sg_table='.$row['sg_table'].'&sg_id='.$row['sg_id'];
                ?>
                <tr>
                    <td class="td_chk">
                        <label for="chk_<?php echo $row['id']; ?>" class="sound_only"><?php echo $row['id']; ?>번 항목 체크</label>
                        <input type="checkbox" name="chk[]" value="<?php echo $row['id']; ?>" id="chk_<?php echo $row['id']; ?>">
                    </td>
                    <td class="td_left"><?php echo date("y-m-d H:i", strtotime($row['sg_datetime'])); ?></td> <!-- New date column data -->
                    <td class="td_left"><?=$row_board['bo_subject']?></td>             <!-- 게시판 -->
                    <td class="td_left"><?=$post_type?></td>                           <!-- 게시물 타입 -->
                    <td class="td_left"><?php echo strip_tags($content); ?></td>        <!-- 제목 또는 댓글 내용 -->
                    <td class="td_left"><?php echo $name; ?></td>                      <!-- 작성자 -->
                    <td><?php echo $singo_type[$row['sg_type']]; ?></td>               <!-- 신고 사유 -->
                    <td style="position:relative">
                        <a id="sg_desc_toggle_<?php echo $row['id']; ?>" href="javascript:toggle_singo_desc(<?php echo $row['id']; ?>);">
                            <strong>펼치기</strong>
                        </a>
                        <p class="sg_desc" id="sg_desc_<?php echo $row['id']; ?>">
                            <?php echo ($row['sg_desc']) ? strip_tags($row['sg_desc']) : '내용이 없습니다.' ; ?>
                        </p>
                    </td>                                                              <!-- 추가 내용 -->
                    <td class="td_left"><?php echo $singo_name; ?></td>                <!-- 신고자 -->
                    <td><a href="<?=$filter_url?>">필터</a></td>                       <!-- 게시물 필터 -->
                    <td><a href="<?=$view_url?>" target="_blank"><strong>보기</strong></a></td>                         <!-- 게시물 보기 -->
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <!--div class="btn_fixed_top">
        <input type="submit" name="act_button" value="선택복구" onclick="document.pressed=this.value" class="btn_02 btn">
        <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn_02 btn">
        <input type="submit" name="act_button" value="선택보류" onclick="document.pressed=this.value" class="btn_02 btn">
    </div-->
</form>

<nav class="pg_wrap">
    <span class="pg">
        <?php
        foreach ($pagination as $p) {
            if ($req_param)
                $pg_url_param = $page_url.'?page='.$p->page.'&'.$req_param;
            else
                $pg_url_param = $page_url.'?page='.$p->page
        ?>
            <?php if ($p->type == 'onePage') { ?>
                <a href="<?php echo $pg_url_param; ?>" class="pg_page pg_start">처음</a>
            <?php } else if ($p->type == 'prevPage') { ?>
                <a href="<?php echo $pg_url_param; ?>" class="pg_page pg_prev">이전</a>
            <?php } else if ($p->type == 'currentPage') { ?>
                <a href="<?php echo $pg_url_param; ?>">
                    <span class="sound_only">열린</span>
                    <strong class="pg_current"><?=$p->page?></strong>
                    <span class="sound_only">페이지</span>
                </a>
            <?php } else if ($p->type == 'page') { ?>
                <a href="<?php echo $pg_url_param; ?>" class="pg_page">
                    <?=$p->page?><span class="sound_only">페이지</span>
                </a>
            <?php } else if ($p->type == 'nextPage') { ?>
                <a href="<?php echo $pg_url_param; ?>" class="pg_page pg_next">다음</a>
            <?php } else if ($p->type == 'endPage') { ?>
                <a href="<?php echo $pg_url_param; ?>" class="pg_page pg_end">맨끝</a>
            <?php } ?>
        <?php } ?>
    </span>
</nav>

<script>
    function fboardlist_submit(f) {
        if (!is_checked("chk[]")) {
            alert(document.pressed + " 하실 항목을 하나 이상 선택하세요.");
            return false;
        }

        if (document.pressed == "선택삭제") {
            if (!confirm("선택한 자료를 정말 삭제하시겠습니까?\n게시물과 신고내역이 모두 삭제됩니다.")) {
                return false;
            }
        }

        return true;
    }

    function toggle_singo_desc(id) {
        $('#sg_desc_' + id).toggle();
        var toggle_link = $('#sg_desc_toggle_' + id + '>strong');
        if (toggle_link.text() === '펼치기') {
            toggle_link.text('접기');
        } else {
            toggle_link.text('펼치기');
        }
    }

    $(function() {
        $(".board_copy").click(function() {
            window.open(this.href, "win_board_copy", "left=100,top=100,width=550,height=450");
            return false;
        });
    });
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
