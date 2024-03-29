<?php

declare(strict_types=1);

namespace Aop\LALR\Contract;

/**
 * A basic contract for a node in an AST.
 */
interface NodeInterface extends \IteratorAggregate, \Countable
{
    /**
     * Returns the children of this node.
     *
     * @return array The children belonging to this node.
     */
    public function getNodes(): array;

    /**
     * Checks for existence of child node named $name.
     *
     * @param string $name The name of the child node.
     *
     * @return boolean If the node exists.
     */
    public function hasNode(string $name): bool;

    /**
     * Returns a child node specified by $name.
     *
     * @param int|string $name The name of the node.
     *
     * @return \Aop\LALR\Contract\NodeInterface The child node specified by $name.
     *
     * @throws \Aop\LALR\Exception\RuntimeException When no child node named $name exists.
     */
    public function getNode(string $name): NodeInterface;

    /**
     * Sets a child node.
     *
     * @param string $name                       The name.
     * @param \Aop\LALR\Contract\NodeInterface $child The new child node.
     */
    public function setNode(string $name, NodeInterface $child): void;

    /**
     * Removes a child node by name.
     *
     * @param string $name The name.
     */
    public function removeNode(string $name): void;

    /**
     * Returns all attributes of this node.
     *
     * @return array The attributes.
     */
    public function getAttributes(): array;

    /**
     * Determines whether this node has an attribute
     * under $key.
     *
     * @param string $key The key.
     *
     * @return boolean Whether there's an attribute under $key.
     */
    public function hasAttribute(string $key): bool;

    /**
     * Gets an attribute by key.
     *
     * @param string $key The key.
     *
     * @return mixed The attribute value.
     *
     * @throws \Aop\LALR\Exception\RuntimeException When no attribute exists under $key.
     */
    public function getAttribute(string $key);

    /**
     * Sets an attribute by key.
     *
     * @param string $key  The key.
     * @param mixed $value The new value.
     */
    public function setAttribute(string $key, $value): void;

    /**
     * Removes an attribute by key.
     *
     * @param string $key The key.
     */
    public function removeAttribute(string $key): void;
}
