<?php
include_once('./_common.php');

$edir = (isset($_REQUEST['edir']) && $_REQUEST['edir']) ? na_fid($_REQUEST['edir']) : '';

$emo = array();

if($edir && is_dir(NA_PATH.'/skin/emo/'.$edir)) {
    $is_emo = true;
    $emo_path = NA_PATH.'/skin/emo/'.$edir;
    $emo_skin = $edir.'/';
} else {
    $is_emo = false;
    $emo_path = NA_PATH.'/skin/emo';
    $emo_skin = '';
}

$handle = opendir($emo_path);
while ($file = readdir($handle)) {
    if(preg_match("/(?<!_thumb)\.(jpg|jpeg|gif|png|webp)$/i", $file)) {
        $emo[] = $file;
    }
}
closedir($handle);
sort($emo);

$emoticon = array();
for($i=0; $i < count($emo); $i++) {
    $emoticon[$i]['name'] = $emo_skin.$emo[$i];
    $emoticon[$i]['url'] = NA_URL.'/skin/emo/'.$emo_skin.$emo[$i];
    $emoticon[$i]['thumb'] = preg_replace("/\.(jpg|jpeg|gif|png|webp)$/i", "", $emoticon[$i]['url']).'_thumb.webp';
}

// Emo Skin
$eskin = array();
$ehandle = opendir(NA_PATH.'/skin/emo');
while ($efile = readdir($ehandle)) {
    if($efile == "." || $efile == ".." || preg_match("/(?<!_thumb)\.(jpg|jpeg|gif|png|webp)$/i", $efile)) continue;

    if (is_dir(NA_PATH.'/skin/emo/'.$efile))
        $eskin[] = $efile;
}
closedir($ehandle);
sort($eskin);
$eskin_cnt = count($eskin);

$g5['title'] = '이모티콘';
include_once(G5_PATH.'/head.sub.php');

include_once(G5_THEME_PATH.'/app/clipboard.php');
?>
<style>
    #emo_icon .emo-img {
        width:48px;
        height:48px;
        cursor:pointer;
    }
</style>

<div class="pe-1">
    <?php if($eskin_cnt) { $clip_change = ($is_clip) ? "+'&clip=1'" : ""; ?>
        <form name="fclip" method="get">
            <div class="row mb-2">
                <div class="col-sm-6 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="bi bi-emoji-smile"></i>
                        </span>
                        <select class="form-select nofocus" name="eskin" onchange="location='<?php echo G5_THEME_URL ?>/include/clip.emo.php?edir='+encodeURIComponent(this.value)<?php echo $clip_change ?>;">
                            <option value="">Basic</option>
                            <?php for($i=0; $i < $eskin_cnt; $i++) { ?>
                                <option value="<?php echo $eskin[$i] ?>"<?php echo get_selected($edir,$eskin[$i]) ?>><?php echo ucfirst($eskin[$i]) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    <?php } ?>

    <div id="emo_icon">
        <?php for($i=0; $i < count($emoticon); $i++) { ?>
            <img src="<?php echo $emoticon[$i]['thumb'] ?>" class="emo-img border m-1" alt="emo-<?php echo $i; ?>"
                id="emo-<?php echo $i; ?>" url="<?php echo $emoticon[$i]['url'] ?>" thumb="<?php echo $emoticon[$i]['thumb'] ?>"
                ontouchend="this.isTouched = !this.isTouched;this.isTouched&&setFocus(this.id)"
                onclick="!this.isTouched && clip_insert('<?php echo $emoticon[$i]['name'] ?>');"
                onmouseenter="!this.isTouched&&setFocus(this.id);" onmouseleave="!this.isTouched&&unFocus(this.id);">
        <?php } ?>
    </div>
</div>

<script>
var currentId = null;
function setFocus(id) {
    if(currentId) unFocus(currentId);
    if(id) {
        var newEle = document.getElementById(id);
        if(newEle) {
            currentId = id;
            newEle.classList.add('border-primary','border-2');
            newEle.timer = setTimeout(function(){newEle.src=newEle.getAttribute('url')},250)
        }
    }
}
function unFocus(id) {
    var oldEle = document.getElementById(id);
    if(oldEle) {
        clearTimeout(oldEle.timer);
        oldEle.isTouched = false;
        oldEle.classList.remove('border-primary','border-2');
        oldEle.src = oldEle.getAttribute('thumb');
    }
}
function clip_insert(txt) {
    var clip = "{emo:" + txt + ":110}";

    if(parent.document.suneditor) {
        parent.document.suneditor.insertHTML(clip);
        window.parent.naClipClose();
    } else {
        <?php if($is_clip) { ?>
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
include_once(G5_PATH.'/tail.sub.php');
