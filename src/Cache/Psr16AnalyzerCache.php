<?php

declare(strict_types=1);

namespace Aop\LALR\Cache;

use Aop\LALR\Contract\AnalysisResultInterface;
use Aop\LALR\Contract\AnalyzerCacheInterface;
use Aop\LALR\Contract\GrammarInterface;
use Aop\LALR\Exception\RuntimeException;
use Psr\SimpleCache\CacheInterface;

final class Psr16AnalyzerCache implements AnalyzerCacheInterface
{
    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    private $cache;

    /**
     * @var bool
     */
    private $dev;

    public function __construct(CacheInterface $cache, bool $dev = false)
    {
        $this->cache = $cache;
        $this->dev   = $dev;
    }

    /**
     * {@inheritdoc}
     */
    public function has(GrammarInterface $grammar): bool
    {
        $key = $this->generateCacheKey($grammar);

        return $this->cache->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function get(GrammarInterface $grammar): AnalysisResultInterface
    {
        $key    = $this->generateCacheKey($grammar);
        $result = $this->cache->get($key);

        if ($result instanceof AnalysisResultInterface) {
            return $result;
        }

        throw new RuntimeException(sprintf('Analysis result for grammar "%s" is not cached.', \get_class($grammar)));
    }

    /**
     * {@inheritdoc}
     */
    public function set(GrammarInterface $grammar, AnalysisResultInterface $analysisResult): void
    {
        $key = $this->generateCacheKey($grammar);

        $this->cache->set($key, $analysisResult, PHP_INT_MAX);
    }

    /**
     * Generates cache key for grammar.
     */
    private function generateCacheKey(GrammarInterface $grammar): string
    {
        $class = \get_class($grammar);

        if (false === $this->dev) {
            return $class;
        }

        $grammarReflection = new \ReflectionClass($class);
        $file              = $grammarReflection->getFileName();
        $mtime             = \filemtime($file);

        return sprintf('%s_%s', $class, (string) $mtime);
    }
}