<?php

namespace Aop\LALR\Lexer;

interface RecognizerInterface
{
    /**
     * Returns a boolean value specifying whether
     * the string matches or not and if it does,
     * returns the match in the second variable.
     *
     * @param string $string The string to match.
     * @param string $result The variable that gets set to the value of the match.
     *
     * @return boolean Whether the match was successful or not.
     */
    public function match(string $string, string &$result): bool;
}