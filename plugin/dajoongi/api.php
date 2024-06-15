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
$result = sql_query("SELECT
        wr_ip,
        GROUP_CONCAT(DISTINCT mb_id) AS dup_mb_ids,
        GROUP_CONCAT(DISTINCT bo_table) AS dup_bd_nm,
        COUNT(1) AS cnt
    FROM
        {$g5['board_new_table']}
    WHERE
        wr_ip <> ''
        AND mb_id NOT IN ('', 'admin')
        AND bn_datetime >= DATE_SUB(CURDATE(), INTERVAL 3 DAY)
    GROUP BY
        wr_ip
    HAVING
        COUNT(DISTINCT mb_id) > 1
");

$list = $result->fetch_all(\MYSQLI_ASSOC);

$list = array_map(function ($item) {
    $item['wr_ip'] = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", G5_IP_DISPLAY, $item['wr_ip']);
    return $item;
}, $list);


// 결과 출력
header('Content-Type: application/json');
echo json_encode($list, JSON_PRETTY_PRINT);
