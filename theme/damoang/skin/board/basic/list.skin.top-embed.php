<?php
if (!defined('_GNUBOARD_'))
    exit; // 개별 페이지 접근 불가

/**************************************************************************
 * 각 게시판에 따라 상단에 출력하고 싶은 기능을 출력하도록 하는 파일
 * 게시판 ID를 구분해서 특정 소모임 게시판에만 해당하는 코드를 넣을 수 있음.
 * theme/damoang/skin/board/basic/list.skin.php 에 inlcude 됨
 **************************************************************************/

/* '공부한당' 소모임 게시판 요청 반영. https://damoang.net/development/726 */
if ($bo_table == 'study')
{
    echo get_qnet_google_calendar_html();
}


/************************************
 *  함수 모음
 ************************************/

/** 큐넷 시험일정 구글캘린더 iframe을 접고 펼치는 부트스트랩 Accordion UI html로 반환  */
function get_qnet_google_calendar_html()
{
    $html = <<<EOT
        <div class="accordion" id="calendarAccordion" style="margin-bottom:10px;">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        <i class="bi bi-calendar3"></i>&nbsp큐넷 기술자격 시험일정
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#calendarAccordion">
                    <div class="accordion-body">
                        <p><a href="https://www.q-net.or.kr/crf021.do?id=crf02103&gSite=Q&gId=&CST_ID=CRF_Stns_06" target="_blank" class="btn btn-primary" style="margin-bottom: 0px;">Q-net에서 보기 <i class="bi bi-box-arrow-up-right"></i></a></p>
                        <iframe style="BORDER-LEFT-WIDTH: 0px; BORDER-RIGHT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-TOP-WIDTH: 0px" height="850" src="https://calendar.google.com/calendar/embed?height=600&amp;wkst=1&amp;bgcolor=%23ffffff&amp;ctz=Asia%2FSeoul&amp;src=b2hxMTZkY2JsdjQ4aDIxc2tlZXI4MmZjMjRAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=bTZpaDk0ZHRxMDRoMGk0NXVyMXJuNmh2dDhAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=NWdzYWxkdWVmc2o3cm9hc2Z0aTg1OWc4Mm9AZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=NmNxNjM3cnFzb2FkY3M4ZW9jNzVpdG90ZHNAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=OXNpajNldWEyZTI0aGtmM3N0Y2tjOWlxcThAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=NTZwYWgybGExbWQya2hrbHVmZDU0dmNqbmdAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=aWpnbGJjMmRwOTFzc2VpdWxtbmp1MnJtb2NAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=aGs0bmtncHYwcGJnYmoxMmUzMTRyamJyZjRAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=Y2J0bjFobjNnMjBtcGIydWNjZWVjOWhuNDBAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=Z3JjcHJvMjAyZ3U4aWxiMHByYWk5cm5laW9AZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=bDA1aDM0c2g3dWtlZHJoaTNjN25uMzhhNW9AZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=Y3I0bWNhMzNhOHRzY2M2bGo4Z3J2aGtnZTRAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=dmRsOGl1ZXJlbDVwcWM4c2ZrbDZxazc5ZmdAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=MXZkbzAwdmVlbjBpNms0MDRoa3ZybHNmYzRAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=Ym1ibm1sYXUwZW4zMXNxbm5wbmFxOWZ2b2tAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=YjIxOTFhc25tbnVzZGFqbmRwZjY3NmNnMzRAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=ZGk5aGxyM3RrYmw3ODk0cWNxYjRlazE0YWtAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=bW1kOTdxaGIyc2x2ZzIzdGVmYmQ5NzNpbDBAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=OWcwdjBvYmYyMnJuN2txM2Q5cnR2NHY5NTRAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=dTRxMTk1N2MycmkwdnZvMGJtdXVibTdwN2NAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=aHFwNGRpMjRvNGJldmk3dDJmbDhzOGtvazhAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=ZmNxdjczZmxjamlsM2gxbWtnbmc2MTlkaDhAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=ZThkOTNwZDg3c2RxNmdkZjZuOWlxNDlpdjRAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=MzkwZTVjcHJhY2Z0cWd0cW0wZnF0cTkxNm9AZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=M2Y3NmlhYWNzM2Y0OW0wY2o5YXBoYW0zYTRAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=ODlzOGIyYTIyZjg0NGdrNXJhZXRucm1yNG9AZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=MmxjZGhnYW91ZnVvdWl1ZDc5ajRoODQxZWtAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=YWc5c29iMThkb3B0NjQ0Mmh0NW5kNGJlaHNAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=OG5vcDNqdnY2Nzk5dGdxNmpzcjg5aHZsYzhAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=YTZrZjAxYm05bjM4amg1OTVlbDdnMDc4MmNAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=NW41bXBibzVyNDNtaG1ndGhkNWEzcW44cGNAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=dmg4cWJpaGFsbjZkMjVlN3U3cmRucjEwcmtAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=MmoydGU3ZjFibmY0cWhlMjFpZ3NiZTM2bThAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=YW0yMnI0NWczdGtkZGZybDRsaDk0dmRnZXNAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;src=NjE3NWlqdGoxMDdxdmNxNmp1M2Jmcmhra3NAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&color=%23009688&color=%23795548&color=%23E67C73&color=%23D50000&color=%23C0CA33&color=%23F4511E&color=%23EF6C00&color=%23F09300&color=%237CB342&color=%230B8043&color=%237CB342&color=%23C0CA33&color=%23E4C441&color=%23F6BF26&color=%2333B679&color=%23039BE5&color=%234285F4&color=%233F51B5&color=%237986CB&color=%23B39DDB&color=%23616161&color=%23A79B8E&color=%23AD1457&color=%23D81B60&color=%238E24AA&color=%239E69AF&color=%23AD1457&color=%23795548&color=%23E67C73&color=%23D50000&color=%23F4511E&color=%23EF6C00&color=%23F09300&color=%23009688&color=%230B8043" frameborder="0" width="100%" scrolling="no"> </iframe>
                    </div>
                </div>
            </div>
        </div>
EOT;
    return $html;
}
