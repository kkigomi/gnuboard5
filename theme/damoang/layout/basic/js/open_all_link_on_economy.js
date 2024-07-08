/**  알뜰구매 게시판글 본문에 붙는 '모든링크열기' 버튼 동작 js  **/

function attachOpenAllLinkOnBtn() {
    var button = document.getElementById('economy-open-all-links');

    if (button) {
        button.addEventListener('click', openAllLinksInUserContent);
    }
}

async function openAllLinksInUserContent() {
    var userTexts = document.querySelectorAll('.economy-user-text'); // 모든 economy-user-text 요소 선택
    var allLinks = [];

    // 각 economy-user-text 요소 내의 링크 수집
    userTexts.forEach(userText => {
        var links = userText.querySelectorAll('a');
        links.forEach(link => {
            // 1) 이미지에 대한 링크 제외
            // 2) 보이지않는 링크 열지 않음 - innerText가 없는 <a>태그의 링크는 제외 (글쓰기 에디터문제인지 본문에 가끔 텍스트 없는 <a>태그로 중복되는 링크가 있음)
            if (!link.querySelector('img') && link.innerText.trim() !== "") {
                allLinks.push(link.href);
            }
        });
    });

    // 링크들을 순차적으로 열기
    for (let i = 0; i < allLinks.length; i++) {
        const url = allLinks[i];
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
                }, i * 100); // 각 링크를 100ms 간격으로 열기
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', attachOpenAllLinkOnBtn);