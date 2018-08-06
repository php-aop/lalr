<?php

declare(strict_types=1);

namespace Aop\LALR\Cache;

use Aop\LALR\Contract\ExpressionCacheInterface;
use Psr\SimpleCache\CacheInterface;

final class Psr16ExpressionCache implements ExpressionCacheInterface
{
    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $expression): bool
    {
        $key = $this->generateKey($expression);

        return $this->cache->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $expression)
    {
        $key = $this->generateKey($expression);

        return $this->cache->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $expression, $node): void
    {
        $key = $this->generateKey($expression);

        return $this->cache->set($key, $node, PHP_INT_MAX);
    }

    /**
     * Generate cache key for expression.
     *
     * @param string $expression An expression.
     * @return string Cache key.
     */
    private function generateKey(string $expression): string
    {
        return md5($expression);
    }
}
