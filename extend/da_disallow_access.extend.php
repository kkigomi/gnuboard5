<?php declare(strict_types=1);

if (!defined('_GNUBOARD_')) {
    http_response_code(404);
    exit;
}

/**
 * 관계자 외 출입금지!
 * 
 * 환경변수로 접근 제한이 설정되면, 허용된 계정 외에는 접속을 거부.
 * 
 * - CAUTION_DISALLOW_ACCESS: 접근 제한을 활성화
 *   - `prod` 환경에서는 이 설정이 무시됨
 * - CAUTION_ALLOW_ACCESS_MEMBER: 접근을 허용하는 계정 아이디
 *   - `,`로 구분된 회원 ID 목록
 * - CAUTION_ALLOW_DEBUG_MEMBER: 접근 허용자 중 디버그 모드 활성 대상
 *   - `,`로 구분된 회원 ID 목록
 *   - 접근이 허용된 대상만 지정할 수 있음
 * 
 * 예시
 * - 접근 제한 활성화
 *   ```
 *   CAUTION_DISALLOW_ACCESS=true
 *   ```
 * - 엑세스 허용 대상 지정
 *   ```
 *   CAUTION_ALLOW_ACCESS_MEMBER="admin:sdk, google_9a630969:kkigomi, google_98bf0920:nstd"
 *   ```
 * - 디버그 활성화 대상 지정
 *   - `CAUTION_ALLOW_ACCESS_MEMBER`와 같은 포맷
 */

if (
    // `prod` 환경에서는 동작하지 않음
    ($_ENV['APP_ENV'] ?? 'prod') === 'prod'
    || ($_ENV['CAUTION_DISALLOW_ACCESS'] ?? 'false') !== 'true'
) {
    return;
}

// 비회원 제한
if ($member->isGuest()) {
    http_response_code(404);
    exit;
}

(function () {
    /**
     * @var string[] $allowIdList 접근 허용 대상
     */
    $allowIdList = explode(',', ($_ENV['CAUTION_ALLOW_ACCESS_MEMBER'] ?? 'admin'));
    $allowIdList = array_reduce($allowIdList, function ($carry = [], $item) {
        if (!$item) {
            return $carry;
        }

        $id = trim(strstr($item, ':', true) ?: $item);

        $carry[] = $id;
        return $carry;
    }, []);

    // 허용 아이디 목록에 없으면 접근 제한
    if (!in_array($GLOBALS['member']->id(), $allowIdList, true)) {
        http_response_code(404);
        exit;
    }

    // 디버그 모드 활성 대상 ---------------------------------------------------
    /**
     * @var string[] $allowDebugIdList 디버그 모드 활성화 대상
     */
    $allowDebugIdList = explode(',', ($_ENV['CAUTION_ALLOW_DEBUG_MEMBER'] ?? ''));
    $allowDebugIdList = array_reduce($allowDebugIdList, function ($carry, $item) use ($allowIdList) {
        if (!$item) {
            return $carry;
        }

        $id = trim(strstr($item, ':', true) ?: $item);

        if (!in_array($id, $allowIdList, true)) {
            return $carry;
        }

        $carry[] = $id;
        return $carry;
    }, []);

    add_replace('get_permission_debug_show', function ($bool = false, $member = []) use ($allowDebugIdList) {
        if (in_array(($member['mb_id'] ?? false), $allowDebugIdList, true)) {
            return true;
        }
        return $bool;
    }, 1, 2);
})();
