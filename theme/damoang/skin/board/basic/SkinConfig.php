<?php
declare(strict_types=1);

namespace Damoang\Theme\Damoang\Skin\Board\Basic;

use Damoang\Lib\G5\Member\Member;

/**
 * @implements \ArrayAccess<mixed,mixed>
 */
class SkinConfig extends \Damoang\Lib\G5\Board\SkinConfig
{
    /**
     * @inheritDoc
     */
    protected $defaults = [
        // 세부 스킨 지정
        'category_skin' => 'basic',
        'list_skin' => 'list',
        'view_skin' => 'basic',
        'comment_skin' => 'basic',

        'bo_admin' => '',
        'check_list_hide_profile' => '0',

        // bo_write_allow_one
        // bo_write_allow_three
        // category_move_message
        // category_move_message;
        // category_move_permit
        // check_category_move
        // check_member_only
        // check_only_permit
        // check_star_rating
        // check_write_permit
        // code
        // comment_convert
        // comment_good
        // comment_image_limit
        // comment_image_size
        // comment_nogood
        // comment_rows
        // comment_sort
        // editor_mo
        // lucky_dice
        // lucky_point
        // mb_db
        // mbs_downlaod
        // mbs_list
        // mbs_view
        // mbs_write
        // member_only_permit
        // noti_mb
        // noti_no
        // post_convert
        // save_image
        // tag
        // video_attach
        // video_auto
        // video_link
        // xp_comment
        // xp_write
    ];

    /**
     * @param ?mixed[] $data
     */
    function __construct($data = [])
    {
        parent::__construct($data);
    }

    public function isHideAuthorProfile(): bool
    {
        return $this->data['check_list_hide_profile'] === '1';
    }

    /**
     * 작성자 닉네임 등 정보를 표시해도 되는지 확인
     *
     * 작성자 정보를 감춰야 한다면 `false` 반환
     */
    public function isProfileRenderable(Member $member = null): bool
    {
        if (!$member) {
            $member = $GLOBALS['member'];
        }

        // 익명 기능
        if (!$this->isHideAuthorProfile()) {
            return true;
        }

        // 최고관리자는 허용
        if ($member->isSuperAdmin()) {
            return true;
        }

        return false;
    }
}
