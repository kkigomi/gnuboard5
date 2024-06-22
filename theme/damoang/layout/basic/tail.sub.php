<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
// 다크모드 스크립트 오류 방지용
// FIXME: 아래 코드를 없애면 오류남.. 대체 왜
?>
<div class="d-none"> <a href="javascript:;" id="bd-theme-trick" data-bs-toggle="dropdown" aria-expanded="false"> <span class="theme-icon-active"> <i class="bi bi-sun"></i> </span> </a> <div class="dropdown-menu dropdown-menu-end py-0 shadow-none border navbar-dropdown-caret theme-dropdown-menu" aria-labelledby="bd-theme-trick" data-bs-popper="static"> <div class="card position-relative border-0"> <div class="card-body p-1"> <button type="button" class="dropdown-item rounded-1" data-bs-theme-value="light"> <span class="me-2 theme-icon"> <i class="bi bi-sun"></i> </span> Light </button> <button type="button" class="dropdown-item rounded-1 my-1" data-bs-theme-value="dark"> <span class="me-2 theme-icon"> <i class="bi bi-moon-stars"></i> </span> Dark </button> <button type="button" class="dropdown-item rounded-1" data-bs-theme-value="auto"> <span class="me-2 theme-icon"> <i class="bi bi-circle-half"></i> </span> Auto </button> </div> </div> </div> </div>

<?php run_event('tail_sub'); ?>
</body>
</html>
