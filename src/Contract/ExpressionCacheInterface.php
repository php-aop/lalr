<?php

declare(strict_types=1);

namespace Aop\LALR\Contract;

interface ExpressionCacheInterface
{
    /**
     * Check if expression is already tokenized and parsed.
     *
     * @param string $expression An expression
     *
     * @return bool TRUE if expression is cached.
     */
    public function has(string $expression): bool;

    /**
     * Get cached parsing result for expression.
     *
     * @param string $expression
     *
     * @return mixed|\Aop\LALR\Contract\NodeInterface Parsing result.
     */
    public function get(string $expression);

    /**
     * Cache parsing result for expression.
     *
     * @param string $expression Expression to cache.
     * @param mixed|\Aop\LALR\Contract\NodeInterface $node Parsing result.
     *
     * @return void
     */
    public function set(string $expression, $node): void;
}
