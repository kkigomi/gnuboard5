<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<!-- .offcanvas, .offcanvas-lg, .offcanvas-md, .offcanvas-sm, .offcanvas-xl, .offcanvas-xxl -->
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="commentOffcanvas" data-bs-scroll="true" aria-labelledby="commentOffcanvasLabel">
    <div class="offcanvas-header pb-0">
        <h5 class="offcanvas-title" id="commentOffcanvasLabel">
            <span class="visually-hidden">전체 검색</span>
        </h5>
        <button type="button" class="btn-close nofocus" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="container h-75 w-100">
            <div class="row justify-content-center align-items-center h-75 p-sm-0">
                <div class="col-md-12 col-xl-8 col-xxl-8" style="padding: 0">
                    <aside id="bo_vc_w">
                        <h3 class="visually-hidden">댓글쓰기</h3>
                        <form id="fviewcomment" name="fviewcomment" action="<?php echo $comment_action_url; ?>" onsubmit="return fviewcomment_submit(this);" method="post" autocomplete="off" class="px-3 mb-3">
                        <input type="hidden" name="w" value="<?php echo $w ?>" id="w">
                        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
                        <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
                        <input type="hidden" name="comment_id" value="<?php echo $c_id ?>" id="comment_id">
                        <?php if($is_paging) { //페이징 ?>
                            <input type="hidden" name="comment_url" value="" id="comment_url">
                            <input type="hidden" name="cob" value="<?php echo $cob ?>">
                        <?php } ?>
                        <input type="hidden" name="sca" value="<?php echo $sca ?>">
                        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
                        <input type="hidden" name="stx" value="<?php echo $stx ?>">
                        <input type="hidden" name="spt" value="<?php echo $spt ?>">
                        <input type="hidden" name="page" value="<?php echo $page ?>" id="comment_page">
                        <input type="hidden" name="is_good" value="">

                        <div class="p-2 bg-body-tertiary border rounded">
                            <?php if ($is_guest) { ?>
                                <div class="row g-2 mb-2">
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <span class="input-group-text" id="comment_name">
                                                <i class="bi bi-person"></i>
                                                <span class="visually-hidden">이름<strong> 필수</strong></span>
                                            </span>
                                            <input type="text" name="wr_name" value="<?php echo get_cookie("ck_sns_name"); ?>" id="wr_name" class="form-control" placeholder="이름" aria-label="이름" aria-describedby="comment_name" maxLength="20">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <span class="input-group-text" id="comment_password">
                                                <i class="bi bi-shield-lock"></i>
                                                <span class="visually-hidden">비밀번호<strong> 필수</strong></span>
                                            </span>
                                            <input type="password" name="wr_password" value="<?php echo get_cookie("ck_sns_name"); ?>" id="wr_password" class="form-control" placeholder="비밀번호" aria-label="비밀번호" aria-describedby="comment_password" maxLength="20">
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if ($comment_min || $comment_max) { ?>
                                <div class="small mb-2" id="char_cnt">
                                    현재 <b id="char_count">0</b>글자
                                    /
                                    <?php if($comment_min) { ?>
                                        <?php echo number_format((int)$comment_min);?>글자 이상
                                    <?php } ?>
                                    <?php if($comment_max) { ?>
                                        <?php echo number_format((int)$comment_max);?>글자 이하
                                    <?php } ?>
                                    등록 가능
                                </div>
                            <?php } ?>

                            <?php if (isset($boset['check_star_rating']) && $boset['check_star_rating']) { ?>
                                <!-- 별점 기능 { -->
                                <div class="mb-2">
                                    <div id="bo_vc_star" class="col-sm-3">
                                        <select name="wr_6" id="wr_star" style="width:120px" class="form-select form-select-sm mb-2">
                                            <option value="0">평가</option>
                                            <option value="1">0.5점</option>
                                            <option value="2">1점</option>
                                            <option value="3">1.5점</option>
                                            <option value="4">2점</option>
                                            <option value="5">2.5점</option>
                                            <option value="6">3점</option>
                                            <option value="7">3.5점</option>
                                            <option value="8">4점</option>
                                            <option value="9">4.5점</option>
                                            <option value="10">5점</option>
                                        </select>
                                        <!-- Add this inside the form where the comment is being posted -->
                                        <div id="star-rating" class="star-rating d-flex">
                                            <div class="da-star star-l"></div>
                                            <div class="da-star star-r"></div>
                                            <div class="da-star star-l"></div>
                                            <div class="da-star star-r"></div>
                                            <div class="da-star star-l"></div>
                                            <div class="da-star star-r"></div>
                                            <div class="da-star star-l"></div>
                                            <div class="da-star star-r"></div>
                                            <div class="da-star star-l"></div>
                                            <div class="da-star star-r"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- } 별점 기능 -->
                            <?php } ?>

                            <style>
                            #wr_content {
                                height:92px; resize: none; overflow-y: hidden; }
                            </style>
                            <div class="mb-2">
                                <script>
                                    $(function () {
                                        $("#fviewcomment textarea, .upload-file-area")
                                        .on("dragenter", function (e) {
                                            e.preventDefault();
                                            e.stopPropagation();
                                        }).on("dragover", function (e) {
                                            e.preventDefault();
                                            e.stopPropagation();
                                            $('.upload-file-area').css("display", "flex");
                                        }).on("dragleave", function (e) {
                                            e.preventDefault();
                                            e.stopPropagation();
                                            if ($(this).hasClass('upload-file-area'))
                                                $('.upload-file-area').css("display", "none");
                                        }).on("drop", function (e) {
                                            e.preventDefault();
                                            var data = new FormData();
                                            var files = e.originalEvent.dataTransfer.files;
                                            data.append('bo_table', '<?php echo $bo_table ?>');
                                            data.append('na_file', files[0]);

                                            $.ajax({
                                                type: 'POST',
                                                url: '<?php echo NA_URL ?>/upload.image.php',
                                                enctype : 'multipart/form-data',
                                                dataType: 'json',
                                                contentType: false,
                                                processData: false,
                                                data: data,
                                                success: function (result) {
                                                    $('.upload-file-area').css("display", "none");
                                                    if(result.success) {
                                                        parent.document.getElementById("wr_content").value += '[' + result.success + ']\n';
                                                    } else {
                                                        var chkErr = result.error.replace(/<[^>]*>?/g, '');
                                                        if(!chkErr) {
                                                            chkErr = '[E1]오류가 발생하였습니다.';
                                                        }
                                                        na_alert(chkErr);
                                                        return false;
                                                    }
                                                },
                                                error: function (request,status,error) {
                                                    let msg = "code:"+request.status+"<br>"+"message:"+request.responseText+"<br>"+"error:"+error;
                                                    na_alert(msg);
                                                    return false;
                                                }
                                            });
                                        });
                                    });
                                </script>
                                <div class="form-floating comment-textarea">
                                    <div class="upload-file-area">
                                        <div class="upload-file-over"></div>
                                        <div class="icon-upload">
                                            <i class="bi bi-upload"></i>
                                        </div>
                                        <div>여기에 파일을 놓아 업로드</div>
                                    </div>
                                    <textarea tabindex="1" placeholder="Leave a comment here" id="wr_content" name="wr_content" maxlength="10000" class="form-control lh-base"
                                    <?php if ($comment_min || $comment_max) { ?>onkeyup="check_byte('wr_content', 'char_count');"<?php } ?>><?php echo $c_wr_content;  ?></textarea>
                                    <label id="wr_msg" for="wr_content">댓글을 입력해 주세요.</label>
                                </div>
                                <?php if ($comment_min || $comment_max) { ?><script> check_byte('wr_content', 'char_count'); </script><?php } ?>
                            </div>

                            <div class="d-flex align-items-center">
                                <div>
                                    <?php include_once(G5_THEME_PATH.'/app/clip.comment.php'); //댓글 버튼 모음 ?>
                                </div>
                                <div class="px-2">
                                    <input type="checkbox" class="btn-check" name="wr_secret" value="secret" id="wr_secret" autocomplete="off">
                                </div>
                                <div class="ms-auto">
                                    <button <?php echo ($is_paging) ? 'type="button" onclick="na_comment(\'viewcomment\');"' : 'type="submit"';?> class="btn btn-primary btn-sm" onKeyDown="na_comment_onKeyDown(<?php echo $is_paging?>);" id="btn_submit" title="댓글등록" tabindex="2">
                                        <span class="d-none d-sm-inline-block">댓글</span>
                                        등록
                                    </button>
                                </div>
                            </div>
                            <?php if($board['bo_use_sns'] && ($config['cf_facebook_appid'] || $config['cf_twitter_key'])) {	?>
                                <div  class="clearfix pt-2">
                                    <div id="bo_vc_opt">
                                        <ol id="bo_vc_sns">
                                            <li id="bo_vc_send_sns"></li>
                                        </ol>
                                    </div>
                                    <script>
                                    // sns 등록
                                    $(function() {
                                        $("#bo_vc_send_sns").load("<?php echo G5_SNS_URL; ?>/view_comment_write.sns.skin.php?bo_table=<?php echo $bo_table; ?>", function() {
                                            save_html = document.getElementById('bo_vc_w').innerHTML;
                                        });
                                    });
                                    </script>
                                </div>
                            <?php } ?>
                            <?php if ($is_guest) { ?>
                                <div class="pt-2 text-center small border-top mt-2">
                                    <?php echo $captcha_html; ?>
                                </div>
                            <?php } ?>
                        </div>
                        </form>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</div>
