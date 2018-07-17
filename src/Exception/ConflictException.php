<?php

declare(strict_types=1);

namespace Aop\LALR\Exception;

use Aop\LALR\Contract\AutomatonInterface;

abstract class ConflictException extends LogicException
{
    /**
     * @var int
     */
    protected $stateNumber;

    /**
     * @var \Aop\LALR\Contract\AutomatonInterface
     */
    protected $automaton;

    public function __construct(string $message, int $stateNumber, AutomatonInterface $automaton)
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
    public function getStateNumber(): int
    {
        return $this->stateNumber;
    }

    /**
     * Returns the faulty automaton.
     *
     * @return \Aop\LALR\Contract\AutomatonInterface
     */
    public function getAutomaton(): AutomatonInterface
    {
        return $this->automaton;
    }
}
