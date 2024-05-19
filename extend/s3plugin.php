<?php

if (!defined('_GNUBOARD_')) {
    exit;
}

use Gnuboard\Plugin\AwsS3\S3Admin;
use Gnuboard\Plugin\AwsS3\S3Service;


require_once(G5_PLUGIN_PATH . '/AwsS3/S3Service.php');
require_once(G5_PLUGIN_PATH . '/AwsS3/S3Admin.php');

// 환경 변수를 $_ENV에 설정
$_ENV['s3_secret_key'] = getenv('s3_secret_key');;
$_ENV['s3_bucket_name'] = getenv('s3_bucket_name');
$_ENV['s3_region'] = getenv('s3_region');
$_ENV['s3_is_use_acl'] = filter_var(getenv('s3_is_use_acl'), FILTER_VALIDATE_BOOLEAN);
$_ENV['s3_is_use'] = filter_var(getenv('s3_is_use'), FILTER_VALIDATE_BOOLEAN);
$_ENV['s3_endpoint'] = getenv('s3_endpoint');
$_ENV['s3_storage_url'] = getenv('s3_storage_url');

S3Service::getInstance();
S3Admin::getInstance();
