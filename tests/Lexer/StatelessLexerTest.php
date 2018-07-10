<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Lexer;

use Aop\LALR\Tests\Stubs\Lexer\StatelessLexer;
use PHPUnit\Framework\TestCase;

final class StatelessLexerTest extends TestCase
{
    /**
     * @var \Aop\LALR\Tests\Stubs\Lexer\StatelessLexer
     */
    protected $lexer;

    public function setUp(): void
    {
        $this->lexer = new StatelessLexer();
    }

    /**
     * @test
     */
    public function lexerShouldWalkThroughTheRecognizers(): void
    {
        $stream = $this->lexer->lex('a (b) c');

        $this->assertEquals(6, $stream->count()); // with EOF
        $this->assertEquals('(', $stream->get(1)->getType());
        $this->assertEquals(1, $stream->get(3)->getLine());
        $this->assertEquals('C', $stream->get(4)->getType());
    }

    /**
     * @test
     */
    public function lexerShouldSkipSpecifiedTokens(): void
    {
        $stream = $this->lexer->lex('a (b) c');

        /**
         * @var \Aop\LALR\Contract\TokenInterface $token
         */
        foreach ($stream as $token) {
            $this->assertNotEquals('WS', $token->getType());
        }
    }

    /**
     * @test
     */
    public function lexerShouldReturnTheBestMatch(): void
    {
        $stream = $this->lexer->lex('class classloremipsum');

        $this->assertEquals('CLASS', $stream->getCurrentToken()->getType());
        $this->assertEquals('WORD', $stream->look(1)->getType());
    }
}
