<?php

namespace Aop\LALR\Contract;

/**
 * Represents operator in a context-free grammar.
 */
interface OperatorInterface
{
    /**
     * Get operator name/symbol.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get operator associativity.
     *
     * @return int
     */
    public function getAssociativity(): int;

    /**
     * Get operator precedence.
     *
     * @return int
     */
    public function getPrecedence(): int;
}
