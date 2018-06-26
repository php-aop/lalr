<?php

namespace Aop\LALR\Lexer;

interface TokenStreamInterface extends \IteratorAggregate, \Countable
{
    /**
     * Returns the current position in the stream.
     *
     * @return int The current position in the stream.
     */
    public function getPosition(): int;

    /**
     * Retrieves the current token.
     *
     * @return \Aop\LALR\Lexer\TokenInterface The current token.
     */
    public function getCurrentToken(): TokenInterface;

    /**
     * Returns a look-ahead token. Negative values are allowed
     * and serve as look-behind.
     *
     * @param int $n The look-ahead.
     *
     * @throws \Aop\LALR\Exception\OutOfBoundsException If current position + $n is out of range.
     *
     * @return \Aop\LALR\Lexer\TokenInterface The lookahead token.
     */
    public function lookAhead(int $n): TokenInterface;

    /**
     * Returns the token at absolute position $n.
     *
     * @param int $n The position.
     *
     * @throws \Aop\LALR\Exception\OutOfBoundsException If $n is out of range.
     *
     * @return \Aop\LALR\Lexer\TokenInterface The token at position $n.
     */
    public function get(int $n): TokenInterface;

    /**
     * Moves the cursor to the absolute position $n.
     *
     * @param int $n The position.
     *
     * @throws \Aop\LALR\Exception\OutOfBoundsException If $n is out of range.
     */
    public function move(int $n): void;

    /**
     * Moves the cursor by $n, relative to the current position.
     *
     * @param int $n The seek.
     *
     * @throws \Aop\LALR\Exception\OutOfBoundsException If current position + $n is out of range.
     */
    public function seek(int $n): void;

    /**
     * Moves the cursor to the next token.
     *
     * @throws \Aop\LALR\Exception\OutOfBoundsException If at the end of the stream.
     */
    public function next(): void;
}
