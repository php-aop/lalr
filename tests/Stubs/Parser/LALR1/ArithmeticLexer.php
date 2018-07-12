<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Stubs\Parser\LALR1;

use Aop\LALR\Lexer\AbstractStatelessLexer;

final class ArithmeticLexer extends AbstractStatelessLexer
{
    public function __construct()
    {
        $this->regex('INT', '/^[1-9][0-9]*/');
        $this->token('(');
        $this->token(')');
        $this->token(',');
        $this->token('+');
        $this->token('-');
        $this->token('**');
        $this->token('*');
        $this->token('/');
        $this->regex('WSP', "/^[ \r\n\t]+/");
        $this->skip('WSP');
        $this->token('Add(');
    }
}
