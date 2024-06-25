<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

// 1단 칼럼 페이지 아이디($pid)
$one_cols = array(
    G5_BBS_DIR . '-page-password_lost', // 아이디 및 비밀번호 찾기 페이지
    G5_BBS_DIR . '-page-register', // 회원약관 페이지
    G5_BBS_DIR . '-page-register_form', // 회원가입폼 페이지
    G5_BBS_DIR . '-page-register_result', // 회원가입폼 완료
    G5_BBS_DIR . '-page-login', // 로그인 페이지
    G5_BBS_DIR . '-page-register_email', // 메일인증 메일주소 변경 페이지
    G5_BBS_DIR . '-page-password_reset', // 비밀번호 변경 페이지
    G5_BBS_DIR . '-page-password', // 비밀번호 입력 페이지
    G5_BBS_DIR . '-page-member_cert_refresh', // 본인인증을 다시 해주세요.
    G5_BBS_DIR . '-page-member_confirm', // 회원 비밀번호 확인
);

// 1단 체크
$is_onecolumn = (in_array($page_id, $one_cols)) ? true : false;

// CSS -------------------------------------------------------------------------
add_stylesheet('<link rel="stylesheet" href="' . G5_THEME_URL . '/css/' . (G5_IS_MOBILE ? 'mobile' : 'default') . '.css?CACHEBUST">', 0);
add_stylesheet('<link rel="stylesheet" href="' . G5_THEME_URL . '/css/nariya.css?CACHEBUST">', 0);
add_stylesheet('<link rel="stylesheet" href="' . LAYOUT_URL . '/css/style.css?CACHEBUST">', 0);
add_stylesheet('<link rel="stylesheet" href="' . G5_THEME_URL . '/css/bootstrap-icons.min.css">', 0);
add_stylesheet('<link rel="stylesheet" href="' . G5_JS_URL . '/font-awesome/css/font-awesome.min.css">', 0);
$agent = $_SERVER["HTTP_USER_AGENT"];
if (!preg_match('/macintosh|mac os x/i', $agent)) {
    add_stylesheet('<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500&family=Roboto:wght@300;400;500&display=swap">', 0);
}

// JS --------------------------------------------------------------------------
add_javascript('<script src="' . G5_THEME_URL . '/js/jquery-3.5.1.min.js"></script>');
add_javascript('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>');
add_javascript('<script src="' . G5_THEME_URL . '/js/common.js"></script>');
add_javascript('<script src="' . G5_THEME_URL . '/js/wrest.js"></script>');
add_javascript('<script src="' . G5_THEME_URL . '/js/bootstrap.bundle.min.js?v50303"></script>');
add_javascript('<script src="' . G5_THEME_URL . '/js/clipboard.min.js"></script>');
add_javascript('<script src="' . G5_THEME_URL . '/js/nariya.js?CACHEBUST"></script>');
add_javascript('<script src="' . LAYOUT_URL . '/js/darkmode.js?CACHEBUST"></script>');
?>
<!doctype html>
<html lang="ko" data-bs-theme="light" class="<?php echo (G5_IS_MOBILE) ? 'is-mobile' : 'is-pc'; ?> is-bbs">

<head>
    <meta charset="utf-8">
    <meta name="viewport" id="meta_viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10">
    <meta name="HandheldFriendly" content="true">
    <meta name="format-detection" content="telephone=no">
    <?php
    // 환경설정 추가 메타 태그
    if ($config['cf_add_meta']) {
        echo $config['cf_add_meta'] . PHP_EOL;
    }
    ?>
    <title><?php echo $g5_head_title; ?></title>
    <link rel="stylesheet" href="<?php echo G5_THEME_URL ?>/css/bootstrap.min.css?v50303">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= G5_THEME_URL ?>/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= G5_THEME_URL ?>/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= G5_THEME_URL ?>/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?= G5_THEME_URL ?>/img/favicon/site.webmanifest?1">
    <link rel="shortcut icon" href="<?= G5_THEME_URL ?>/img/favicon/favicon.ico">
    <meta name="msapplication-config" content="<?= G5_THEME_URL ?>/img/favicon/browserconfig.xml">

    <script>
        var g5_url = <?php var_export(G5_URL) ?>;
        var g5_bbs_url = <?php var_export(G5_BBS_URL) ?>;
        var g5_is_member = <?php var_export(($is_member ?? false) ? '1' : '') ?>;
        var g5_is_admin = <?php var_export($is_admin ?? '') ?>;
        var g5_is_mobile = <?php var_export(G5_IS_MOBILE ? '1' : '') ?>;
        var g5_bo_table = <?php var_export($bo_table ?? '') ?>;
        var g5_sca = <?php var_export($sca ?? '') ?>;
        var g5_editor = <?php var_export(($config['cf_editor'] && isset($board['bo_use_dhtml_editor']) && $board['bo_use_dhtml_editor']) ? $config['cf_editor'] : '') ?>;
        var g5_cookie_domain = <?php var_export(G5_COOKIE_DOMAIN) ?>;
        <?php if (defined('G5_IS_ADMIN')) { ?>
            var g5_admin_url = <?php var_export(G5_ADMIN_URL) ?>;
        <?php } ?>
        var na_url = <?php var_export(NA_URL) ?>;
        <?php
        // FIXME: Member 객체 테스트
        if (method_exists($member, 'id')) {
        ?>
            var _member = <?= json_encode([
                'id' => $member->id(),
                'certified' => $member->isCertified(),
                'certifiedType' => $member['mb_certify'],
                'point' => $member->point(),
                'level' => $member->level(),
                'isMember' => $member->isMember(),
                'isGuest' => $member->isGuest(),
                'isLogged' => $member->isLogged(),
                'isAdmin' => $member->isAdmin(),
                'adminType' => $member->adminType(),
            ]); ?>;
        <?php } ?>

        (function () {
            'use strict';

            const shortcuts = {
                // abcdefghijklmnopqrstuvwxyz
                'KeyA': '/event',
                'KeyB': '/bug',
                'KeyC': '/',
                'KeyD': '/music',
                'KeyE': '/economy',
                'KeyF': '/free',
                'KeyG': '/gallery',
                'KeyH': '/',
                'KeyI': '/',
                'KeyJ': '/truthroom',
                'KeyK': '/notice',
                'KeyL': '/lecture',
                'KeyM': '/angmap',
                'KeyN': '/new',
                'KeyO': '/angtt',
                'KeyP': '/pds',
                'KeyQ': '/qa',
                'KeyR': 'refresh', // Refresh the page when 'R' is pressed
                'KeyS': '/bbs/group.php?gr_id=group',
                'KeyT': '/tutorial',
                'KeyU': '/',
                'KeyV': '/',
                'KeyW': '/promotion',
                'KeyX': '/angreport',
                'KeyY': '/bbs/noti.php',
                'KeyZ': '/hardware',
            };

            function isInputElement(element) {
                return ['INPUT', 'TEXTAREA', 'SELECT'].includes(element.tagName);
            }

            function isKeyCombination(event) {
                return event.ctrlKey || event.shiftKey || event.altKey || event.metaKey;
            }

            function isContentEditableElement(element) {
                while (element) {
                    if (element.contentEditable === 'true') {
                        return true;
                    }
                    element = element.parentElement;
                }
                return false;
            }

            function handleKeyPress(event) {
                if (isInputElement(event.target) || isKeyCombination(event) || isContentEditableElement(event.target)) {
                    return;
                }

                const code = event.code;
                if (shortcuts[code]) {
                    if (shortcuts[code] === 'refresh') {
                        window.location.reload(); // Refresh the page
                    } else {
                        window.location.href = shortcuts[code]; // Navigate to the specified URL
                    }
                }
            }
            window.addEventListener('keydown', handleKeyPress);
        })();
    </script>

    <?php
    // 환경설정 추가 스크립트
    if (!defined('G5_IS_ADMIN')) {
        echo $config['cf_add_script'];
    }
    ?>
</head>

<body>
