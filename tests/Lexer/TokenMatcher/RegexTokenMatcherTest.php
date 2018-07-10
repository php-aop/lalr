<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Lexer\TokenMatcher;

use Aop\LALR\Lexer\TokenMatcher\RegexTokenMatcher;
use PHPUnit\Framework\TestCase;

final class RegexTokenMatcherTest extends TestCase
{
    /**
     * @test
     */
    public function recognizerShouldMatchAndPassTheValueByReference(): void
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
    public function recognizerShouldFailAndTheValueShouldStayNull(): void
    {
        $recognizer = new RegexTokenMatcher('/[a-z]+/');
        $result     = $recognizer->match('123 456', $value);

        $this->assertFalse($result);
        $this->assertNull($value);
    }

    /**
     * @test
     */
    public function recognizerShouldFailIfTheMatchIsNotAtTheBeginningOfTheString(): void
    {
        $recognizer = new RegexTokenMatcher('/[a-z]+/');
        $result     = $recognizer->match('234 class', $value);

        $this->assertFalse($result);
        $this->assertNull($value);
    }
}
