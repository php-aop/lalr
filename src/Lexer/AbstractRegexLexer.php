<?php

declare(strict_types=1);

namespace Aop\LALR\Lexer;

use Aop\LALR\Contract\LexerInterface;
use Aop\LALR\Contract\TokenStreamInterface;

/**
 * Highly performant, less user friendly lexer based on Doctrine's lexer.
 *
 * @see https://github.com/doctrine/lexer/blob/master/lib/Doctrine/Common/Lexer/AbstractLexer.php
 */
abstract class AbstractRegexLexer implements LexerInterface
{
    /**
     * {@inheritDoc}
     */
    public function lex(string $string): TokenStreamInterface
    {
        static $regex;

        if (null === $regex) {
            $catchablePatterns    = implode(')|(', $this->getCatchablePatterns());
            $nonCatchablePatterns = implode('|', $this->getNonCatchablePatterns());
            $regex                = sprintf('/(%s)|%s/i', $catchablePatterns, $nonCatchablePatterns);
        }

        $string = strtr($string, ["\r\n" => "\n", "\r" => "\n"]);

        $flags       = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE;
        $matches     = preg_split($regex, $string, -1, $flags);
        $tokens      = [];
        $line        = 1;
        $oldPosition = 0;

        foreach ($matches as [$value, $position]) {

            $type = $this->getType($value);

            if ($position > 0) {
                $line += substr_count($string, "\n", $oldPosition, $position - $oldPosition);
            }

            $oldPosition = $position;

            $tokens[] = new Token($type, $value, $line);
        }

        $tokens[] = new Token(LexerInterface::TOKEN_EOF, '', $line);

        return new TokenStream($tokens);
    }

    /**
     * The patterns corresponding to tokens.
     *
     * @return array
     */
    abstract protected function getCatchablePatterns(): array;

    /**
     * The patterns corresponding to tokens to be skipped.
     *
     * @return array
     */
    abstract protected function getNonCatchablePatterns(): array;

    /**
     * Retrieves the token type.
     *
     * @param string $value
     *
     * @return string $type
     */
    abstract protected function getType(string &$value): string;
}