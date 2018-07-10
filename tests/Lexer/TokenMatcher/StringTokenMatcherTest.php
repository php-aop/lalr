<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Lexer\TokenMatcher;

use Aop\LALR\Lexer\TokenMatcher\StringTokenMatcher;
use PHPUnit\Framework\TestCase;

final class StringTokenMatcherTest extends TestCase
{
    /**
     * @test
     */
    public function recognizerShouldMatchAndPassTheValueByReference(): void
    {
        $recognizer = new StringTokenMatcher('class');
        $result     = $recognizer->match('class lorem ipsum', $value);

        $this->assertTrue($result);
        $this->assertNotNull($value);
        $this->assertEquals('class', $value);
    }

    /**
     * @test
     */
    public function recognizerShouldFailAndTheValueShouldStayNull(): void
    {
        $recognizer = new StringTokenMatcher('class');
        $result     = $recognizer->match('lorem ipsum', $value);

        $this->assertFalse($result);
        $this->assertNull($value);
    }
}
