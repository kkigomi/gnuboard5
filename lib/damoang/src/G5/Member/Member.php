<?php
declare(strict_types=1);

namespace Damoang\Lib\G5\Member;

use Damoang\Lib\G5\G5CommonObject;
use Damoang\Lib\Helper\MemberHelper;

class Member extends G5CommonObject
{
    /** @var ?string */
    protected $memberId = null;

    /** @var mixed[] */
    protected $defaults = ['mb_id' => '', 'mb_level' => 1, 'mb_name' => '', 'mb_nick' => '', 'mb_point' => 0, 'mb_certify' => '', 'mb_dupinfo' => '', 'mb_email' => '', 'mb_open' => '', 'mb_homepage' => '', 'mb_tel' => '', 'mb_hp' => '', 'mb_zip1' => '', 'mb_zip2' => '', 'mb_addr1' => '', 'mb_addr2' => '', 'mb_addr3' => '', 'mb_addr_jibeon' => '', 'mb_signature' => '', 'mb_profile' => '', 'mb_1' => '', 'mb_2' => '', 'mb_3' => '', 'mb_4' => '', 'mb_5' => '', 'mb_6' => '', 'mb_7' => '', 'mb_8' => '', 'mb_9' => '', 'mb_10' => ''];

    protected $casts = [
        'mb_level' => 'int',
        'mb_point' => 'int',
    ];

    /**
     * @param ?mixed[] $data
     */
    function __construct($data = [])
    {
        $data['mb_id'] = MemberHelper::cleanId($data['mb_id'] ?? '');
        parent::__construct($data);

        // 아이디가 없으면 비회원. 비회원의 레벨은 1
        if (!$this->id()) {
            $this->data['mb_level'] = 1;
        }
    }

    /**
     * 회원 아이디
     */
    public function id(): ?string
    {
        return !empty($this->data['mb_id']) ? $this->data['mb_id'] : null;
    }

    public function isAuthor(?string $authorId): bool
    {
        if (!$this->id() || !$authorId) {
            return false;
        }

        return $this->id() === $authorId;
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

    /**
     * Alias of Member::isAdmin()
     */
    public function isSuperAdmin(): bool
    {
        return $this->isAdmin();
    }

    public function adminType(): ?string
    {
        if (!$this->id()) {
            return null;
        }

        return \is_admin($this->id());
    }

    public function isCertified(): bool
    {
        return MemberHelper::isCertified(
            trim($this->data['mb_certify']),
            trim($this->data['mb_dupinfo'])
        );
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        switch ($offset) {
            case 'mb_level':
                if (!$this->isMember()) {
                    $value = 1;
                }
                break;
            case 'mb_point':
                if (!$this->isMember()) {
                    $value = 0;
                }
                break;
        }

        parent::setAttr($offset, $value);
    }
}
