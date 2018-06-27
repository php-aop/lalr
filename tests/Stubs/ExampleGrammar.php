<?php

namespace Aop\LALR\Tests\Stubs;

use Aop\LALR\Parser\Grammar;

final class ExampleGrammar extends Grammar
{
    public function __construct()
    {
        $this('Foo')
            ->is('a', 'b', 'c')
            ->is('x', 'y', 'z');

        $this->start('Foo');
    }
}
