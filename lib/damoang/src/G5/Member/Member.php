<?php
declare(strict_types=1);

namespace Damoang\Lib\G5\Member;

use Damoang\Lib\Helper\MemberHelper;

/**
 * @implements \ArrayAccess<mixed,mixed>
 */
class Member implements \ArrayAccess
{
    /** @var string */
    private $memberId = '';

    /** @var mixed[] */
    private $defaults = ['mb_id' => '', 'mb_level' => 1, 'mb_name' => '', 'mb_nick' => '', 'mb_point' => 0, 'mb_certify' => '', 'mb_dupinfo' => '', 'mb_email' => '', 'mb_open' => '', 'mb_homepage' => '', 'mb_tel' => '', 'mb_hp' => '', 'mb_zip1' => '', 'mb_zip2' => '', 'mb_addr1' => '', 'mb_addr2' => '', 'mb_addr3' => '', 'mb_addr_jibeon' => '', 'mb_signature' => '', 'mb_profile' => '', 'mb_1' => '', 'mb_2' => '', 'mb_3' => '', 'mb_4' => '', 'mb_5' => '', 'mb_6' => '', 'mb_7' => '', 'mb_8' => '', 'mb_9' => '', 'mb_10' => ''];

    // private $required = ['mb_id', 'mb_level', 'mb_certify', 'mb_dupinfo', 'mb_nick'];

    /** @var mixed[] */
    private $data;

    /**
     * @param ?mixed[] $data
     */
    function __construct($data = [])
    {
        if (!is_array($data)) {
            $data = [];
        }

        $memberId = $data['mb_id'] ?? '';
        $this->memberId = MemberHelper::cleanId($memberId);
        $this->setData(array_merge($this->defaults, $data));
        $this->data['mb_id'] = $this->memberId;
        $this->data['mb_nick'] = trim($this->data['mb_nick']);
        $this->data['mb_level'] = intval($this->data['mb_level'] ?? 1);
        $this->data['mb_point'] = intval($this->data['mb_point'] ?? 0);

        // 아이디가 없으면 비회원
        // 비회원의 레벨은 1
        if (!$this->id()) {
            $this->data['mb_level'] = 1;
        }
    }

    /**
     * 회원 아이디
     */
    public function id(): string
    {
        return $this->memberId;
    }

    public function nick(): string
    {
        return $this->data['mb_nick'];
    }

    // public function displayName(): string
    // {
    //     return $this->data['mb_nick'];
    // }

    /**
     * 레벨
     */
    public function level(): int
    {
        return $this->data['mb_level'];
    }

    /**
     * 현재 포인트
     */
    public function point(): int
    {
        return $this->data['mb_point'];
    }

    public function isMember(): bool
    {
        if ($this->id()) {
            return true;
        }

        return false;
    }

    public function isGuest(): bool
    {
        return !$this->isMember();
    }

    public function isLogged(): bool
    {
        if (!$this->id()) {
            return false;
        }

        return MemberHelper::loggedUser() === $this->id();
    }

    public function isAdmin(): bool
    {
        if (!$this->id()) {
            return false;
        }

        return $this->adminType() === 'super';
    }

    public function isSuperAdmin(): bool
    {
        return $this->isAdmin();
    }

    public function adminType(): string
    {
        if (!$this->id()) {
            return '';
        }

        return \is_admin($this->id());
    }

    public function isCertified(): bool
    {
        return MemberHelper::isCertified(trim($this->data['mb_certify']), trim($this->data['mb_dupinfo']));
    }

    /**
     * @param mixed[] $data
     */
    private function setData(array $data): void
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'mb_no':
                    $this->data[$key] = intval($value);
                    break;
                case 'mb_id':
                    $this->memberId = MemberHelper::cleanId($value);
                    $this->data[$key] = $this->memberId;
                    break;
                case 'mb_level':
                    $this->data[$key] = $this->isMember() ? intval($value) : 1;
                    break;
                case 'mb_point':
                    $this->data[$key] = $this->isMember() ? intval($value) : 0;
                    break;
                default:
                    $this->data[$key] = $value;
            }
        }
    }

    // ArrayAccess -------------------------------------------------------------
    // FIXME
    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        switch ($offset) {
            case 'mb_id':
                return $this->id();
            case 'mb_level':
                return $this->level();
            case 'mb_point':
                return $this->point();
        }

        return $this->data[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        $this->setData([$offset => $value]);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
        if ($offset === 'mb_id') {
            $this->memberId = '';
        }
    }
}
