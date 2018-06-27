<?php

namespace Aop\LALR\Tests\Recognizer;

use Aop\LALR\Lexer\Recognizer\SimpleRecognizer;
use PHPUnit\Framework\TestCase;

final class SimpleRecognizerTest extends TestCase
{
    /**
     * @test
     */
    public function recognizerShouldMatchAndPassTheValueByReference()
    {
        $recognizer = new SimpleRecognizer('class');
        $result     = $recognizer->match('class lorem ipsum', $value);

        $this->assertTrue($result);
        $this->assertNotNull($value);
        $this->assertEquals('class', $value);
    }

    /**
     * @test
     */
    public function recognizerShouldFailAndTheValueShouldStayNull()
    {
        $recognizer = new SimpleRecognizer('class');
        $result     = $recognizer->match('lorem ipsum', $value);

        $this->assertFalse($result);
        $this->assertNull($value);
    }
}
