<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
if (preg_match("/.*\/(plugin\/social\/register_member)\.php/", $_SERVER['REQUEST_URI'])) {
    die('<script>confirm("당분간 눈으로만 응원해 주세요.");location.replace("'.G5_URL.'"); </script>');
}