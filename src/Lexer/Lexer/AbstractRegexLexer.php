<?php

namespace Aop\LALR\Lexer\Lexer;

use Aop\LALR\Lexer\LexerInterface;
use Aop\LALR\Lexer\Token;
use Aop\LALR\Lexer\TokenStream;
use Aop\LALR\Contract\TokenStreamInterface;
use Aop\LALR\Parser\LALR1\Parser;

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

        $tokens[] = new Token(Parser::EOF_TOKEN_TYPE, '', $line);

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