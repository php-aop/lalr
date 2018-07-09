<?php

namespace Aop\LALR\Tests\Lexer;

use Aop\LALR\Lexer\Lexer\AbstractLexer;
use Aop\LALR\Lexer\Token;
use Aop\LALR\Contract\TokenInterface;

final class StubLexer extends AbstractLexer
{
    protected function extractToken(string $string): ?TokenInterface
    {
        if (0 === \strlen(\utf8_decode($string))) {
            return null;
        }

        $char = $string[0];

        if ($char === 'd') { // unrecognizable token
            return null;
        }

        return new Token($char, $char, $this->getCurrentLine());
    }

    protected function shouldSkipToken(TokenInterface $token): bool
    {
        return 'e' === $token->getType();
    }
}
