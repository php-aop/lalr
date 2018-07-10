<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Stubs\Lexer;

use Aop\LALR\Lexer\AbstractSimpleLexer;
use Aop\LALR\Lexer\Token;
use Aop\LALR\Contract\TokenInterface;

final class SimpleLexer extends AbstractSimpleLexer
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
