<?php

declare(strict_types=1);

namespace Aop\LALR\Parser;

final class Operator
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
     * Get operator name/symbol.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get operator associativity.
     *
     * @return int
     */
    public function getAssociativity(): int
    {
        return $this->associativity;
    }

    /**
     * Get operator precedence.
     *
     * @return int
     */
    public function getPrecedence(): int
    {
        return $this->precedence;
    }

    /**
     * Set operator associativity.
     *
     * @param int $associativity
     *
     * @return \Aop\LALR\Parser\Operator Fluent interface.
     */
    public function setAssociativity(int $associativity): Operator
    {
        $this->associativity = $associativity;

        return $this;
    }

    /**
     * Set operator precedence.
     *
     * @param int|null $precedence Precedence.
     *
     * @return \Aop\LALR\Parser\Operator Fluent interface.
     */
    public function setPrecedence(?int $precedence): Operator
    {
        $this->precedence = $precedence;

        return $this;
    }
}