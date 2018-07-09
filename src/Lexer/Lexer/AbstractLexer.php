<?php

namespace Aop\LALR\Lexer\Lexer;

use Aop\LALR\Exception\RecognitionException;
use Aop\LALR\Lexer\LexerInterface;
use Aop\LALR\Lexer\Token;
use Aop\LALR\Contract\TokenInterface;
use Aop\LALR\Lexer\TokenStream;
use Aop\LALR\Contract\TokenStreamInterface;
use Aop\LALR\Parser\LALR1\Parser;

use function Aop\LALR\Functions\utf8_strlen;
use function Aop\LALR\Functions\substring;

/**
 * Lexers prototype
 */
abstract class AbstractLexer implements LexerInterface
{
    /**
     * @var int
     */
    private $line = 1;

    /**
     * {@inheritDoc}
     */
    public function lex(string $string): TokenStreamInterface
    {
        $string = strtr($string, ["\r\n" => "\n", "\r" => "\n"]);

        $tokens         = [];
        $position       = 0;
        $originalString = $string;
        $originalLength = utf8_strlen($string);

        while (true) {
            $token = $this->extractToken($string);

            if ($token === null) {
                break;
            }

            if (!$this->shouldSkipToken($token)) {
                $tokens[] = $token;
            }

            $shift = utf8_strlen($token->getValue());

            $position += $shift;

            if ($position > 0) {
                $this->line = substr_count($originalString, "\n", 0, $position) + 1;
            }

            $string = substring($string, $shift);
        }

        if ($position !== $originalLength) {
            $lines        = explode("\n", $originalString);
            $errorLine    = $lines[$this->line - 1];
            $linePosition = strpos($errorLine, $string);

            throw new RecognitionException($string, $linePosition, $this->line);
        }

        $tokens[] = new Token(Parser::EOF_TOKEN_TYPE, '', $this->line);

        return new TokenStream($tokens);
    }

    /**
     * Returns the current line.
     *
     * @return int The current line.
     */
    protected function getCurrentLine(): int
    {
        return $this->line;
    }

    /**
     * Attempts to extract another token from the string.
     * Returns the token on success or null on failure.
     *
     * @param string $string The string to extract the token from.
     *
     * @return \Aop\LALR\Contract\TokenInterface|null The extracted token or null.
     */
    abstract protected function extractToken(string $string): ?TokenInterface;

    /**
     * Should given token be skipped?
     *
     * @param \Aop\LALR\Contract\TokenInterface $token The token to evaluate.
     *
     * @return boolean Whether to skip the token.
     */
    abstract protected function shouldSkipToken(TokenInterface $token): bool;
}
