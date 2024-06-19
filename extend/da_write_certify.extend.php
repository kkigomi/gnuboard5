<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

add_event('write_update_before', 'alert_no_cert');
add_event('comment_update_before', 'alert_no_cert');
add_event('bbs_write', 'alert_no_cert');

function alert_no_cert() {
    global $is_member, $is_admin, $config, $member, $bo_table;

    $certify_required = explode(',', $config['cf_7']);
    if (!empty($config['cf_7'])) {
        foreach ($certify_required as $val) {
            if (trim($val) === $bo_table) { // 실명인증 필수 설정한 게시판일때
                if ($is_admin != 'super' && empty($member['mb_certify'])) { // 본인인증이 안된 계정일때
                    goto_url(G5_BBS_URL."/member_cert_refresh.php");
                    break;
                }
            }
        }
    }

    return;
}
