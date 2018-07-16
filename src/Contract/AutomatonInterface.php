<?php

namespace Aop\LALR\Contract;

use Aop\LALR\Parser\LALR1\Analysis\State;

/**
 * A finite-state automaton for recognizing
 * grammar productions.
 */
interface AutomatonInterface
{
    /**
     * Returns a state by its number.
     *
     * @param int $number The state number.
     *
     * @return \Aop\LALR\Parser\LALR1\Analysis\State The requested state.
     */
    public function getState(int $number): State;

    /**
     * Does this automaton have a state identified by $number?
     *
     * @param int $number The state number.
     *
     * @return boolean
     */
    public function hasState(int $number): bool;

    /**
     * Returns all states in this FSA.
     *
     * @return \Aop\LALR\Parser\LALR1\Analysis\State[] The states of this FSA.
     */
    public function getStates(): array;

    /**
     * Returns the transition table for this automaton.
     *
     * @return array The transition table.
     */
    public function getTransitionTable(): array;
}
