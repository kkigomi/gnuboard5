<?php
if (!defined('_GNUBOARD_'))
    exit; // 개별 페이지 접근 불가
/******************************
 * '정보수정' 혹은 '탈퇴'시 비밀번호 확인하는 페이지
 *  다모앙테마의 파일 (SNS 로그인뿐이라서 비밀번호 확인과정 사용 불가)
 * 관리자 환경설정에서 '회원가입' 테마를 다모앙 테마로 설정했을때 이 파일이 동작됨.
 * 로컬개발환경등에서 '회원가입' 테마설정이 'basic'으로 되어있다면 본 다모앙테마 파일이 아닌, skin/member/basic/member_confirm.skin.php 의 페이지가 동작함
 ******************************/

include_once (G5_PATH . '/head.php');
include_once (G5_PLUGIN_PATH . '/social/includes/functions_custom.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="' . $member_skin_url . '/style.css?CACHEBUST">', 0);

// 회원탈퇴를 위한 토큰 발급
if (!isset($leave_token) && $leave_token !== get_session('ss_leave_token')) {
    $token = md5(uniqid(rand(), true));
    set_session('ss_leave_token', $token);
}

$token = $leave_token ?? '';

$is_social_confirmed = false;

if( isset($_REQUEST['provider']) && $_REQUEST['provider']  && function_exists('social_nonce_is_valid') ){   //모바일로 소셜 연결을 했다면
    if( social_nonce_is_valid(get_session("social_link_token"), $provider) ){  //토큰값이 유효한지 체크
        $_POST['mb_id'] = $member['mb_id'];
        $is_social_confirmed = true;
    }
}

if (function_exists('social_member_leave_redirect') && !(isset($leave_token) && $leave_token === get_session('ss_leave_token')))
    $is_social_confirmed = false;

if ($url == 'member_leave.php' && !$is_social_confirmed) {
    if( function_exists('social_member_leave_redirect') && (! $url || $url === 'member_leave.php') ){
        social_member_leave_redirect();
    }
}

/**** 원 본 ( 비밀번호 확인 버전 백업: 사용 안함)****/
// $confirm_message = ($url == 'member_leave.php') ? '비밀번호를 입력하시면 회원탈퇴가 완료됩니다.' : '회원님의 정보를 안전하게 보호하기 위해 비밀번호를 한번 더 확인합니다.';
// echo <<<EOT
// <div id="mb_confirm" class="max-400 mx-auto py-md-5">
//     <form name="fmemberconfirm" action="{$url}" onsubmit="return fmemberconfirm_submit(this);" method="post">
//         <input type="hidden" name="mb_id" value="{$member['mb_id']}">
//         <input type="hidden" name="w" value="u">
//         <h3 class="px-3 py-2 mb-0 fs-5">
//             <i class="bi bi-shield-lock"></i>
//             비밀번호 확인
//         </h3>
//         <ul class="list-group list-group-flush line-top mb-4">
//             <li class="list-group-item">
//                 <strong>비밀번호를 한번 더 입력해주세요.</strong>
//                 <p class="my-3">
//                     {$confirm_message}
//                 </p>
//                 <div class="input-group mb-2">
//                     <span class="input-group-text">비밀번호<strong class="visually-hidden"> 필수</strong></span>
//                     <input type="password" autocomplete="current-password" name="mb_password" id="confirm_mb_password" required class="form-control required" maxLength="255">
//                     <button type="submit" id="btn_submit" class="btn btn-primary">확인</button>
//                 </div>
//             </li>
//             <li class="list-group-item text-center pt-3">
//                 <a href="{$g5['url']}">
//                     <i class="bi bi-house-fill"></i>
//                     홈으로 돌아가기
//                 </a>
//             </li>
//         </ul>
//     </form>
// </div>
// <script>
// function fmemberconfirm_submit(f) {
//     document.getElementById("btn_submit").disabled = true;
//     return true;
// }
// </script>
// <!-- } 회원 비밀번호 확인 끝 -->
// EOT;

/**************** 수정본 (재로그인 안내로 변경) ********************/
$confirm_text = '재로그인 안내';
$confirm_message = ($url == 'member_leave.php') ? '탈퇴안내: 로그인한지 오래되었습니다. 재로그인 후 이용해주세요' : '정보수정안내:  로그인한지 오래되었습니다. 재로그인 후 이용해주세요';

$leave_form = <<<EOT
<p>탈퇴하려면 아래 입력 상자에 아이디 <strong>{$member['mb_id']}</strong>를 입력하고 확인을 눌러주세요.</p>
<div class="input-group mb-2">
    <input type="text" autocomplete="off" name="confirm_mb_id" id="confirm_mb_id" required class="form-control required" maxLength="255">
    <button type="submit" id="btn_submit" class="btn btn-primary">확인</button>
</div>
EOT;

$confirm_form = <<<EOT
<strong>다시 로그인 후 이용해주세요</strong>
<p class="my-3">
    {$confirm_message}
</p>
EOT;

if ($url == 'member_leave.php') {
    if (!function_exists('social_member_leave_redirect') || $is_social_confirmed) {
        $confirm_text = '정말 탈퇴하시겠습니까?';
        $confirm_form = $leave_form;
    }
}

echo <<<EOT
<div id="mb_confirm" class="max-400 mx-auto py-md-5">
    <form name="fmemberconfirm" action="{$url}" onsubmit="return fmemberconfirm_submit(this);" method="post">
        <input type="hidden" name="_token" value="{$token}">
        <h3 class="px-3 py-2 mb-0 fs-5">
            <i class="bi bi-shield-lock"></i>
            {$confirm_text}
        </h3>
        <ul class="list-group list-group-flush line-top mb-4">
            <li class="list-group-item">
                {$confirm_form}
            </li>
            <li class="list-group-item text-center pt-3">
                <a href="/">
                    <i class="bi bi-house-fill"></i>
                    홈으로 돌아가기
                </a>
            </li>
        </ul>
    </form>
</div>
EOT;

/**** 꼬리 ****/
include_once (G5_PATH . '/tail.php');
