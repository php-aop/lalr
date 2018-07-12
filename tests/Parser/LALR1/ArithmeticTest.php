<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Parser\LALR1;

use Aop\LALR\Exception\UnexpectedTokenException;
use Aop\LALR\Parser\LALR1\Parser;
use Aop\LALR\Tests\Stubs\Parser\LALR1\ArithmeticGrammar;
use Aop\LALR\Tests\Stubs\Parser\LALR1\ArithmeticLexer;
use PHPUnit\Framework\TestCase;

final class ArithmeticTest extends TestCase
{
    /**
     * @var \Aop\LALR\Tests\Stubs\Parser\LALR1\ArithmeticLexer
     */
    private $lexer;

    /**
     * @var \Aop\LALR\Parser\LALR1\Parser
     */
    private $parser;

    protected function setUp()
    {
        $this->lexer  = new ArithmeticLexer();
        $grammar      = new ArithmeticGrammar();
        $this->parser = new Parser($grammar);
    }

    /**
     * @test
     */
    public function parserShouldProcessTheTokenStreamAndUseGrammarCallbacksForReductions(): void
    {
        $this->assertEquals(-2, $this->parser->parse($this->lexer->lex('-1 - 1')));

        $this->assertEquals(11664, $this->parser->parse($this->lexer->lex('6 ** (1 + 1) ** 2 * (5 + 4)')));

        $this->assertEquals(-4, $this->parser->parse($this->lexer->lex('3 - 5 - 2')));

        $this->assertEquals(262144, $this->parser->parse($this->lexer->lex('4 ** 3 ** 2')));
    }

    /**
     * @test
     */
    public function parserShouldProcessTokenStreamWithMultipleArgs(): void
    {
        $this->assertEquals(5, $this->parser->parse($this->lexer->lex('Add(1, 2, 2)')));
    }

    /**
     * @test
     */
    public function parserShouldProcessTokenStreamWithNoArgs(): void
    {
        $this->assertEquals(0, $this->parser->parse($this->lexer->lex('Add()')));
    }

    /**
     * @test
     */
    public function parserShouldThrowAnExceptionOnInvalidInput(): void
    {
        try {
            $this->parser->parse($this->lexer->lex('6 ** 5 3'));
            $this->fail('Expected an UnexpectedTokenException.');
        } catch (UnexpectedTokenException $e) {
            $this->assertEquals('INT', $e->getToken()->getType());
            $this->assertEquals(['$eof', '+', '-', '*', '/', '**', ')', ','], $e->getExpected());
            $this->assertEquals('Unexpected "3 (INT)" at line 1. Expected one of "$eof", "+", "-", "*", "/", "**", ")", ",".', $e->getMessage());
        }
    }
}
