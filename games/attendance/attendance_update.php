<?php
include_once ("./_common.php");

if (!$is_member) {
    alert("로그인 후 이용해주세요.");
}

if ($_POST['number'] && $_POST['point'] && $_POST['besu1'] && $_POST['besu2'] && $_POST['besu3'] && $_POST['range1'] && $_POST['range2'] && $_POST['minus']) {

    $game_point = trim(strip_tags($_POST['number']));
    $point = trim(strip_tags($_POST['point']));
    $besu1 = trim(strip_tags($_POST['besu1']));
    $besu2 = trim(strip_tags($_POST['besu2']));
    $besu3 = trim(strip_tags($_POST['besu3']));
    $range1 = trim(strip_tags($_POST['range1']));
    $range2 = trim(strip_tags($_POST['range2']));
    $minus = trim(strip_tags($_POST['minus']));


    $row = sql_fetch("SELECT * from `{$g5['point_table']}` where SUBSTRING(po_datetime,1,10) = '" . G5_TIME_YMD . "' and po_rel_table = '@attendance' and mb_id = '{$member['mb_id']}' ");

    if ($row['po_id'])
        alert("이미 출석 했잖아요. 왜이러세요^^?");

    if ($game_point == 100) {
        $point = $point * $besu1;
        $msg = "대단하십니다 ! 정확히 $game_point 에 멈추셧군요. 금메달을 획득하셨습니다.";
        $agelevel = "a";
    } else if ((100 - $range1) <= $game_point && (100 + $range1) >= $game_point) {
        $point = $point * $besu2;
        $msg = "아쉽네요 $game_point 에 멈추셧군요. 은메달을 획득하셨습니다.";
        $agelevel = "b";
    } else if ((100 - $range2) <= $game_point && (100 + $range2) >= $game_point) {
        $point = $point * $besu3;
        $msg = "힘내세요! $game_point 에 멈추셧군요. 동메달을 획득하셨습니다.";
        $agelevel = "c";
    } else if ((100 - $minus) >= $game_point || (100 + $minus) <= $game_point) {
        $point = "-" . $point * $besu3;
        $msg = "할말이 없습니다. $game_point 에 멈추셧군요. 포인트가 감점되었습니다.";
        $agelevel = "e";
    } else {
        $point = $point;
        $msg = "$game_point 에 멈추셧군요. 포인트만을 얻으셨습니다.";
        $agelevel = "d";
    }

    $rel_action = $agelevel . "출첵" . $row['cnt'];

    $row = sql_fetch("SELECT count(po_id) as cnt from `{$g5['point_table']}` where mb_id = '{$member['mb_id']}' and po_rel_table = '@attendance' ");

    insert_point($member['mb_id'], $point, "게임점수 [$game_point] 출첵", "@attendance", $member['mb_id'], $rel_action);

    alert($msg, "./index.php");

} else {

    alert("올바른 방법을 이용해주세요");

}
