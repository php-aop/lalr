<?php

declare(strict_types=1);

namespace Aop\LALR\Lexer\TokenMatcher;

use Aop\LALR\Contract\TokenMatcherInterface;

/**
 * Matches a string using a regular expression.
 */
final class RegexTokenMatcher implements TokenMatcherInterface
{
    /**
     * @var string
     */
    private $regex;

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
    public function match(string $string, &$result): bool
    {
        $matches = \preg_match($this->regex, $string, $match, PREG_OFFSET_CAPTURE);

        if (1 === $matches && 0 === $match[0][1]) {
            $result = $match[0][0];

            return true;
        }

        return false;
    }
}
