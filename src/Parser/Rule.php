<?php

declare(strict_types=1);

namespace Aop\LALR\Parser;

use Aop\LALR\Contract\RuleInterface;

/**
 * Default implementation of \Aop\LALR\Contract\RuleInterface
 */
final class Rule implements RuleInterface
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
     * {@inheritdoc}
     */
    public function getNumber(): int
    {
        return $this->number;
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
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * {@inheritdoc}
     */
    public function getComponent(int $index): ?string
    {
        if (!isset($this->components[$index])) {
            return null;
        }

        return $this->components[$index];
    }

    /**
     * {@inheritdoc}
     */
    public function getCallback(): ?callable
    {
        return $this->callback;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrecedence(): ?int
    {
        return $this->precedence;
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
     * Set rule precedence.
     *
     * @param int|null $precedence Precedence.
     */
    public function setPrecedence(?int $precedence): void
    {
        $this->precedence = $precedence;
    }
}
