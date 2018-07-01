<?php

namespace Aop\LALR\Tests\Parser;

use Aop\LALR\Parser\Grammar;
use Aop\LALR\Tests\Stubs\ExampleGrammar;
use PHPUnit\Framework\TestCase;

final class GrammarTest extends TestCase
{
    /**
     * @var \Aop\LALR\Tests\Stubs\ExampleGrammar
     */
    private $grammar;

    protected function setUp()
    {
        $this->grammar = new ExampleGrammar();
    }

    /**
     * @test
     */
    public function ruleAlternativesShouldHaveTheSameName()
    {
        $rules = $this->grammar->getRules();

        $this->assertEquals('Foo', $rules[1]->getName());
        $this->assertEquals('Foo', $rules[2]->getName());
    }

    /**
     * @test
     */
    public function theGrammarShouldBeAugmentedWithAStartRule()
    {
        $this->assertEquals(
            Grammar::START_RULE_NAME,
            $this->grammar->getStartRule()->getName()
        );

        $this->assertEquals(
            array('Foo'),
            $this->grammar->getStartRule()->getComponents()
        );
    }

    /**
     * @test
     */
    public function shouldReturnAlternativesGroupedByName()
    {
        $rules = $this->grammar->getGroupedRules();
        $this->assertCount(2, $rules['Foo']);
    }

    /**
     * @test
     */
    public function nonterminalsShouldBeDetectedFromRuleNames()
    {
        $this->assertTrue($this->grammar->hasNonterminal('Foo'));
    }
}