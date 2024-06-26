<?php
if (!defined('_GNUBOARD_')) exit; //개별 페이지 접근 불가

global $view, $mb, $bo_table;

// 비회원글 제외
if(!isset($view['mb_id']) || !$view['mb_id'])
	return;

$mbs = array();
$mbs = (isset($mb['mb_id']) && $mb['mb_id']) ? $mb : get_member($view['mb_id']);

// 회원정보가 없거나, 차단 또는 탈퇴회원 제외
if(!(isset($mbs['mb_id']) && $mbs['mb_id']) || (isset($mbs['mb_intercept_date']) && $mbs['mb_intercept_date']) || (isset($mbs['mb_leave_date']) && $mbs['mb_leave_date']))
	return;

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
// add_stylesheet('<link rel="stylesheet" href="'.$widget_url.'/widget.css" type="text/css">', 0);
//add_stylesheet('<link rel="stylesheet" href="'.$widget_url.'/widget.css?CACHEBUST" type="text/css">', 0);
$mbs['as_max'] = (isset($mbs['as_max']) && $mbs['as_max'] > 0) ? $mbs['as_max'] : 1;
$per = (int)(($mbs['as_exp'] / $mbs['as_max']) * 100);

// 글 추출
$wset['bo_list'] = $bo_table;
$wset['mb_list'] = $view['mb_id'];
$wset['rows'] = 10;
$wset['sort'] = 'wr_num';

$sign_list = array();
$sign_list = na_board_rows($wset);
$sign_list_cnt = count($sign_list);

?>
<style>
    .float-start .sv_wrap a .profile_img {
        display: none !important;
    }
    .float-start .xp-icon {
        margin-right: 2px;
    }
</style>
<!-- ================= 서명 New Start=================  -->
<div class="border mx-3 mx-sm-0 mb-3 p-3">
    <div class="row row-cols-1 row-cols-md-2 align-items-center">
        <div class="col-sm-5 col-md-4 pb-3">

            <?php echo na_widget('damoang-image-banner', 'sign-banner'); ?>
            신규 광고 slot 발견 ❤️❤️ 일단은 저만 쓸게요. <br>
            비싼 자리에 광고를 넣어보세요. <br>
        </div>
        <div class="col-md-8 col-sm-7 border-start" id="sign-content">
            <p class="mt-3"><?php echo $mb['mb_signature'] ?></p>
        </div>
        <div class="col-md-4 col-sm-5 pb-3">
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
            <div class="progress" title="레벨업까지 <?php echo number_format($mbs['as_max'] - $mbs['as_exp']);?> 경험치 필요">
                <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $per ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $per ?>%">
                    <span class="sr-only"><?php echo $per ?>%</span>
                </div>
            </div>
        </div>
    </div>
    <div class="border-top" id="sign-recent-list-container">
    <div id="sign-recent-list">
            <ul class="list-group list-group-flush border-bottom" style="padding-left:0px;overflow-y:auto;max-height:205px">
            <?php
            // 리스트
            for ($i=0; $i < $sign_list_cnt; $i++) {

                // 아이콘 체크
                if (isset($sign_list[$i]['icon_secret']) && $sign_list[$i]['icon_secret']) {
                    $is_lock = true;
                    $wr_icon = '<span class="na-icon na-secret"></span> ';
                } else if(isset($sign_list[$i]['icon_new']) && $sign_list[$i]['icon_new']) {
                    $wr_icon = '<span class="na-icon na-new"></span> ';
                } else {
                    $wr_icon = '';
                }

                // 파일 아이콘
                $icon_file = '';
                if($thumb || (isset($sign_list[$i]['as_thumb']) && $sign_list[$i]['as_thumb'])) {
                    $icon_file = '<span class="na-ticon na-image"></span>';
                } else if(isset($sign_list[$i]['icon_file']) && $sign_list[$i]['icon_file']) {
                    $icon_file = '<span class="na-ticon na-file"></span>';
                }
            ?>
                <li class="list-group-item d-flex">
                    <div class="d-flex flex-fill overflow-hidden align-items-center">
                        <?php

                        /* '회원만' 보기 표식 */
                        echo $sign_list[$i]['da_member_only'] ?? '';

                        /* 글제목: '답변'글 표식  + 글제목 */
                        ?>
                        <a href="<?php echo $sign_list[$i]['href'] ?>" class="da-link-block subject-ellipsis" title="<?php echo $sign_list[$i]['wr_subject']; ?>">
                            <?php if($sign_list[$i]['icon_reply']) { ?>
                                <i class="bi bi-arrow-return-right"></i>
                                <span class="visually-hidden">답변</span>
                            <?php } ?>
                            <?php echo $wr_icon ?>
                            <?php echo $sign_list[$i]['subject']; // 제목 ?>
                        </a>

                        <?php /* 댓글표식 */
                        if($sign_list[$i]['wr_comment']) { ?>
                            <span class="visually-hidden">댓글</span>
                            <span class="count-plus orangered mx-1">
                                <?php echo $sign_list[$i]['wr_comment'] ?>
                            </span>
                        <?php } ?>
                    </div>

                    <div class="f-sm fw-normal ms-md-2">
                        <span class="sr-only">등록일</span>
                        <?php echo na_date($sign_list[$i]['wr_datetime'], 'orangered') ?>
                    </div>
                </li>
            <?php } ?>
            </ul>
        </div>
    </div>
</div>
<!-- ================= 서명 New Start=================  -->
<?php if($setup_href) { ?>
	<div class="btn-wset">
		<a href="<?php echo $setup_href;?>" class="btn-setup">
			<span class="f-sm text-muted"><i class="fa fa-cog"></i> 위젯설정</span>
		</a>
	</div>
<?php } ?>
<script>
var signature = `<?=$mb['mb_signature']?>`;

if (signature === "" || signature === null || signature === undefined) {
    $("#sign-recent-list").hide();
    var recentList = $("#sign-recent-list").html();
    $("#sign-content").html(recentList);
    $("#sign-recent-list").remove();
    $("#sign-recent-list-container").removeClass("border-top");
}

</script>
