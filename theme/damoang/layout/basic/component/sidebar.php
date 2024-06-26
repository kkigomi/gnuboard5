<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>


<div class="px-3 mb-4">
    <?php echo na_widget('outlogin'); // 외부로그인 ?>
</div>
<!-- 배너 -->
<div class="px-3 mb-4">
    <?php echo na_widget('damoang-image-banner', 'dmg-banner'); ?>
</div>

<div class="na-menu">
    <div class="nav nav-pills nav-vertical">
        <div class="nav-item">
            <a class="nav-link" href="/verification" data-placement="left">
                <!--                        <i class="bi-youtube nav-icon"></i>-->
                <span class="nav-link-title">🎖️🎖️해외앙님 실명인증</span>
            </a>
        </div>
        <div id="sidebar-site-menu" class="mb-3">
            <?php
            if (!empty($config['cf_9'])) {
                ?>
                <div class="nav-item">
                    <a class="nav-link" href="<?php echo $config['cf_9'];?>" data-placement="left" target="_blank">
<!--                        <i class="bi-youtube nav-icon"></i>-->
                        <span class="nav-link-title">▶️ 다모앙 방송국</span>
                    </a>
                </div>
            <?php } ?>

            <?php if($member['mb_level'] >= 2) { ?>
                <div class="nav-item nav-link">
                    <a href="<?= \G5_URL ?>/bbs/search.php?sfl=mb_id&stx=<?php echo $member['mb_id'] ?>&wr_is_comment=0">내 글</a>&nbsp;|&nbsp;<a href="<?= \G5_URL ?>/bbs/search.php?sfl=mb_id&stx=<?php echo $member['mb_id'] ?>&wr_is_comment=1">내 댓글</a>&nbsp;|&nbsp;<a href="/bbs/noti.php"><i class="bi bi-bell"></i>알림</a>
                </div>
            <?php } ?>

            <!-- 사이드 메뉴 -->
            <div class="nav-item">
                <!-- 사이트 메뉴 -->
                <?php
                // 메뉴. `inc.menu.php` 파일에서 정의 됨
                foreach ($siteMenus as $menuGroupIndex => $menuGroup) {
                    $groupTitle = $menuGroup['title'] ?? null;
                ?>

                    <?php if ($groupTitle): ?>
                        <div class="dropdown-header"><?= $groupTitle ?></div>
                    <?php endif; ?>

                    <?php
                    foreach ($menuGroup['items'] as $menuTitle => $menuItem) {
                        $hasSub = false;
                        $subMenus = null;

                        if (!is_array($menuItem)) {
                            $menuItem = ['url' => $menuItem];
                        }

                        $menuItem['url'] = $menuItem['url'] ?? '#';
                        $menuItem['icon'] = $menuItem['icon'] ?? 'bi-clipboard';
                        $hasSub = !empty($menuItem['items']) && is_array($menuItem['items']);
                        $menuItem['page_id'] = $menuItem['page_id'] ?? '';
                        if (!$menuItem['page_id']) {
                            if (preg_match('/\/([a-zA-Z0-9]+)$/i', $menuItem['url'], $matches)) {
                                $menuItem['page_id'] = G5_BBS_DIR . '-board-' . $matches[1];
                            }
                        }

                        $menuItem['tooltip'] = $menuItem['tooltip'] ?? '';
                        if ($menuItem['tooltip'] !== '') {
                            $menuItem['tooltip'] = 'title="' . $menuItem['tooltip'] . '"';
                        }

                        $menuItem['shortcut'] = $menuItem['shortcut'] ?? '';
                        $menuItem['icon'] = $menuItem['icon'] ?? '';
                        $menuItem['class'] = $menuItem['class'] ?? '';
                        if (!$menuItem['class'] && $menuItem['page_id']) {
                            $menuItem['class'] = 'da--menu-' . $menuItem['page_id'];
                        }

                        if (!$menuItem['page_id']) {
                            if (preg_match('/\/([a-zA-Z0-9]+)$/i', $menuItem['url'], $matches)) {
                                $menuItem['page_id'] = G5_BBS_DIR . '-board-' . $matches[1];
                            }
                        }

                        if ($hasSub) {
                            $menuIndex = array_search($menuTitle, array_keys($siteMenus[$menuGroupIndex]['items']));
                            $menuToggleId = "sidebar-sub-s{$menuGroupIndex}-{$menuIndex}";
                            $menuUrlOrigin = $menuItem['url'];
                            $menuItem['url'] = '#' . $menuToggleId;
                            $subMenus = $menuItem['items'];
                        }
                        ?>
                        <div class="nav-item da-menu--<?= $menuItem['page_id'] ?>">
                            <a
                                class="nav-link <?= ($menuItem['page_id'] === $page_id) ? ' active ' : ''; ?><?= ($hasSub) ? 'dropdown-toggle collapsed collapsed' : '' ?>"
                                href="<?= $menuItem['url'] ?>"
                                <?= $menuItem['tooltip'] ?>
                                data-placement="left"
                                <?= ($hasSub) ? 'role="button" data-bs-toggle="collapse" data-bs-target="#' . $menuToggleId . '" aria-expanded="false" aria-controls="' . $menuToggleId . '"' : '' ?>
                            >
                                <span class="d-flex align-items-center gap-2 nav-link-title">
<!--                                    <i class="--><?php //= $menuItem['icon'] ?><!-- nav-icon"></i>-->
                                    <?php if ($menuItem['shortcut']) { ?>
                                        <span class="badge p-1 text-bg-secondary"><?= $menuItem['shortcut'] ?></span>
                                    <?php } ?>
                                    <?= $menuTitle ?>
                                </span>
                            </a>

                            <!-- 서브 메뉴 -->
                            <?php if ($hasSub): ?>
                                <div id="<?= $menuToggleId ?>" class="nav-collapse collapse" data-bs-parent="#sidebar-site-menu">
                                    <?php foreach ($subMenus as $subMenuTitle => $subMenuUrl): ?>
                                        <?php
                                            $subMenuTooltip = '';
                                            //4자리 초과된 소모임 메뉴는 툴팁으로 메뉴명을 보여주도록 함
                                            if (mb_strlen(trim($subMenuTitle)) > 4) {
                                                $subMenuTooltip = 'title="' . $subMenuTitle . '"';
                                            }
                                        ?>
                                        <a class="nav-link" href="<?= $subMenuUrl ?>" <?= $subMenuTooltip ?>>
                                            <?= $subMenuTitle ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php } ?>
                <?php } // endforeach $siteMenus ?>

                <div class="nav-item da-menu--device-mode">
                    <a class="nav-link" href="<?php echo get_device_change_url() ?>" rel="nofollow" data-placement="left">
<!--                        <i class="--><?php //echo (G5_IS_MOBILE) ? 'bi-pc-display' : 'bi-tablet'; ?><!-- nav-icon"></i>-->
                        <span class="nav-link-title"><?php echo (G5_IS_MOBILE) ? 'PC' : '모바일'; ?> 버전</span>
                    </a>
                </div>
                <!-- 배너 -->
                <div class="d-none d-lg-block justify-content-center my-4">
                    <?php echo na_widget('only-damoang-image-banner', 'side-banner'); ?>
                </div>

                <!-- 배너 -->
                <div class="d-none d-lg-block justify-content-center my-4">
                    <?php echo na_widget('damoang-image-banner', 'side-banner'); ?>
                </div>
<!--TODO: -->
<!--                애드센스 제거 후 파랑색 검색 바가 제거 되는 지 TEST-->
                <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6922133409882969"
                        crossorigin="anonymous"></script>
                <!-- right -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-6922133409882969"
                     data-ad-slot="5261209468"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>                <!-- 배너 -->
                <div class="d-none d-lg-block justify-content-center my-4">
                    <?php echo na_widget('damoang-image-banner', 'side-banner'); ?>
                </div>


            </div>
        </div>
    </div><!-- end .na-menu -->
