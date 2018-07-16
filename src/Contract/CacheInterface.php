<?php

declare(strict_types=1);

namespace Aop\LALR\Contract;

interface CacheInterface
{
    /**
     * Writes the grammar analysis result to cache.
     *
     * @param \Aop\LALR\Contract\GrammarInterface $grammar A grammar.
     * @param \Aop\LALR\Contract\AnalysisResultInterface $result Analysis result.
     */
    public function set(GrammarInterface $grammar, AnalysisResultInterface $result): void;

    /**
     * @param \Aop\LALR\Contract\GrammarInterface $grammar Grammar full qualified class name.
     *
     * @return \Aop\LALR\Contract\AnalysisResultInterface Analysis result of the grammar.
     */
    public function get(GrammarInterface $grammar): AnalysisResultInterface;

    /**
     * Check if there is a cached analysis result of the grammar.
     *
     * @param \Aop\LALR\Contract\GrammarInterface $grammar A grammar.
     *
     * @return bool TRUE if there is a cached analysis result of the grammar.
     */
    public function has(GrammarInterface $grammar): bool;

    /**
     * Clear grammar analysis result cache.
     *
     * @param \Aop\LALR\Contract\GrammarInterface|null $grammar Cache for grammar to clear. If not provided, clear everything.
     */
    public function clear(GrammarInterface $grammar = null): void;
}
