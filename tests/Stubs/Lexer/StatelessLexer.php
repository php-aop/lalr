<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Stubs\Lexer;

use Aop\LALR\Lexer\AbstractStatelessLexer;

final class StatelessLexer extends AbstractStatelessLexer
{
    public function __construct()
    {
        $this
            ->token('A', 'a')
            ->token('(')
            ->token('B', 'b')
            ->token(')')
            ->token('C', 'c')
            ->token('CLASS', 'class')
            ->regex('WORD', '/[a-z]+/')
            ->regex('WS', "/[ \n\t\r]+/")
            ->skip('WS');
    }
}
