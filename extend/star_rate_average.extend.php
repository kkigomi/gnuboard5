<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if( !isset($g5['board_rate_average_table']) ){
    $g5['board_rate_average_table'] = G5_TABLE_PREFIX.'board_rate_average';
}

add_event('comment_update_after', 'star_rate_average');
add_event('view_skin_before', 'star_rate_average');
add_event('bbs_delete_comment', 'star_rate_average');
add_event('bbs_delete', 'star_rate_average');

function star_rate_average() {
    global $g5, $boset, $bo_table, $write_table, $write, $wr_comment,
            $wr_id, $wr_6, $w, $delete_comment_token;

    if (!isset($delete_comment_token) && !$boset['check_star_rating']) return;

    if (isset($delete_comment_token)) {
        $parent = get_write($write_table, $write['wr_parent'], true);
        $wr_id = $parent['wr_id'];
    }

    $average_table = $g5['board_rate_average_table'];

    $sql = " SELECT * FROM {$average_table}
             WHERE bo_table = '{$bo_table}' AND wr_id = '{$wr_id}' LIMIT 1 ";
    $row = sql_fetch($sql);

    // 초기화
    $sum = 0;
    $average = 0;
    $count = 0;
    $count_split = [0,0,0,0,0,0,0,0,0,0];

    if ($row) {
        if (isset($delete_comment_token)) $wr_6 = $write['wr_6'];

        if (!isset($wr_6)) return;

        for ($i=0; $i<=9; $i++) {
            $count_split[$i] = $row['rate_count_'.($i + 1)];
        }

        $sum = (int) $row['rate_sum'];
        $count = (int) $row['rate_count'];

        if ((int) $wr_6 > 0) {
            if (isset($wr_comment) && $w == 'cu') {
                $sum = $sum - (int) $wr_comment['wr_6'];
                $count--;

                $count_split[(int) $wr_comment['wr_6'] - 1]--;
            }

            if (isset($delete_comment_token)) {
                $sum = $sum - (int) $wr_6;
                $count--;

                $count_split[(int) $wr_6 - 1]--;
            } else {
                $sum = $sum + (int) $wr_6;
                $count++;

                $count_split[(int) $wr_6 - 1]++;
            }
        }

        if (isset($delete_comment_token) && $sum <= 0) {
            sql_query(" delete from {$average_table} where bo_table = '{$bo_table}' and wr_id = '{$wr_id}' ");
        }
    } else if (!isset($delete_comment_token)) {
        $sql_where = " WHERE wr_parent = {$wr_id} AND wr_is_comment = '1' ";
        $sql_comments = " SELECT * FROM {$write_table}".$sql_where;
        $result = sql_query($sql_comments);
        for ($i=0; $comment=sql_fetch_array($result); $i++) {
            $star_rated = $comment['wr_6'];

            if ((int) $star_rated > 0) {
                $sum = $sum + (int) $star_rated;
                $count++;

                $count_split[(int) $star_rated - 1]++;
            }
        }
    }

    if ($sum > 0) {
        $average = $sum / $count;

        // INSERT
        $result = sql_query(
            " INSERT INTO {$average_table}
            (bo_table,wr_id,rate_sum,rate_average,
             rate_count,rate_count_1,rate_count_2,rate_count_3,
             rate_count_4,rate_count_5,rate_count_6,rate_count_7,
             rate_count_8,rate_count_9,rate_count_10)
            VALUES ('{$bo_table}', '{$wr_id}', '{$sum}', '{$average}',
                    '{$count}', '{$count_split[0]}', '{$count_split[1]}',
                    '{$count_split[2]}', '{$count_split[3]}', '{$count_split[4]}',
                    '{$count_split[5]}', '{$count_split[6]}', '{$count_split[7]}',
                    '{$count_split[8]}', '{$count_split[9]}')
            ON DUPLICATE KEY UPDATE
                `rate_sum` = VALUES(rate_sum),
                `rate_average` = VALUES(rate_average),
                `rate_count` = VALUES(rate_count),
                `rate_count_1` = VALUES(rate_count_1),
                `rate_count_2` = VALUES(rate_count_2),
                `rate_count_3` = VALUES(rate_count_3),
                `rate_count_4` = VALUES(rate_count_4),
                `rate_count_5` = VALUES(rate_count_5),
                `rate_count_6` = VALUES(rate_count_6),
                `rate_count_7` = VALUES(rate_count_7),
                `rate_count_8` = VALUES(rate_count_8),
                `rate_count_9` = VALUES(rate_count_9),
                `rate_count_10` = VALUES(rate_count_10)
        ");
    }
}
