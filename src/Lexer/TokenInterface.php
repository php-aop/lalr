<?php

namespace Aop\LALR\Lexer;

/**
 * A common contract for tokens.
 */
interface TokenInterface
{
    /**
     * Returns the token type.
     *
     * @return mixed The token type.
     */
    public function getType();

    /**
     * Returns the token value.
     *
     * @return string The token value.
     */
    public function getValue(): string;

    /**
     * Returns the line on which the token was found.
     *
     * @return int The line.
     */
    public function getLine(): int;
}
