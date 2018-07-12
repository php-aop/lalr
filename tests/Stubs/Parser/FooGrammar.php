<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Stubs\Parser;

use Aop\LALR\Parser\AbstractGrammar;

final class FooGrammar extends AbstractGrammar
{
    public function __construct()
    {
        $this
            ->define('Foo')
            ->is('a', 'b', 'c')
            ->is('x', 'y', 'z');

        $this->start('Foo');
    }
}
