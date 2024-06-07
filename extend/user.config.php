<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
if (preg_match("/.*\/(plugin\/social\/register_member)\.php/", $_SERVER['REQUEST_URI'])) {
    die('<script>confirm("실명 인증 도입 작업중입니다.");location.replace("'.G5_URL.'"); </script>');
}
