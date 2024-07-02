<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

if (!$config['cf_social_login_use']) {
    //소셜 로그인을 사용하지 않으면
    return;
}

$social_pop_once = false;

$self_url = G5_BBS_URL . "/login.php";

//새창을 사용한다면
if (G5_SOCIAL_USE_POPUP) {
    $self_url = G5_SOCIAL_LOGIN_URL . '/popup.php';
}

add_stylesheet('<link rel="stylesheet" href="' . get_social_skin_url() . '/style.css?ver=' . G5_CSS_VER . '">', 10);
?>
<li class="list-group-item pt-3">
    <?php
    // 개발환경에서 아이디로 로그인 활성화
    if (
        in_array($_ENV['APP_ENV'] ?? 'prod', ['dev', 'rc', 'stage', 'local'])
        || ($_ENV['DA_ID_LOGIN'] ?? 'false') === 'true'
    ) { ?>
        <form id="memberLogin" class="pt-1" name="memberLogin" method="post" action="<?php echo G5_HTTPS_BBS_URL ?>/login_check.php" autocomplete="off">
            <input type="hidden" name="url" value="<?php echo $urlencode; ?>">
            <div class="input-group mb-2">
                <span class="input-group-text">
                    <i class="bi bi-person text-muted"></i>
                </span>
                <div class="form-floating">
                    <input type="text" name="mb_id" id="memberId" class="form-control required nofocus" placeholder="아이디">
                    <label for="mb_id100">아이디</label>
                </div>
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text">
                    <i class="bi bi-shield-lock text-muted"></i>
                </span>
                <div class="form-floating">
                    <input type="password" autocomplete="current-password" name="mb_password" id="memberPw" class="form-control required nofocus" placeholder="비밀번호">
                    <label for="mb_pw100">비밀번호</label>
                </div>
            </div>

            <div class="d-flex gap-3 mb-3">
                <div>
                    <a href="<?php echo G5_BBS_URL ?>/register.php" rel="nofollow" class="btn btn-basic py-2">
                        <i class="bi bi-person-plus"></i>
                        회원가입
                    </a>
                </div>
                <div class="flex-grow-1">
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        로그인
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="form-check form-check-inline form-switch">
                        <input class="form-check-input auto-login" type="checkbox" name="auto_login" role="switch" id="memberAutoLogin">
                        <label class="form-check-label" for="memberAutoLogin">자동로그인</label>
                    </div>
                </div>
                <div>
                    <a href="<?php echo G5_BBS_URL ?>/password_lost.php" rel="nofollow">
                        <i class="bi bi-search"></i>
                        아이디/비밀번호 찾기
                    </a>
                </div>
            </div>
        </form>
    <?php } ?>

    <div id="sns_login" class="login-sns sns-wrap-32 sns-wrap-over">
        <h3 class="visually-hidden">소셜계정으로 로그인</h3>
        <div class="sns-wrap">
            <?php if (social_service_check('naver')) { //네이버 로그인을 사용한다면 ?>
                <a href="<?php echo $self_url; ?>?provider=naver&amp;url=<?php echo $urlencode; ?>" class="sns-icon social_link sns-naver my-2" title="네이버">
                    <span class="ico"></span>
                    <span class="txt">네이버<i> 로그인</i></span>
                </a>
            <?php } //end if ?>
            <?php if (social_service_check('kakao')) { //카카오 로그인을 사용한다면 ?>
                <a href="<?php echo $self_url; ?>?provider=kakao&amp;url=<?php echo $urlencode; ?>" class="sns-icon social_link sns-kakao my-2" title="카카오">
                    <span class="ico"></span>
                    <span class="txt">카카오<i> 로그인</i></span>
                </a>
            <?php } //end if ?>
            <?php if (social_service_check('facebook')) { //페이스북 로그인을 사용한다면 ?>
                <a href="<?php echo $self_url; ?>?provider=facebook&amp;url=<?php echo $urlencode; ?>" class="sns-icon social_link sns-facebook my-2" title="페이스북">
                    <span class="ico"></span>
                    <span class="txt">페이스북<i> 로그인</i></span>
                </a>
            <?php } //end if ?>
            <?php if (social_service_check('google')) { //구글 로그인을 사용한다면 ?>
                <a href="<?php echo $self_url; ?>?provider=google&amp;url=<?php echo $urlencode; ?>" class="sns-icon social_link sns-google my-2" title="구글">
                    <span class="ico"></span>
                    <span class="txt">구글<i> 로그인</i></span>
                </a>
            <?php } //end if ?>
            <?php if (social_service_check('twitter')) { //트위터 로그인을 사용한다면 ?>
                <a href="<?php echo $self_url; ?>?provider=twitter&amp;url=<?php echo $urlencode; ?>" class="sns-icon social_link sns-twitter my-2" title="트위터">
                    <span class="ico"></span>
                    <span class="txt">트위터<i> 로그인</i></span>
                </a>
            <?php } //end if ?>
            <?php if (social_service_check('payco')) { //페이코 로그인을 사용한다면 ?>
                <a href="<?php echo $self_url; ?>?provider=payco&amp;url=<?php echo $urlencode; ?>" class="sns-icon social_link sns-payco my-2" title="페이코">
                    <span class="ico"></span>
                    <span class="txt">페이코<i> 로그인</i></span>
                </a>
            <?php } //end if ?>

            <?php if (G5_SOCIAL_USE_POPUP && !$social_pop_once) {
                $social_pop_once = true;
                ?>
                <script>
                    jQuery(function ($) {
                        $(".sns-wrap").on("click", "a.social_link", function (e) {
                            e.preventDefault();

                            var pop_url = $(this).attr("href");
                            var newWin = window.open(
                                pop_url,
                                "social_sing_on",
                                "location=0,status=0,scrollbars=1,width=600,height=500"
                            );

                            if (!newWin || newWin.closed || typeof newWin.closed == 'undefined') {
                                na_alert('브라우저에서 팝업이 차단되어 있습니다. 팝업 활성화 후 다시 시도해 주세요.');
                            }

                            return false;
                        });
                    });
                </script>
            <?php } ?>

            <div class="form-check text-start mt-3">
                <input class="form-check-input sociallogin_remeber" type="checkbox" name="sociallogin_remeber" id="sociallogin_remeber" />
                <label class="form-check-label" for="sociallogin_remeber">자동로그인</label>
                <div class="alert alert-light social-remember-alert" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> <strong>자동로그인은 공공장소에서 정보 유출의 위험이 초래될 수 있습니다.</strong>
                    <hr>
                    <u>최대 30일 동안</u> 로그인이 유지되므로 여러 사람이 이용하는 기기에서 사용하지 마세요.
                </div>
            </div>
        </div>
    </div>
</li>
