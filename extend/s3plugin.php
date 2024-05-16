<?php

if (!defined('_GNUBOARD_')) {
    exit;
}

use Gnuboard\Plugin\AwsS3\S3Admin;
use Gnuboard\Plugin\AwsS3\S3Service;


require_once(G5_PLUGIN_PATH . '/AwsS3/S3Service.php');
require_once(G5_PLUGIN_PATH . '/AwsS3/S3Admin.php');

S3Service::getInstance();
S3Admin::getInstance();
