<?php

namespace Damoang\Tests\Acceptance;

use AcceptanceTester;

class ExampleCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function tryLogin(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->submitForm('#memberLogin', [
            'mb_id' => 'admin',
            'mb_password' => 'admin',
        ]);
        $I->see('최고관리자', '.hide-profile-img .sv_name');
    }
}