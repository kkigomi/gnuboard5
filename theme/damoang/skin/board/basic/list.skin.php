<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="' . $board_skin_url . '/style.css?CACHEBUST">', 0);

// 멤버십
na_membership('list', '멤버십 회원만 목록을 볼 수 있습니다.');

// 다모앙 회원 메모
$list = run_replace('da_board_list', $list);

// 회원만 보기
foreach ($list as &$item) {
    $item['da_is_member_only'] = false;
    $item['da_member_only'] = '';

    if (empty($item['wr_1'])) {
        continue;
    }

    $item['da_is_member_only'] = true;
    $item['da_member_only'] = '<em class="border rounded p-1 me-1" style="font-size: 0.75em; font-style: normal;">회원만</em>';
}

// 분류 스킨
$category_skin = isset($boset['category_skin']) && $boset['category_skin'] ? $boset['category_skin'] : 'basic';
$category_skin_url = $board_skin_url.'/category/'.$category_skin;
$category_skin_path = $board_skin_path.'/category/'.$category_skin;

// 목록 스킨
$list_skin = isset($boset['list_skin']) && $boset['list_skin'] ? $boset['list_skin'] : 'list';
$list_skin_url = $board_skin_url.'/list/'.$list_skin;
$list_skin_path = $board_skin_path.'/list/'.$list_skin;
?>

<!--   <div class="alert alert-light mb-4 mx-3 mx-sm-0" role="alert">-->
<!--    <img src="https://damoang.net/logo/0416_02.gif" style="max-width: 100%"><br/>-->
<!--        -->
<!--   </div>-->
<?php echo na_widget('damoang-image-banner', 'board-head'); ?>

<?php
/* 게시판 페이지 글목록 상단에 특수기능 출력. 구글캘린더등 소모임 대문에 특수기능을 출력할 수 있도록 하는 파일 */
include_once 'list.skin.top-embed.php';
?>

<?php echo $config['cf_10'];?>
<div class="rolling-noti-container-list small" id="rolling-noti-container-list">
  <div class="fixed-text">
    <span class="bi bi-bell"></span> 알림
  </div>
  <div class="divider">|</div>
  <div class="rolling-noti-list" id="rolling-noti-list"></div>
</div>

<?php 
/********** 
 * PAI 위젯 설정 버튼: (관리자) 현재 게시판이 직접홍보 게시판에서 위젯설정을 눌렀을때 promotion-ad-insertion 위젯 데이터 설정을 여는 버튼 표시.
 * 이 위젯이 별도로 출력하는 HTML은 없음.
*/
if ($bo_table == 'promotion')
{
    echo na_widget('promotion-ad-insertion', 'pai');
}
?>

<div id="bo_list_wrap">
    <?php
        // 분류 스킨
        $skin_file = $category_skin_path.'/category.skin.php';
        if (is_file($skin_file)) {
            include_once $skin_file;
        } else {
            echo '<div class="text-center px-3 py-5">'.str_replace(G5_PATH, '', $skin_file).' 스킨 파일이 없습니다.</div>'.PHP_EOL;
        }
    ?>

    <form name="fboardlist" id="fboardlist" method="post">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
        <input type="hidden" name="stx" value="<?php echo $stx ?>">
        <input type="hidden" name="spt" value="<?php echo $spt ?>">
        <input type="hidden" name="sca" value="<?php echo $sca ?>">
        <input type="hidden" name="sst" value="<?php echo $sst ?>">
        <input type="hidden" name="sod" value="<?php echo $sod ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>">
        <input type="hidden" name="sw" value="">

        <?php
        // 목록 스킨
        $skin_file = $list_skin_path.'/list.skin.php';
        if (is_file($skin_file)) {
            include_once $skin_file;
        } else {
            echo '<div class="text-center px-3 py-5">'.str_replace(G5_PATH, '', $skin_file).' 스킨 파일이 없습니다.</div>'.PHP_EOL;
        }
        ?>

        <?php /***** 목록 하단 페이지네이션 ****/  ?>
        <ul class="pagination pagination-sm justify-content-center">
            <?php if($prev_part_href) { ?>
                <li class="page-item"><a class="page-link" href="<?php echo $prev_part_href;?>">Prev</a></li>
            <?php } ?>
            <?php echo na_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, get_pretty_url($bo_table, '', $qstr.'&amp;page=')) ?>
            <?php if($next_part_href) { ?>
                <li class="page-item"><a  class="page-link" href="<?php echo $next_part_href;?>">Next</a></li>
            <?php } ?>

            <?php /* 페이지네이션 옆에 검색과 글쓰기 버튼 추가 */?>
            <li>
                <a href="#boardSearc_b" data-bs-toggle="collapse" data-bs-target="#boardSearch_b" aria-expanded="false" aria-controls="boardSearch_b" class="btn btn-basic ms-2">
                    <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="검색">
                        <i class="bi bi-search"></i>
                        <span class="visually-hidden">검색</span>
                    </span>
                </a>
            </li>
            <li>
                 <a href="<?php echo $write_href ?>" class="btn btn-basic  ms-1" style="white-space: nowrap;">
                    <i class="bi bi-pencil-square"></i>
                    쓰기
                </a>
            </li>
        </ul>

    </form>

    <?php /*** 페이지네이션 옆 검색버튼 form ***/ ?>
    <div class="collapse<?php echo ($stx) ? ' show' : '';?>" id="boardSearch_b">
        <div class="px-3 py-2 border-top">
            <form id="fsearch" name="fsearch" method="get" action="<?php echo get_pretty_url($bo_table); ?>">
                <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
                <input type="hidden" name="sca" value="<?php echo $sca ?>">
                <div class="row g-2">
                    <div class="col-6 col-md-3 col-lg-2">
                        <label for="bo_sfl" class="visually-hidden">검색대상</label>
                        <select id="bo_sfl" name="sfl" class="form-select form-select-sm">
                            <?php echo get_board_sfl_select_options($sfl); ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                        <label for="bo_sop" class="visually-hidden">검색조건</label>
                        <select id="bo_sop" name="sop" class="form-select form-select-sm">
                            <option value="and"<?php echo get_selected($sop, "and") ?>>그리고</option>
                            <option value="or"<?php echo get_selected($sop, "or") ?>>또는</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-8">
                        <label for="bo_stx" class="visually-hidden">검색어 필수</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="stx" id="bo_stx" value="<?php echo stripslashes($stx) ?>" required placeholder="검색어 입력">
                            <a href="<?php echo get_pretty_url($bo_table); ?>" rel="nofollow" class="btn btn-basic" title="초기화">
                                <i class="bi bi-arrow-clockwise"></i>
                                <span class="visually-hidden">초기화</span>
                            </a>
                            <button class="btn btn-primary" type="submit" id="fsearch_submit" title="검색">
                                <i class="bi bi-search"></i>
                                <span class="d-none d-sm-inline-block">검색</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
// 게시판 스킨 설정
if($is_admin)
    @include_once G5_THEME_PATH.'/app/board.setup.php';
?>
<?php if ($is_checkbox) { ?>
<noscript>
<p align="center">자바스크립트를 사용하지 않는 경우 별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
</noscript>

<script>
function all_checked(sw) {
    var f = document.fboardlist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]")
            f.elements[i].checked = sw;
    }
}

function fboardlist_submit(txt) {
    var f = document.fboardlist;
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        na_alert(txt + '할 게시물을 하나 이상 선택하세요.');
        return false;
    }

    if(txt == "선택복사") {
        select_copy("copy");
        return;
    }

    if(txt == "선택이동") {
        select_copy("move");
        return;
    }

    if(txt == "선택삭제") {
        let msg = '선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다.\n답변글이 있는 게시글을 선택하신 경우 답변글도 선택하셔야 게시글이 삭제됩니다.';
        na_confirm(msg, function() {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'btn_submit';
            input.value = '선택삭제';
            f.appendChild(input);
            f.removeAttribute("target");
            f.action = g5_bbs_url+"/board_list_update.php";
            f.submit();
        });
        return false;
    }

    return false;
}

// 선택한 게시물 복사 및 이동
function select_copy(sw) {
    var f = document.fboardlist;

    if (sw == "copy")
        str = "복사";
    else
        str = "이동";

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.sw.value = sw;
    f.target = "move";
    f.action = g5_bbs_url+"/move.php";
    f.submit();
}

</script>
<?php } ?>
<?php
//
function getRollingNotiDataList($key) {
    global $rollingNotiData;

    foreach ($rollingNotiData as $noti) {
        if ($noti['bo_table'] === $key) {
            return $noti['msg'];
        }
    }

    return [];
}
?>
<script>
// 롤링 공지 호출 함수
function showRollingNotiList() {
    const rollingNotiContainer = document.getElementById('rolling-noti-container-list');
    const rollingNoti = document.getElementById('rolling-noti-list');

    rollingNotiContainer.style.display = 'none';

    let allBoardMessages = <?=json_encode(getRollingNotiDataList('all_board'))?>;
    let keyMessages = <?=json_encode(getRollingNotiDataList($bo_table))?>;

    allBoardMessages = allBoardMessages.filter(function(message) {
        return message.charAt(0) !== '#';
    });

    keyMessages = keyMessages.filter(function(message) {
        return message.charAt(0) !== '#';
    });

    let messages = allBoardMessages.concat(keyMessages);

    if (messages.length === 0) {
        rollingNotiContainer.style.display = 'none';
        return;
    }

    rollingNotiContainer.style.display = 'flex';

    let index = 0;
    let intervalId;

    function createRollingNotiElement(text, isNext) {
        const element = document.createElement('div');
        element.innerHTML = text;
        if (isNext) {
            element.style.transform = 'translateY(100%)';
        }
        return element;
    }

    function updateRollingNoti() {
        const currentElement = rollingNoti.firstChild;
        let nextIndex = index;
        const nextElement = createRollingNotiElement(messages[nextIndex], true);
        nextIndex = (index +1) % messages.length;

        rollingNoti.appendChild(nextElement);

        nextElement.offsetHeight;

        nextElement.style.transform = 'translateY(0)';
        if (currentElement) {
            currentElement.style.transform = 'translateY(-100%)';
        }

        setTimeout(() => {
            if (rollingNoti.firstChild && rollingNoti.firstChild !== nextElement) {
                rollingNoti.removeChild(rollingNoti.firstChild);
            }
            index = nextIndex;
        }, 1000);
    }

    rollingNoti.appendChild(createRollingNotiElement(messages[index], false));

    index = (messages.length === 1) ? 0 : 1;

    intervalId = setInterval(updateRollingNoti, 4000);

    return () => clearInterval(intervalId);
}

if (!document.querySelector('.rolling-noti-container-view')) {
    try {
        showRollingNotiList();
    } catch (e) {
        console.error(e);
    }
}
</script>
