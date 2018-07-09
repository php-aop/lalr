<?php

declare(strict_types=1);

namespace Aop\LALR\Lexer\TokenMatcher;

use Aop\LALR\Contract\TokenMatcherInterface;

final class StringTokenMatcher implements TokenMatcherInterface
{
    /**
     * @var string
     */
    private $string;

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
