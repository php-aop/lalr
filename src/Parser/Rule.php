<?php

declare(strict_types=1);

namespace Aop\LALR\Parser;

/**
 * Represents a rule in a context-free grammar.
 */
final class Rule
{
    /**
     * @var int
     */
    private $number;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $components;

    /**
     * @var callable|null
     */
    private $callback;

    /**
     * @var int|null
     */
    private $precedence;

    /**
     * Constructor.
     *
     * @param int $number          The number of the rule in the grammar.
     * @param string $name         The name (lhs) of the rule ("A" in "A -> a b c")
     * @param string[] $components The components of this rule.
     */
    public function __construct(int $number, string $name, array $components)
    {
        $this->number     = $number;
        $this->name       = $name;
        $this->components = $components;
    }

    /**
     * Returns the number of this rule.
     *
     * @return int The number of this rule.
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * Returns the name of this rule.
     *
     * @return string The name of this rule.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the components of this rule.
     *
     * @return string[] The components of this rule.
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * Returns a component at index $index or null
     * if index is out of range.
     *
     * @param int $index The index.
     *
     * @return string|null The component at index $index.
     */
    public function getComponent($index): ?string
    {
        if (!isset($this->components[$index])) {
            return null;
        }

        return $this->components[$index];
    }

    /**
     * Sets the callback (the semantic value) of the rule.
     *
     * @param callable $callback The callback.
     */
    public function setCallback(callable $callback): void
    {
        $this->callback = $callback;
    }

    /**
     * Gets the callback (the semantic value) of the rule.
     */
    public function getCallback(): ?callable
    {
        return $this->callback;
    }

    /**
     * Get rule precedence.
     *
     * @return int|null
     */
    public function getPrecedence(): ?int
    {
        return $this->precedence;
    }

    /**
     * Set rule precedence.
     *
     * @param int|null $precedence Precedence.
     */
    public function setPrecedence(?int $precedence): void
    {
        $this->precedence = $precedence;
    }
}
