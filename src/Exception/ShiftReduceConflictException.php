<?php

declare(strict_types=1);

namespace Aop\LALR\Exception;

use Aop\LALR\Contract\AutomatonInterface;
use Aop\LALR\Contract\RuleInterface;

class ShiftReduceConflictException extends ConflictException
{
    /**
     * The exception message template.
     */
    private const MESSAGE = <<<EOT
The grammar exhibits a shift/reduce conflict on rule:

  %d. %s -> %s

(on lookahead "%s" in state %d). Restructure your grammar or choose a conflict resolution mode.
EOT;

    /**
     * @var \Aop\LALR\Contract\RuleInterface
     */
    protected $rule;

    /**
     * @var string
     */
    protected $lookahead;

    /**
     * Constructor.
     *
     * @param string $state                                    State.
     * @param \Aop\LALR\Contract\RuleInterface $rule           The conflicting grammar rule.
     * @param string $lookahead                                The conflicting lookahead to shift.
     * @param \Aop\LALR\Contract\AutomatonInterface $automaton The faulty automaton.
     */
    public function __construct(string $state, RuleInterface $rule, string $lookahead, AutomatonInterface $automaton)
    {
        $components = $rule->getComponents();

        parent::__construct(
            sprintf(
                self::MESSAGE,
                $rule->getNumber(),
                $rule->getName(),
                empty($components) ? '\/* empty *\/' : implode(' ', $components),
                $lookahead,
                $state
            ),
            $state,
            $automaton
        );

        $this->rule      = $rule;
        $this->lookahead = $lookahead;
    }

    /**
     * Returns the conflicting rule.
     *
     * @return \Aop\LALR\Contract\RuleInterface The conflicting rule.
     */
    public function getRule(): RuleInterface
    {
        return $this->rule;
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
