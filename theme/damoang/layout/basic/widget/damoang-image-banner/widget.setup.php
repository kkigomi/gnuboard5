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
            <label class="col-sm-2 col-form-label">배너 설정</label>
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

                <ul id="items" class="list-group">
                    <?php
                    // 직접등록 입력폼
                    $data = array();
                    $data_cnt = (isset($wset['d']['img']) && is_array($wset['d']['img'])) ? count($wset['d']['img']) : 1;

                    for ($i = 0; $i < $data_cnt; $i++) {
                        $n = $i + 1;
                        $d_img = isset($wset['d']['img'][$i]) ? $wset['d']['img'][$i] : '';
                        $d_link = isset($wset['d']['link'][$i]) ? $wset['d']['link'][$i] : '';
                        $d_captitle = isset($wset['d']['captitle'][$i]) ? $wset['d']['captitle'][$i] : '';
                        $d_captitle_size = isset($wset['d']['captitle_size'][$i]) ? $wset['d']['captitle_size'][$i] : '';
                        $d_capdesc = isset($wset['d']['capdesc'][$i]) ? $wset['d']['capdesc'][$i] : '';
                        $d_capdesc_size = isset($wset['d']['capdesc_size'][$i]) ? $wset['d']['capdesc_size'][$i] : '';
                        $d_alt = isset($wset['d']['alt'][$i]) ? $wset['d']['alt'][$i] : '';
                        $d_target = isset($wset['d']['target'][$i]) ? $wset['d']['target'][$i] : '';

                        ?>
                        <li class="list-group-item">
                            <div class="mb-3">
                                <label class="form-label">이미지</label>
                                <?php $wset['no_img'] = isset($wset['no_img']) ? $wset['no_img'] : ''; ?>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <a href="<?php echo G5_THEME_URL ?>/app/image.php?fid=img_<?php echo $n ?>&amp;type=banner_image" class="win_point">
                                            <i class="bi bi-image"></i>
                                        </a>
                                    </span>
                                    <input type="text" id="img_<?php echo $n ?>" name="wset[d][img][]" value="<?php echo $d_img ?>" class="form-control" placeholder="https://...">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">링크</label>
                                <div class="input-group">
                                    <input type="text" id="link_<?php echo $n ?>" name="wset[d][link][]" value="<?php echo $d_link ?>" class="form-control  w-75" placeholder="http://...">
                                    <select id="target_<?php echo $n ?>" name="wset[d][target][]" class="form-select">
                                        <option value="_blank" <?php echo get_selected('_blank', $d_target) ?>>새 창</option>
                                        <option value="_self">현재 창</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col">
                                        <label class="form-label">대체 텍스트</label>
                                        <input type="text" id="alt_<?php echo $n ?>" name="wset[d][alt][]" value="<?php echo $d_alt ?>" class="form-control">
                                    </div>
                                    <div class="col">
                                        <label class="form-label">관리용 메모</label>
                                        <input type="text" id="memo_<?php echo $n ?>" name="wset[d][memo][]" value="<?php echo $d_memo ?>" class="form-control">
                                    </div>
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

                <div class="row gx-2 mb-2">
                    <label class="col-md-2 col-form-label">
                        data-dd-action-name
                    </label>
                    <div class="col-md-10">
                        <input type="text" name="wset[action_name]" value="<?php echo $wset['action_name'] ?>" class="form-control" placeholder="배너 효율 측정용 action-name" />
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="button" class="btn btn-outline-primary btn-lg en" id="addrow">
                        배너 추가
                    </button>
                </div>
            </div>
        </div>
    </li>
</ul>

<script>
    $(document).ready(function () {
        var counter = <?php echo $data_cnt + 1 ?>;
        $("#addrow").on("click", function () {
            var cols = `<li class="list-group-item">
                <div class="mb-3">
                    <label class="form-label">이미지</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <a href="<?php echo G5_THEME_URL ?>/app/image.php?fid=img_${counter}&amp;type=banner_image" class="win_point">
                                <i class="bi bi-image"></i>
                            </a>
                        </span>
                        <input type="text" id="img_${counter}" name="wset[d][img][]" value="" class="form-control" placeholder="https://...">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">링크</label>
                    <div class="input-group">
                        <input type="text" id="link_${counter}" name="wset[d][link][]" value="" class="form-control w-75" placeholder="http://...">
                        <select id="target_${counter}" name="wset[d][target][]" class="form-select">
                            <option value="_blank">새 창</option>
                            <option value="_self">현재 창</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col">
                            <label class="form-label">대체 텍스트</label>
                            <input type="text" id="alt_${counter}" name="wset[d][alt][]" value="" class="form-control">
                        </div>
                        <div class="col">
                            <label class="form-label">관리용 메모</label>
                            <input type="text" id="memo_${counter}" name="wset[d][memo][]" value="" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <a href="javascript:;" class="ibtnDel"><i class="fa fa-times-circle fa-2x text-muted"></i></a>
                </div>
            </li>`;

            $("#items.list-group").append($(cols));
            counter++;
        });

        $("#items").on("click", ".ibtnDel", function (event) {
            $(this).closest("li").remove();
        });

        $("#items.list-group").sortable();
    });
</script>
