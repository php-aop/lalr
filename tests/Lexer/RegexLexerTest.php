<?php

namespace Aop\LALR\Tests\Lexer;

use Aop\LALR\Parser\LALR1\Parser;
use PHPUnit\Framework\TestCase;

final class RegexLexerTest extends TestCase
{
    /**
     * @var \Aop\LALR\Lexer\Lexer\AbstractRegexLexer
     */
    private $lexer;

    protected function setUp()
    {
        $this->lexer = new StubRegexLexer();
    }

    /**
     * @test
     */
    public function itShouldCallGetTypeToRetrieveTokenType()
    {
        $stream = $this->lexer->lex('5 + 6');

        $this->assertCount(4, $stream);
        $this->assertEquals('INT', $stream->get(0)->getType());
        $this->assertEquals('+', $stream->get(1)->getType());
        $this->assertEquals(Parser::EOF_TOKEN_TYPE, $stream->get(3)->getType());
    }

    /**
     * @test
     */
    public function itShouldTrackLineNumbers()
    {
        $stream = $this->lexer->lex("5\n+\n\n5");

        $this->assertEquals(2, $stream->get(1)->getLine());
        $this->assertEquals(4, $stream->get(2)->getLine());
    }
}