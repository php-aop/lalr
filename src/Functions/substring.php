<?php

namespace Aop\LALR\Functions;

/**
 * Extracts a substring of a UTF-8 string.
 *
 * @param string $str The string to extract the substring from.
 * @param int $position The position from which to start extracting.
 * @param int $length The length of the substring.
 *
 * @return string The substring.
 */
function substring(string $str, int $position, ?int $length = null): string {
    static $lengthFunc = null;

    if ($lengthFunc === null) {
        $lengthFunc = function_exists('mb_substr') ? 'mb_substr' : 'iconv_substr';
    }

    if ($length === null) {
        $length = utf8_strlen($str);
    }

    return $lengthFunc($str, $position, $length, 'UTF-8');
}