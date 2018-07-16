<?php

declare(strict_types=1);

namespace Aop\LALR\Contract;

/**
 * Represents a rule in a context-free grammar.
 */
interface RuleInterface
{
    /**
     * Returns the number of this rule.
     *
     * @return int The number of this rule.
     */
    public function getNumber(): int;

    /**
     * Returns the name of this rule.
     *
     * @return string The name of this rule.
     */
    public function getName(): string;

    /**
     * Returns the components of this rule.
     *
     * @return string[] The components of this rule.
     */
    public function getComponents(): array;

    /**
     * Returns a component at index $index or null
     * if index is out of range.
     *
     * @param int $index The index.
     *
     * @return string|null The component at index $index.
     */
    public function getComponent(int $index): ?string;

    /**
     * Gets the callback (the semantic value) of the rule.
     */
    public function getCallback(): ?callable;

    /**
     * Get rule precedence.
     *
     * @return int|null
     */
    public function getPrecedence(): ?int;
}
