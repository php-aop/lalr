<?php

declare(strict_types=1);

namespace Aop\LALR\Parser\LALR1\Analysis;

use Aop\LALR\Contract\AutomatonInterface;

/**
 * Default \Aop\LALR\Contract\AutomatonInterface implementation
 */
final class Automaton implements AutomatonInterface
{
    /**
     * @var \Aop\LALR\Parser\LALR1\Analysis\State[]
     */
    private $states = [];

    /**
     * @var int[]
     */
    private $transitionTable = [];

    /**
     * {@inheritdoc}
     */
    public function getState(int $number): State
    {
        return $this->states[$number];
    }

    /**
     * {@inheritdoc}
     */
    public function hasState(int $number): bool
    {
        return isset($this->states[$number]);
    }

    /**
     * {@inheritdoc}
     */
    public function getStates(): array
    {
        return $this->states;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransitionTable(): array
    {
        return $this->transitionTable;
    }

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
}
