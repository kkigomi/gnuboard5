let lognBnIndex = 0;
let squareBnIndex = 0;

function showLongSlide(index) {
    const slider = document.querySelector('.carousel-inner');
    const numLongSlides = document.querySelectorAll('.carousel-item').length; // 긴 배너 슬라이드 개수 동적 계산
    lognBnIndex = index % numLongSlides; // 동적으로 계산된 슬라이드 개수로 나눈 나머지로 설정
    slider.style.transform = `translateX(-${lognBnIndex * 100}%)`;
}

function showSquareSlide(index) {
    const squareSlider = document.querySelector('.square-carousel-inner');
    const numSlides = document.querySelectorAll('.square-carousel-item').length;
    squareBnIndex = (index + numSlides) % numSlides;
    squareSlider.style.transform = `translateX(-${squareBnIndex * 100}%)`;
}

function startAutoSlide() {
    setInterval(function () {
        showLongSlide(lognBnIndex + 1); // 현재 인덱스에서 1을 더해 다음 슬라이드로 이동
    }, 4000); // 4초 마다 슬라이드가 자동으로 넘어갑니다.
}


function startSquareAutoSlide() {
    setInterval(function () {
        showSquareSlide(squareBnIndex + 1);
    }, 4000); // 4초 마다 슬라이드가 자동으로 넘어갑니다.
}



window.onload = function () {
    startAutoSlide(); // 기존 슬라이더
    startSquareAutoSlide(); // 새 정사각형 슬라이더
}