<?php
include_once("./_common.php");

$noti_readall = (function($g5, $is_member, $member) {
    $result = new stdClass();

    if (!$is_member) {
        $result->message = '회원만 접근이 가능합니다.';
        $result->status = 401;
        return $result;
    }

    if (!(isset($_POST['_token']) && $_POST['_token'] === get_session('ss_noti_readall_token'))) {
        $result->message = '토큰 정보가 올바르지 않습니다.';
        $result->status = 403;
        return $result;
    }

    // 로그인한 회원의 읽지 않은 알림 읽음으로 업데이트
    sql_query(" UPDATE {$g5['na_noti']} SET ph_readed = 'Y'
                WHERE mb_id = '{$member['mb_id']}' AND ph_readed = 'N' ");
    na_noti_update($member['mb_id']);

    $result->message = '모든 알림을 읽음 표시했습니다.';
    $result->status = 200;
    return $result;
})($g5, $is_member, $member);

header('Content-type: application/json');
http_response_code($noti_readall->status);
echo json_encode($noti_readall);
exit;
