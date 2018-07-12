<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Stubs\Parser\LALR1\Analysis;

use Aop\LALR\Parser\AbstractGrammar;

final class Grammar extends AbstractGrammar
{
    public function buildAutomatonShouldBeCorrectlyBuiltGrammar(): void
    {
        $this
            ->define('S')
            ->is('a', 'S', 'b')
            ->is();

        $this->start('S');
    }

    public function buildLookaheadShouldBeCorrectlyPumpedGrammar(): void
    {
        $this
            ->define('S')
            ->is('A', 'B', 'C', 'D');

        $this
            ->define('A')
            ->is('a');

        $this
            ->define('B')
            ->is('b');

        $this
            ->define('C')
            ->is(/* empty */);

        $this
            ->define('D')
            ->is('d');

        $this->start('S');
    }

    public function buildParseTableShouldBeCorrectlyBuiltGrammar(): void
    {
        $this
            ->define('S')
            ->is('a', 'S', 'b')
            ->is(/* empty */);

        $this->start('S');
    }

    public function buildUnexpectedConflictsShouldThrowAnExceptionGrammar(): void
    {
        $this
            ->define('S')
            ->is('a', 'b', 'C', 'd')
            ->is('a', 'b', 'E', 'd');

        $this
            ->define('C')
            ->is(/* empty */);

        $this
            ->define('E')
            ->is(/* empty */);

        $this->start('S');
    }

    public function buildExpectedConflictsShouldBeRecordedGrammar(): void
    {
        $this
            ->define('S')
            ->is('S', 'S', 'S')
            ->is('S', 'S')
            ->is('b');

        $this->resolve(self::ALL);
        $this->start('S');
    }
}
