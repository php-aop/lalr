<?php

declare(strict_types=1);

namespace Aop\LALR\Contract;

interface AutomatonDumperInterface
{
    /**
     * Check if dumper can dump automaton in given format.
     *
     * @param \Aop\LALR\Contract\AutomatonInterface $automaton
     * @param string $format
     *
     * @return bool TRUE if dumper can dump requested automaton in given format.
     */
    public function supports(AutomatonInterface $automaton, string $format): bool;

    /**
     * Dump automaton for debugging and visualization purposes.
     *
     * @param \Aop\LALR\Contract\AutomatonInterface $automaton
     *
     * @return string
     */
    public function dump(AutomatonInterface $automaton): string;
}
