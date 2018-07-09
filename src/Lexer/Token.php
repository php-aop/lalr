<?php

declare(strict_types=1);

namespace Aop\LALR\Lexer;

use Aop\LALR\Contract\TokenInterface;

/**
 * A simple token representation.
 */
final class Token implements TokenInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $line;

    /**
     * Constructor.
     *
     * @param string $type  The type of the token.
     * @param string $value The token value.
     * @param int $line     The line.
     */
    public function __construct(string $type, string $value, int $line)
    {
        $this->type  = $type;
        $this->value = $value;
        $this->line  = $line;
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public function getLine(): int
    {
        return $this->line;
    }
}
