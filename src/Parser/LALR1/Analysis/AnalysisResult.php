<?php

declare(strict_types=1);

namespace Aop\LALR\Parser\LALR1\Analysis;

use Aop\LALR\Contract\AnalysisResultInterface;
use Aop\LALR\Contract\AutomatonInterface;

/**
 * Analysis result for LALR(1) analyzer.
 */
final class AnalysisResult implements AnalysisResultInterface
{
    /**
     * @var \Aop\LALR\Contract\AutomatonInterface
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
     * @param \Aop\LALR\Contract\AutomatonInterface $automaton Automaton.
     * @param array $parseTable                                The parse table.
     * @param array $conflicts                                 An array of conflicts resolved during parse table
     *                                                         construction.
     */
    public function __construct(AutomatonInterface $automaton, array $parseTable, array $conflicts)
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
    public function getAutomaton(): AutomatonInterface
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
