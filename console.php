#!/usr/bin/env php
<?php
// CLI에서만 동작해야 함
if (PHP_SAPI !== 'cli') {
    // 존재를 감추기위해 404 반환
    http_response_code(404);
    exit;
}

require __DIR__ . '/common.php';

use Symfony\Component\Console\Application;

$application = new Application();

// command 등록을 위한 이벤트
run_event('console:register', $application);

$application->run();

exit();
