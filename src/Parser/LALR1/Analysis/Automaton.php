<?php

namespace Aop\LALR\Parser\LALR1\Analysis;

/**
 * A finite-state automaton for recognizing
 * grammar productions.
 */
final class Automaton
{
    /**
     * @var array
     */
    private $states = [];

    /**
     * @var array
     */
    private $transitionTable = [];

    /**
     * Adds a new automaton state.
     *
     * @param \Aop\LALR\Parser\LALR1\Analysis\State $state The new state.
     */
    public function addState(State $state): void
    {
        $this->states[$state->getNumber()] = $state;
    }

    /**
     * Adds a new transition in the FSA.
     *
     * @param int $origin   The number of the origin state.
     * @param string $label The symbol that triggers this transition.
     * @param int $dest     The destination state number.
     */
    public function addTransition(int $origin, string $label, int $dest): void
    {
        $this->transitionTable[$origin][$label] = $dest;
    }

    /**
     * Returns a state by its number.
     *
     * @param int $number The state number.
     *
     * @return \Aop\LALR\Parser\LALR1\Analysis\State The requested state.
     */
    public function getState($number): State
    {
        return $this->states[$number];
    }

    /**
     * Does this automaton have a state identified by $number?
     *
     * @param int $number The state number.
     *
     * @return boolean
     */
    public function hasState(int $number): bool
    {
        return isset($this->states[$number]);
    }

    /**
     * Returns all states in this FSA.
     *
     * @return array The states of this FSA.
     */
    public function getStates(): array
    {
        return $this->states;
    }

    /**
     * Returns the transition table for this automaton.
     *
     * @return array The transition table.
     */
    public function getTransitionTable(): array
    {
        return $this->transitionTable;
    }
}
