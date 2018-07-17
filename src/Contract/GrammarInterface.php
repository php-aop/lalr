<?php

declare(strict_types=1);

namespace Aop\LALR\Contract;

/**
 * Marker pattern, denotes a grammar.
 */
interface GrammarInterface
{
    /**
     * The name given to the rule the grammar is augmented with
     * when start() is called.
     */
    public const START = '$start';

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
     * resolved by taking operator precedence
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
    public const NONASSOCIATIVE = 2;

    /**
     * Returns the set of rules of this grammar.
     *
     * @return \Aop\LALR\Contract\RuleInterface[] The rules.
     */
    public function getRules(): array;

    /**
     * Get rule by its number.
     *
     * @param int $number Rule number
     *
     * @return \Aop\LALR\Contract\RuleInterface
     *
     * @throws \Aop\LALR\Exception\OutOfBoundsException
     */
    public function getRule(int $number): RuleInterface;

    /**
     * Returns rules grouped by nonterminal name.
     *
     * @return array The rules grouped by nonterminal name.
     */
    public function getGroupedRules(): array;

    /**
     * Returns the augmented start rule. For internal use only.
     *
     * @return \Aop\LALR\Contract\RuleInterface The start rule.
     */
    public function getStartRule(): RuleInterface;

    /**
     * Returns the conflict resolution mode for this grammar.
     *
     * @return int The bitmask of the resolution mode.
     */
    public function getConflictsMode(): int;

    /**
     * Check if nonterminal exist in the grammar.
     *
     * @param string $name The name of the nonterminal.
     *
     * @return boolean TRUE if exists.
     */
    public function hasNonterminal(string $name): bool;

    /**
     * Check if passed token is operator
     *
     * @param string $token The token type.
     *
     * @return boolean TRUE if token is operator.
     */
    public function hasOperator(string $token): bool;

    /**
     * Get operator.
     *
     * @param string $token
     *
     * @return \Aop\LALR\Contract\OperatorInterface
     */
    public function getOperator(string $token): OperatorInterface;
}
