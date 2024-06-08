<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

class G5_NARIYA_ANONYMITY {

    // Hook 포함 클래스 작성 요령
    // https://github.com/Josantonius/PHP-Hook/blob/master/tests/Example.php
    /**
     * Class instance.
     */

    public static function getInstance() {
        static $instance = null;
        if (null === $instance) {
            $instance = new self();
        }

        return $instance;
    }

    public static function singletonMethod() {
        return self::getInstance();
    }

    public function __construct() {

		$this->add_hooks();
    }

	public function add_hooks() {

		// 글수정
		add_event('bbs_write', array($this, 'bbs_write'), 1, 3);

	}

	// 글 수정시 회원아이디 치환
	public function bbs_write($board, $wr_id, $w=''){
		global $write, $member;

		if($w == 'u') {
			if($write['wr_8'] && $write['wr_8'] === $member['mb_id']) {
				$write['mb_id'] = $write['wr_8'];
			}
		}
	}
}

$GLOBALS['g5_nariya_anonymity'] = G5_NARIYA_ANONYMITY::getInstance();