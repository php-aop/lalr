<?php

declare(strict_types=1);

namespace Aop\LALR\Cache;

use Aop\LALR\Contract\AnalysisResultInterface;
use Aop\LALR\Contract\AnalyzerCacheInterface;
use Aop\LALR\Contract\GrammarInterface;
use Aop\LALR\Exception\RuntimeException;

final class NullAnalyzerCache implements AnalyzerCacheInterface
{
    /**
     * {@inheritdoc}
     */
    public function has(GrammarInterface $grammar): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function get(GrammarInterface $grammar): AnalysisResultInterface
    {
        throw new RuntimeException();
    }

    /**
     * {@inheritdoc}
     */
    public function set(GrammarInterface $grammar, AnalysisResultInterface $analysisResult): void
    {
        // noop
    }
}
