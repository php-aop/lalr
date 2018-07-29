<?php
declare(strict_types=1);

namespace Aop\LALR\Contract;

/**
 * Cache for grammar analysis result.
 */
interface AnalyzerCacheInterface
{
    /**
     * Check if there is a cached analysis result for grammar.
     */
    public function has(GrammarInterface $grammar): bool;

    /**
     * Get cached analysis result for grammar.
     *
     * @throws \Aop\LALR\Exception\RuntimeException If there is no cache.
     */
    public function get(GrammarInterface $grammar): AnalysisResultInterface;

    /**
     * Set analysis result into cache.
     *
     * @param \Aop\LALR\Contract\GrammarInterface $grammar
     */
    public function set(GrammarInterface $grammar, AnalysisResultInterface $analysisResult): void;
}
