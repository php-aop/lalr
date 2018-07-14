<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Parser\LALR1\Analysis;

use Aop\LALR\Parser\LALR1\Analysis\Item;
use Aop\LALR\Parser\LALR1\Analysis\State;
use Aop\LALR\Parser\Rule;
use PHPUnit\Framework\TestCase;

final class StateTest extends TestCase
{
    /**
     * @test
     */
    public function stateShouldKeepItemsByRuleNumberAndPosition(): void
    {
        $item1 = new Item(new Rule(1, 'E', array('E', '+', 'T')), 0);
        $state = new State(0, array($item1));

        $this->assertSame($item1, $state->get(1, 0));

        $item2 = new Item(new Rule(2, 'T', array('T', '+', 'F')), 0);
        $state->add($item2);

        $this->assertSame($item2, $state->get(2, 0));
    }
}
