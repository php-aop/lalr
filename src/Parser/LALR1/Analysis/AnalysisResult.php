<?php

declare(strict_types=1);

namespace Aop\LALR\Parser\LALR1\Analysis;

final class AnalysisResult
{
    /**
     * @var \Aop\LALR\Parser\LALR1\Analysis\Automaton
     */
    private $automaton;

    /**
     * @var array
     */
    private $parseTable;

    /**
     * @var array
     */
    private $resolvedConflicts;

    /**
     * Constructor.
     *
     * @param \Aop\LALR\Parser\LALR1\Analysis\Automaton $automaton Automaton.
     * @param array $parseTable                                    The parse table.
     * @param array $conflicts                                     An array of conflicts resolved during parse table
     *                                                             construction.
     */
    public function __construct(Automaton $automaton, array $parseTable, array $conflicts)
    {
        $this->automaton         = $automaton;
        $this->parseTable        = $parseTable;
        $this->resolvedConflicts = $conflicts;
    }

    /**
     * Returns the handle-finding FSA.
     *
     * @return \Aop\LALR\Parser\LALR1\Analysis\Automaton
     */
    public function getAutomaton(): Automaton
    {
        return $this->automaton;
    }

    /**
     * Returns the resulting parse table.
     *
     * @return array The parse table.
     */
    public function getParseTable(): array
    {
        return $this->parseTable;
    }

    /**
     * Returns an array of resolved parse table conflicts.
     *
     * @return array The conflicts.
     */
    public function getResolvedConflicts(): array
    {
        return $this->resolvedConflicts;
    }
}
