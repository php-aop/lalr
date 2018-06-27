<?php

namespace Aop\LALR\Tests\Lexer;

use Aop\LALR\Lexer\Lexer\StatefulLexer;
use PHPUnit\Framework\TestCase;

final class StatefulLexerTest extends TestCase
{
    /**
     * @var \Aop\LALR\Lexer\Lexer\StatefulLexer
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
    public function addingNewTokenShouldThrowAnExceptionWhenNoStateIsBeingBuilt()
    {
        $this->lexer->regex('WORD', '/[a-z]+/');
    }

    /**
     * @test
     * @expectedException \Aop\LALR\Exception\LogicException
     */
    public function anExceptionShouldBeThrownOnLexingWithoutAStartingState()
    {
        $this->lexer->state('root');
        $this->lexer->lex('foo');
    }

    /**
     * @test
     */
    public function theStateMechanismShouldCorrectlyPushAndPopStatesFromTheStack()
    {
        $this->lexer->state('root')
            ->regex('WORD', '/[a-z]+/')
            ->regex('WS', "/[ \r\n\t]+/")
            ->token('"')->action('string')
            ->skip('WS');

        $this->lexer->state('string')
            ->regex('STRING_CONTENTS', '/(\\\\"|[^"])*/')
            ->token('"')->action(StatefulLexer::POP_STATE);

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
    public function defaultActionShouldBeNop()
    {
        $this->lexer->state('root')
            ->regex('WORD', '/[a-z]+/')
            ->regex('WS', "/[ \r\n\t]+/")
            ->skip('WS');

        $this->lexer->state('string');

        $this->lexer->start('root');

        $stream = $this->lexer->lex('foo bar');
        $this->assertEquals(3, $stream->count());
    }
}