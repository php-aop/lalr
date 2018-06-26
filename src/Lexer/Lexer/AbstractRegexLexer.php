<?php

namespace Aop\LALR\Lexer\Lexer;

use Aop\LALR\Lexer\LexerInterface;
use Aop\LALR\Lexer\Token;
use Aop\LALR\Lexer\TokenStream\ArrayTokenStream;
use Aop\LALR\Lexer\TokenStreamInterface;

abstract class AbstractRegexLexer implements LexerInterface
{
    /**
     * {@inheritDoc}
     */
    public function lex(string $string): TokenStreamInterface
    {
        static $regex;

        if (!isset($regex)) {
            $regex = '/(' . implode(')|(', $this->getCatchablePatterns()) . ')|'
                . implode('|', $this->getNonCatchablePatterns()) . '/i';
        }

        $string = strtr($string, array("\r\n" => "\n", "\r" => "\n"));

        $flags = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE;
        $matches = preg_split($regex, $string, -1, $flags);
        $tokens = array();
        $line = 1;
        $oldPosition = 0;

        foreach ($matches as $match) {
            list ($value, $position) = $match;

            $type = $this->getType($value);

            if ($position > 0) {
                $line += substr_count($string, "\n", $oldPosition, $position - $oldPosition);
            }

            $oldPosition = $position;

            $tokens[] = new Token($type, $value, $line);
        }

        $tokens[] = new Token(Parser::EOF_TOKEN_TYPE, '', $line);

        return new ArrayTokenStream($tokens);
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