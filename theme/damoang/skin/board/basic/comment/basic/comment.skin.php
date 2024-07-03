<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
// 댓글 여분필드 사용 내역
// wr_7 : 신고(lock)
// wr_9 : 대댓글 대상
// wr_10 : 럭키 포인트

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
if(!$is_ajax)
    add_stylesheet('<link rel="stylesheet" href="' . $comment_skin_url . '/comment.css?CACHEBUST">', 0);
?>

<?php if(!$is_ajax) { // 1번만 출력 ?>
<script>
// 글자수 제한
var char_min = parseInt(<?php echo $comment_min ?>); // 최소
var char_max = parseInt(<?php echo $comment_max ?>); // 최대
</script>
<div id="viewcomment" class="mt-4">
<?php } ?>

<?php if (isset($boset['check_star_rating']) && $boset['check_star_rating']) { ?>
    <!-- 별점 평균 { -->
    <?php
    $average_row = sql_fetch(
        " SELECT * FROM {$g5['board_rate_average_table']}
            WHERE bo_table = '{$bo_table}' AND wr_id = '{$wr_id}' LIMIT 1 ");
    ?>
    <div class="card mb-2 border-0 border-bottom rounded-0">
        <div class="card-body pt-0">
            <div class="row">
                <div class="col-5 col-md-3 d-flex justify-content-center align-items-center">
                    <div class="d-flex flex-column">
                        <div class="fs-1 text-center"><?php echo $average_row['rate_average'] ? round((float) $average_row['rate_average'] / 2, 2) : 0.0; ?></div>
                        <div>
                            <div class="star-rated d-flex p-2 justify-content-center align-items-center">
                                <?php
                                    $average = (float) $average_row['rate_average'] * 2;
                                    echo na_generate_star_rating($average / 2);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-7 col-md-9">
                    <div class="flex-column">
                        <?php for ($i = 10; $i > 0; $i--) { $rated = $i / 2; ?>
                            <div class="d-flex px-2 gap-2 align-items-center">
                                <div class="text-ultra-sm text-end" style="width:15px"><?=$rated?></div>
                                <div class="flex-fill">
                                    <div class="progress da-star--rate-progress" role="progressbar">
                                        <?php
                                        $row_count = isset($average_row['rate_count_'.$i]) ? (int) $average_row['rate_count_'.$i] : 0;
                                        $rate_count = isset($average_row['rate_count']) ? (int) $average_row['rate_count'] : 0;
                                        ?>
                                        <div class="progress-bar" style="width: <?php echo ($row_count > 0) ? $row_count * 100 / $rate_count : 0; ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- } 별점 평균 -->
<?php } ?>
    <div class="d-flex justify-content-between align-items-end px-3 mb-2">
        <div>
            댓글 <b><?php echo $write['wr_comment'] ?></b>
            <?php if($is_paging && $page) echo ' / '.$page.' 페이지'.PHP_EOL; ?>
        </div>
        <?php if($is_paging) { //페이징
            $comment_sort_href = NA_URL.'/comment.page.php?bo_table='.$bo_table.'&amp;wr_id='.$wr_id;
            switch($cob) {
                case 'new'		: $comment_sort_txt = '최신순'; break;
                case 'good'		: $comment_sort_txt = '추천순'; break;
                case 'nogood'	: $comment_sort_txt = '비추천순'; break;
                default			: $comment_sort_txt = '과거순'; break;
            }
        ?>

            <div>
                <div class="btn-group">
                    <button type="button" class="btn btn-basic btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $comment_sort_txt ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <button class="dropdown-item" type="button" onclick="na_comment_sort('viewcomment', '<?php echo $comment_sort_href ?>', 'old');">과거순</button>
                        </li>
                        <li>
                            <button class="dropdown-item" type="button" onclick="na_comment_sort('viewcomment', '<?php echo $comment_sort_href ?>', 'new');">최신순</button>
                        </li>
                        <?php if($is_comment_good) { ?>
                            <button class="dropdown-item" type="button" onclick="na_comment_sort('viewcomment', '<?php echo $comment_sort_href ?>', 'good');">추천순</button>
                        <?php } ?>
                        <?php if($is_comment_nogood) { ?>
                            <button class="dropdown-item" type="button" onclick="na_comment_sort('viewcomment', '<?php echo $comment_sort_href ?>', 'nogood');">비추천순</button>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        <?php } ?>
    </div>

    <section id="bo_vc" class="na-fadein">
        <?php
        $good_list = array();
        if ($member['mb_id']) {
            // 추천/비추천 여부 확인을 위한 댓글의 추천내역 가져오기
            // 2024.04.15 서버 부하로 사용 안함
            /*$sql = " select {$write_table}.wr_id, {$g5['board_good_table']}.bg_id, {$g5['board_good_table']}.bg_flag
                    from {$write_table}
                    left join {$g5['board_good_table']} on
                        {$write_table}.wr_id = {$g5['board_good_table']}.wr_id
                    where {$write_table}.wr_id != '{$wr_id}'
                    and {$write_table}.wr_parent = '{$wr_id}'
                    and {$g5['board_good_table']}.mb_id = '{$member['mb_id']}'";
            $result = sql_query($sql);

            for ($i=0; $row=sql_fetch_array($result); $i++) {
                $good_list[$row['wr_id']] = $row['bg_flag'];
            }*/
        }

        // 댓글목록

        $comment_cnt = count($list);
        $wr_names = [];
        for ($i = 0; $i < $comment_cnt; $i++) {
            $comment_id = $list[$i]['wr_id'];
            $comment_depth = strlen($list[$i]['wr_comment_reply']) * 1;
            $comment = $list[$i]['content'];

            $wr_names[$list[$i]['wr_comment'] . ':' . $list[$i]['wr_comment_reply']] = $list[$i]['wr_name'];

            // 이미지
            $comment = preg_replace("/\[\<a\s*href\=\"(http|https|ftp)\:\/\/([^[:space:]]+)\.(gif|png|jpg|jpeg|bmp|webp)\"\s*[^\>]*\>[^\s]*\<\/a\>\]/i", "<img src=\"$1://$2.$3\" alt=\"\">", $comment);

            // 이미지 썸네일
            $comment = str_replace('<img', '<img class="img-fluid"', get_view_thumbnail($comment));

            // 동영상
            $comment = preg_replace("/\[\<a\s.*href\=\"(http|https|ftp|mms)\:\/\/([^[:space:]]+)\.(mp3|wma|wmv|asf|asx|mpg|mpeg)\".*\<\/a\>\]/i", "<script>doc_write(obj_movie('$1://$2.$3'));</script>", $comment);

            // 컨텐츠
            $comment = na_content($comment);

            $comment_sv = $comment_cnt - $i + 1; // 댓글 헤더 z-index 재설정 ie8 이하 사이드뷰 겹침 문제 해결
            $c_reply_href = $comment_common_url.'&amp;c_id='.$comment_id.'&amp;w=c#bo_vc_w';
            $c_edit_href = $comment_common_url.'&amp;c_id='.$comment_id.'&amp;w=cu#bo_vc_w';
            $is_comment_reply_edit = ($list[$i]['is_reply'] || $list[$i]['is_edit'] || $list[$i]['is_del']) ? 1 : 0;

            $comment_name = get_text($list[$i]['wr_name']);

            $list[$i]['is_del'] = ($is_admin == 'super') || ($list[$i]['mb_id'] == $member['mb_id']) ? true : false;

            // 글 작성자가 쓴 댓글, 로그인 한 사용자가 쓴 댓글, 일반 댓글 색상으로 구분하기
            if (!empty($view['mb_id']) && $view['mb_id'] == $list[$i]['mb_id']) {
                $by_writer = 'bg-secondary-subtle'; // 글 작성자가 쓴 댓글
            } elseif (!empty($member['mb_id']) && $member['mb_id'] == $list[$i]['mb_id']) {
                $by_writer = 'bg-comment-writer'; // 로그인 한 사용자가 쓴 댓글
            } else {
                $by_writer = 'bg-body-tertiary'; // 일반 사용자가 쓴 댓글
            }

            $parent_wr_name = $wr_names[$list[$i]['wr_comment'] . ':' . substr($list[$i]['wr_comment_reply'], 0, -1)] ?? '';

        ?>
        <article id="c_<?php echo $comment_id ?>" <?php if ($comment_depth) { ?>style="margin-left:<?php echo $comment_depth ?>rem;"<?php } ?>>
            <div class="comment-list-wrap position-relative">
                <header style="z-index:<?php echo $comment_sv ?>">
                    <h3 class="visually-hidden">
                        <?php echo $comment_name; ?>님의
                        <?php if ($comment_depth) { ?><span class="visually-hidden">댓글의</span><?php } ?> 댓글
                    </h3>
                    <div class="d-flex align-items-center border-top <?php echo $by_writer ?> py-1 px-3 small">
                        <div class="me-2">
                            <?php if ($comment_depth) { ?>
                                <i class="bi bi-arrow-return-right"></i>
                                <span class="visually-hidden">대댓글</span>
                            <?php } ?>
                            <span class="visually-hidden">작성자</span>
                            <span class="d-inline-block"><?php echo na_name_photo($list[$i]['mb_id'], $list[$i]['name']); ?></span>
                            <?php
                            // 회원 메모
                            echo $list[$i]['da_member_memo'] ?? '';
                            ?>
                            (<?php echo $list[$i]['ip'] ?>)
                        </div>
                        <div>
                            <?php include(G5_SNS_PATH.'/view_comment_list.sns.skin.php'); // SNS ?>
                        </div>
                        <div class="ms-auto" title="<?= get_text($list[$i]['wr_datetime']) ?>">
                            <span class="visually-hidden">작성일</span>
                            <?php echo na_date($list[$i]['wr_datetime'], 'orangered'); ?>
                        </div>
                    </div>
                </header>
                <div class="comment-content p-3">
                    <?php if (isset($boset['check_star_rating']) && $boset['check_star_rating'] && !$comment_depth) {
                        $star_rate = (int) $list[$i]['wr_6'];
                        if ($star_rate > 10) $star_rate = 0;

                        $star_rated_text = na_convert_star_rating($star_rate);
                        $star_html = na_generate_star_rating($star_rate);
                        ?>
                        <div class="star-rated d-flex pb-2 px-0 mb-2 align-items-center">
                            <span class="me-2 small">별점:</span>
                            <?php echo $star_html; ?>
                            <span class="ms-1 small"><?php echo $star_rated_text; ?></span>
                        </div>
                    <?php } ?>
                    <div class="<?php echo $is_convert ?>">
                        <?php if ($comment_depth) { ?>
                            <?php if ($parent_wr_name) { ?>
                                <em class="da-commented-to"><strong>@<?= $parent_wr_name ?></strong>님에게 답글</em>
                            <?php } else { ?>
                                <em class="da-commented-to">다른 누군가에게 답글</em>
                            <?php } ?>
                        <?php } ?>
                        <?php
                        $is_lock = false;
                        if (strstr($list[$i]['wr_option'], "secret")) {
                            $is_lock = true;
                        ?>
                            <span class="na-icon na-secret"></span>
                        <?php } ?>

                        <?php if(empty($comment)) echo "[삭제된 댓글입니다]"; else echo $comment ?>
                    </div>
                    <?php if((int)$list[$i]['wr_10'] > 0) { // 럭키포인트 ?>
                        <div class="small mt-3">
                            <i class="bi bi-gift"></i>
                            <b><?php echo number_format((int)$list[$i]['wr_10']) ?></b> 랜덤 럭키포인트 당첨을 축하드립니다.
                        </div>
                    <?php } ?>

                    <div class="d-flex justify-content-between mt-3">
                        <div class="btn-group btn-group-sm" role="group">
                        <?php if($is_comment_reply_edit) {
                            if($w == 'cu') {
                                $sql = " select wr_id, wr_content, mb_id from $write_table where wr_id = '$c_id' and wr_is_comment = '1' ";
                                $cmt = sql_fetch($sql);
                                if (!($is_admin || ($member['mb_id'] == $cmt['mb_id'] && $cmt['mb_id'])))
                                    $cmt['wr_content'] = '';
                                $c_wr_content = $cmt['wr_content'];
                            }
                        ?>
                            <?php if ($list[$i]['is_reply']) { ?>
                                <button type="button" class="btn btn-basic" onclick="comment_box('<?php echo $comment_id ?>','c','<?php echo $comment_name;?>');" class="btn btn-basic btn-sm" title="답글">
                                    <i class="bi bi-chat-dots"></i>
                                    답글
                                </button>
                            <?php } ?>
                            <?php if ($list[$i]['is_edit']) { ?>
                                <button type="button" class="btn btn-basic" onclick="comment_box('<?php echo $comment_id ?>','cu','<?php echo $comment_name;?>');" class="btn btn-basic btn-sm" title="수정">
                                    <i class="bi bi-pencil"></i>
                                    <span class="d-none d-sm-inline-block">수정</span>
                                </button>
                            <?php } ?>
                            <?php
                            if ($list[$i]['is_del']) {
                            ?>
                                <a href="<?php echo $list[$i]['del_link']; ?>" rel="nofollow" onclick="<?php echo (isset($list[$i]['del_back']) && $list[$i]['del_back']) ? "na_delete('viewcomment', '".$list[$i]['del_href']."','".$list[$i]['del_back']."'); return false;" : "return comment_delete(this.href);";?>" class="btn btn-basic" title="삭제">
                                    <i class="bi bi-trash"></i>
                                    <span class="d-none d-sm-inline-block">삭제</span>
                                </a>
                            <?php } ?>
                        <?php } ?>
                            <?php if(!empty($is_member)) { // 로그인한 회원만 복사 가능 ?>
                            <button type="button" onclick="copy_comment_link('<?php echo $comment_id ?>');" class="btn btn-basic" title="복사">
                                <i class="bi bi-copy"></i>
                                <span class="d-none d-sm-inline-block">복사</span>
                            </button>
                            <?php } ?>
                            <button type="button" onclick="na_singo('<?php echo $bo_table ?>', '<?php echo $list[$i]['wr_id'] ?>', '0', 'c_<?php echo $comment_id ?>');" class="btn btn-basic" title="신고">
                                <i class="bi bi-eye-slash"></i>
                                <span class="d-none d-sm-inline-block">신고</span>
                            </button>
                            <?php if($list[$i]['mb_id']) { // 회원만 가능 ?>
                                <button type="button" onclick="na_chadan('<?php echo $list[$i]['mb_id'] ?>');" class="btn btn-basic" title="차단">
                                    <i class="bi bi-person-slash"></i>
                                    <span class="d-none d-sm-inline-block">차단</span>
                                </button>
                            <?php } ?>
                        </div>
                        <?php if($is_comment_good || $is_comment_nogood) { ?>
                            <div class="btn-group btn-group-sm" role="group">
                                <?php if($is_comment_good) { ?>
                                    <button type="button" onclick="na_good('<?php echo $bo_table ?>', '<?php echo $comment_id ?>', 'good', 'c_g<?php echo $comment_id ?>', 1);" class="btn good-border <?php echo (isset($good_list[$list[$i]['wr_id']]) && $good_list[$list[$i]['wr_id']] == 'good') ? 'btn-primary' : 'btn-basic' ?>" title="추천">
                                        <span class="visually-hidden">추천</span>
                                        <i class="bi bi-hand-thumbs-up"></i>
                                        <span id="c_g<?php echo $comment_id ?>"><?php echo $list[$i]['wr_good'] ?></span>
                                    </button>
                                <?php } ?>
                                <?php if($is_comment_nogood) { ?>
                                    <button type="button" class="btn good-border <?php echo (isset($good_list[$list[$i]['wr_id']]) && $good_list[$list[$i]['wr_id']] == 'nogood') ? 'btn-primary' : 'btn-basic' ?>" onclick="na_good('<?php echo $bo_table ?>', '<?php echo $comment_id ?>', 'nogood', 'c_ng<?php echo $comment_id ?>', 1);" title="비추천">
                                        <span class="visually-hidden">비추천</span>
                                        <i class="bi bi-hand-thumbs-down"></i>
                                        <span id="c_ng<?php echo $comment_id;?>"><?php echo $list[$i]['wr_nogood']; ?></span>
                                    </button>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="clearfix">
                    <?php // 현재 별점
                        $wr_6 = (int) $list[$i]['wr_6'];
                        if (!$comment_depth && !empty($list[$i]['wr_6'])) {
                            $data_wr_6 = " data-star-rated=\"{$wr_6}\"";
                        }
                    ?>
                    <span id="edit_<?php echo $comment_id ?>"<?php echo $data_wr_6 ?? ''; ?> class="bo_vc_w<?php echo $comment_depth ? ' is-deeper' : ''; ?>"></span><!-- 수정 -->
                    <span id="reply_<?php echo $comment_id ?>" class="bo_vc_re<?php echo $comment_depth ? ' is-deeper' : ''; ?>"></span><!-- 답변 -->
                    <?php if($is_paging) { ?>
                        <input type="hidden" value="<?php echo $comment_url.'&amp;page='.$page; ?>" id="comment_url_<?php echo $comment_id ?>">
                        <input type="hidden" value="<?php echo $page; ?>" id="comment_page_<?php echo $comment_id ?>">
                    <?php } ?>
                    <input type="hidden" value="<?php echo strstr($list[$i]['wr_option'],"secret") ?>" id="secret_comment_<?php echo $comment_id ?>">
                    <textarea id="save_comment_<?php echo $comment_id ?>" class="d-none"><?php echo get_text($list[$i]['content1'], 0) ?></textarea>
                </div>
            </div>
        </article>
        <?php } ?>
        <?php if($is_paging) { //페이징 ?>
            <div class="d-flex flex-column flex-sm-row border-top justify-content-between p-3 gap-2">
                <div>
                    <ul class="pagination pagination-sm justify-content-center m-0">
                        <?php echo na_ajax_paging('viewcomment', $write_pages, $page, $total_page, $comment_page); ?>
                    </ul>
                </div>
                <div>
                    <button class="btn btn-basic btn-sm w-100" onclick="na_comment_new('viewcomment','<?php echo $comment_url ?>','<?php echo $total_count ?>');">
                        <i class="bi bi-arrow-clockwise"></i>
                        새로운 댓글 확인
                    </button>
                </div>
            </div>
        <?php } ?>
    </section>
<?php
// 아래 내용은 1번만 출력
if($is_ajax)
    return;
?>
</div><!-- #viewcomment 닫기 -->
<?php
    $certify_required = explode(',', $config['cf_7']);
    if (!empty($config['cf_7'])) {
        foreach ($certify_required as $val) {
            if (trim($val) === $bo_table) { // 실명인증 필수 설정한 게시판일때
                if ($is_member && $is_admin != 'super' && empty($member['mb_certify'])) { // 본인인증이 안된 계정일때
                    $is_no_certified = true;
                }
            }
        }
    }
?>
<!-- onclick="comment_box('','w','<?php echo $comment_name;?>');" -->
<?php if ($is_comment_write && !isset($is_no_certified)) { $w = ($w == '') ? 'c' : $w; ?>
    <div id="float-comment" class="p-3 gap-2 mt-3">
        <button id="comment-write-button" data-bs-toggle="offcanvas" data-bs-target="#commentOffcanvas" aria-controls="commentOffcanvas" class="d-none"></button>
        <button class="btn btn-primary btn-sm" style="width:95px" data-bs-toggle="offcanvas" data-bs-target="#commentOffcanvas" aria-controls="commentOffcanvas" onclick="comment_box('','w','<?php echo $comment_name;?>');">
            <i class="bi bi-pencil-square"></i>
            댓글 등록
        </button>
    </div>

    <?php include_once LAYOUT_PATH . '/component/comment.offcanvas.php' // 전체검색 오프캔버스;?>

<?php } else if (isset($is_no_certified)) { ?>
    <div id="bo_vc_login" class="alert alert-light mb-3 py-4 text-center mx-3" role="alert">
        <a href="<?php echo G5_BBS_URL ?>/member_cert_refresh.php">본인인증을 완료한 회원만 댓글 등록이 가능합니다.</a>
    </div>
<?php } else { ?>
    <div id="bo_vc_login" class="alert alert-light mb-3 py-4 text-center mx-3" role="alert">
        <?php if($is_guest) { ?>
            <a href="<?php echo G5_BBS_URL ?>/login.php?wr_id=<?php echo $wr_id.$qstr ?>&amp;url=<?php echo urlencode(get_pretty_url($bo_table, $wr_id, $qstr).'#bo_vc_w') ?>" rel="nofollow">로그인한 회원만 댓글 등록이 가능합니다.</a>
        <?php } else { ?>
            댓글을 등록할 수 있는 권한이 없습니다.
        <?php } ?>
    </div>
<?php } ?>
<script>
    //commentOpencanvas 활성 버튼 요소
    const commentButton = document.querySelector('#comment-write-button');

    // commentOpencanvas 활성 버튼
    const openCommentOffCanvas = () => {
        commentButton.click();
    };
</script>
<?php if ($is_comment_write) { ?>
    <script>
    var save_before = '';
    var save_html = document.getElementById('bo_vc_w').innerHTML;

    // 버튼 요소를 가져옵니다.

    function good_and_write() {
        var f = document.fviewcomment;
        if (fviewcomment_submit(f)) {
            f.is_good.value = 1;
            f.submit();
        } else {
            f.is_good.value = 0;
        }
    }

    function fviewcomment_submit(f) {

        f.is_good.value = 0;

        var subject = "";
        var content = "";
        $.ajax({
            url: g5_bbs_url+"/ajax.filter.php",
            type: "POST",
            data: {
                "subject": "",
                "content": f.wr_content.value
            },
            dataType: "json",
            async: false,
            cache: false,
            success: function(data, textStatus) {
                subject = data.subject;
                content = data.content;
            }
        });

        if (content) {
            na_alert("내용에 금지단어('"+content+"')가 포함되어있습니다", function() {
                f.wr_content.focus();
            });
            return false;
        }

        // 양쪽 공백 없애기
        var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자
        document.getElementById('wr_content').value = document.getElementById('wr_content').value.replace(pattern, "");
        if (char_min > 0 || char_max > 0) {
            check_byte('wr_content', 'char_count');
            var cnt = parseInt(document.getElementById('char_count').innerHTML);
            if (char_min > 0 && char_min > cnt) {
                na_alert("댓글은 최소 "+char_min+"글자 이상 쓰셔야 합니다.");
                return false;
            } else if (char_max > 0 && char_max < cnt) {
                na_alert("댓글은 쵀대 "+char_max+"글자 이하로 쓰셔야 합니다.");
                return false;
            }
        } else if (!document.getElementById('wr_content').value) {
            na_alert('댓글을 입력하여 주십시오.', function() {
                f.wr_content.focus();
            });
            return false;
        }

        if (typeof(f.wr_name) != 'undefined') {
            f.wr_name.value = f.wr_name.value.replace(pattern, "");
            if (f.wr_name.value == '') {
                na_alert('이름이 입력되지 않았습니다.', function() {
                    f.wr_name.focus();
                });
                return false;
            }
        }

        if (typeof(f.wr_password) != 'undefined') {
            f.wr_password.value = f.wr_password.value.replace(pattern, "");
            if (f.wr_password.value == '') {
                na_alert('비밀번호가 입력되지 않았습니다.', function() {
                    f.wr_password.focus();
                });
                return false;
            }
        }

        <?php if($is_guest) echo chk_captcha_js();  ?>

        set_comment_token(f);

        document.getElementById("btn_submit").disabled = "disabled";

        return true;
    }

    function comment_box(comment_id, work, name) {
        var el_id

        // 댓글 아이디가 넘어오면 답변, 수정
        if (comment_id) {
            if (work == 'c')
                el_id = 'reply_' + comment_id;
            else
                el_id = 'edit_' + comment_id;
        } else
            el_id = 'bo_vc_w';

        var star = document.getElementById('bo_vc_star');
        if (comment_id && star) {
            // 대댓글일때 별점 사용 안함
            var target_el = document.getElementById(el_id);
            if (target_el.classList.contains('is-deeper') || work == 'c') {
                star.parentElement.style.display = 'none';
                document.querySelector("#commentOffcanvas").style.height = "220px";
            } else {
                star.parentElement.style.display = 'block';
                document.querySelector("#commentOffcanvas").style.height = "300px";
            }

            var starRate = target_el.dataset.starRated;
            if (target_el.dataset.starRated) {
                starRating.initStars();
                starRating.setRate(starRate);
                starRating.filledRate(starRate - 1);
            } else {
                starRating.initStars();
            }
        } else {
            // 댓글 쓰기의 경우 별점 선택을 오픈해준다.
            if(el_id == "bo_vc_w" && star){
                star.parentElement.style.display = 'block';
                document.querySelector("#commentOffcanvas").style.height = "300px";
            } else {
                document.querySelector("#commentOffcanvas").style.height = "220px";
            }
        }

        if (save_before != el_id) {
            //document.getElementById(el_id).style.display = '';

            //입력값 초기화
            //if(save_before !== "") openCommentOffCanvas();
            document.getElementById('wr_content').value = '';

            // 댓글 수정
            if (work == 'cu') {
                document.getElementById('wr_content').value = document.getElementById('save_comment_' + comment_id).value;
                if (typeof char_count != 'undefined')
                    check_byte('wr_content', 'char_count');
                if (document.getElementById('secret_comment_'+comment_id).value)
                    document.getElementById('wr_secret').checked = true;
                else
                    document.getElementById('wr_secret').checked = false;
            }

            document.getElementById('comment_id').value = comment_id;
            document.getElementById('w').value = work;
            if (comment_id && work == 'c') {
                document.getElementById('wr_msg').innerHTML = '<i class="bi bi-person-circle"></i> ' + name + '님에게 대댓글 쓰기';
            } else if(comment_id && work == 'cu') {
                document.getElementById('wr_msg').innerHTML = '<i class="bi bi-pencil-square"></i> ' + name + '님의 댓글 수정';
            } else {
                document.getElementById('wr_msg').innerHTML = '<i class="bi bi-chat-dots"></i> 댓글을 입력해 주세요.';
            }

            <?php if($is_paging) { //페이징 ?>
            if (comment_id) {
                document.getElementById('comment_page').value = document.getElementById('comment_page_'+comment_id).value;
                document.getElementById('comment_url').value = document.getElementById('comment_url_'+comment_id).value;
            } else {
                document.getElementById('comment_page').value = '';
                document.getElementById('comment_url').value = '<?php echo NA_URL ?>/comment.page.php?bo_table=<?php echo $bo_table;?>&wr_id=<?php echo $wr_id;?>';
            }
            <?php } ?>

            if(save_before)
                $("#captcha_reload").trigger("click");

            save_before = el_id;

            if(el_id != "bo_vc_w") {
                // 답글, 수정인 경우 commentOffcanvas를 띄워준다.
                openCommentOffCanvas();
            } else {
                // 댓글 쓰기인 경우 별점 선택창 요소를 초기화 해준다.
                $("#wr_star option[value='0']").prop("selected", true);
                $("#star-rating .da-star").removeClass("star-fill");
                document.querySelector("#wr_content").value = "";
            }
        } else {
            if (el_id != "bo_vc_w") openCommentOffCanvas();
        }
        //$('.comment-textarea').find('textarea').keyup(); //댓글 수정 후, textarea height 자동조절
    }

    function comment_delete(url){
        na_confirm('이 댓글을 삭제하시겠습니까?', function() {
            location.href = url;
        });
        return false;
    }

    comment_box('', 'c'); // 댓글 입력폼이 보이도록 처리하기위해서 추가 (root님)

    // 댓글 링크 복사
    function copy_comment_link(commentId) {
        if (commentId !== "") {
            var fullCommentLink = window.location.protocol
                + "//" + window.location.host
                + "/<?php echo $bo_table;?>/<?php echo $wr_id;?>#c_" + commentId;

            navigator.clipboard.writeText(fullCommentLink).then(() => {
                show_message("댓글 주소가 복사되었습니다");
            }).catch(error => {
                show_message("댓글 복사에 실패하였습니다. 유지관리 게시판에 에러메시지를 포함하여 신고 바랍니다." + error);
            });
        }
    }
    // 알림 메시지를 화면 중앙에 출력한다.
    function show_message(message) {
        var $message = $('<div class="semi-alert-message">' + message + '</div>');

        var msgStyle = `
        <style>
            .semi-alert-message {
                display: none;
                width: 205px;
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background-color: #000;
                color: #fff;
                padding: 5px 10px;
                border-radius: 5px;
                z-index: 1000;
            }
        </style>`;
        $("head").append(msgStyle);
        $('body').append($message);

        $message.css({
            display: 'block'
        });

        setTimeout(() => {
            $message.fadeOut(500, function() {
                $(this).remove();
            });
        }, 1000);
    }

    $(function() {
        $('.comment-textarea').on('keyup', 'textarea', function (e){
            $(this).css('height', 'auto');

            $(this).height(this.scrollHeight - 22);
        });

        $('.comment-textarea').find('textarea').keyup();
    });

    </script>
<?php } ?>

<style>
    .da-commented-to {
        display: block;
        position: relative;
        top: -0.5rem;
        color: rgb(var(--bs-secondary-rgb));
        font-size: 0.875em;
        font-style: normal;
    }

    #float-comment {
        position: fixed;
        visibility: hidden;
    }
</style>
<script>
window.addEventListener('DOMContentLoaded', (event) => {
    const order1Element = document.querySelector('.order-1');
    const floatComment = document.getElementById('float-comment');

    function updatePosition() {
        const rect = order1Element.getBoundingClientRect();
        const floatCommentHeight = floatComment.offsetHeight;
        const viewportHeight = window.innerHeight;

        if (rect.bottom < viewportHeight) {
            // order1의 영역이 뷰포트보다 작을 때는 order1의 아래에 고정
            floatComment.style.top = (rect.bottom - floatCommentHeight) + 'px';
            floatComment.style.left = (rect.right - floatComment.offsetWidth) + 'px';
            floatComment.style.bottom = 'auto';
            floatComment.style.right = 'auto';
            floatComment.style.setProperty('padding-right', '1.5rem', 'important');
        } else {
            // order1의 영역이 뷰포트보다 클 때는 화면의 아래에 고정
            floatComment.style.bottom = '0';
            floatComment.style.left = (rect.right - floatComment.offsetWidth) + 'px';
            floatComment.style.top = 'auto';
            floatComment.style.right = 'auto';
            floatComment.style.setProperty('padding-right', '2.5rem', 'important');
            floatComment.style.setProperty('bottom', '5px', 'important');
        }
        floatComment.style.visibility = 'visible';
    }

    // 초기 위치 설정
    updatePosition();

    // 창 크기 조정 시 위치 업데이트
    window.addEventListener('resize', updatePosition);

    // 스크롤 시 위치 업데이트
    window.addEventListener('scroll', updatePosition);
});
</script>
