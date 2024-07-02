<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

global $view, $mb, $bo_table;

// 비회원글 제외
if (!isset($view['mb_id']) || !$view['mb_id'])
    return;

$mbs = array();
$mbs = (isset($mb['mb_id']) && $mb['mb_id']) ? $mb : get_member($view['mb_id']);

// 회원정보가 없거나, 차단 또는 탈퇴회원 제외
if (!(isset($mbs['mb_id']) && $mbs['mb_id']) || (isset($mbs['mb_intercept_date']) && $mbs['mb_intercept_date']) || (isset($mbs['mb_leave_date']) && $mbs['mb_leave_date'])) {
    return;
}

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
// add_stylesheet('<link rel="stylesheet" href="'.$widget_url.'/widget.css" type="text/css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.$widget_url.'/widget.css?CACHEBUST" type="text/css">', 0);
$mbs['as_max'] = (isset($mbs['as_max']) && $mbs['as_max'] > 0) ? $mbs['as_max'] : 1;
$per = (int) (($mbs['as_exp'] / $mbs['as_max']) * 100);

// 글 추출(아래 2가지 값만 필수 값이며 다른 옵션은 지원하지 않음)
$wset['mb_list'] = $view['mb_id'];
$wset['rows'] = 10;

$sign_list = array();
//$sign_list = na_board_rows($wset);
$sign_list = recent_board_rows($wset);
$sign_list_cnt = count($sign_list);
/*
서명 디자인 옵션
    1 : 서명이 프로필 오른쪽 영역에 표시
    2 : 서명이 프로필 아래 영역에 단독으로 표시
*/
$mb_sign_ui = '1';

/*
 배너 옵션(LONG, SHORT, NONE)
    LONG : 긴 배너, 설명 없이 긴 이미지배너로 구성된다.
    SHORT : 짧은 배너(배너와 설명으로 구성되어 진다)
    TWIN : 짧은 배너 광고 2개가 5:5 배율로 구성된다.
    NONE : 배너 광고 사용하지 않음(화면에서 감춘다)
 */
$mb_sign_banner_type = 'NONE';
?>

<!-- ================= 서명 New Start=================  -->
<aside id="bo_v_sign">
    <div class="border mx-0 mb-3 p-3 rounded-3">
        <div class="row row-cols-1 row-cols-md-2 align-items-center" id="sign-profile-container">
            <div class="col-md-4 col-sm-5 pb-3" id="sign-profile">
                <div class="text-center mb-2 mb-sm-0">
                    <img src="<?php echo na_member_photo($mbs['mb_id']) ?>" class="rounded-circle">
                </div>
                <div class="clearfix f-sm">
                    <span class="float-start d-flex pt-1">
                        <?php echo na_xp_icon($mbs['mb_id'], '', $mbs) ?>
                        <?php echo $view['name'] ?>
                    </span>
                    <span class="float-end">
                        Exp <?php echo number_format($mb['as_exp']) ?>
                    </span>
                </div>
                <div class="progress" title="레벨업까지 <?php echo number_format($mbs['as_max'] - $mbs['as_exp']); ?> 경험치 필요">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $per ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $per ?>%">
                        <span class="sr-only"><?php echo $per ?>%</span>
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-sm-7 border-start" id="sign-content">
                <p class="mt-3"><?php echo $mb['mb_signature'] ?></p>
            </div>
        </div>


        <div class="border-top mt-3" id="sign-recent-list-container">
            <div id="sign-recent-list">
                <ul class="list-group list-group-flush border-bottom" style="padding-left:0px;overflow-y:auto;max-height:205px">
                    <?php
                    // 리스트
                    for ($i = 0; $i < $sign_list_cnt; $i++) {
                        // 아이콘 체크
                        if (isset($sign_list[$i]['icon_secret']) && $sign_list[$i]['icon_secret']) {
                            $is_lock = true;
                            $wr_icon = '<span class="na-icon na-secret"></span> ';
                        } else if (isset($sign_list[$i]['icon_new']) && $sign_list[$i]['icon_new']) {
                            $wr_icon = '<span class="na-icon na-new"></span> ';
                        } else {
                            $wr_icon = '';
                        }

                        // 파일 아이콘
                        $icon_file = '';
                        if ($thumb || (isset($sign_list[$i]['as_thumb']) && $sign_list[$i]['as_thumb'])) {
                            $icon_file = '<span class="na-icon na-image"></span>';
                        } else if (isset($sign_list[$i]['icon_file']) && $sign_list[$i]['icon_file']) {
                            $icon_file = '<span class="na-icon na-file"></span>';
                        }
                        ?>
                        <li class="list-group-item d-flex">
                            <div class="d-flex flex-fill overflow-hidden align-items-center">
                                <?php
                                /* '회원만' 보기 표식 */
                                echo $sign_list[$i]['da_member_only'] ?? '';

                                /* 글제목: 새글표식+글제목+첨부표식+댓글표식 */
                                ?>
                                <a href="<?php echo $sign_list[$i]['href'] ?>" class="da-link-block subject-ellipsis" title="<?php echo $sign_list[$i]['wr_subject']; ?>">
                                    <?php echo "<span class='subject-mobile d-none'>".$sign_list[$i]['subject']."</span>"; // 제목?>
                                    <?php echo "<span class='subject-pc'>"."[".$sign_list[$i]['bo_subject']."] ".$sign_list[$i]['subject']."</span>"; // 제목?>
                                    <?php echo $wr_icon ?>
                                    <?php echo $icon_file ?>
                                </a>

                                <?php /* 댓글표식 */ if ($sign_list[$i]['wr_comment']) { ?>
                                    <span class="visually-hidden">댓글</span>
                                    <span class="count-plus orangered mx-1">
                                        <?php echo $sign_list[$i]['wr_comment'] ?>
                                    </span>
                                <?php } ?>
                            </div>

                            <div class="f-sm fw-normal ms-md-2" style="white-space:nowrap">
                                <span class="sr-only">등록일</span>
                                <?php echo na_date($sign_list[$i]['wr_datetime'], 'orangered') ?>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    <!-- 왼쪽 컨텐츠 End -->
     <?php
        if($mb_sign_ui == '1' && $mb['mb_signature'] && $mb['mb_signature'] != ''){
     ?>
        <div class="border-top"></div>
        <div class="row row-cols-1 align-items-center" id="sign-content-type1">
            <div id="sign-content">
                <p class="mt-3"><?php echo $mb['mb_signature'] ?></p>
            </div>
        </div>
    <?php } ?>
    <?php
        if($mb_sign_banner_type == "SHORT") {
    ?>
            <?php // 짧은 배너를 사용할 경우 아래에 짧은 배너 정보를 넣어주세요.?>
            <div class="border-top"></div>
            <div class="row row-cols-1 row-cols-md-2 align-items-center pt-3" id="sign-ad-container">
                <div class="col-md-4 col-sm-5 border-end text-center" id="sign-ad-banner-s1">
                    <?php echo na_widget('damoang-image-banner', 'sign-banner'); ?>
                    <div class="mb-4">
                    <a href="https://damoang.net/notice/13580" rel="nofollow noopenner" target="_blank">
                        <img src="https://damoang.net/data/nariya/image/banner_image-40bd001563085fc35165329ea1ff5c5ecbdbbeef.webp" style="max-width: 100%;" alt="" title="">
                    </a>
                    </div>
                </div>
                <div class="col-md-8 col-sm-7" id="sign-ad-content">
                    신규 광고 slot 발견 ❤️❤️ 일단은 저만 쓸게요. <br>
                    비싼(프리미엄) 자리에 광고를 넣어보세요. <br>
                </div>
            </div>
    <?php } else if($mb_sign_banner_type == "TWIN") {?>
            <?php // 짧은 배너 2개를 사용할 경우 아래에 짧은 배너 2개를 넣어주세요.?>
            <div class="border-top"></div>
            <div class="row row-cols-1 row-cols-md-2 align-items-center pt-3" id="sign-ad-container">
                <div class="col-md-6 col-sm-6 border-end text-center" id="sign-ad-banner-t1">
                    <?php echo na_widget('damoang-image-banner', 'sign-banner'); ?>
                    <div class="mb-4">
                    <a href="https://damoang.net/notice/13580" rel="nofollow noopenner" target="_blank">
                        <img src="https://damoang.net/data/nariya/image/banner_image-40bd001563085fc35165329ea1ff5c5ecbdbbeef.webp" style="max-width: 100%;" alt="" title="">
                    </a>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 text-center" id="sign-ad-banner-t2">
                    <?php echo na_widget('damoang-image-banner', 'sign-banner'); ?>
                    <a href="https://damoang.net/notice/13580" rel="nofollow noopenner" target="_blank">
            <img src="https://damoang.net/data/nariya/image/banner_image-40bd001563085fc35165329ea1ff5c5ecbdbbeef.webp" style="max-width: 100%;" alt="" title="">
        </a>
                </div>
            </div>
    <?php } else if($mb_sign_banner_type == "LONG") {?>
            <?php // 긴 배너를 사용할 경우 아래에 긴 배너 정보를 넣어주세요.?>
            <div class="border-top"></div>
            <div class="row row-cols-1 align-items-center pt-3" id="sign-ad-container">
                <div id="sign-ad-banner-l1" class="text-center">
                    <?php //echo na_widget('damoang-image-banner', 'sign-banner'); ?>
                    <div class="mb-4">
                    <a href="https://damoang.net/notice/13580" rel="nofollow noopenner" target="_blank">
                        <img src="https://damoang.net/data/nariya/image/banner_image-40bd001563085fc35165329ea1ff5c5ecbdbbeef.webp" style="max-width: 100%;" alt="" title="">
                    </a>
                    </div>
                </div>
            </div>
    <?php } ?>
    </div>
</aside>

<?php if ($setup_href) { ?>
    <div class="btn-wset">
        <a href="<?php echo $setup_href; ?>" class="btn-setup">
            <span class="f-sm text-muted"><i class="fa fa-cog"></i> 위젯설정</span>
        </a>
    </div>
<?php } ?>
<script>
    var signature = `<?=$mb['mb_signature']?>`;
    var signRecentList = $("#sign-recent-list");
    var signContent = $("#sign-content");
    var signRecentListContainer = $("#sign-recent-list-container");
    var signProfile = $("#sign-profile");
    var mgSignBannerType = `<?=$mb_sign_banner_type?>`;

    function moveRecentListToContent() {
        signRecentList.hide();
        signContent.addClass("border-start").html(signRecentList.html());
        signRecentList.remove();
        signRecentListContainer.removeClass("border-top");
    }

    if (!signature || "<?=$mb_sign_ui?>" === "1") {
        moveRecentListToContent();
    } else {
        signRecentList.find("ul").css("max-height", "205px");
        signProfile.addClass("border-end");
        signContent.removeClass("border-start");
    }
</script>

<?php
// 게시판 통합 최신글 가져오는 함수
function recent_board_rows($wset) {
    $wset['bo_new'] = isset($wset['bo_new']) ? (int)$wset['bo_new'] : 24;
    $rows = isset($wset['rows']) ? (int)$wset['rows'] : 5;
    $rows = ($rows > 0) ? $rows : 5;

    $list = array();

    // 전체 게시판 기준 최신 00개 게시글 가져오는 개별 쿼리 생성
    $generated_post_query = sql_query("SELECT
            CONCAT(
            'SELECT ''', bo_table, ''' as bo_table, wr_id, (SELECT bo_subject FROM g5_board WHERE bo_table = ''', bo_table, ''') as bo_subject, wr_subject, wr_datetime, mb_id, wr_file, wr_option, wr_1, wr_7, wr_comment FROM g5_write_', bo_table,
            ' WHERE wr_id=', wr_id
            ) as post_query
        FROM
            g5_board_new
        WHERE
            mb_id = '".$wset['mb_list']."'
            and wr_is_comment = 0
        ORDER BY
            bn_datetime DESC
        LIMIT ".$rows);

    if (!$generated_post_query) {
        return $list;
    }

    while ($row = sql_fetch_array($generated_post_query)) {
        $post_query = $row['post_query'];

        // 개별 쿼리 실행
        $post_result = sql_query($post_query);
        if (!$post_result) {
            // 개별 쿼리 실행 실패
            continue;
        }

        while ($post_row = sql_fetch_array($post_result)) {
            $list[] = array(
                'bo_table' => $post_row['bo_table'],
                'bo_subject' => $post_row['bo_subject'],
                'subject' => $post_row['wr_subject'],
                'wr_subject' => $post_row['wr_subject'],
                'wr_datetime' => $post_row['wr_datetime'],
                'mb_id' => $post_row['mb_id'],
                'wr_comment' => $post_row['wr_comment'],
                'icon_new' => ($post_row['wr_datetime'] >= date("Y-m-d H:i:s", G5_SERVER_TIME - ($wset['bo_new'] * 3600))) ? true : false,
                'icon_secret' => (strstr($post_row['wr_option'], 'secret') || $post_row['wr_7'] == 'lock') ? true : false,
                'da_member_only' => ($post_row['wr_1'] == '1') ? '<em class="border rounded p-1 me-1" style="font-size: 0.75em; font-style: normal; min-width:44px">회원만</em>' : false,
                'icon_file' => !empty($post_row['wr_file']) ? true : false,
                'href' => "/".$post_row['bo_table']."/".$post_row['wr_id'],
            );
        }
    }

    return $list;
}


?>
