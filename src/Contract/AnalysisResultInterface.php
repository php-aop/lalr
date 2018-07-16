<?php
declare(strict_types=1);

namespace Aop\LALR\Contract;

/**
 * Analysis result of the grammar.
 */
interface AnalysisResultInterface
{
    /**
     * Returns the handle-finding FSA.
     *
     * @return \Aop\LALR\Parser\LALR1\Analysis\Automaton
     */
    public function getAutomaton(): AutomatonInterface;

    /**
     * Returns the resulting parse table.
     *
     * @return array The parse table.
     */
    public function getParseTable(): array;

    /**
     * Returns an array of resolved parse table conflicts.
     *
     * @return array The conflicts.
     */
    public function getResolvedConflicts(): array;
}
