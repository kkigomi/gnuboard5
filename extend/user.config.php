<?php
//파일명에 register 가 들어간 경로 접근시 차단
$base_filename = basename($_SERVER['PHP_SELF']);
if(strpos($base_filename,'register') !== false){
    alert("가능하지 않습니다.", G5_URL);
    die();
}
