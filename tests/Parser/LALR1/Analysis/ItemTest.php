<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Parser\LALR1\Analysis;

use Aop\LALR\Parser\LALR1\Analysis\Item;
use Aop\LALR\Parser\Rule;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
{
    /**
     * @test
     */
    public function getActiveComponentShouldReturnTheComponentAboutToBeEncountered(): void
    {
        $rule = new Rule(1, 'A', ['a', 'b', 'c']);
        $item = new Item($rule, 1);

        $this->assertEquals('b', $item->getActiveComponent());
    }

    /**
     * @test
     */
    public function itemShouldBeAReduceItemIfAllComponentsHaveBeenEncountered(): void
    {
        $item = new Item(new Rule(1, 'A', ['a', 'b', 'c']), 1);
        $this->assertFalse($item->isReduceItem());

        $item = new Item(new Rule(1, 'A', ['a', 'b', 'c']), 3);
        $this->assertTrue($item->isReduceItem());
    }

    /**
     * @test
     */
    public function itemShouldPumpLookaheadIntoConnectedItems(): void
    {
        $item1 = new Item(new Rule(1, 'A', ['a', 'b', 'c']), 1);
        $item2 = new Item(new Rule(1, 'A', ['a', 'b', 'c']), 2);

        $item1->connect($item2);
        $item1->pump('d');

        $this->assertContains('d', $item2->getLookahead());
    }

    /**
     * @test
     */
    public function itemShouldPumpTheSameLookaheadOnlyOnce(): void
    {
        $item1 = new Item(new Rule(1, 'A', ['a', 'b', 'c']), 1);
        $item2 = new Item(new Rule(1, 'A', ['a', 'b', 'c']), 1);

        $item1->connect($item2);

        $item1->pump('d');
        $item1->pump('d');

        $lookahead = $item1->getLookahead();

        $this->assertCount(1, $lookahead);
        $this->assertContains('d', $lookahead);
    }

    /**
     * @test
     */
    public function getUnrecognizedComponentsShouldReturnAllComponentAfterTheDottedOne(): void
    {
        $rule = new Rule(1, 'A', ['a', 'b', 'c']);
        $item = new Item($rule, 1);

        $this->assertEquals(['c'], $item->getUnrecognizedComponents());
    }
}
