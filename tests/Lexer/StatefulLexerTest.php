<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Lexer;

use Aop\LALR\Tests\Stubs\Lexer\StatefulLexer;
use PHPUnit\Framework\TestCase;

final class StatefulLexerTest extends TestCase
{
    /**
     * @var \Aop\LALR\Tests\Stubs\Lexer\StatefulLexer
     */
    protected $lexer;

    protected function setUp()
    {
        $this->lexer = new StatefulLexer();
    }

    /**
     * @test
     * @expectedException \Aop\LALR\Exception\LogicException
     * @expectedExceptionMessage Define a lexer state first.
     */
    public function addingNewTokenShouldThrowAnExceptionWhenNoStateIsBeingBuilt(): void
    {
        $this->lexer->regex('WORD', '/[a-z]+/');
    }

    /**
     * @test
     * @expectedException \Aop\LALR\Exception\LogicException
     */
    public function anExceptionShouldBeThrownOnLexingWithoutAStartingState(): void
    {
        $this->lexer->state('root');
        $this->lexer->lex('foo');
    }

    /**
     * @test
     */
    public function theStateMechanismShouldCorrectlyPushAndPopStatesFromTheStack(): void
    {
        $this->lexer->state('root');
        $this->lexer->regex('WORD', '/[a-z]+/');
        $this->lexer->regex('WS', "/[ \r\n\t]+/");
        $this->lexer->token('"');
        $this->lexer->action('string');
        $this->lexer->skip('WS');

        $this->lexer->state('string');
        $this->lexer->regex('STRING_CONTENTS', '/(\\\\"|[^"])*/');
        $this->lexer->token('"');
        $this->lexer->action(1); /* POP_STATE */

        $this->lexer->start('root');

        $stream = $this->lexer->lex('foo bar "long \\" string" baz quux');

        $this->assertCount(8, $stream);
        $this->assertEquals('STRING_CONTENTS', $stream->get(3)->getType());
        $this->assertEquals('long \\" string', $stream->get(3)->getValue());
        $this->assertEquals('quux', $stream->get(6)->getValue());
    }

    /**
     * @test
     */
    public function defaultActionShouldBeNoop(): void
    {
        $this->lexer->state('root');
        $this->lexer->regex('WORD', '/[a-z]+/');
        $this->lexer->regex('WS', "/[ \r\n\t]+/");
        $this->lexer->skip('WS');
        $this->lexer->state('string');
        $this->lexer->start('root');

        $stream = $this->lexer->lex('foo bar');

        $this->assertEquals(3, $stream->count());
    }
}