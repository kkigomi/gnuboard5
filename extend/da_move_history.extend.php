<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if( !isset($g5['da_move_history_table']) ){
    $g5['da_move_history_table'] = G5_TABLE_PREFIX.'da_move_history';
}

add_event('bbs_move_copy', 'bbs_move_history');
add_event('bbs_redirect_moved', 'bbs_redirect_moved');

function bbs_move_history() {
    global $g5, $sw, $bo_table, $move_bo_table, $insert_id, $row2;

    if ($sw != 'move') return;
    if ($row2['wr_is_comment'] == '1') return;

    $history_table = $g5['da_move_history_table'];
    // insert history
    sql_query(" INSERT INTO {$history_table} 
            (org_bo_table,org_wr_id,new_bo_table,new_wr_id)
            VALUES ('{$bo_table}','{$row2['wr_id']}','{$move_bo_table}','{$insert_id}') ");

    // 기존 new_write를 새 글로 update
    sql_query(" UPDATE {$history_table}
                SET new_bo_table = '{$move_bo_table}', new_wr_id = '{$insert_id}'
                WHERE new_bo_table = '{$bo_table}' AND new_wr_id = '{$row2['wr_id']}' ");
}

function bbs_redirect_moved() {
    global $g5, $bo_table;

    if (!$bo_table) return;
    if (!isset($_GET['wr_id'])) return;

    $wr_id = $_GET['wr_id'];

    $history_table = $g5['da_move_history_table'];
    $sql = " SELECT * FROM {$history_table} WHERE org_bo_table = '{$bo_table}' AND org_wr_id = '{$wr_id}' ";
    $row = sql_fetch($sql);
    if (!$row) return;

    $write_table = $g5['write_prefix'] . $row['new_bo_table'];
    $wr = get_write($write_table, $row['new_wr_id']);
    if (!$wr) return;

    $redirect_url = short_url_clean(G5_HTTP_BBS_URL.'/board.php?bo_table='.$row['new_bo_table'].'&amp;wr_id='.$row['new_wr_id']);
    goto_url($redirect_url);
}
