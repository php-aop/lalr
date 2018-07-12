<?php

declare(strict_types=1);

namespace Aop\LALR\Exception;

use Aop\LALR\Contract\TokenInterface;

class UnexpectedTokenException extends RuntimeException
{
    /**
     * @var \Aop\LALR\Contract\TokenInterface
     */
    protected $token;

    /**
     * @var string[]
     */
    protected $expected;

    /**
     * Constructor.
     *
     * @param \Aop\LALR\Contract\TokenInterface $token The unexpected token.
     * @param string[] $expected                       The expected token types.
     */
    public function __construct(TokenInterface $token, array $expected)
    {
        $this->token    = $token;
        $this->expected = $expected;
        $tokenInfo      = ($token->getValue() !== $token->getType()) ? sprintf('%s (%s)', $token->getValue(), $token->getType()) : $token->getType();
        $message        = sprintf('Unexpected "%s" at line %d. Expected one of "%s".', $tokenInfo, $token->getLine(), implode('", "', $expected));

        parent::__construct($message);
    }

    /**
     * Returns the unexpected token.
     *
     * @return \Aop\LALR\Contract\TokenInterface The unexpected token.
     */
    public function getToken(): TokenInterface
    {
        return $this->token;
    }

    /**
     * Returns the expected token types.
     *
     * @return string[] The expected token types.
     */
    public function getExpected(): array
    {
        return $this->expected;
    }
}
