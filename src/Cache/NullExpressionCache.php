<?php

declare(strict_types=1);

namespace Aop\LALR\Cache;

use Aop\LALR\Contract\ExpressionCacheInterface;
use Aop\LALR\Exception\RuntimeException;

final class NullExpressionCache implements ExpressionCacheInterface
{
    /**
     * {@inheritdoc}
     */
    public function has(string $expression): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $expression)
    {
        throw new RuntimeException();
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $expression, $node): void
    {
        // noop
    }
}