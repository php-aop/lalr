<?php

namespace Aop\LALR\Lexer\Lexer;

use Aop\LALR\Lexer\TokenMatcher\RegexTokenMatcher;
use Aop\LALR\Lexer\TokenMatcher\StringTokenMatcher;
use Aop\LALR\Lexer\Token;
use Aop\LALR\Contract\TokenInterface;

use function Aop\LALR\Functions\utf8_strlen;

/**
 * SimpleLexer uses specified recognizers
 * without keeping track of state.
 */
class SimpleLexer extends AbstractLexer
{
    /**
     * @var array
     */
    private $skipTokens = [];

    /**
     * @var array
     */
    private $recognizers = [];

    /**
     * Adds a new token definition. If given only one argument,
     * it assumes that the token type and recognized value are
     * identical.
     *
     * @param string $type  The token type.
     * @param string $value The value to be recognized.
     *
     * @return \Aop\LALR\Lexer\Lexer\SimpleLexer This instance for fluent interface.
     */
    public function token(string $type, ?string $value = null): SimpleLexer
    {
        $this->recognizers[$type] = new StringTokenMatcher($value ?? $type);

        return $this;
    }

    /**
     * Adds a new regex token definition.
     *
     * @param string $type  The token type.
     * @param string $regex The regular expression used to match the token.
     *
     * @return \Aop\LALR\Lexer\Lexer\SimpleLexer This instance for fluent interface.
     */
    public function regex(string $type, string $regex): SimpleLexer
    {
        $this->recognizers[$type] = new RegexTokenMatcher($regex);

        return $this;
    }

    /**
     * Marks the token types given as arguments to be skipped.
     *
     * @param string[] $types Token types to skip.
     *
     * @return \Aop\LALR\Lexer\Lexer\SimpleLexer This instance for fluent interface.
     */
    public function skip(string ...$types): SimpleLexer
    {
        $this->skipTokens = $types;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function shouldSkipToken(TokenInterface $token): bool
    {
        return in_array($token->getType(), $this->skipTokens);
    }

    /**
     * {@inheritDoc}
     */
    protected function extractToken(string $string): ?TokenInterface
    {
        $value = $type = null;

        foreach ($this->recognizers as $t => $recognizer) {

            $v = null;

            if (null === $string) {
                continue;
            }

            if ($recognizer->match($string, $v)) {
                if ($value === null || utf8_strlen($v) > utf8_strlen($value)) {
                    $value = $v;
                    $type  = $t;
                }
            }
        }

        if ($type !== null) {
            return new Token($type, $value, $this->getCurrentLine());
        }

        return null;
    }
}
