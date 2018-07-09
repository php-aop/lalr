<?php

declare(strict_types=1);

namespace Aop\LALR\Contract;

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
     * @return \Aop\LALR\Contract\TokenInterface The current token.
     */
    public function getCurrentToken(): TokenInterface;

    /**
     * Returns a look-ahead or look-behind token (if negative offset is given).
     *
     * @param int $offset The look-ahead.
     *
     * @throws \Aop\LALR\Exception\OutOfBoundsException If current position + $offset is out of range.
     *
     * @return \Aop\LALR\Contract\TokenInterface The lookahead token.
     */
    public function look(int $offset): TokenInterface;

    /**
     * Returns the token at absolute position $n.
     *
     * @param int $position The position.
     *
     * @throws \Aop\LALR\Exception\OutOfBoundsException If $position is out of range.
     *
     * @return \Aop\LALR\Contract\TokenInterface The token at position $position.
     */
    public function get(int $position): TokenInterface;

    /**
     * Moves the cursor to the absolute position $position.
     *
     * @param int $position The position.
     *
     * @throws \Aop\LALR\Exception\OutOfBoundsException If $position is out of range.
     */
    public function move(int $position): void;

    /**
     * Moves the cursor by $offset, relative to the current position.
     *
     * @param int $offset The seek.
     *
     * @throws \Aop\LALR\Exception\OutOfBoundsException If current position + $offset is out of range.
     */
    public function seek(int $offset): void;

    /**
     * Moves the cursor to the next token.
     *
     * @throws \Aop\LALR\Exception\OutOfBoundsException If at the end of the stream.
     */
    public function next(): void;
}
