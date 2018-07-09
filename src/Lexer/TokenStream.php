<?php

declare(strict_types=1);

namespace Aop\LALR\Lexer;

use Aop\LALR\Exception\OutOfBoundsException;
use Aop\LALR\Contract\TokenInterface;
use Aop\LALR\Contract\TokenStreamInterface;

/**
 * Default token stream implementation using array as default data structure.
 */
final class TokenStream implements TokenStreamInterface
{
    /**
     * @var \Aop\LALR\Lexer\Token[]
     */
    private $tokens;

    /**
     * @var int
     */
    private $position;

    /**
     * Constructor.
     *
     * @param \Aop\LALR\Lexer\Token[] $tokens The tokens in this stream.
     */
    public function __construct(array $tokens)
    {
        $this->tokens   = $tokens;
        $this->position = 0;
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
    public function look(int $offset): TokenInterface
    {
        if (isset($this->tokens[$this->position + $offset])) {
            return $this->tokens[$this->position + $offset];
        }

        throw new OutOfBoundsException(\sprintf('Invalid look-ahead position "%d", there are only "%d" tokens in stream.', $offset, \count($this->tokens)));
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $position): TokenInterface
    {
        if (isset($this->tokens[$position])) {
            return $this->tokens[$position];
        }

        throw new OutOfBoundsException(\sprintf('Invalid index "%d", there are only "%d" tokens in stream.', $position, \count($this->tokens)));

    }

    /**
     * {@inheritdoc}
     */
    public function move(int $position): void
    {
        if (!isset($this->tokens[$position])) {
            throw new OutOfBoundsException(\sprintf('Invalid index "%d" to move on, there are only "%d" tokens in stream.', $position, \count($this->tokens)));
        }

        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function seek(int $offset): void
    {
        if (!isset($this->tokens[$this->position + $offset])) {
            throw new OutOfBoundsException(\sprintf('Invalid index "%d" to seek, there are only "%d" tokens in stream.', $this->position + $offset, \count($this->tokens)));
        }

        $this->position += $offset;
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        if (!isset($this->tokens[$this->position + 1])) {
            throw new OutOfBoundsException(\sprintf('Attempting to move beyond the end of the stream, there are only "%d" tokens in stream.', \count($this->tokens)));
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
        return \count($this->tokens);
    }
}