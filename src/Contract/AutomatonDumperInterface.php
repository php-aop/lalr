<?php

declare(strict_types=1);

namespace Aop\LALR\Contract;

interface AutomatonDumperInterface
{
    /**
     * Check if dumper can dump automaton in given format.
     *
     * @param string $format
     *
     * @return bool TRUE if dumper can dump requested automaton in given format.
     */
    public function supports(string $format): bool;

    /**
     * Dump automaton of given grammar for debugging and visualization purposes.
     *
     * @param \Aop\LALR\Contract\GrammarInterface $grammar Grammar for which automaton is being dumped.
     * @param string $format                               Dumping format.
     *
     * @return string
     */
    public function dump(GrammarInterface $grammar, string $format): string;
}
