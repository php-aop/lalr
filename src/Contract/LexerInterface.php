<?php

declare(strict_types=1);

namespace Aop\LALR\Contract;

/**
 * A lexer takes an input string and processes it into a token stream.
 */
interface LexerInterface
{
    /**
     * Lexes the given string, returning a token stream.
     *
     * @param string $string The string to lex.
     *
     * @throws \Aop\LALR\Exception\RecognitionException When unable to extract more tokens from the string.
     *
     * @return \Aop\LALR\Contract\TokenStreamInterface The resulting token stream.
     */
    public function lex(string $string): TokenStreamInterface;
}
