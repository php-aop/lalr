<?php

declare(strict_types=1);

namespace Aop\LALR\Parser;

use Aop\LALR\Contract\GrammarInterface;
use Aop\LALR\Contract\OperatorInterface;
use Aop\LALR\Contract\RuleInterface;
use Aop\LALR\Exception\LogicException;
use Aop\LALR\Exception\OutOfBoundsException;

/**
 * Represents a context-free grammar.
 */
abstract class AbstractGrammar implements GrammarInterface
{
    /**
     * @var \Aop\LALR\Parser\Rule[]
     */
    protected $rules = [];

    /**
     * @var array
     */
    protected $groupedRules = [];

    /**
     * @var int
     */
    protected $nextRuleNumber = 1;

    /**
     * @var int
     */
    protected $conflictsMode = 9; // SHIFT | OPERATORS

    /**
     * @var string
     */
    protected $currentNonterminal;

    /**
     * @var \Aop\LALR\Parser\Rule
     */
    protected $currentRule;

    /**
     * @var array|\Aop\LALR\Parser\Operator[]
     */
    protected $operators = [];

    /**
     * @var array|\Aop\LALR\Parser\Operator[]
     */
    protected $currentOperators;

    /**
     * {@inheritdoc}
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function getRule(int $number): RuleInterface
    {
        if (isset($this->rules[$number])) {
            return $this->rules[$number];
        }

        throw new OutOfBoundsException(sprintf('Unable to get rule "%d", there are only "%d" rules defined.', $number, \count($this->rules)));
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupedRules(): array
    {
        return $this->groupedRules;
    }

    /**
     * {@inheritdoc}
     */
    public function getStartRule(): RuleInterface
    {
        if (!isset($this->rules[0])) {
            throw new LogicException('No start rule specified.');
        }

        return $this->rules[0];
    }

    /**
     * {@inheritdoc}
     */
    public function getConflictsMode(): int
    {
        return $this->conflictsMode;
    }

    /**
     * {@inheritdoc}
     */
    public function hasNonterminal(string $name): bool
    {
        return array_key_exists($name, $this->groupedRules);
    }

    /**
     * {@inheritdoc}
     */
    public function hasOperator(string $token): bool
    {
        return array_key_exists($token, $this->operators);
    }

    /**
     * {@inheritdoc}
     */
    public function getOperator(string $token): OperatorInterface
    {
        return $this->operators[$token];
    }

    /**
     * Define a grammar rule.
     *
     * @param string $name
     *
     * @return \Aop\LALR\Parser\AbstractGrammar
     */
    protected function define(string $name): AbstractGrammar
    {
        $this->currentNonterminal = $name;

        return $this;
    }

    /**
     * Defines an alternative for a grammar rule.
     *
     * @param string[] $components The components of the rule.
     *
     * @return \Aop\LALR\Parser\AbstractGrammar This instance.
     */
    protected function is(string ...$components): AbstractGrammar
    {
        $this->currentOperators = null;

        if ($this->currentNonterminal === null) {
            throw new LogicException('You must specify a name of the rule first.');
        }

        $num  = $this->nextRuleNumber++;
        $rule = new Rule($num, $this->currentNonterminal, $components);

        $this->rules[$num]                               = $rule;
        $this->currentRule                               = $rule;
        $this->groupedRules[$this->currentNonterminal][] = $rule;

        return $this;
    }

    /**
     * Sets the callback for the current rule.
     *
     * @param callable $callback The callback.
     *
     * @return \Aop\LALR\Parser\AbstractGrammar This instance.
     */
    protected function call(callable $callback): AbstractGrammar
    {
        if ($this->currentRule === null) {
            throw new LogicException('You must specify a rule first.');
        }

        $this->currentRule->setCallback($callback);

        return $this;
    }

    /**
     * Sets a start rule for this grammar.
     *
     * @param string The name of the start rule.
     */
    protected function start(string $name): void
    {
        $this->rules[0] = new Rule(0, self::START, [$name]);
    }

    /**
     * Sets the mode of conflict resolution.
     *
     * @param int $mode The bitmask for the mode.
     */
    protected function resolve(int $mode): void
    {
        $this->conflictsMode = $mode;
    }

    /**
     * Defines a group of operators.
     *
     * @param string[] $operators Any number of tokens that serve as the operators.
     *
     * @return \Aop\LALR\Parser\AbstractGrammar This instance for fluent interface.
     */
    protected function operators(string ...$operators): AbstractGrammar
    {
        $this->currentRule      = null;
        $this->currentOperators = $operators;

        foreach ($operators as $operator) {
            $this->operators[$operator] = new Operator($operator, 1, self::LEFT);
        }

        return $this;
    }

    /**
     * Marks the current group of operators as left-associative.
     *
     * @return \Aop\LALR\Parser\AbstractGrammar This instance for fluent interface.
     */
    protected function left(): AbstractGrammar
    {
        return $this->associativity(self::LEFT);
    }

    /**
     * Marks the current group of operators as right-associative.
     *
     * @return \Aop\LALR\Parser\AbstractGrammar This instance for fluent interface.
     */
    protected function right(): AbstractGrammar
    {
        return $this->associativity(self::RIGHT);
    }

    /**
     * Marks the current group of operators as non-associative.
     *
     * @return \Aop\LALR\Parser\AbstractGrammar This instance for fluent interface.
     */
    protected function nonassociative(): AbstractGrammar
    {
        return $this->associativity(self::NONASSOCIATIVE);
    }

    /**
     * Explicitly sets the associativity of the current group of operators.
     *
     * @param int $flag One of AbstractGrammar::LEFT, AbstractGrammar::RIGHT, AbstractGrammar::NONASSOCIATIVE
     *
     * @return \Aop\LALR\Parser\AbstractGrammar This instance for fluent interface.
     */
    protected function associativity(int $flag): AbstractGrammar
    {
        if (!$this->currentOperators) {
            throw new LogicException('Define a group of operators first.');
        }

        foreach ($this->currentOperators as $operator) {
            $this->operators[$operator]->setAssociativity($flag);
        }

        return $this;
    }

    /**
     * Sets the precedence (as an integer) of the current group of operators.
     * If no group of operators is being specified, sets the precedence
     * of the currently described rule.
     *
     * @param int $precedence The precedence as an integer.
     *
     * @return \Aop\LALR\Parser\AbstractGrammar This instance for fluent interface.
     */
    protected function precedence(int $precedence): AbstractGrammar
    {
        if (!$this->currentOperators) {

            if (!$this->currentRule) {
                throw new LogicException('Define a group of operators or a rule first.');
            }

            $this->currentRule->setPrecedence($precedence);

            return $this;
        }

        foreach ($this->currentOperators as $operator) {
            $this->operators[$operator]->setPrecedence($precedence);
        }

        return $this;
    }
}
