<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
/*****************************************
 * 게시판 대문의 글목록을 출력하는 파일(PC)
 * plugin/nariya/bbs/list.php 에서 받은 글목록($list) item을 순환하여 출력한다.
 *****************************************/

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="' . $list_skin_url . '/list.css?CACHEBUST">', 0);
?>

<section id="bo_list" class="line-top mb-3">
    <ul class="list-group list-group-flush border-bottom">

        <?php 
        /*****************************************
         * 글목록 칼럼 제목줄 출력  
         *****************************************/ 
        ?>
        <li class="list-group-item d-none d-md-block hd-wrap">
            <div class="d-flex flex-md-row align-items-md-center gap-1 fw-bold">
                <?php if ($is_good) { ?>
                    <div class="hd-num text-center">추천</div>
                <?php } ?>
                <div class="text-center flex-grow-1">제목</div>
                <div class="ms-md-auto">
                    <div class="d-flex gap-2">
                        <?php if (!isset($boset['check_list_hide_profile']) || (isset($boset['check_list_hide_profile']) && !$boset['check_list_hide_profile'])) { ?>
                            <div class="hd-name text-center">이름</div>
                        <?php } ?>
                        <div class="hd-date text-center">날짜</div>
                        <div class="hd-num text-center">조회</div>
                    </div>
                </div>
            </div>
        </li>


    <?php
        /*****************************************
         * 글 항목( li 아이템) iteration  
         * $list 변수는 plugin/nariya/bbs/list.php (각 게시판 글목록을 만드는 플러그인 파일)에 있음.
         *****************************************/
        $list_cnt = count($list);
        for ($i=0; $i < $list_cnt; $i++) {
            // 여분필드 사용 내역
            // wr_7 : 신고(lock)
            // wr_8 : 태그
            // wr_9 : 유튜브 동영상
            // wr_10 : 대표 이미지

            $row = $list[$i];

            /* 글유형(공지,잠금) 및 나의글 강조 css 클래스*/
            $li_css = get_wr_class_and_set_row_f20240616($row, $wr_id);
            // 내가 작성한 글 강조하기
            $writter_bg = "";
            if(trim($list[$i]['mb_id']) == trim($member['mb_id'])){
                $writter_bg = "writter-bg";
            }
            /***************** <li> 글항목 아이템 시작: *************************/
        ?>
            <li class="list-group-item da-link-block <?php echo $li_css; ?> <?php echo $writter_bg; ?>">
                <div class="d-flex align-items-center gap-1">

                    <?php 
                    /******** 추천 칼럼: 공지, 홍보, 추천수 표식 ********
                     * '홍보' 글이라면 별도의 컬러 사용( is_advertiser_post는 plugin/nariya/bbs/list.php 직홍게 위젯 PAI 코드에서 세팅됨 ) 
                     ****************************************************/
                     if (isset($row['is_advertiser_post']) && $row['is_advertiser_post']) { 
                        echo <<<EOT
                            <div class="wr-num text-nowrap rcmd-pc">
                                <div class="rcmd-box step-pai">
                                    <span>홍보</span>
                                </div>
                            </div>
                        EOT;
                    } else if ($is_good) { 
                        /* '추천'칼럼 사용시 : 공지/일반글(추천수에따라 다른 컬러스텝)*/
                        $rcmd_step = get_color_step_f20240616($row['wr_good']);
                        echo <<<EOT
                            <div class="wr-num text-nowrap rcmd-pc">
                                <i class="bi bi-hand-thumbs-up d-inline-block d-md-none"></i>
                                <div class="{$rcmd_step}">
                                    {$row['wr_good']}
                                </div>
                                <span class="visually-hidden">추천</span>
                            </div>
                        EOT;
                    } 
                    ?>

                    <!-- <div class="col-1 wr-no d-none d-md-block">
                        <?//php echo $row['num'] ?>
                    </div> -->


                    <?php /******** 체크박스 칼럼(관리자) ********/
                     if ($is_checkbox) { ?>
                        <div>
                            <input class="form-check-input me-1" type="checkbox" name="chk_wr_id[]" value="<?php echo $row['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
                            <label for="chk_wr_id_<?php echo $i ?>" class="visually-hidden">
                                <?php echo $row['subject'] ?>
                            </label>
                        </div>
                    <?php } ?>
                    <?php /******** 제목칼럼 + 메타그룹 시작 : ********/ ?>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                            
                        <?php /******** 제목 칼럼 시작 : ********/ ?>
                            <div class="d-inline-flex flex-fill overflow-hidden align-items-center">
                                <?php
                                /*  제목앞 추가 '홍보' 표식 ( 모바일용. pc에서는 .pai-mb 숨겨짐) */
                                if (isset($row['is_advertiser_post']) && $row['is_advertiser_post']) { 
                                    echo <<<EOT
                                        <div class="wr-num text-nowrap pai-mb">
                                            <div class="rcmd-box step-pai">
                                                <span>홍보</span>
                                            </div>
                                        </div>
                                    EOT;
                                }

                                /* '회원만' 보기 표식 */
                                echo $row['da_member_only'] ?? '';
                                
                                /* 글제목: '답변'글 표식  + 글제목 */
                                ?>
                                <a href="<?php echo $row['href'] ?>" class="da-link-block subject-ellipsis" title="<?php echo $row['wr_subject']; ?>">
                                    <?php if($row['icon_reply']) { ?>
                                        <i class="bi bi-arrow-return-right"></i>
                                        <span class="visually-hidden">답변</span>
                                    <?php } ?>
                                    <?php echo $row['subject']; // 제목 ?>
                                </a>

                                <?php /* 글 카테고리 분류명 표식 */
                                if (!$sca && $is_category && $row['ca_name']) { ?>
                                    <a href="<?php echo $row['ca_name_href'] ?>" class="badge text-body-tertiary px-1">
                                        <?php echo $row['ca_name'] ?>
                                        <span class="visually-hidden">분류</span>
                                    </a>
                                <?php } ?>

                                <?php  
                                /* 제목 뒤 첨부파일 유형 표식 (사진,링크,영상,N,HOT 등 ) */
                                $attachmentIcon = get_attachment_icon_f20240616($row, na_check_youtube($row['wr_9']), na_check_img($row['wr_10']));
                                echo $attachmentIcon; 
                                ?>

                                <?php /* 댓글표식 */
                                if($row['wr_comment']) { ?>
                                    <span class="visually-hidden">댓글</span>
                                    <span class="count-plus orangered">
                                        <?php echo $row['wr_comment'] ?>
                                    </span>
                                <?php } ?>

                                <?php  /* 회원메모 출력 */
                                if ($row['da_member_memo'] ?? '') { ?>
                                    <!-- 다모앙 회원 메모 -->
                                    <span class="ms-auto"><?= $row['da_member_memo'] ?></span>
                                <?php } ?>
                            </div>
                            <?php 
                            /******** : 제목 칼럼 끝 ********/



                            /******** 메타 그룹 시작: ********/
                            ?>
                            <div class="da-list-meta">
                                <div class="d-flex gap-2">

                                    <?php /******** 글쓴이 프사+이름 ********/ ?>
                                    <?php if (!isset($boset['check_list_hide_profile']) || (isset($boset['check_list_hide_profile']) && !$boset['check_list_hide_profile'])) { ?>
                                        <div class="wr-name ms-auto order-last order-md-1 text-truncate">
                                            <?php
                                                $wr_name = ($row['mb_id']) ? str_replace('sv_member', 'sv_member text-truncate d-block', $row['name']) : str_replace('sv_guest', 'sv_guest text-truncate d-block', $row['name']);
                                                echo na_name_photo($row['mb_id'], $wr_name);
                                            ?>
                                        </div>
                                    <?php } ?>


                                    <?php /******** 글쓴 시간 ********/ ?>
                                    <div class="wr-date text-nowrap order-5 order-md-2">
                                        <i class="bi bi-clock d-inline-block d-md-none"></i>
                                        <?php echo na_date($row['wr_datetime'], 'orangered da-list-date') ?>
                                        <span class="visually-hidden">등록</span>
                                    </div>
                                    
                                    <?php /******** 추천수 (모바일) ********/
                                    if($is_good && $row['wr_good'] > 0) { ?>
                                        <!-- 추천 수 (모바일) -->
                                        <div class="wr-num da-rcmd rcmd-mb text-nowrap d-md-none">
                                            <div class="<?php echo $rcmd_step ?> w-auto">
                                            <?php if(!strpos($row['wr_good'], '공지')) { ?>
                                                <i class="bi bi-hand-thumbs-up" style="font-size:.7rem"></i>
                                            <?php } ?>
                                            <?php echo $row['wr_good'] ?>
                                            </div>
                                            <span class="visually-hidden">추천</span>
                                        </div>
                                    <?php } 
                                    

                                    /******** 조회수 & 댓글 ********/
                                    ?>
                                    <div class="wr-num text-nowrap order-4">
                                        <i class="bi bi-eye d-inline-block d-md-none"></i>
                                        <?php echo $row['wr_hit'] ?>
                                        <span class="visually-hidden">조회</span>
                                    </div>
                                    <div class="wr-num text-nowrap order-2 d-md-none d-none da-list-meta--comments">
                                        <i class="bi bi-chat-dots d-inline-block d-md-none"></i>
                                        <?php echo $row['wr_comment'] ?>
                                        <span class="visually-hidden">댓글</span>
                                    </div>
                                </div>
                            </div>
                            <?php /******** : 메타 그룹 끝 ********/ ?>
                        </div>
                    </div>
                    <?php /******** : 제목칼럼 + 메타그룹 끝 ********/ ?>
                </div>
            </li>
    <?php } 
        /*****************************************
         * 끝: 글 항목( li 아이템) iteration  
         *****************************************/  ?>
    
    <?php if ($list_cnt - $notice_count === 0) { ?>
        <li class="list-group-item text-center py-5">
            게시물이 없습니다.
        </li>
    <?php } ?>
    </ul>
</section>

<?php
/********
 * 함수 
 *******/
function get_wr_class_and_set_row_f20240616(&$row, $wr_id)
{
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
    else
    {
        $li_css = '';
    }
    return $li_css;
}


function get_color_step_f20240616($wr_good)
{
    if (strpos($wr_good, '공지') !== false)
    {
        return ""; // 공지는 컬러 박스 없음
    }
    switch (true)
    {
        case $wr_good == 0:
            return "rcmd-box step0";
        case $wr_good <= 5:
            return "rcmd-box step1";
        case $wr_good > 5 && $wr_good <= 10:
            return "rcmd-box step2";
        case $wr_good > 10 && $wr_good <= 50:
            return "rcmd-box step3";
        case $wr_good > 50:
            return "rcmd-box step4";
        default:
            return "rcmd-box step1";
    }
}

/** 글 제목에 뒤따르는 추가 아이콘: N(새글),비밀글,인기글,영상첨부,사진첨부,파일포함,링크포함 표식 */
function get_attachment_icon_f20240616($row, $has_video, $has_img)
{
    $iconHtml = '';
    if (isset($row['icon_new']) && $row['icon_new'])
        $iconHtml .= '<span class="na-icon na-new"></span>' . PHP_EOL;

    if (isset($row['icon_secret']) && $row['icon_secret'])
        $iconHtml .= '<span class="na-icon na-secret"></span>' . PHP_EOL;

    if (isset($row['icon_hot']) && $row['icon_hot'])
        $iconHtml .= '<span class="na-icon na-hot"></span>' . PHP_EOL;

    if ($has_video['vid'])
    {
        $iconHtml .= '<span class="na-icon na-video"></span>' . PHP_EOL;
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
