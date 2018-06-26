<?php

namespace Aop\LALR\Exception;

use Aop\LALR\Parser\LALR1\Analysis\Automaton;
use Aop\LALR\Parser\Rule;

class ReduceReduceConflictException extends ConflictException
{
    /**
     * The exception message template.
     */
    const MESSAGE = <<<EOT
The grammar exhibits a reduce/reduce conflict on rules:

  %d. %s -> %s

vs:

  %d. %s -> %s

(on lookahead "%s" in state %d). Restructure your grammar or choose a conflict resolution mode.
EOT;

    /**
     * @var \Aop\LALR\Parser\Rule
     */
    protected $firstRule;

    /**
     * @var \Aop\LALR\Parser\Rule
     */
    protected $secondRule;

    /**
     * @var string
     */
    protected $lookahead;

    /**
     * Constructor.
     *
     * @param int $state                                           The number of the inadequate state.
     * @param \Aop\LALR\Parser\Rule $firstRule                     The first conflicting grammar rule.
     * @param \Aop\LALR\Parser\Rule $secondRule                    The second conflicting grammar rule.
     * @param string $lookahead                                    The conflicting lookahead.
     * @param \Aop\LALR\Parser\LALR1\Analysis\Automaton $automaton The faulty automaton.
     */
    public function __construct(int $state, Rule $firstRule, Rule $secondRule, string $lookahead, Automaton $automaton)
    {
        $components1 = $firstRule->getComponents();
        $components2 = $secondRule->getComponents();

        parent::__construct(
            sprintf(
                self::MESSAGE,
                $firstRule->getNumber(),
                $firstRule->getName(),
                empty($components1) ? '/* empty */' : implode(' ', $components1),
                $secondRule->getNumber(),
                $secondRule->getName(),
                empty($components2) ? '/* empty */' : implode(' ', $components2),
                $lookahead,
                $state
            ),
            $state,
            $automaton
        );

        $this->firstRule  = $firstRule;
        $this->secondRule = $secondRule;
        $this->lookahead  = $lookahead;
    }

    /**
     * Returns the first conflicting rule.
     *
     * @return \Aop\LALR\Parser\Rule The first conflicting rule.
     */
    public function getFirstRule(): Rule
    {
        return $this->firstRule;
    }

    /**
     * Returns the second conflicting rule.
     *
     * @return \Aop\LALR\Parser\Rule The second conflicting rule.
     */
    public function getSecondRule(): Rule
    {
        return $this->secondRule;
    }

    /**
     * Returns the conflicting lookahead.
     *
     * @return string The conflicting lookahead.
     */
    public function getLookahead(): string
    {
        return $this->lookahead;
    }
}