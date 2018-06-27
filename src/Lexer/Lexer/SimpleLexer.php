<?php

namespace Aop\LALR\Lexer\Lexer;

use function Aop\LALR\Functions\utf8_strlen;
use Aop\LALR\Lexer\Recognizer\RegexRecognizer;
use Aop\LALR\Lexer\Recognizer\SimpleRecognizer;
use Aop\LALR\Lexer\Token;
use Aop\LALR\Lexer\TokenInterface;

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
        $this->recognizers[$type] = new SimpleRecognizer($value ?? $type);

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
        $this->recognizers[$type] = new RegexRecognizer($regex);

        return $this;
    }

    /**
     * Marks the token types given as arguments to be skipped.
     *
     * @param mixed $type,... Unlimited number of token types.
     *
     * @return \Aop\LALR\Lexer\Lexer\SimpleLexer This instance for fluent interface.
     */
    public function skip(): SimpleLexer
    {
        $this->skipTokens = func_get_args();

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
