<?php

declare(strict_types=1);

namespace Aop\LALR\Lexer;

use Aop\LALR\Lexer\TokenMatcher\RegexTokenMatcher;
use Aop\LALR\Lexer\TokenMatcher\StringTokenMatcher;
use Aop\LALR\Contract\TokenInterface;

use function Aop\LALR\Functions\utf8_strlen;

/**
 * AbstractStatelessLexer uses specified matchers without keeping track of state.
 */
abstract class AbstractStatelessLexer extends AbstractSimpleLexer
{
    /**
     * @var array
     */
    private $skipTokens = [];

    /**
     * @var array
     */
    private $tokenMatchers = [];

    /**
     * Adds a new token definition. If given only one argument,
     * it assumes that the token type and recognized value are
     * identical.
     *
     * @param string $type  The token type.
     * @param string $value The value to be recognized.
     *
     * @return \Aop\LALR\Lexer\AbstractStatelessLexer Fluent interface.
     */
    protected function token(string $type, ?string $value = null): AbstractStatelessLexer
    {
        $this->tokenMatchers[$type] = new StringTokenMatcher($value ?? $type);

        return $this;
    }

    /**
     * Adds a new regex token definition.
     *
     * @param string $type  The token type.
     * @param string $regex The regular expression used to match the token.
     *
     * @return \Aop\LALR\Lexer\AbstractStatelessLexer Fluent interface.
     */
    protected function regex(string $type, string $regex): AbstractStatelessLexer
    {
        $this->tokenMatchers[$type] = new RegexTokenMatcher($regex);

        return $this;
    }

    /**
     * Marks the token types given as arguments to be skipped.
     *
     * @param string[] $types Token types to skip.
     *
     * @return \Aop\LALR\Lexer\AbstractStatelessLexer Fluent interface.
     */
    protected function skip(string ...$types): AbstractStatelessLexer
    {
        $this->skipTokens = $types;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function shouldSkipToken(TokenInterface $token): bool
    {
        return \in_array($token->getType(), $this->skipTokens, true);
    }

    /**
     * {@inheritDoc}
     */
    protected function extractToken(string $string): ?TokenInterface
    {
        $value = null;
        $type  = null;

        /**
         * @var \Aop\LALR\Contract\TokenMatcherInterface $tokenMatcher
         */
        foreach ($this->tokenMatchers as $tokenType => $tokenMatcher) {

            $matchedValue = null;

            if (null === $string) {
                continue;
            }

            if (!$tokenMatcher->match($string, $matchedValue)) {
                continue;
            }

            if ($value === null || utf8_strlen($matchedValue) > utf8_strlen($value)) {
                $value = $matchedValue;
                $type  = $tokenType;
            }
        }

        if ($type !== null) {
            return new Token($type, $value, $this->getCurrentLine());
        }

        return null;
    }
}
