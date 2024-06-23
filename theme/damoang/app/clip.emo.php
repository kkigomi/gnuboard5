<?php
include_once ('./_common.php');

$edir = (isset($_REQUEST['edir']) && $_REQUEST['edir']) ? na_fid($_REQUEST['edir']) : '';

$emo = array();

$is_emo = false;
$emo_path = NA_PATH . '/skin/emo';
$emo_skin = '';

// 기본 크기(width)
$defaultSize = 100;

// 이모티콘 목록 순서 및 이미지 기본 크기(width) 지정
// 파일명에서 숫자 이전의 문자열 (ex: damoang-air-001.gif = damoang-air)
// 지정된 순서로 출력되며, 지정되지 않은 것은 이름 순으로 나열
$emoticon = [
    'damoang-air' => [],
    'damoang-emo' => [],
    'moon-emo' => ['size' => 100],
    'president' => ['size' => 100],
];

foreach (glob($emo_path . "/*") as $filename) {
    $pathinfo = pathinfo($filename);
    if (
        $filename == '.'
        || $filename == '..'
        || !in_array($pathinfo['extension'], ['webp', 'gif', 'jpg', 'jpeg', 'svg'])
        || strpos($pathinfo['basename'], '_thumb') !== false
    ) {
        continue;
    }

    $temp = explode('-', $pathinfo['basename']);
    $category = implode('-', array_splice($temp, 0, count($temp) - 1));

    if (!isset($emoticon[$category])) {
        $emoticon[$category] = [];
    }
    if (!isset($emoticon[$category]['size'])) {
        $emoticon[$category]['size'] = $defaultSize;
    }
    if (!isset($emoticon[$category]['items'])) {
        $emoticon[$category]['items'] = [];
    }

    $url = str_replace(\G5_PATH, \G5_URL, $filename);
    $emoticon[$category]['items'][] = [
        'id' => $pathinfo['filename'],
        'name' => $pathinfo['basename'],
        'insert_name' => "{$pathinfo['basename']}:{$emoticon[$category]['size']}",
        'url' => $url,
        'thumb' => str_replace($pathinfo['basename'], "{$pathinfo['filename']}_thumb.webp", $url),
    ];
}

$g5['title'] = '이모티콘';
include_once (G5_PATH . '/head.sub.php');

include_once (G5_THEME_PATH . '/app/clipboard.php');
?>
<style>
    #emo_icon .emo-img {
        width: 50px;
        height: 50px;
        cursor: pointer;
    }
</style>

<div class="pe-1">
    <div id="emo_icon">
        <?php foreach ($emoticon as $categoryName => $data) { ?>
            <div>
                <?php foreach ($data['items'] as $item) { ?>
                    <img src="<?php echo $item['thumb'] ?>"
                        class="emo-img border m-1"
                        alt="emo-<?php echo $item['id'] ?>"
                        id="emo-<?php echo $item['id'] ?>"
                        url="<?php echo $item['url'] ?>"
                        thumb="<?php echo $item['thumb'] ?>"
                        ontouchend="this.isTouched = !this.isTouched;this.isTouched&&setFocus(this.id)"
                        onclick="!this.isTouched && clip_insert('<?php echo $item['insert_name'] ?>');"
                        onmouseenter="!this.isTouched&&setFocus(this.id);"
                        onmouseleave="!this.isTouched&&unFocus(this.id);" />
                <?php } ?>
            </div>
            <hr>
        <?php } ?>
    </div>
</div>

<script>
    var currentId = null;
    function setFocus(id) {
        if (currentId) unFocus(currentId);
        if (id) {
            var newEle = document.getElementById(id);
            if (newEle) {
                currentId = id;
                newEle.classList.add('border-primary', 'border-2');
                newEle.timer = setTimeout(function () { newEle.src = newEle.getAttribute('url') }, 250)
            }
        }
    }
    function unFocus(id) {
        var oldEle = document.getElementById(id);
        if (oldEle) {
            clearTimeout(oldEle.timer);
            oldEle.isTouched = false;
            oldEle.classList.remove('border-primary', 'border-2');
            oldEle.src = oldEle.getAttribute('thumb');
        }
    }
    function clip_insert(txt) {
        var clip = `{emo:${txt}}`;

        if (parent.document.suneditor) {
            parent.document.suneditor.insertHTML(clip);
            window.parent.naClipClose();
        } else {
            <?php if ($is_clip) { ?>
                $("#txtClip").val(clip);
                $('#clipModal').modal('show');
            <?php } else { ?>
                parent.document.getElementById("wr_content").value += clip;
                window.parent.naClipClose();
            <?php } ?>
        }
    }
</script>

<?php
include_once (G5_PATH . '/tail.sub.php');
