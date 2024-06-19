<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

$widget = 'banner';
add_javascript('<script src="' . \LAYOUT_URL . '/js/jquery-ui/jquery-ui.min.js"></script>');
add_stylesheet('<link href="' . \LAYOUT_URL . '/js/jquery-ui/jquery-ui.theme.min.css">');
?>

<ul class="list-group">
    <li class="list-group-item">
        <div class="form-group row mb-0">
            <label class="col-sm-2 col-form-label">로고 설정</label>
            <div class="col-sm-10">
                <style>
                    #widgetData.table {
                        border-left: 0;
                        border-right: 0;
                    }

                    #widgetData thead th {
                        border-bottom: 0;
                    }

                    #widgetData th,
                    #widgetData td {
                        vertical-align: middle;
                        border-left: 0;
                        border-right: 0;
                    }
                </style>

                <p class="form-control-plaintext">
                    <i class="fa fa-caret-right" aria-hidden="true"></i>
                    이미지 높이는 미입력시 48px
                </p>

                <ul id="items" class="list-group">
                    <?php
                    // 직접등록 입력폼
                    $data = array();
                    $data_cnt = (isset($wset['d']['img']) && is_array($wset['d']['img'])) ? count($wset['d']['img']) : 1;

                    for ($i = 0; $i < $data_cnt; $i++) {
                        $n = $i + 1;
                        $d_img = isset($wset['d']['img'][$i]) ? $wset['d']['img'][$i] : '';
                        $d_alt = isset($wset['d']['alt'][$i]) ? $wset['d']['alt'][$i] : '';
                        $d_memo = isset($wset['d']['memo'][$i]) ? $wset['d']['memo'][$i] : '';
                        $d_height = isset($wset['d']['height'][$i]) ? $wset['d']['height'][$i] : '';
                        ?>
                        <li class="list-group-item">
                            <div class="mb-2">
                                <label class="form-label">이미지</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <a href="<?php echo G5_THEME_URL ?>/app/image.php?fid=img_<?php echo $n ?>&amp;type=logo" class="win_point">
                                            <i class="bi bi-image"></i>
                                        </a>
                                    </span>
                                    <input type="text" id="img_<?php echo $n ?>" name="wset[d][img][]" value="<?php echo $d_img ?>" class="form-control" placeholder="이미지 주소">
                                </div>
                                <div><img id="img_<?php echo $n ?>_preview" src="<?php echo $d_img ?>" style="max-width: 100%; max-height: 48px;" /></div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-2">
                                    <label class="form-label">이미지 높이</label>
                                    <input type="text" id="height_<?php echo $n ?>" name="wset[d][height][]" value="<?php echo $d_height ?>" class="form-control">
                                </div>
                                <div class="col">
                                    <label class="form-label">대체 텍스트</label>
                                    <input type="text" id="alt_<?php echo $n ?>" name="wset[d][alt][]" value="<?php echo $d_alt ?>" class="form-control">
                                </div>
                                <div class="col">
                                    <label class="form-label">관리용 메모</label>
                                    <input type="text" id="memo_<?php echo $n ?>" name="wset[d][memo][]" value="<?php echo $d_memo ?>" class="form-control">
                                </div>
                            </div>
                            <div class="text-center">
                                <?php if ($i > 0) { ?>
                                    <a href="javascript:;" class="ibtnDel"><i class="fa fa-times-circle fa-2x text-muted"></i></a>
                                <?php } ?>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>

            <div class="text-center mt-3">
                <button type="button" class="btn btn-outline-primary btn-lg en" id="addrow">
                    항목 추가
                </button>
            </div>
        </div>
    </li>
</ul>

<script>
    $(document).ready(function () {
        var counter = <?php echo $data_cnt + 1 ?>;
        $("#addrow").on("click", function () {
            var cols = `<li class="list-group-item">
                <div class="mb-2">
                    <label class="form-label">이미지</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <a href="<?php echo G5_THEME_URL ?>/app/image.php?fid=img_${counter}&amp;type=logo" class="win_point">
                                <i class="bi bi-image"></i>
                            </a>
                        </span>
                        <input type="text" id="img_${counter}" name="wset[d][img][]" value="" class="form-control" placeholder="이미지 주소">
                    </div>
                    <div><img id="img_${counter}_preview" src="" style="max-width: 100%; max-height: 48px;" /></div>
                </div>
                <div class="mb-2 row">
                    <div class="col-2">
                        <label class="form-label">이미지 높이</label>
                        <input type="text" id="height_${counter}" name="wset[d][height][]" value="" class="form-control">
                    </div>
                    <div class="col">
                        <label class="form-label">대체 텍스트</label>
                        <input type="text" id="alt_${counter}" name="wset[d][alt][]" value="" class="form-control">
                    </div>
                    <div class="col">
                        <label class="form-label">관리용 메모</label>
                        <input type="text" id="memo_${counter}" name="wset[d][memo][]" value="" class="form-control">
                    </div>
                </div>
                <div class="text-center">
                    <a href="javascript:;" class="ibtnDel"><i class="fa fa-times-circle fa-2x text-muted"></i></a>
                </div>
            </li>`;

            $("#items.list-group").prepend($(cols));
            counter++;
        });

        $("#items").on("click", ".ibtnDel", function (event) {
            $(this).closest("li").remove();
        });

        $("#items.list-group").sortable();
    });
</script>
