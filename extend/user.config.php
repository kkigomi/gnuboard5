<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (strpos($_SERVER['SCRIPT_NAME'], 'register.php') || strpos($_SERVER['SCRIPT_NAME'], 'register_form.php'))
    alert('당분간 가입신청을 받지 않습니다', G5_URL);

if (strpos($_SERVER['SCRIPT_NAME'], 'register_form_update.php') && $w == '')
    alert('당분간 가입신청을 받지 않습니다', G5_URL);
