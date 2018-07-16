<?php

declare(strict_types=1);

namespace Aop\LALR\Cache;

use Aop\LALR\Contract\AnalysisResultInterface;
use Aop\LALR\Contract\CacheInterface;
use Aop\LALR\Contract\GrammarInterface;
use Aop\LALR\Exception\LogicException;

final class VoidCache implements CacheInterface
{
    /**
     * {@inheritdoc}
     */
    public function set(GrammarInterface $grammar, AnalysisResultInterface $result): void
    {
        // noop
    }
    /**
     * {@inheritdoc}
     */
    public function get(GrammarInterface $grammar): AnalysisResultInterface
    {
        throw new LogicException('Void cache does not stores cache.');
    }
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
    public function clear(GrammarInterface $grammar = null): void
    {
        // noop
    }
}
