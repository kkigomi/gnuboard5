<?php
declare(strict_types=1);

include_once __DIR__ . '/../../common.php';

$_ENV['DAMOANG_API_TOKEN'] = $_ENV['DAMOANG_API_TOKEN'] ?? null;

// 인증
if (
    !$_ENV['DAMOANG_API_TOKEN']
    || !($_SERVER['HTTP_AUTHORIZATION'] ?? null)
    || $_ENV['DAMOANG_API_TOKEN'] !== $_SERVER['HTTP_AUTHORIZATION']
) {
    http_response_code(404);
    exit();
}

// 목록 가져오기
$list = \Damoang\Plugin\Dajoongi\Dajoongi::getList();

// 결과 출력
header('Content-Type: application/json');
echo json_encode($list, JSON_PRETTY_PRINT);
