<?php

declare(strict_types=1);

namespace Aop\LALR\Contract;

/**
 * A common contract for tokens.
 */
interface TokenInterface
{
    /**
     * Returns the token type.
     *
     * @return string The token type.
     */
    public function getType(): string;

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
