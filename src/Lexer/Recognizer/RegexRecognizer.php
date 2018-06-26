<?php

namespace Aop\LALR\Lexer\Recognizer;

use Aop\LALR\Lexer\RecognizerInterface;

/**
 * The RegexRecognizer matches a string using a
 * regular expression.
 */
final class RegexRecognizer implements RecognizerInterface
{
    /**
     * @var string
     */
    protected $regex;

    /**
     * Constructor.
     *
     * @param string $regex The regex to use in the match.
     */
    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }

    /**
     * {@inheritdoc}
     */
    public function match(string $string, string &$result): bool
    {
        $r = preg_match($this->regex, $string, $match, PREG_OFFSET_CAPTURE);

        if ($r === 1 && $match[0][1] === 0) {
            $result = $match[0][0];

            return true;
        }

        return false;
    }
}
