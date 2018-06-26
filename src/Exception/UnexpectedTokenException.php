<?php

namespace Aop\LALR\Exception;

use Aop\LALR\Lexer\TokenInterface;

class UnexpectedTokenException extends RuntimeException
{
    private const MESSAGE = <<<EOT
Unexpected %s at line %d.

Expected one of %s.
EOT;

    /**
     * @var \Aop\LALR\Lexer\TokenInterface
     */
    protected $token;

    /**
     * @var string[]
     */
    protected $expected;

    /**
     * Constructor.
     *
     * @param \Aop\LALR\Lexer\TokenInterface $token The unexpected token.
     * @param string[] $expected                    The expected token types.
     */
    public function __construct(TokenInterface $token, array $expected)
    {
        $this->token    = $token;
        $this->expected = $expected;
        $info           = ($token->getValue() !== $token->getType()) ? sprintf('%s (%s)', $token->getValue(), $token->getType()) : $token->getType();

        parent::__construct(sprintf(
            self::MESSAGE,
            $info,
            $token->getLine(),
            implode(', ', $expected)
        ));
    }

    /**
     * Returns the unexpected token.
     *
     * @return \Aop\LALR\Lexer\TokenInterface The unexpected token.
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
