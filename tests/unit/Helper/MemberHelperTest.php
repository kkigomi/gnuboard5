<?php

namespace Damoang\Tests\Unit\Helper;

use Damoang\Lib\Helper\MemberHelper;

class MemberHelperTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testValidCertified()
    {
        // 간편인증
        $result = MemberHelper::isCertified('simple', 'b5bea41b6c623f7c09f1bf24dcae58ebab3c0cdd90ad966bc43a45b44867e12b');
        $this->assertTrue($result);

        // 재외국민
        $result = MemberHelper::isCertified('abroad', '');
        $this->assertTrue($result);
        $result = MemberHelper::isCertified('abroad', 'dummydummydummydummy');
        $this->assertTrue($result);
    }

    public function testInvalidCertified()
    {
        // 간편인증
        $result = MemberHelper::isCertified();
        $this->assertFalse($result);

        $result = MemberHelper::isCertified('simple', 'incorrect');
        $this->assertFalse($result);

        $result = MemberHelper::isCertified('simple', 'incorrectincorrectincorrectincorrectincorrectincorrectincorrecti');
        $this->assertFalse($result);
    }

    public function testUnknownTypeCertified()
    {
        $result = MemberHelper::isCertified();
        $this->assertFalse($result);

        $result = MemberHelper::isCertified('unknown-type', 'b5bea41b6c623f7c09f1bf24dcae58ebab3c0cdd90ad966bc43a45b44867e12b');
        $this->assertFalse($result);
    }
}
