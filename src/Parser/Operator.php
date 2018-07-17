<?php

declare(strict_types=1);

namespace Aop\LALR\Parser;

use Aop\LALR\Contract\OperatorInterface;

/**
 * Default implementation of operator in context-free gramar.
 */
final class Operator implements OperatorInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $associativity;

    /**
     * @var int
     */
    private $precedence;

    public function __construct(string $name, int $associativity, int $precedence)
    {
        $this->name          = $name;
        $this->associativity = $associativity;
        $this->precedence    = $precedence;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociativity(): int
    {
        return $this->associativity;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrecedence(): int
    {
        return $this->precedence;
    }

    /**
     * Set operator associativity.
     *
     * @param int $associativity
     */
    public function setAssociativity(int $associativity): void
    {
        $this->associativity = $associativity;
    }

    /**
     * Set operator precedence.
     *
     * @param int|null $precedence Precedence.
     */
    public function setPrecedence(?int $precedence): void
    {
        $this->precedence = $precedence;
    }
}