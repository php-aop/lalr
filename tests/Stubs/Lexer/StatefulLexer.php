<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Stubs\Lexer;

use Aop\LALR\Lexer\AbstractStatefulLexer;

final class StatefulLexer extends AbstractStatefulLexer
{
    public function token(string $type, ?string $value = null): AbstractStatefulLexer
    {
        return parent::token($type, $value);
    }

    public function regex(string $type, ?string $regex): AbstractStatefulLexer
    {
        return parent::regex($type, $regex);
    }

    public function state(string $state): AbstractStatefulLexer
    {
        return parent::state($state);
    }

    public function action($action): AbstractStatefulLexer
    {
        return parent::action($action);
    }

    public function start(string $state): AbstractStatefulLexer
    {
        return parent::start($state);
    }

    public function skip(string ...$types): AbstractStatefulLexer
    {
        return parent::skip(...$types);
    }
}
