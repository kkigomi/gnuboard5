<?php

// 소셜 로그인일 때 자동로그인 활성화
add_event('login_session_before', function ($mb = [], $is_social_login = false) {
    // `sociallogin_remeber` 쿠키를 확인
    if (
        $is_social_login
        && ($_COOKIE['sociallogin_remeber'] ?? null) === 'true'
    ) {
        // 로그인 유지
        $GLOBALS['auto_login'] = 'on';
    }
}, G5_HOOK_DEFAULT_PRIORITY, 2);
