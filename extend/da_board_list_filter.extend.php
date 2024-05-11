<?php

add_replace('da_board_list', function ($list = []) {
    global $member, $singo_write;

    $chadan_list = explode(',', $member['as_chadan'] ?? '');

    // A. 목록에서 제외
    $list = array_reduce($list, function ($carry, $item) use ($chadan_list, $singo_write) {
        $mb_id = $item['mb_id'] ?? false;
        if (
            !$mb_id
            || (
                // 차단한 회원의 글 제외
                !in_array($mb_id, $chadan_list)
                // 신고한 글 제외
                && !in_array((string) $item['wr_id'], $singo_write)
            )
        ) {
            $carry[] = $item;
        }
        return $carry;
    }, []);

    // B. 목록 유지하되 일부 가림
    // foreach ($list as &$item) {
    //     $mb_id = $item['mb_id'] ?? false;
    //     $item['is_chadan'] = false;
    //     if ($mb_id && in_array($mb_id, $chadan_list)) {
    //         $item['wr_subject'] = $item['subject'] = $item['subject'] . ' [차단한 회원의 글]';
    //         $item['is_chadan'] = true;
    //         $item['mb_id'] = '';
    //         $item['wr_name'] = $item['name'] = $item['wr_name'] . ' [차단됨]';
    //     }
    //     if (in_array((string) $item['wr_id'], $singo_write)) {
    //         $item['wr_subject'] = $item['subject'] = $item['subject'] . ' [신고한 글]';
    //         $item['is_singo'] = true;
    //     }
    // }

    return $list;
}, \G5_HOOK_DEFAULT_PRIORITY, 1);