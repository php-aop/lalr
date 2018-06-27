<?php

namespace Aop\LALR\Parser\LALR1\Dumper;

/**
 * A common contract for parse table dumpers.
 */
interface TableDumperInterface
{
    /**
     * Dumps the parse table.
     *
     * @param array $table The parse table.
     *
     * @return string The resulting string representation of the table.
     */
    public function dump(array $table): string;
}
