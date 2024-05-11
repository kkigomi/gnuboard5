<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
if(preg_match("/.*\/bbs\/(register|register_form|member_leave)\.php?/", $_SERVER['REQUEST_URI'])) die('<script>confirm("회원가입 하실 필요 없습니다.");location.replace("'.G5_URL.'"); </script>');