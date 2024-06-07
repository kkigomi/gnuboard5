<?php
/**
 *
 * @author    Chongmyung Park <chongmyung.park@gmail.com>
 * @copyright Chongmyung Park
 */
define('MENTION_VERSION', '1.0.0');

if (!function_exists('curl_version')) {
    die('맨션 플러그인을 사용하기 위해서는 cURL 확장 모듈이 필요합니다.');
}

include_once(__DIR__.'/lib/mention.class.php');

function _mention_view($view) {
    global $mentionPlugin;
    if($mentionPlugin) {
        $view['content'] = $mentionPlugin->content($view['content']);
        if(MENTION_ON_SUBJECT) {
            $view['mention_subject'] = $mentionPlugin->content($view['wr_subject']);
        }  
    }
    return $view;
}

function _mention_comment_list($list) {
    global $mentionPlugin;
    if($mentionPlugin) {
        $list = $mentionPlugin->comments($list);
    }
    return $list;
}

function _mention_write_update($board, $wr_id, $w, $qstr, $redirect_url) {
    global $mentionPlugin;
    ($mentionPlugin && $mentionPlugin->onWriteUpdate());
}

function _mention_write_comment_update($board, $wr_id, $w, $qstr, $redirect_url, $comment_id, $reply_array) {
    global $mentionPlugin;
    ($mentionPlugin && $mentionPlugin->onWriteCommentUpdate());
}

function _mention_js() {
    global $wr_id, $bo_table, $board;
    $lfile = G5_DATA_PATH.'/mention.data';
    if(!file_exists($lfile) || filesize($lfile) < 100) {
        return false;
    }
    add_javascript(sprintf('<script type="text/javascript" src="%s/%s/mention.min.js"></script>', G5_PLUGIN_URL, basename(__DIR__)));
    return true;
}

function _mention_popup() {
    add_javascript(sprintf('<script>$(function() {
            $("body").on("click", ".mention-lnk", function(evt) {        
                evt.preventDefault();
                var dualScreenLeft = window.screenLeft !==  undefined ? window.screenLeft : window.screenX;
                var dualScreenTop = window.screenTop !==  undefined   ? window.screenTop  : window.screenY;
                var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
                var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
                var systemZoom = width / window.screen.availWidth;
                var w = %s, h = %s;
                var left = (width - w) / 2 / systemZoom + dualScreenLeft;
                var top = (height - h) / 2 / systemZoom + dualScreenTop;
                var opt = "scrollbars=yes,width="+(w/systemZoom)+",height="+(h/systemZoom)+",top="+top+",left="+left;
                var href = $(this).attr("href");
                var newwin = window.open(href, "winprofile", opt);
                newwin.focus();
                return false;
            });
        });</script>', MENTION_POPUP_WIDTH, MENTION_POPUP_HEIGHT), 20);
}

function _mention_subject() {
    add_javascript('<script>var mention_on_subject = "yes";</script>', 20);
}

$mentionPlugin = new MentionPlugin();

// 커스텀 replace
add_replace('noti_list', 'mention_notification', 1, 10);
add_replace('board_view', '_mention_view', 1, 10);
add_replace('view_comment_list', '_mention_comment_list', 1, 10);

// 그누보드 event
add_event('write_update_after', '_mention_write_update', 5, 10);
add_event('comment_update_after', '_mention_write_comment_update', 7, 10);

// 게시판일때만 JS 처리
if(isset($bo_table) && isset($board) && $board) {
    add_event('tail_sub', '_mention_js', 0, 10);
    if(MENTION_POPUP_ON_CLICK) {
        add_event('tail_sub', '_mention_popup', 0, 10);
    }
    if(MENTION_ON_SUBJECT) {
        add_event('tail_sub', '_mention_subject', 0, 10);
    }
}
