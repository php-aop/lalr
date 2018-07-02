<?php

namespace Aop\LALR\Functions;

/**
 * Determines length of a UTF-8 string.
 *
 * @param string $str The string in UTF-8 encoding.
 *
 * @return int The length.
 */
function utf8_strlen(string $str): int
{
    return \strlen(\utf8_decode($str));
}
