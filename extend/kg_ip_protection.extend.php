<?php declare(strict_types=1);
/**
 * 환경변수 설정 예시
 * 
 * 오류 검증은 하지 않으므로 설정에 주의해야 함
 * 
 * 관리자 IP 고정
 * KG_IPPROTECT_ADMIN_IP=127.0.0.1
 * 
 * 특정 ID의 IP 고정 (사용하지 않으려면 설정하지 마세요)
 * `아이디:IP` 형태를 `,`로 여러 ID를 지정할 수 있습니다
 * KG_IPPROTECT_LIST=member1:192.168.0.1
 * KG_IPPROTECT_LIST=member1:192.168.0.1,member2:192.168.0.2
 */

$_ENV['KG_IPPROTECT_ADMIN_IP'] = $_ENV['KG_IPPROTECT_ADMIN_IP'] ?? null;
if ($is_admin === 'super' && $_ENV['KG_IPPROTECT_ADMIN_IP']) {
    $_SERVER['REMOTE_ADDR'] = $_ENV['KG_IPPROTECT_ADMIN_IP'];
}

$_ENV['KG_IPPROTECT_LIST'] = $_ENV['KG_IPPROTECT_LIST'] ?? '';
if (empty($_ENV['KG_IPPROTECT_LIST'])) {
    return;
}

$_ENV['KG_IPPROTECT_LIST'] = explode(',', $_ENV['KG_IPPROTECT_LIST']);

foreach ($_ENV['KG_IPPROTECT_LIST'] as $item) {
    [$id, $ip] = explode(':', $item);
    if ($_SESSION['ss_mb_id'] === $id) {
        $_SERVER['REMOTE_ADDR'] = $ip;
    }
}

