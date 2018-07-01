<?php

namespace Aop\LALR\Tests\Parser\LALR1\Analysis;

use Aop\LALR\Parser\LALR1\Analysis\Automaton;
use Aop\LALR\Parser\LALR1\Analysis\State;
use PHPUnit\Framework\TestCase;

final class AutomatonTest extends TestCase
{
    /**
     * @var \Aop\LALR\Parser\LALR1\Analysis\Automaton
     */
    private $automaton;

    protected function setUp()
    {
        $this->automaton = new Automaton();
        $this->automaton->addState(new State(0, array()));
        $this->automaton->addState(new State(1, array()));
    }

    /**
     * @test
     */
    public function addingATransitionShouldBeVisibleInTheTransitionTable()
    {
        $this->automaton->addTransition(0, 'a', 1);
        $table = $this->automaton->getTransitionTable();

        $this->assertEquals(1, $table[0]['a']);
    }

    /**
     * @test
     */
    public function aNewStateShouldBeIdentifiedByItsNumber()
    {
        $state = new State(2, array());
        $this->automaton->addState($state);

        $this->assertSame($state, $this->automaton->getState(2));
    }
}
