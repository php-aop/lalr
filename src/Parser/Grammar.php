<?php

namespace Aop\LALR\Parser;

use Aop\LALR\Exception\LogicException;
use Aop\LALR\Exception\OutOfBoundsException;

/**
 * Represents a context-free grammar.
 */
class Grammar
{
    /**
     * The name given to the rule the grammar is augmented with
     * when start() is called.
     */
    public const START_RULE_NAME = '$start';

    /**
     * The epsilon symbol signifies an empty production.
     */
    public const EPSILON = '$epsilon';

    /**
     * Signifies that the parser should not resolve any
     * grammar conflicts.
     */
    public const NONE = 0;

    /**
     * Signifies that the parser should resolve
     * shift/reduce conflicts by always shifting.
     */
    public const SHIFT = 1;

    /**
     * Signifies that the parser should resolve
     * reduce/reduce conflicts by reducing with
     * the longer rule.
     */
    public const LONGER_REDUCE = 2;

    /**
     * Signifies that the parser should resolve
     * reduce/reduce conflicts by reducing
     * with the rule that was given earlier in
     * the grammar.
     */
    public const EARLIER_REDUCE = 4;

    /**
     * Signifies that the conflicts should be
     * resolved by taking operator precendence
     * into account.
     */
    public const OPERATORS = 8;

    /**
     * Signifies that the parser should automatically
     * resolve all grammar conflicts.
     */
    public const ALL = 15;

    /**
     * Left operator associativity.
     */
    public const LEFT = 0;

    /**
     * Right operator associativity.
     */
    public const RIGHT = 1;

    /**
     * The operator is nonassociative.
     */
    public const NONASSOC = 2;

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
     * @var array
     */
    protected $operators = [];

    /**
     * @var array
     */
    protected $currentOperators;

    public function __invoke($nonterminal)
    {
        $this->currentNonterminal = $nonterminal;

        return $this;
    }

    /**
     * Defines an alternative for a grammar rule.
     *
     * @param string[] $components The components of the rule.
     *
     * @return \Aop\LALR\Parser\Grammar This instance.
     */
    public function is(string ...$components): Grammar
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
     * @return \Aop\LALR\Parser\Grammar This instance.
     */
    public function call(callable $callback): Grammar
    {
        if ($this->currentRule === null) {
            throw new LogicException('You must specify a rule first.');
        }

        $this->currentRule->setCallback($callback);

        return $this;
    }

    /**
     * Returns the set of rules of this grammar.
     *
     * @return \Aop\LALR\Parser\Rule[] The rules.
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Get rule by its number.
     *
     * @param int $number Rule number
     *
     * @return \Aop\LALR\Parser\Rule
     */
    public function getRule(int $number): Rule
    {
        try {
            return $this->rules[$number];
        } catch (\Exception $e) {
            throw new OutOfBoundsException(sprintf('Unable to get rule "%d", there are only "%d" rules defined.', $number, count($this->rules)), 0, $e);
        }
    }

    /**
     * Returns the nonterminal symbols of this grammar.
     *
     * @return string[] The nonterminals.
     */
    public function getNonterminals()
    {
        return $this->nonterminals;
    }

    /**
     * Returns rules grouped by nonterminal name.
     *
     * @return array The rules grouped by nonterminal name.
     */
    public function getGroupedRules(): array
    {
        return $this->groupedRules;
    }

    /**
     * Sets a start rule for this grammar.
     *
     * @param string The name of the start rule.
     */
    public function start(string $name): void
    {
        $this->rules[0] = new Rule(0, self::START_RULE_NAME, [$name]);
    }

    /**
     * Returns the augmented start rule. For internal use only.
     *
     * @return \Aop\LALR\Parser\Rule The start rule.
     */
    public function getStartRule(): Rule
    {
        if (!isset($this->rules[0])) {
            throw new LogicException('No start rule specified.');
        }

        return $this->rules[0];
    }

    /**
     * Sets the mode of conflict resolution.
     *
     * @param int $mode The bitmask for the mode.
     */
    public function resolve(int $mode): void
    {
        $this->conflictsMode = $mode;
    }

    /**
     * Returns the conflict resolution mode for this grammar.
     *
     * @return int The bitmask of the resolution mode.
     */
    public function getConflictsMode(): int
    {
        return $this->conflictsMode;
    }

    /**
     * Does a nonterminal $name exist in the grammar?
     *
     * @param string $name The name of the nonterminal.
     *
     * @return boolean
     */
    public function hasNonterminal(string $name): bool
    {
        return array_key_exists($name, $this->groupedRules);
    }

    /**
     * Defines a group of operators.
     *
     * @param string[] $operators Any number of tokens that serve as the operators.
     *
     * @return \Aop\LALR\Parser\Grammar This instance for fluent interface.
     */
    public function operators(string ...$operators): Grammar
    {
        $this->currentRule      = null;
        $this->currentOperators = $operators;

        foreach ($operators as $operator) {
            $this->operators[$operator] = [
                'prec'  => 1,
                'assoc' => self::LEFT,
            ];
        }

        return $this;
    }

    /**
     * Marks the current group of operators as left-associative.
     *
     * @return \Aop\LALR\Parser\Grammar This instance for fluent interface.
     */
    public function left(): Grammar
    {
        return $this->assoc(self::LEFT);
    }

    /**
     * Marks the current group of operators as right-associative.
     *
     * @return \Aop\LALR\Parser\Grammar This instance for fluent interface.
     */
    public function right(): Grammar
    {
        return $this->assoc(self::RIGHT);
    }

    /**
     * Marks the current group of operators as non-associative.
     *
     * @return \Aop\LALR\Parser\Grammar This instance for fluent interface.
     */
    public function nonassoc(): Grammar
    {
        return $this->assoc(self::NONASSOC);
    }

    /**
     * Explicitly sets the associatity of the current group of operators.
     *
     * @param int $flag One of Grammar::LEFT, Grammar::RIGHT, Grammar::NONASSOC
     *
     * @return \Aop\LALR\Parser\Grammar This instance for fluent interface.
     */
    public function assoc(int $flag): Grammar
    {
        if (!$this->currentOperators) {
            throw new LogicException('Define a group of operators first.');
        }

        foreach ($this->currentOperators as $operator) {
            $this->operators[$operator]['assoc'] = $flag;
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
     * @return \Aop\LALR\Parser\Grammar This instance for fluent interface.
     */
    public function prec(int $precedence): Grammar
    {
        if (!$this->currentOperators) {

            if (!$this->currentRule) {
                throw new LogicException('Define a group of operators or a rule first.');
            }

            $this->currentRule->setPrecedence($precedence);

            return $this;
        }

        foreach ($this->currentOperators as $op) {
            $this->operators[$op]['prec'] = $precedence;
        }

        return $this;
    }

    /**
     * Is the passed token an operator?
     *
     * @param string $token The token type.
     *
     * @return boolean
     */
    public function hasOperator(string $token): bool
    {
        return array_key_exists($token, $this->operators);
    }

    public function getOperatorInfo(string $token): array
    {
        return $this->operators[$token];
    }
}
