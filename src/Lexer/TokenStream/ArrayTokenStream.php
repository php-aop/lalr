<?php

namespace Aop\LALR\Lexer\TokenStream;

use Aop\LALR\Exception\OutOfBoundsException;
use Aop\LALR\Lexer\TokenInterface;
use Aop\LALR\Lexer\TokenStreamInterface;

final class ArrayTokenStream implements TokenStreamInterface
{
    /**
     * @var \Aop\LALR\Lexer\Token[]
     */
    private $tokens;

    /**
     * @var int
     */
    private $position = 0;

    /**
     * Constructor.
     *
     * @param \Aop\LALR\Lexer\Token[] $tokens The tokens in this stream.
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentToken(): TokenInterface
    {
        return $this->tokens[$this->position];
    }

    /**
     * {@inheritdoc}
     */
    public function lookAhead(int $n): TokenInterface
    {
        if (isset($this->tokens[$this->position + $n])) {
            return $this->tokens[$this->position + $n];
        }

        throw new OutOfBoundsException('Invalid look-ahead position "%d", there are only "%d" tokens in stream.', $n, count($this->tokens));
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $n): TokenInterface
    {
        if (isset($this->tokens[$n])) {
            return $this->tokens[$n];
        }

        throw new OutOfBoundsException('Invalid index "%d", there are only "%d" tokens in stream.', $n, count($this->tokens));

    }

    /**
     * {@inheritdoc}
     */
    public function move(int $n): void
    {
        if (!isset($this->tokens[$n])) {
            throw new OutOfBoundsException('Invalid index "%d" to move on, there are only "%d" tokens in stream.', $n, count($this->tokens));
        }

        $this->position = $n;
    }

    /**
     * {@inheritdoc}
     */
    public function seek(int $n): void
    {
        if (!isset($this->tokens[$this->position + $n])) {
            throw new OutOfBoundsException('Invalid index "%d" to seek, there are only "%d" tokens in stream.', $this->position + $n, count($this->tokens));
        }

        $this->position += $n;
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        if (!isset($this->tokens[$this->position + 1])) {
            throw new OutOfBoundsException('Attempting to move beyond the end of the stream, there are only "%d" tokens in stream.', count($this->tokens));
        }

        $this->position++;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->tokens);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->tokens);
    }
}