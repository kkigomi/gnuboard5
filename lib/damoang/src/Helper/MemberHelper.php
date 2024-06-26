<?php declare(strict_types=1);

namespace Damoang\Lib\Helper;

class MemberHelper
{
    public static function cleanId(string $id): string
    {
        return substr(preg_replace('/[^a-z0-9_]/i', '', trim($id)), 0, 20);
    }

    public static function loggedUser(): ?string
    {
        return $_SESSION['ss_mb_id'] ?? null;
    }

    public static function isCertified(string $type = null, string $dupinfo = null): bool
    {
        $type = trim($type ?? '');
        $dupinfo = trim($dupinfo ?? '');

        /**
         * 간편인증:
         * Type: simple
         * dupinfo: DI. sha256 hash
         */
        if (
            $type === 'simple'
            && strlen($dupinfo) === 64
            && preg_match('/[a-f0-9]{64}/i', $dupinfo)
        ) {
            return true;
        }

        /**
         * 재외국민
         * Type: abroad
         * dypinfo: 없음. 체크하지 않음
         */
        if (
            $type === 'abroad'
        ) {
            return true;
        }

        return false;
    }
}
