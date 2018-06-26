<?php

namespace Aop\LALR\Parser;

use Aop\LALR\Lexer\TokenStreamInterface;

/**
 * The parser interface.
 */
interface ParserInterface
{
    /**
     * The token type that represents an EOF.
     */
    public const EOF_TOKEN_TYPE = '$eof';

    /**
     * Parses a token stream and returns the semantical value
     * of the input.
     *
     * @param \Aop\LALR\Lexer\TokenStreamInterface $stream The token stream.
     *
     * @return mixed The semantical value of the input.
     */
    public function parse(TokenStreamInterface $stream);
}