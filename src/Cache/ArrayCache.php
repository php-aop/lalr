<?php

declare(strict_types=1);

namespace Aop\LALR\Cache;

use Aop\LALR\Contract\AnalysisResultInterface;
use Aop\LALR\Contract\CacheInterface;
use Aop\LALR\Contract\GrammarInterface;

final class ArrayCache implements CacheInterface
{
    /**
     * @var array
     */
    private $cache = [];

    /**
     * {@inheritdoc}
     */
    public function set(GrammarInterface $grammar, AnalysisResultInterface $result): void
    {
        $this->cache[\get_class($grammar)] = $result;
    }

    /**
     * {@inheritdoc}
     */
    public function get(GrammarInterface $grammar): AnalysisResultInterface
    {
        return $this->cache[\get_class($grammar)];
    }

    /**
     * {@inheritdoc}
     */
    public function has(GrammarInterface $grammar): bool
    {
        return \array_key_exists(\get_class($grammar), $this->cache);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(GrammarInterface $grammar = null): void
    {
        if (null === $grammar) {
            $this->cache = [];
            return;
        }

        if ($this->has($grammar)) {
            unset($this->cache[\get_class($grammar)]);
        }
    }
}
