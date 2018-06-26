<?php

namespace Aop\LALR\Exception;

use Aop\LALR\Parser\LALR1\Analysis\Automaton;

class ConflictException extends LogicException
{
    protected $stateNumber;
    protected $automaton;

    public function __construct(string $message, int $stateNumber, Automaton $automaton)
    {
        parent::__construct($message);

        $this->stateNumber = $stateNumber;
        $this->automaton   = $automaton;
    }

    /**
     * Returns the number of the inadequate state.
     *
     * @return int
     */
    public function getStateNumber()
    {
        return $this->stateNumber;
    }

    /**
     * Returns the faulty automaton.
     *
     * @return \Aop\LALR\Parser\LALR1\Analysis\Automaton
     */
    public function getAutomaton()
    {
        return $this->automaton;
    }
}
