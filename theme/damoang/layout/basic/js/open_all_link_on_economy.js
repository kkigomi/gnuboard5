/**  알뜰구매 게시판글 본문에 붙는 '모든링크열기' 버튼 동작 js  **/

function attachOpenAllLinkOnBtn() {
    var button = document.getElementById('economy-open-all-links');

    if (button) {
        button.addEventListener('click', openAllLinksInUserContent);
    }
}

function openAllLinksInUserContent() {
    var userText = document.querySelector('.economy-user-text');
    var links = userText.querySelectorAll('a');

    links.forEach((link, index) => {
        const url = link.href;
        if (url) {
            setTimeout(() => {
                let a = document.createElement('a');
                a.href = url;
                a.target = '_blank';
                a.rel = 'noopener noreferrer';

                document.body.appendChild(a);
                a.click();

                // a 태그를 document에서 제거
                document.body.removeChild(a);
            }, index * 50); // 각 링크를 50ms 간격으로 열기
        }
    });
}

document.addEventListener('DOMContentLoaded', attachOpenAllLinkOnBtn);