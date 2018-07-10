<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Lexer;

use Aop\LALR\Exception\RecognitionException;
use Aop\LALR\Parser\LALR1\Parser;
use Aop\LALR\Tests\Stubs\Lexer\SimpleLexer;
use PHPUnit\Framework\TestCase;

final class AbstractLexerTest extends TestCase
{
    /**
     * @var \Aop\LALR\Lexer\AbstractSimpleLexer
     */
    private $lexer;

    public function setUp(): void
    {
        $this->lexer = new SimpleLexer();
    }

    /**
     * @test
     */
    public function lexShouldDelegateToExtractTokenUpdatingTheLineAndOffsetAccordingly(): void
    {
        $stream = $this->lexer->lex("ab\nc");

        $this->assertEquals('a', $stream->getCurrentToken()->getValue());
        $this->assertEquals(1, $stream->getCurrentToken()->getLine());
        $stream->next();

        $this->assertEquals('b', $stream->getCurrentToken()->getValue());
        $this->assertEquals(1, $stream->getCurrentToken()->getLine());
        $stream->next();

        $this->assertEquals("\n", $stream->getCurrentToken()->getValue());
        $this->assertEquals(1, $stream->getCurrentToken()->getLine());
        $stream->next();

        $this->assertEquals('c', $stream->getCurrentToken()->getValue());
        $this->assertEquals(2, $stream->getCurrentToken()->getLine());
    }

    /**
     * @test
     */
    public function lexShouldAppendAnEofTokenAutomatically(): void
    {
        $stream = $this->lexer->lex('abc');
        $stream->seek(3);

        $this->assertEquals(Parser::EOF_TOKEN_TYPE, $stream->getCurrentToken()->getType());
        $this->assertEquals(1, $stream->getCurrentToken()->getLine());
    }

    /**
     * @test
     */
    public function lexShouldThrowAnExceptionOnAnUnrecognizableToken(): void
    {
        try {
            $this->lexer->lex('abcd');
            $this->fail('Expected a RecognitionException.');
        } catch (RecognitionException $e) {
            $this->assertEquals(1, $e->getSourceLine());
            $this->assertEquals(3, $e->getPosition());
            $this->assertEquals('d', $e->getParameter());
        }
    }

    /**
     * @test
     */
    public function lexShouldNormalizeLineEndingsBeforeLexing(): void
    {
        $stream = $this->lexer->lex("a\r\nb");

        $this->assertEquals("\n", $stream->get(1)->getValue());
    }

    /**
     * @test
     */
    public function lexShouldSkipTokensIfToldToDoSo(): void
    {
        $stream = $this->lexer->lex('aeb');

        $this->assertNotEquals('e', $stream->get(1)->getType());
    }
}
