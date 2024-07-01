<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
/*****************************************
 * PAI 위젯 설정 파일 - promotion-ad-insertion 직접홍보 게시글 롤링할 게시판, 광고주 관리 위젯 설정 파일
 * '직접홍보' 게시판(ID: promotion)에서 위젯설정을 켰을 때 위젯설정버튼이 나타나고, 클릭하면 나타나는 창.
 * theme/damoang/skin/board/basic/list.skin.php 에서 na_widget('promotion-ad-insertion', 'pai')로 호출됨. 
 * 위젯데이터 저장파일: /data/nariya/widget/w-promotion-ad-insertion-{위젯ID}-{플랫폼}.php
 * 이 위젯설정에서 설정하면 plugin/nariya/bbs/list.php (각 게시판 글목록을 만드는 파일)에서 데이터를 가져와 각 게시판의 글목록에 직홍게 게시글을 삽입함
 *****************************************/


$widget = 'promotion-ad-insertion';

/****** 위젯데이터 (/data/nariya/w-promotion-ad-insertion-{}.php 의 배열) ******/
//광고주 이름 목록
$advertisers = isset($wset['d']['advertisers']) ? $wset['d']['advertisers'] : [];
$advertiser_cnt = count($advertisers);
//표시 제외할 게시판ID
$board_exception = isset($wset['d']['board_exception']) ? $wset['d']['board_exception'] : '';
$board_exception_str = is_array($board_exception) ? implode(',', $board_exception) : $board_exception;
// 몇번째 인덱스에 직홍게글을 표시할지
$insert_index = isset($wset['d']['insert_index']) ? $wset['d']['insert_index'] : 0;
// 인덱스 시작 최소한 글 갯수
$min_cnt_for_insert_index = isset($wset['d']['min_cnt_for_insert_index']) ? $wset['d']['min_cnt_for_insert_index'] : 5;
// 직홍게 글 몇개를 표시할지
$how_many_to_display = isset($wset['d']['how_many_to_display']) ? $wset['d']['how_many_to_display'] : 1;


/****** 저장된 위젯데이터로 구성된 HTML 테이블 rows ******/
$rowsOfSavedAdvertisers = '';
for ($i = 0; $i < $advertiser_cnt; $i++) {
    $rowsOfSavedAdvertisers .= getAdvertiserRow_pai(htmlspecialchars($advertisers[$i], ENT_QUOTES), $i + 1);
}

echo <<<EOT
<ul class="list-group">
    <p>'직홍게 홍보글 삽입 위젯(PAI) 데이터 설정: 직접홍보 게시판(promotion)의 글을 여러 게시판에서 글목록에 '홍보'글로 삽입하기 위한 규칙을 설정합니다. 데이터 저장 위치: /data/nariya/widget/w-promotion-ad-insertion-pai-{}.php</p>
    <li class="list-group-item">
        <div class="form-group row mb-0">
            <label class="col-sm-2 col-form-label">계약중인 광고주</label>
            <div class="col-sm-10">
                <div class="table-responsive">
                    <table id="widgetData" class="table table-bordered order-list mb-0">
                        <p>현재 계약중이며 직접홍보 게시판에 글을 작성한 광고주 이름을 <strong>정확히</strong> 입력해주세요. 이름이 중복되지 않게 주의해주세요. 순서는 상관 없습니다.</p>
                        <p>광고주 목록 중 아래의 '삽입 개수' 만큼의 광고주가 랜덤으로 선택되어 해당 광고주 각자의 가장 최근 게시물이 '홍보'글로 게시판 목록에 삽입됩니다.</p>
                        <thead>
                            <tr class="bg-light">
                                <th class="text-center nw-20">광고주 이름</th>
                            </tr>
                        </thead>
                        <tbody id="sortable">
                            $rowsOfSavedAdvertisers
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-outline-primary btn-lg en" id="addrow">추가</button>
                </div>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row mb-0">
            <label class="col-sm-2 col-form-label">제외 게시판</label>
            <div class="col-sm-10">
                <input type="text" name="wset[d][board_exception]" value="{$board_exception_str}" class="form-control" placeholder="쉼표로 구분된 게시판 ID">
                <p>직홍게 글을 삽입하지 않을 게시판 ID를 입력하세요. 여러 개의 ID는 쉼표로 구분합니다. 예: promotion,free</p>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row mb-0">
            <label class="col-sm-2 col-form-label">삽입 인덱스</label>
            <div class="col-sm-10">
                <input type="number" name="wset[d][insert_index]" value="{$insert_index}" class="form-control" placeholder="삽입될 인덱스">
                <p>직홍게글을 몇번째 글로 삽입 할까요? 공지글을 제외한  몇 번째에 직홍게 글을 넣을지 결정합니다. 예: 0을 입력하면 직홍게글을 게시판의 첫번째 항목으로 삽입합니다.</p>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row mt-0">
            <label class="col-sm-2 col-form-label">최소 글 갯수</label>
            <div class="col-sm-10">
                <input type="number" name="wset[d][min_cnt_for_insert_index]" value="{$min_cnt_for_insert_index}" class="form-control" placeholder="삽입인덱스 적용을 위한 최소한의 글 갯수">
                <p>출력되는 게시판의 글수가 몇 개 이상일때 부터 '삽입 인덱스'를 적용할지 입력하세요. 회원 게시글이 이 개수보다 적다면 위의 '삽입 인덱스'는 0으로 고정됩니다.</p>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="form-group row mb-0">
            <label class="col-sm-2 col-form-label">삽입갯수</label>
            <div class="col-sm-10">
                <input type="number" name="wset[d][how_many_to_display]" value="{$how_many_to_display}" class="form-control" placeholder="직홍게 글 삽입갯수">
                <p>출력대상 직홍게 글 중 몇개의 글이 게시판 글목록에 삽입될지 결정. 예: 2를 입력하면 램덤으로 두개의 직홍게글이 삽입됩니다. 최대 3개 초과 출력되지 않습니다.</p>
            </div>
        </div>
    </li>
</ul>
EOT;
?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var counter = <?php echo $advertiser_cnt + 1; ?>;
        var emptyInputRowToAdd = <?php echo json_encode(getAdvertiserRow_pai('', $advertiser_cnt + 1)); ?>;

        document.getElementById("addrow").addEventListener("click", function () {
            var trbg = (counter % 2 === 1) ? 'bg-light-1' : 'bg-light';
            var newRow = document.createElement("tr");
            newRow.className = trbg;
            newRow.innerHTML = emptyInputRowToAdd;
            document.getElementById("sortable").appendChild(newRow);
            counter++;
        });
    });

    function removeRow(button) {
        var row = button.closest("tr");
        row.remove();
    }
</script>

<?php
/*********
 * 함수
 **********/

/**
 * 광고주 이름 입력 필드를 포함하는 테이블 행을 생성합니다.
 *
 * @param string $inputValue 입력 필드의 값. (기본값: 빈 문자열, 데이터 로드시에는 위젯데이터를 입력)
 * @param int $counter 행 번호에 따라 행의 배경색을 결정하는 데 사용됩니다.
 * @return string 테이블 행(row)의 HTML
 */
function getAdvertiserRow_pai($inputValue = '', $counter)
{
    $tr_class = ($counter % 2 === 1) ? 'bg-light-1' : 'bg-light';
    $html = <<<EOT
<tr class="$tr_class">
    <td>
        <div class="input-group">
            <input type="text" name="wset[d][advertisers][]" value="{$inputValue}" class="form-control" placeholder="광고주 이름 입력">
            <button type="button" class="btn btn-danger" onclick="removeRow(this)">삭제</button>
        </div>
    </td>
</tr>
EOT;
    return $html;
}