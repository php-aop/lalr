<?php

declare(strict_types=1);

namespace Aop\LALR\Contract;

/**
 * The parser interface.
 */
interface ParserInterface
{
    /**
     * Parses a token stream and returns the semantical value
     * of the input.
     *
     * @param \Aop\LALR\Contract\TokenStreamInterface $stream The token stream.
     *
     * @return mixed The semantical value of the input.
     */
    public function parse(TokenStreamInterface $stream);
}