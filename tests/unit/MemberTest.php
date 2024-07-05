<?php
use Damoang\Lib\G5\Member\Member;

class MemberTest extends \Codeception\Test\Unit
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

    public function testArrayAccess()
    {
        $memberId = 'member_1';
        $member = new Member([]);

        $member['mb_id'] = $memberId;
        $this->assertSame($memberId, $member->id());
        $this->assertSame($memberId, $member['mb_id']);

        $member['mb_id'] = $memberId;
        $member['mb_point'] = '200';
        $member['mb_level'] = '5';
        $member['unknown'] = 'value';
        $this->assertSame($memberId, $member->id());
        $this->assertSame(200, $member['mb_point']);
        $this->assertSame(200, $member->point());
        $this->assertSame(5, $member['mb_level']);
        $this->assertSame(5, $member->level());

        $this->assertArrayHasKey('unknown', $member);
        $this->assertSame('value', $member['unknown']);
        unset($member['unknown']);
        $this->assertArrayNotHasKey('unknown', $member);
    }

    public function testId()
    {
        $member = new Member([]);
        $this->assertSame(null, $member->id());
        $this->assertSame('', $member['mb_id']);

        $member = new Member(['mb_id' => '']);
        $this->assertSame(null, $member->id());
        $this->assertSame('', $member['mb_id']);

        $memberId = 'member_1';
        $member = new Member(['mb_id' => 'member_1']);
        $this->assertSame($memberId, $member->id());
        $this->assertSame($memberId, $member['mb_id']);

        $member = new Member(['mb_id' => '   memb가나다er   !@#$%^&*()-=_1  ']);
        $this->assertSame($memberId, $member->id());
        $this->assertSame($memberId, $member['mb_id']);
    }

    public function testIsLogged()
    {
        $id = 'memberid';
        $member = new Member([]);

        $_SESSION['ss_mb_id'] = '';
        $this->assertFalse($member->isLogged());

        $_SESSION['ss_mb_id'] = $id;
        $this->assertFalse($member->isLogged());

        $member['mb_id'] = $id;
        $this->assertTrue($member->isLogged());
    }

    public function testLevel()
    {
        $member = new Member([]);
        $this->assertSame(1, $member->level());
        $this->assertSame(1, $member['mb_level']);

        $member = new Member(['mb_id' => 'memberid', 'mb_level' => '2']);
        $this->assertSame(2, $member->level());
        $this->assertSame(2, $member['mb_level']);

        $member['mb_level'] = '3';
        $this->assertSame(3, $member->level());
        $this->assertSame(3, $member['mb_level']);
    }

    public function testGuestLevel()
    {
        $member = new Member([]);
        $this->assertSame(1, $member->level());
        $this->assertSame(1, $member['mb_level']);

        $member = new Member(['mb_level' => '2']);
        $this->assertSame(1, $member->level());
        $this->assertSame(1, $member['mb_level']);

        $member['mb_level'] = '3';
        $this->assertSame(1, $member->level());
        $this->assertSame(1, $member['mb_level']);
    }

    public function testUserType()
    {
        $member = new Member([]);
        $this->assertTrue($member->isGuest());
        $this->assertFalse($member->isMember());

        $member = new Member(['mb_id' => 'memberid']);
        $this->assertTrue($member->isMember());
        $this->assertFalse($member->isGuest());
    }

    public function testPoint()
    {
        $member = new Member([]);
        $this->assertSame(0, $member->point());
        $this->assertSame(0, $member['mb_point']);

        $member['mb_point'] = 100;
        $this->assertSame(0, $member->point());
        $this->assertSame(0, $member['mb_point']);

        $member = new Member(['mb_id' => 'memberid', 'mb_point' => 100]);
        $this->assertSame(100, $member->point());
        $this->assertSame(100, $member['mb_point']);

        $member['mb_point'] = '200';
        $this->assertSame(200, $member->point());
        $this->assertSame(200, $member['mb_point']);
    }

    public function testAdminType()
    {
        $member = new Member([]);
        $this->assertEquals('', $member->adminType());

        $id = 'memberid';
        $member['mb_id'] = $id;

        $GLOBALS['board'] = $GLOBALS['board'] ?? [];
        $GLOBALS['board']['bo_admin'] = $id;
        $this->assertEquals('board', $member->adminType());

        $GLOBALS['group'] = $GLOBALS['group'] ?? [];
        $GLOBALS['group']['gr_admin'] = $id;
        $this->assertEquals('group', $member->adminType());

        $GLOBALS['config'] = $GLOBALS['config'] ?? [];
        $GLOBALS['config']['cf_admin'] = $id;
        $this->assertEquals('super', $member->adminType());
        $this->assertTrue($member->isAdmin());
        $this->assertTrue($member->isSuperAdmin());

        $member['mb_id'] = '';
        $this->assertFalse($member->isAdmin());
        $this->assertFalse($member->isSuperAdmin());
    }

    public function testNickName()
    {
        $member = new Member(['mb_nick' => 'member_nick']);
        $this->assertEquals('member_nick', $member->nick());
    }

    public function testIsAuthor()
    {
        $memberId = 'member1';
        $member = new Member(['mb_id' => $memberId]);
        $this->assertTrue($member->isAuthor($memberId));
        $this->assertFalse($member->isAuthor(''));
        $this->assertFalse($member->isAuthor(null));
        $this->assertFalse($member->isAuthor(0));
        $this->assertFalse($member->isAuthor(1));

        $memberId = '';
        $member = new Member(['mb_id' => $memberId]);
        $this->assertFalse($member->isAuthor($memberId));
        $this->assertFalse($member->isAuthor(''));
        $this->assertFalse($member->isAuthor(null));
        $this->assertFalse($member->isAuthor(0));
        $this->assertFalse($member->isAuthor(1));

        $member = new Member([]);
        $this->assertFalse($member->isAuthor($memberId));
        $this->assertFalse($member->isAuthor(''));
        $this->assertFalse($member->isAuthor(null));
        $this->assertFalse($member->isAuthor(0));
        $this->assertFalse($member->isAuthor(1));
    }

    public function testIsCertified()
    {
        $member = new Member([
            'mb_id' => 'memberid',
            'mb_certify' => 'simple',
            'mb_dupinfo' => 'b5bea41b6c623f7c09f1bf24dcae58ebab3c0cdd90ad966bc43a45b44867e12b',
        ]);
        $this->assertTrue($member->isCertified());
    }
}
