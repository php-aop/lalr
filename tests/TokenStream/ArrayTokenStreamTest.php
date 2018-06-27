<?php

namespace Aop\LALR\Tests\TokenStream;

use Aop\LALR\Lexer\Token;
use Aop\LALR\Lexer\TokenStream\ArrayTokenStream;
use PHPUnit\Framework\TestCase;

final class ArrayTokenStreamTest extends TestCase
{
    /**
     * @var \Aop\LALR\Lexer\TokenStream\ArrayTokenStream
     */
    protected $stream;

    protected function setUp()
    {
        $this->stream = new ArrayTokenStream(array(
            new Token('INT', '6', 1),
            new Token('PLUS', '+', 1),
            new Token('INT', '5', 1),
            new Token('MINUS', '-', 1),
            new Token('INT', '3', 1),
        ));
    }

    /**
     * @test
     */
    public function theCursorShouldBeOnFirstTokenByDefault()
    {
        $this->assertEquals('6', $this->stream->getCurrentToken()->getValue());
    }

    /**
     * @test
     */
    public function getPositionShouldReturnCurrentPosition()
    {
        $this->stream->seek(2);
        $this->stream->next();

        $this->assertEquals(3, $this->stream->getPosition());
    }

    /**
     * @test
     */
    public function lookAheadShouldReturnTheCorrectToken()
    {
        $this->assertEquals('5', $this->stream->lookAhead(2)->getValue());
    }

    /**
     * @test
     * @expectedException \Aop\LALR\Exception\OutOfBoundsException
     */
    public function lookAheadShouldThrowAnExceptionWhenInvalid()
    {
        $this->stream->lookAhead(15);
    }

    /**
     * @test
     */
    public function getShouldReturnATokenByAbsolutePosition()
    {
        $this->assertEquals('3', $this->stream->get(4)->getValue());
    }

    /**
     * @test
     * @expectedException \Aop\LALR\Exception\OutOfBoundsException
     */
    public function getShouldThrowAnExceptionWhenInvalid()
    {
        $this->stream->get(15);
    }

    /**
     * @test
     */
    public function moveShouldMoveTheCursorByToAnAbsolutePosition()
    {
        $this->stream->move(2);
        $this->assertEquals('5', $this->stream->getCurrentToken()->getValue());
    }

    /**
     * @test
     * @expectedException \Aop\LALR\Exception\OutOfBoundsException
     */
    public function moveShouldThrowAnExceptionWhenInvalid()
    {
        $this->stream->move(15);
    }

    /**
     * @test
     */
    public function seekShouldMoveTheCursorByRelativeOffset()
    {
        $this->stream->seek(4);
        $this->assertEquals('3', $this->stream->getCurrentToken()->getValue());
    }

    /**
     * @test
     * @expectedException \Aop\LALR\Exception\OutOfBoundsException
     */
    public function seekShouldThrowAnExceptionWhenInvalid()
    {
        $this->stream->seek(15);
    }

    /**
     * @test
     */
    public function nextShouldMoveTheCursorOneTokenAhead()
    {
        $this->stream->next();
        $this->assertEquals('PLUS', $this->stream->getCurrentToken()->getType());

        $this->stream->next();
        $this->assertEquals('5', $this->stream->getCurrentToken()->getValue());
    }

    /**
     * @test
     * @expectedException \Aop\LALR\Exception\OutOfBoundsException
     */
    public function nextShouldThrowAnExceptionWhenAtTheEndOfTheStream()
    {
        $this->stream->seek(4);
        $this->stream->next();
    }
}
