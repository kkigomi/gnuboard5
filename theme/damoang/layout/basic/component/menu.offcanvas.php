<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<style>
    #menuOffcanvas .offcanvas-title .btn-menu {
        display: none;
    }
</style>
<div class="offcanvas offcanvas-end" tabindex="-1" id="menuOffcanvas" aria-labelledby="menuOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title px-2" id="menuOffcanvasLabel">
            <?php echo $offcanvas_buttons ?>
        </h5>
        <button type="button" class="btn-close nofocus" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body pt-0">
        <div class="na-menu">
            <!-- 배너 -->
            <div class="px-3 mb-4">
                <?php echo na_widget('damoang-image-banner', 'dmg-banner'); ?>
            </div>
        </div><!-- end .na-menu -->
        <!-- 배너 -->
        <div class="justify-content-center my-4">
            <?php echo na_widget('damoang-image-banner', 'side-banner'); ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // 기존 메뉴 복사해서 넣기
        $("#menuOffcanvas").find(".na-menu").append($(".na-menu").html());
    });
</script>
