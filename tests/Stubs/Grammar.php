<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Stubs;

use Aop\LALR\Parser\AbstractGrammar;

final class Grammar extends AbstractGrammar
{
    public function define(string $name): AbstractGrammar
    {
        return parent::define($name);
    }

    public function is(string ...$components): AbstractGrammar
    {
        return parent::is(...$components);
    }

    public function call(callable $callback): AbstractGrammar
    {
        return parent::call($callback);
    }

    public function start(string $name): void
    {
        parent::start($name);
    }

    public function resolve(int $mode): void
    {
        parent::resolve($mode);
    }
}
