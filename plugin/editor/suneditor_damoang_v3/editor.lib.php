<?php

require_once __DIR__ .'/utils.php';

if (!defined('_GNUBOARD_')) {
    exit;
}


function editor_html($id, $content, $is_dhtml_editor = true)
{
  global $config, $w, $board, $write;
  static $js = true;

  if (
    $is_dhtml_editor && $content && (
      (!$w && (isset($board['bo_insert_content']) && !empty($board['bo_insert_content'])))
      || ($w == 'u' && isset($write['wr_option']) && strpos($write['wr_option'], 'html') === false))
  ) {       //글쓰기 기본 내용 처리
    if (preg_match('/\r|\n/', $content) && $content === strip_tags($content, '<a><strong><b>')) {  //textarea로 작성되고, html 내용이 없다면
      $content = nl2br($content);
    }
  }
  //$config['cf_editor'] 는 common.php에서 처리하네.
  $editor_url = (isset($board['bo_select_editor']) && $board['bo_select_editor'] != '') ?  G5_EDITOR_URL . '/' . $board['bo_select_editor'] : G5_EDITOR_URL . '/' . $config['cf_editor'];

  $html = '';
  $html .= '<span class="sr-only">웹에디터 시작</span>';
  if ($is_dhtml_editor && $js) {
      $editor_url = G5_EDITOR_URL . '/' . $config['cf_editor'];
      $html .= <<<HTML
    <script src="$editor_url/suneditor.min.js"></script>
    <script src="$editor_url/ko.js"></script>
    <link rel="stylesheet" href="$editor_url/css/suneditor.min.css">
    <link rel="stylesheet" href="$editor_url/css/suneditor-damoang.css">
    <script src="$editor_url/codemirror/codemirror.min.js"></script>
    <script src="$editor_url/codemirror/css.js"></script>
    <script src="$editor_url/codemirror/xml.js"></script>
    <script src="$editor_url/codemirror/htmlmixed.js"></script>
    <link href="$editor_url/codemirror/codemirror.min.css" rel="stylesheet"/>
    HTML;
    $js = false;
  }

  ft_nonce_create(UPLOAD_NONCE_TOKEN_NAME);

  $suneditor_class = $is_dhtml_editor ? 'suneditor ' : '';
  $html .= '<textarea id="' . $id . '" name="' . $id . '" class=" form-control ' . $suneditor_class . '" maxlength="65536">' . $content . '</textarea>';
  $html .= '<span class="sr-only">웹 에디터 끝</span>';

  $html .= "<script>
  $(function(){
    document.suneditor = SUNEDITOR.create(document.getElementById('$id'), {
      plugins: SUNEDITOR.plugins,
      lang: SUNEDITOR_LANG['ko'],
		attributeWhitelist: { '*': 'class' },
        width : 'auto',
        minWidth : '200px',
        height : '450px',
        minHeight : '450px',
        popupDisplay : 'local',
        resizingBar : true,
        buttonList : [
          ['undo', 'redo'],
          [':단락-default.more_paragraph', 'font', 'fontSize', 'formatBlock', 'paragraphStyle', 'blockquote'],
          ['bold', 'underline', 'italic', 'strike'],
          ['fontColor', 'backgroundColor', 'textStyle'],
          ['image'],
          ['removeFormat'],
          ['outdent', 'indent'],
          ['align', 'hr', 'list', 'lineHeight'],
          ['table', 'link', 'video', 'audio' /** ,'math' */], // You must add the 'katex' library at options to use the 'math' plugin.
          /** ['imageGallery'] */ // You must add the 'imageGalleryUrl'.
          ['-right', ':보기-default.more_vertical', 'showBlocks', 'codeView', 'preview'],
          ['%992', [
            ['undo', 'redo'],
            [':단락-default.more_paragraph', 'font', 'fontSize', 'formatBlock', 'paragraphStyle', 'blockquote'],
            ['bold', 'underline', 'italic', 'strike'],
            [':글자-default.more_text', 'fontColor', 'backgroundColor', 'textStyle'],
            ['image'],
            ['removeFormat'],
            ['outdent', 'indent'],
            ['align', 'hr', 'list', 'lineHeight'],
            [':첨부-default.more_plus', 'table', 'link', 'video', 'audio'],
            ['-right', ':보기-default.more_vertical', 'showBlocks', 'codeView', 'preview'],
          ]],
          ['%768', [
              ['undo', 'redo'],
              [':단락-default.more_paragraph', 'font', 'fontSize', 'formatBlock', 'paragraphStyle', 'blockquote'],
              [':글자-default.more_text', 'bold', 'underline', 'italic', 'strike', 'fontColor', 'backgroundColor', 'textStyle', 'removeFormat'],
              ['image'],
              [':라인-default.more_horizontal', 'outdent', 'indent', 'align', 'hr', 'list', 'lineHeight'],
              [':첨부-default.more_plus', 'table', 'link', 'video', 'audio'],
              ['-right', ':보기-default.more_vertical', 'showBlocks', 'codeView', 'preview']
          ]]
        ],
        image: {
          uploadUrl: '{$editor_url}/image-uploader.php',
          linkEnableFileUpload: true,
          allowMultiple: true,
          useFormatType: true,
          defaultFormatType: 'block',
          uploadSingleSizeLimit: 20971520,
        },
        imageAccept : '.jpg, .jpeg, .gif, .png, .webp, .svg',
        charCounter : true,
        lineAttrReset : '*',
        codeMirror: CodeMirror,
    });

    document.suneditor.getContents = function() {
      return document.suneditor.html.get();
    }

    document.suneditor.setContents = function(_contents) {
      document.suneditor.html.set(_contents);
    }

     document.suneditor.insertHTML = function(_contents) {
      document.suneditor.html.insert(_contents);
    }
  });
  </script>";

  return $html;
}


// textarea 로 값을 넘긴다. js 필수
function get_editor_js($id, $is_dhtml_editor = true)
{
  if ($is_dhtml_editor) {
    return " var {$id}_editor_data = document.suneditor.getContents(); ";
  } else {
    return ' var ' . $id . '_editor = document.getElementById("' . $id . '"); ';
  }
}


//textarea 의 값이 비어 있는지 검사
function chk_editor_js($id, $is_dhtml_editor = true)
{
  if ($is_dhtml_editor) {
    return ' if (!' . $id . '_editor_data) { alert("내용을 입력해 주십시오."); document.suneditor.focus();  return false; } if (typeof(f.' . $id . ')!="undefined") f.' . $id . '.value = ' . $id . '_editor_data; ';
  } else {
    return ' if (!' . $id . '_editor.value) { alert("내용을 입력해 주십시오."); ' . $id . '_editor.focus(); return false; } ';
  }
}
