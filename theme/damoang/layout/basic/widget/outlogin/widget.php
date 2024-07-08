<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

global $is_member, $member, $is_admin;
?>

<?php if ($is_member) { ?>
    <div class="d-flex align-items-center my-2">
        <!-- 프로필 사진 -->
        <div class="pe-2">
            <a href="<?php echo G5_BBS_URL ?>/myphoto.php" target="_blank" class="win_memo" title="내 사진 관리">
                <img src="<?php echo na_member_photo($member['mb_id']); ?>" class="rounded-circle" style="max-width:60px;">
            </a>
        </div>

        <div class="d-flex flex-column flex-grow-1 overflow-hidden">
            <!-- 닉네임 -->
            <div class="d-flex align-items-center gap-2 ps-1">
                <div class="text-truncate">
                    <strong class="fs-5 lh-base mb-0 fw-bold hide-profile-img" style="letter-spacing:-1px;">
                        <?php echo str_replace('sv_member', 'sv_member ellipsis-1', $member['sideview']) ?>
                    </strong>
                </div>
                <span class="badge rounded-pill text-bg-light"><?php echo ($member['mb_grade']) ? $member['mb_grade'] : $member['mb_level'] . '등급'; ?></span>
            </div>

            <!-- 경험치 레벨 -->
            <?php
            $member['as_max'] = (isset($member['as_max']) && $member['as_max'] > 0) ? $member['as_max'] : 1;
            $per = (int) (($member['as_exp'] / $member['as_max']) * 100);
            ?>
            <div class="d-flex justify-content-between mb-1 small">
                <small>
                    Level
                    <?php
                    $currentDate = date('Y-m-d');
                    $targetDate = '2024-07-07';

                    // 날짜 비교 if문
                    if ($currentDate <= $targetDate) {
                        echo "100";
                    } else {
                        echo $member['as_level'];
                    }
                    ?>
                    <?//php echo $member['as_level'] ?>
                </small>
                <small>
                    <a href="<?php echo G5_BBS_URL ?>/exp.php" target="_blank" class="win_point">
                        Exp <?php echo number_format($member['as_exp']) ?>
                    </a>
                </small>
            </div>

            <div data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Next <?php echo number_format($member['as_max'] - $member['as_exp']) ?>">
                <div class="progress" role="progressbar" aria-label="Exp" aria-valuenow="<?php echo $per ?>" aria-valuemin="0" aria-valuemax="100" style="--bs-progress-height: 8px;">
                    <div class="progress-bar small rounded" style="width: <?php echo $per ?>%; background: linear-gradient(90deg, #9EC5FE 0%, #0D6EFD 100%);"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-grow-1 gap-1 justify-content-between mt-2">
        <?php if ($is_admin === 'super') { ?>
            <div>
                <button type="button" class="widget-setup btn btn-basic btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="위젯설정" role="button">
                    <i class="bi bi-magic"></i><span class="visually-hidden">위젯설정</span>
                </button>
            </div>
            <div>
                <a href="<?php echo correct_goto_url(G5_ADMIN_URL) ?>" class="btn btn-basic btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="관리자" role="button" target="_blank">
                    <i class="bi bi-gear"></i><span class="visually-hidden">관리자</span>
                </a>
            </div>
        <?php } ?>
        <div class="flex-fill">
            <button class="btn btn-basic btn-sm w-100" data-bs-toggle="offcanvas" data-bs-target="#memberOffcanvas" aria-controls="memberOffcanvas" role="button">
                <i class="bi bi-grid"></i>
                마이메뉴
            </button>
        </div>
        <div class="flex-fill">
            <a href="<?php echo G5_BBS_URL ?>/logout.php" class="btn btn-basic btn-sm w-100" role="button">
                <i class="bi bi-power"></i>
                로그아웃
            </a>
        </div>

    </div>

    <?php if ($member['mb_level'] >= 2) { ?>
        <div class="row">
            <div class="btn-group mt-2">
                <a href="<?= \G5_URL ?>/bbs/search.php?sfl=mb_id&stx=<?php echo $member['mb_id'] ?>&wr_is_comment=0" class="btn btn-sm btn-light" aria-current="page"><i class="bi bi-chat-square-text"></i> 내 글</a>
                <a href="<?= \G5_URL ?>/bbs/search.php?sfl=mb_id&stx=<?php echo $member['mb_id'] ?>&wr_is_comment=1" class="btn btn-sm btn-light"><i class="bi bi-chat"></i> 내 댓글</a>
                <a href="/bbs/noti.php" class="btn btn-sm btn-light"><i class="bi bi-bell"></i> 알림</a>
            </div>
        </div>
    <?php } ?>

<?php } else { ?>

    <a class="btn btn-primary w-100 py-2 mb-2" href="#memberOffcanvas" rel="nofollow" data-bs-toggle="offcanvas" data-bs-target="#memberOffcanvas" aria-controls="memberOffcanvas" role="button">
        로그인
    </a>

    <div class="row gx-2">
        <div class="col">
            <a href="<?php echo G5_BBS_URL ?>/register.php" rel="nofollow" class="btn btn-basic btn-sm w-100">
                <i class="bi bi-person-plus"></i>
                회원가입
            </a>
        </div>
        <div class="col">
            <a href="<?php echo G5_BBS_URL ?>/password_lost.php" rel="nofollow" class="btn btn-basic btn-sm w-100">
                <i class="bi bi-search"></i>
                정보찾기
            </a>
        </div>
    </div>
<?php } ?>
