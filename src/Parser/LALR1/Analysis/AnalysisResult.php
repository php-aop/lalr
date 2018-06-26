<?php

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
     * @param array $parseTable The parse table.
     * @param \Aop\LALR\Parser\LALR1\Analysis\Automaton $automaton
     * @param array $conflicts  An array of conflicts resolved during parse table
     *                          construction.
     */
    public function __construct(array $parseTable, Automaton $automaton, array $conflicts)
    {
        $this->parseTable        = $parseTable;
        $this->automaton         = $automaton;
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
