<?php

namespace Aop\LALR\Tests\TokenMatcher;

use Aop\LALR\Lexer\TokenMatcher\RegexTokenMatcher;
use PHPUnit\Framework\TestCase;

final class RegexRecognizerTest extends TestCase
{
    /**
     * @test
     */
    public function recognizerShouldMatchAndPassTheValueByReference()
    {
        $recognizer = new RegexTokenMatcher('/[a-z]+/');
        $result     = $recognizer->match('lorem ipsum', $value);

        $this->assertTrue($result);
        $this->assertNotNull($value);
        $this->assertEquals('lorem', $value);
    }

    /**
     * @test
     */
    public function recognizerShouldFailAndTheValueShouldStayNull()
    {
        $recognizer = new RegexTokenMatcher('/[a-z]+/');
        $result     = $recognizer->match('123 456', $value);

        $this->assertFalse($result);
        $this->assertNull($value);
    }

    /**
     * @test
     */
    public function recognizerShouldFailIfTheMatchIsNotAtTheBeginningOfTheString()
    {
        $recognizer = new RegexTokenMatcher('/[a-z]+/');
        $result     = $recognizer->match('234 class', $value);

        $this->assertFalse($result);
        $this->assertNull($value);
    }
}
