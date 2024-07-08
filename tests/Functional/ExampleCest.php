<?php

namespace Damoang\Tests\Functional;

use FunctionalTester;

class ExampleCest
{
    public function _before(FunctionalTester $I)
    {
    }

    // tests
    public function tryLogin(FunctionalTester $I)
    {
        $I->amOnPage('/');
        $I->submitForm('#memberLogin', [
            'mb_id' => 'admin',
            'mb_password' => 'admin',
        ]);
        $I->see('최고관리자', '.hide-profile-img .sv_name');
    }
}
