<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Lexer;

use Aop\LALR\Lexer\Token;
use Aop\LALR\Lexer\TokenStream;
use PHPUnit\Framework\TestCase;

final class TokenStreamTest extends TestCase
{
    /**
     * @var \Aop\LALR\Lexer\TokenStream
     */
    protected $stream;

    protected function setUp(): void
    {
        $this->stream = new TokenStream(array(
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
    public function theCursorShouldBeOnFirstTokenByDefault(): void
    {
        $this->assertEquals('6', $this->stream->getCurrentToken()->getValue());
    }

    /**
     * @test
     */
    public function getPositionShouldReturnCurrentPosition(): void
    {
        $this->stream->seek(2);
        $this->stream->next();

        $this->assertEquals(3, $this->stream->getPosition());
    }

    /**
     * @test
     */
    public function lookAheadShouldReturnTheCorrectToken(): void
    {
        $this->assertEquals('5', $this->stream->look(2)->getValue());
    }

    /**
     * @test
     * @expectedException \Aop\LALR\Exception\OutOfBoundsException
     */
    public function lookAheadShouldThrowAnExceptionWhenInvalid(): void
    {
        $this->stream->look(15);
    }

    /**
     * @test
     */
    public function getShouldReturnATokenByAbsolutePosition(): void
    {
        $this->assertEquals('3', $this->stream->get(4)->getValue());
    }

    /**
     * @test
     * @expectedException \Aop\LALR\Exception\OutOfBoundsException
     */
    public function getShouldThrowAnExceptionWhenInvalid(): void
    {
        $this->stream->get(15);
    }

    /**
     * @test
     */
    public function moveShouldMoveTheCursorByToAnAbsolutePosition(): void
    {
        $this->stream->move(2);
        $this->assertEquals('5', $this->stream->getCurrentToken()->getValue());
    }

    /**
     * @test
     * @expectedException \Aop\LALR\Exception\OutOfBoundsException
     */
    public function moveShouldThrowAnExceptionWhenInvalid(): void
    {
        $this->stream->move(15);
    }

    /**
     * @test
     */
    public function seekShouldMoveTheCursorByRelativeOffset(): void
    {
        $this->stream->seek(4);
        $this->assertEquals('3', $this->stream->getCurrentToken()->getValue());
    }

    /**
     * @test
     * @expectedException \Aop\LALR\Exception\OutOfBoundsException
     */
    public function seekShouldThrowAnExceptionWhenInvalid(): void
    {
        $this->stream->seek(15);
    }

    /**
     * @test
     */
    public function nextShouldMoveTheCursorOneTokenAhead(): void
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
    public function nextShouldThrowAnExceptionWhenAtTheEndOfTheStream(): void
    {
        $this->stream->seek(4);
        $this->stream->next();
    }
}
