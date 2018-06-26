<?php

namespace Aop\LALR\Lexer;

/**
 * A simple token representation.
 */
final class Token implements TokenInterface
{
    /**
     * @var mixed
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
     * @param mixed $type   The type of the token.
     * @param string $value The token value.
     * @param int $line     The line.
     */
    public function __construct($type, string $value, int $line)
    {
        $this->type  = $type;
        $this->value = $value;
        $this->line  = $line;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
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
