<?php

class SanityCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function landing_01(AcceptanceTester $I)
    {
        //  1. www.damoang.net (or .dev)  접속
        $I->amOnPage('/');

        // 2. 대문 화면의 구성요소들 확인
        $I->see('추천 글');
        $I->see('공지사항');
        $I->see('새로운 소식');
        $I->see('질문답변');
        $I->see('앙지도');
        $I->see('알뜰구매');
        $I->see('자유게시판');
        $I->see('강좌/팁');
        $I->see('사용기');
        $I->see('다모앙 추천 리뷰');
        $I->see('소모임');

        $I->see('로그인');
        $I->see('회원가입');
        $I->see('정보찾기');
    }

    public function landing_02(AcceptanceTester $I)
    {
        // 1. 대문상태에서 시작
        $I->amOnPage('/');

        // 2. 자유게시판 클릭
        $I->click('자유게시판');

        // 3. 이동되었는지 확인
        $I->seeInCurrentUrl('/free');
    }

    public function landing_03(AcceptanceTester $I)
    {
        //1. 대문상태에서 시작
        $I->amOnPage('/');

        // 2. 오른쪽 위 햄버거 메뉴 클릭
        $I->click("//i[@class='bi bi-list fs-4']");

        // 3. slide menu 떴는지 확인
        $I->seeElement('//*[@id="menuOffcanvas"]');
    }

    public function landing_04(AcceptanceTester $I)
    {
        // 1. 대문상태에서 시작
        $I->amOnPage('/');

        // 2. 오른쪽 위 햄버거 메뉴 클릭
        $I->click("//i[@class='bi bi-list fs-4']");
        $I->seeElement('//*[@id="menuOffcanvas"]');

        // 3. slide menu 에서 소모임 굴러간당 클릭
        $I->click("//div[@class='nav-item da-menu--bbs-group-group']");
        $I->click("굴러간당");

        // 4. 굴러간당 진입되었는지 확인
        $I->seeInCurrentUrl('/car');
    }
}
