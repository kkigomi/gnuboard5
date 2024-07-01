<?php
if (!defined('_GNUBOARD_'))
    exit; // 개별 페이지 접근 불가
/*****************************************
 * PAI - promotion-ad-insertion 직접홍보 게시글 롤링할 광고주 관리 위젯
 * '직접홍보' 게시판(ID: promotion)에서 위젯설정을 켰을 때 위젯설정버튼이 나타남: theme/damoang/skin/board/basic/list.skin.php  na_widget('promotion-ad-insertion', 'pai').
 * 이 위젯이 표시하는 것은 설정 버튼밖에 없음.
 * 위젯데이터: /data/nariya/widget/w-promotion-ad-insertion-pai-{플랫폼}.php
 *****************************************/

 //위젯 데이터 설정 버튼 표시
echo GetWidgetSettingsBtn_pai($list, $list_cnt, $wset, $setup_href);


/*********
 * 함수
 **********/
/** 위젯 설정 Edit 버튼. "직홍게 광고주 목록" 이라는 버튼을 표시 */
function GetWidgetSettingsBtn_pai($list, $list_cnt, $wset, $setup_href)
{
    $html = '';
    $setup_button = $setup_href ? <<<EOT
                                    <div class="btn-wset py-2">
                                        <button onclick="naClipView('$setup_href');" class="btn btn-basic btn-sm">
                                            <i class="bi bi-gear"></i>직홍게 광고주 목록
                                        </button>
                                    </div>
                            EOT : '';
    $html = $setup_button;

    return $html;
}
