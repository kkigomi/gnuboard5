/**  알뜰구매 게시판글 본문에 붙는 '모든링크열기' 버튼 동작 js  **/

function attachOpenAllLinkOnBtn() {
    var button = document.getElementById('economy-open-all-links');

    if (button) {
        button.addEventListener('click', openAllLinksInUserContent);
    }
}

async function openAllLinksInUserContent() {
    var userText = document.querySelector('.economy-user-text');
    var links = userText.querySelectorAll('a');

    for (let i = 0; i < links.length; i++) {
        const url = links[i].href;
        if (url) {
            await new Promise(resolve => {
                setTimeout(() => {
                    // 백그라운드에서 새 탭 열기
                    let newTab = window.open(url, '_blank', 'noopener,noreferrer');
                    if (newTab) {
                        newTab.blur(); // 새 탭 포커스 해제
                        window.focus(); // 현재 탭에 포커스 유지
                    }
                    resolve();
                }, i * 100); // 각 링크를 ?ms 간격으로 열기
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', attachOpenAllLinkOnBtn);