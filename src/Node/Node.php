<?php

namespace Aop\LALR\Node;

use Aop\LALR\Exception\RuntimeException;

/**
 * An AST node.
 */
final class Node implements NodeInterface
{
    /**
     * @var array
     */
    protected $nodes;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * Constructor.
     *
     * @param array $attributes The attributes of this node.
     * @param array $nodes      The children of this node.
     */
    public function __construct(array $attributes = [], array $nodes = [])
    {
        $this->attributes = $attributes;
        $this->nodes      = $nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * {@inheritdoc}
     */
    public function hasNode(string $key): bool
    {
        return isset($this->nodes[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function getNode(string $key): NodeInterface
    {
        if (!isset($this->nodes[$key])) {
            throw new RuntimeException(sprintf('No child node "%s" exists.', $key));
        }

        return $this->nodes[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function setNode(string $key, NodeInterface $child): void
    {
        $this->nodes[$key] = $child;
    }

    /**
     * {@inheritdoc}
     */
    public function removeNode(string $key): void
    {
        unset($this->nodes[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute(string $key)
    {
        if (!isset($this->attributes[$key])) {
            throw new RuntimeException(sprintf('No attribute "%s" exists.', $key));
        }

        return $this->attributes[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttribute(string $key): void
    {
        unset($this->attributes[$key]);
    }

    public function count(): int
    {
        return count($this->nodes);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->nodes);
    }
}
