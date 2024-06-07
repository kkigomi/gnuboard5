<?php
/**
 *
 * @author    Chongmyung Park <chongmyung.park@gmail.com>
 * @copyright Chongmyung Park
 */


#라이센스 등록된 이메일 주소를 입력하세요
define('MENTION_LICENSE', 'newcomposer@gmail.com');

# '@사용자닉' 을 클릭했을 때, 이동할 주소를 입력하세요.
# {UID} 는 $mb_id 로 치환되며
# {BO_TABLE} 은 $bo_table 값으로 치환됩니다.
# example1 : define('MENTION_USER_URL', G5_BBS_URL.'/new.php?mb_id={UID}');
# example2 : define('MENTION_USER_URL', G5_BBS_URL.'/profile.php?mb_id={UID}');
define('MENTION_USER_URL', G5_BBS_URL.'/profile.php?mb_id={UID}');

# '@사용자닉' 클릭 시 팝업으로 보여줄지와 팝업 크기를 설정합니다.
define('MENTION_POPUP_ON_CLICK', true); // 팝업사용 true, 미사용 false
define('MENTION_POPUP_WIDTH', 320);     // 팝업 너비
define('MENTION_POPUP_HEIGHT', 600);    // 팝업 높이

# 게시글 제목에도 맨션을 사용할지 설정 
#   : true 로 설정할경우 게시판에서 제목 출력시 $view['mention_subjuect'] 를 사용해야 합니다.
define('MENTION_ON_SUBJECT', false);    

# 게시글 내용에도 맨션을 사용할지 설정
define('MENTION_ON_CONTENT', true);

# 알림에 맨션 출력하는 함수
function mention_notification($list) {
   foreach($list as $i=>$row) {
       if($row['ph_from_case'] == 'mention') {
            $tmp_to_name = ($row['mb_nick']) ? $row['mb_nick'] : $row['rel_mb_nick'];
            $tmp_mb_count = count(array_unique(explode("," ,$row['g_rel_mb']))); 
            $tmp_total = ($tmp_mb_count) ? $tmp_mb_count : $tmp_total;
            switch($row['ph_to_case']) {
                case 'board':
                    $tmp_msg = "<b>".$tmp_to_name."</b>님이 게시글에 맨션을 남기셨습니다.";
                break;
                case 'comment':
                    $tmp_add_msg = ($tmp_total > 1) ? "외 ".((int)$tmp_total - 1)."명이 " : "이 ";
                    $tmp_msg = "<b>".$tmp_to_name."</b>님".$tmp_add_msg."댓글에 맨션을 남기셨습니다.";
                break;
            }   
            if($tmp_msg) {
                $list[$i]['msg'] = $tmp_msg;
            }   
       }   
   }   
   return $list;
}
include_once(G5_PLUGIN_PATH.'/mention/bootstrap.php');
