<?php

declare(strict_types=1);

use Symfony\Component\VarDumper\VarDumper;

if (!function_exists('dump')) {
    /**
     * @author Nicolas Grekas <p@tchwork.com>
     * @param mixed $var
     * @param mixed ...$moreVars
     * @return mixed
     */
    function dump($var, ...$moreVars)
    {
        global $member;

        if (
            ($_ENV['APP_ENV'] ?? 'prod') !== 'prod'
            && (
                $member->isSuperAdmin()
                || ($_ENV['CAUTION_VARDUMPER_FORCE_ENABLE'] ?? 'false') === 'true'
            )
        ) {
            VarDumper::dump($var);

            foreach ($moreVars as $v) {
                VarDumper::dump($v);
            }
        }

        if (1 < func_num_args()) {
            return func_get_args();
        }

        return $var;
    }
}

if (!function_exists('dd')) {
    /**
     * @author Nicolas Grekas <p@tchwork.com>
     * @param mixed ...$vars
     * @return never|void
     */
    function dd(...$vars)
    {
        global $member;

        if (
            ($_ENV['APP_ENV'] ?? 'prod') === 'prod'
            || (
                !$member->isSuperAdmin()
                && ($_ENV['CAUTION_VARDUMPER_FORCE_ENABLE'] ?? 'false') !== 'true'
            )
        ) {
            return;
        }

        if (!in_array(\PHP_SAPI, ['cli', 'phpdbg'], true) && !headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
        }

        foreach ($vars as $v) {
            VarDumper::dump($v);
        }

        exit(1);
    }
}
