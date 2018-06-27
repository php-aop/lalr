<?php

namespace Aop\LALR\Lexer\Recognizer;

use Aop\LALR\Lexer\RecognizerInterface;

final class SimpleRecognizer implements RecognizerInterface
{
    /**
     * @var string
     */
    protected $string;

    /**
     * Constructor.
     *
     * @param string $string The string to match by.
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * {@inheritdoc}
     */
    public function match(string $string, &$result): bool
    {
        if (strncmp($string, $this->string, \strlen($this->string)) === 0) {
            $result = $this->string;

            return true;
        }

        return false;
    }
}
