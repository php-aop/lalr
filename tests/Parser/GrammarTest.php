<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Parser;

use Aop\LALR\Parser\AbstractGrammar;
use Aop\LALR\Tests\Stubs\Parser\FooGrammar;
use PHPUnit\Framework\TestCase;

final class GrammarTest extends TestCase
{
    /**
     * @var \Aop\LALR\Tests\Stubs\Parser\FooGrammar
     */
    private $grammar;

    protected function setUp(): void
    {
        $this->grammar = new FooGrammar();
    }

    /**
     * @test
     */
    public function ruleAlternativesShouldHaveTheSameName(): void
    {
        $rules = $this->grammar->getRules();

        $this->assertEquals('Foo', $rules[1]->getName());
        $this->assertEquals('Foo', $rules[2]->getName());
    }

    /**
     * @test
     */
    public function theGrammarShouldBeAugmentedWithAStartRule(): void
    {
        $this->assertEquals(AbstractGrammar::START, $this->grammar->getStartRule()->getName());
        $this->assertEquals(['Foo'], $this->grammar->getStartRule()->getComponents());
    }

    /**
     * @test
     */
    public function shouldReturnAlternativesGroupedByName(): void
    {
        $rules = $this->grammar->getGroupedRules();

        $this->assertCount(2, $rules['Foo']);
    }

    /**
     * @test
     */
    public function nonterminalsShouldBeDetectedFromRuleNames(): void
    {
        $this->assertTrue($this->grammar->hasNonterminal('Foo'));
    }
}
