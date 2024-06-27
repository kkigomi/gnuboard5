<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
/*****************************************
 * webzine 타입 게시판 글목록을 출력하는 파일
 * 게시판 목록 스킨 설정: [게시판 글목록헤더의 gear아이콘->스킨설정->목록스킨:webzine]으로 설정하면 적용됨
 * plugin/nariya/bbs/list.php 에서 받은 글목록($list) item을 순환하여 출력한다.
 *****************************************/

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="' . $list_skin_url . '/list.css?CACHEBUST">', 0);

$list_cnt = count($list);

// 썸네일 및 이미지 비율
$thumb_w = G5_IS_MOBILE ? $board['bo_mobile_gallery_width'] : $board['bo_gallery_width'];
$thumb_h = G5_IS_MOBILE ? $board['bo_mobile_gallery_height'] : $board['bo_gallery_height'];

$thumb_w = $thumb_w ? $thumb_w : 400;
$thumb_h = $thumb_h ? $thumb_h : 300;
$ratio = na_img_ratio($thumb_w, $thumb_h, 75);
?>
<style>
#bo_list .ratio { --bs-aspect-ratio: <?php echo $ratio ?>%; overflow:hidden; }
</style>




<section id="bo_list" class="line-top">
    <?php
    /***********************************************
    * 공지 (목록형으로 표시)
    ************************************************/
    if($notice_count) { // 공지 
    ?>
        <ul class="list-group list-group-flush border-bottom">
      
        <?php
        /****************
        * 공지 나열 시작
        *****************/
        for ($i=0; $i < $list_cnt; $i++) {
            $isNotice = $list[$i]['is_notice'];
            $isPromotion = $list[$i]['is_advertiser_post']; //직홍게글

            // 공지글도 직홍게 홍보글도 아니라면 패스.
            if (!$isNotice && !$isPromotion )
                continue;

            /* 글유형(공지,잠금) 및 나의글 강조 css 클래스 및 $row값 세팅 */  
            $row = $list[$i];             
            $li_css = get_wr_class_and_set_row_f20240628($row, $wr_id);
        ?>
            <?php /******** 공지 글항목 아이템 <li> 시작: *******/ ?>
            <li class="list-group-item <?php echo $li_css ?>">
                <div class="d-flex align-items-center gap-1">

                    <?php /******** '공지' 표식  *******/ ?>
                    <?php //TODO: 홍보표식 추가 ?>
                    <div class="wr-num text-nowrap pe-2">
                        <?php echo $row['num'] ?>
                    </div>
                    










                    <?php /******** (관리자) 체크박스 *******/
                    if ($is_checkbox) { ?>
                        <div>
                            <input class="form-check-input me-1" type="checkbox" name="chk_wr_id[]" value="<?php echo $row['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
                            <label for="chk_wr_id_<?php echo $i ?>" class="visually-hidden">
                                <?php echo $row['subject'] ?>
                            </label>
                        </div>
                    <?php } ?>

                    <?php /******** 제목 칼럼 (회원만보기 + 답변글 표식 + 제목 + 첨부아이콘 + 메모)   *******/ ?>
                    <div class="flex-grow-1">

                        <?php /* TODO: 모바일 전용 : 제목앞에 '공지','홍보' 글자추가 (pc에서는 .pai-mb 숨겨짐) */ ?>



















                        <?php
                        // 회원만 보기
                        echo $row['da_member_only'] ?? '';
                        ?>
                        <!-- 답변글 표식 + 제목 링크-->
                        <a href="<?php echo $row['href'] ?>">
                            <?php if($row['icon_reply']) { ?>
                                <i class="bi bi-arrow-return-right"></i>
                                <span class="visually-hidden">답변</span>
                            <?php } ?>
                            <?php echo $row['subject']; // 제목 ?>
                        </a>




                        <?php //게시판 분류 카테고리 표식
                        if (!$sca && $is_category && $row['ca_name']) { ?>
                            <a href="<?php echo $row['ca_name_href'] ?>" class="badge text-body-tertiary px-1">
                                <?php echo $row['ca_name'] ?>
                                <span class="visually-hidden">분류</span>
                            </a>
                        <?php } ?>

                        
                        <?php // 제목 뒤 첨부유형 표식 아이콘 (N,사진,영상,파일,링크) 
                        echo get_attachment_icon_f20240628($row, na_check_youtube($row['wr_9']), na_check_img($row['wr_10'])); //구: $wr_icon ?>

                        <?php //댓글 카운트 표식
                        if($row['wr_comment']) { ?>
                            <span class="visually-hidden">댓글</span>
                            <span class="count-plus orangered">
                                <?php echo $row['wr_comment'] ?>
                            </span>
                        <?php } ?>


                        <?php // 회원 메모
                        if ($row['da_member_memo'] ?? '') { ?>
                            <span class="float-end"><?= $row['da_member_memo'] ?></span>
                        <?php } ?>
                    </div>

                    <?php /********  날짜시간 칼럼 ********/ ?>
                    <div class="wr-num text-nowrap ps-2 d-none d-sm-block">
                        <?php echo na_date($row['wr_datetime'], 'orangered') ?>
                        <span class="visually-hidden">등록</span>
                    </div>
                </div>
            </li>
  <?php } ?>
        </ul>
    <?php 
    } 
    /***********************************************
    * 끝: 공지 
    ************************************************/ ?>


    <?php 
    /*****************************************************
    * 사진 글목록 - 이미지썸네일 + 글제목 + Excerpt
    *******************************************************/    ?>
    <div class="p-3">
        <?php
        /******************
        * 웹진형식 글 나열 시작 
        *******************/
        for ($i=0; $i < $list_cnt; $i++) {
            // 공지, 홍보글은 제외
            if ($list[$i]['is_notice'] || $list[$i]['is_advertiser_post'] )
                continue;
    
            $row = $list[$i];
    
            //썸네일 이미지 셋팅. 회원만 보기 설정된 글은 썸네일 감춤
            $img = na_check_img($row['wr_10']); // 유뷰트 동영상(wr_9), 이미지(wr_10)
            $img = $img ? na_thumb($img, $thumb_w, $thumb_h) : G5_THEME_URL.'/img/no_image.gif';
            if ($list[$i]['da_is_member_only']) {
                $img = G5_THEME_URL . '/img/no_image.gif';
            }

            $label_band = get_grid_label_band_and_set_row($row,$wr_id );

        ?>
            <?php /************ 글목록 Row **********/ ?>
            <div class="card mb-3">
                <div class="row row-cols-1 row-cols-sm-2 g-0">
                    <?php /**** 썸네일 이미지 칼럼 ***/ ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="position-relative overflow-hidden">
                            <?php // 이미지 링크  ?>
                            <a href="<?php echo $row['href'] ?>" class="position-relative overflow-hidden">
                                <div class="ratio rounded-start">
                                    <img src="<?php echo $img ?>" class="object-fit-cover" alt="<?php echo str_replace('"', '', get_text($row['wr_subject'])) ?>">
                                </div>
                                <?php if($label_band) { ?>
                                    <div class="label-band text-bg-danger"><?php echo $label_band ?></div>
                                <?php } ?>
                            </a>
                            <?php  // (관리자) 체크박스
                            if ($is_checkbox) { ?>
                            <div class="position-absolute top-0 start-0 p-2 z-1">
                                <input class="form-check-input" type="checkbox" name="chk_wr_id[]" value="<?php echo $row['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
                                <label for="chk_wr_id_<?php echo $i ?>" class="visually-hidden">
                                <?php echo $row['subject'] ?>
                                </label>
                            </div>
                            <?php 
                            } ?>
                        </div>
                    </div>

                    <?php /**** 제목 + Excerpt + 메타정보 칼럼 ***/ ?>
                    <div class="col-md-8 col-lg-9">
                        <div class="card-body d-flex flex-column h-100">
                            <?php  /* 글제목 그룹 */  ?>
                            <div class="card-title">
                                <?php
                                // 회원만 보기
                                echo $row['da_member_only'] ?? '';
                                ?>
                                <a href="<?php echo $row['href'] ?>" class="fw-bold">
                                    <?php echo $row['subject'] ?>
                                </a>

                            <?php // 게시판 분류 카테고리
                            if (!$sca && $is_category && $row['ca_name']) { ?>
                                <a href="<?php echo $row['ca_name_href'] ?>" class="badge text-body-tertiary px-1">
                                    <?php echo $row['ca_name'] ?>
                                    <span class="visually-hidden">분류</span>
                                </a>
                            <?php } ?>

                            <?php //제목 뒤 첨부파일 유형 아이콘
                            echo get_attachment_icon_f20240628($row, na_check_youtube($row['wr_9']), $img); //구: $wr_icon; ?>

                            <?php //댓글 카운트
                            if($row['wr_comment']) { ?>
                                <span class="visually-hidden">댓글</span>
                                <span class="count-plus orangered">
                                    <?php echo $row['wr_comment'] ?>
                                </span>
                            <?php } ?>
                            </div>


                            <?php /* 본문 Excerpt */ ?>
                            <div class="card-text small text-body-secondary ellipsis-2 mb-2">
                                <?php echo na_get_text($row['wr_content']) ?>
                            </div>


                            <?php  /* 글 메타정보 그룹 */  ?>
                            <div class="mt-auto w-100">
                                <div class="d-flex align-items-end small wr-num text-nowrap gap-2">

                                    <?php //조회수 ?>
                                    <div class="flex-fill">
                                        <i class="bi bi-eye"></i>
                                        <?php echo $row['wr_hit'] ?>
                                        <span class="visually-hidden">조회</span>
                                    </div>

                                <?php //추천
                                if($is_good) { ?>
                                    <div>
                                        <i class="bi bi-hand-thumbs-up"></i>
                                        <?php echo $row['wr_good'] ?>
                                        <span class="visually-hidden">추천</span>
                                    </div>
                                <?php } ?>

                                <?php //회원 메모 
                                if ($row['da_member_memo'] ?? '') { ?>
                                    <span class="float-end"><?= $row['da_member_memo'] ?></span>
                                <?php } ?>

                                <?php // 날짜시간 ?>
                                    <div class="">
                                        <?php echo na_date($row['wr_datetime'], 'orangered') ?>
                                        <span class="visually-hidden">등록</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php } ?>

        <?php if ($list_cnt - $notice_count === 0) { ?>
            <div class="text-center py-5">
                게시물이 없습니다.
            </div>
        <?php } ?>
    </div>

</section>

<?php
/********
 * 함수 
 ********/ 


/** 글유형(공지,잠금) 및 나의글 강조 css 클래스를 반환하고 $row 참조값을 수정한다. */
function get_wr_class_and_set_row_f20240628(&$row, $wr_id)
{
    $li_css = '';
    if ($row['wr_7'] == 'lock')
    { // 잠금(wr_7)
        $li_css = '';
        $row['subject'] = '<span class="text-decoration-line-through">' . $row['subject'] . '</span>';
        $row['num'] = '<span class="orangered">잠금</span>';
    }
    else if ($wr_id == $row['wr_id'])
    { // 열람
        $li_css = ' bg-light-subtle';
        $row['subject'] = '<b class="text-primary fw-medium">' . $row['subject'] . '</b>';
        $row['num'] = '<span class="orangered">열람</span>';
    }
    else if ($row['is_notice'])
    { // 공지
        $li_css = ' bg-light-subtle';
        $row['subject'] = '<strong class="fw-medium">' . $row['subject'] . '</strong>';
        $row['num'] = '<span class="orangered">공지</span>';
        $row['wr_good'] = '<span class="orangered">공지</span>';
    }
    
    return $li_css;
}

/** 사진썸네일 label의 글자를 표시하고 $row 참조값 변경 */
function get_grid_label_band_and_set_row(&$row, $wr_id) {
    $label_band = '';
    
    if ($row['wr_7'] == 'lock') { // 잠금(wr_7)
        $label_band = 'LOCK';
        $row['subject'] = '<span class="text-decoration-line-through">'.$row['subject'].'</span>';
    } else if ($wr_id == $row['wr_id']) { // 열람
        $label_band = 'NOW';
        $row['subject'] = '<b class="text-primary fw-medium">'.$row['subject'].'</b>';
    }

    return $label_band;
}


/** 글 제목에 뒤따르는 추가 아이콘: N(새글),비밀글,인기글,영상첨부,사진첨부,파일포함,링크포함 표식 */
function get_attachment_icon_f20240628($row, $has_video, $has_img)
{
    $iconHtml = ''; // 구: $wr_icon
    if (isset($row['icon_new']) && $row['icon_new'])
        $iconHtml .= '<span class="na-icon na-new"></span>'.PHP_EOL;

    if (isset($row['icon_secret']) && $row['icon_secret'])
        $iconHtml .= '<span class="na-icon na-secret"></span>'.PHP_EOL;

    if (isset($row['icon_hot']) && $row['icon_hot'])
        $iconHtml .= '<span class="na-icon na-hot"></span>'.PHP_EOL;

    if ($has_video['vid'])
    {
        $iconHtml .= '<span class="na-icon na-video"></span>'.PHP_EOL;
    }
    else if ($has_img)
    {
        $iconHtml .= '<span class="na-icon na-image"></span>' . PHP_EOL;
    }
    else if (isset($row['icon_file']) && $row['icon_file'])
    {
        $iconHtml .= '<span class="na-icon na-file"></span>' . PHP_EOL;
    }
    else if (isset($row['icon_link']) && $row['icon_link'])
    {
        $iconHtml .= '<span class="na-icon na-link"></span>' . PHP_EOL;
    }

    return $iconHtml;
}