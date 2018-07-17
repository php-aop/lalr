<?php

declare(strict_types=1);

namespace Aop\LALR\Exception;

use Aop\LALR\Contract\AutomatonInterface;
use Aop\LALR\Contract\RuleInterface;

class ReduceReduceConflictException extends ConflictException
{
    /**
     * The exception message template.
     */
    private const MESSAGE = <<<EOT
The grammar exhibits a reduce/reduce conflict on rules:

  %d. %s -> %s

vs:

  %d. %s -> %s

(on lookahead "%s" in state %d). Restructure your grammar or choose a conflict resolution mode.
EOT;

    /**
     * @var \Aop\LALR\Contract\RuleInterface
     */
    protected $firstRule;

    /**
     * @var \Aop\LALR\Contract\RuleInterface
     */
    protected $secondRule;

    /**
     * @var string
     */
    protected $lookahead;

    /**
     * Constructor.
     *
     * @param int $state                                       The number of the inadequate state.
     * @param \Aop\LALR\Contract\RuleInterface $firstRule      The first conflicting grammar rule.
     * @param \Aop\LALR\Contract\RuleInterface $secondRule     The second conflicting grammar rule.
     * @param string $lookahead                                The conflicting lookahead.
     * @param \Aop\LALR\Contract\AutomatonInterface $automaton The faulty automaton.
     */
    public function __construct(int $state, RuleInterface $firstRule, RuleInterface $secondRule, string $lookahead, AutomatonInterface $automaton)
    {
        $components1 = $firstRule->getComponents();
        $components2 = $secondRule->getComponents();

        parent::__construct(
            sprintf(
                self::MESSAGE,
                $firstRule->getNumber(),
                $firstRule->getName(),
                empty($components1) ? '\/* empty *\/' : implode(' ', $components1),
                $secondRule->getNumber(),
                $secondRule->getName(),
                empty($components2) ? '\/* empty *\/' : implode(' ', $components2),
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
     * @return \Aop\LALR\Contract\RuleInterface The first conflicting rule.
     */
    public function getFirstRule(): RuleInterface
    {
        return $this->firstRule;
    }

    /**
     * Returns the second conflicting rule.
     *
     * @return \Aop\LALR\Contract\RuleInterface The second conflicting rule.
     */
    public function getSecondRule(): RuleInterface
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
