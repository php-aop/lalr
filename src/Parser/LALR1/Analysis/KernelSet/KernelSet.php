<?php

namespace Aop\LALR\Parser\LALR1\Analysis\KernelSet;

/**
 * A BST implementation for more efficient lookup
 * of states by their kernel items.
 */
final class KernelSet
{
    /**
     * @var int
     */
    private $nextNumber = 0;

    /**
     * @var \Aop\LALR\Parser\LALR1\Analysis\KernelSet\Node|null
     */
    private $root;

    /**
     * Inserts a new node in the BST and returns
     * the number of the new state if no such state
     * exists. Otherwise, returns the number of the
     * existing state.
     *
     * @param array $kernel The state kernel.
     *
     * @return int The state number.
     */
    public function insert(array $kernel): int
    {
        $kernel = KernelSet::hashKernel($kernel);

        if ($this->root === null) {
            $this->root = new Node($kernel, $next = $this->nextNumber++);

            return $next;
        }

        $node = $this->root;

        while (true) {

            if ($kernel < $node->kernel) {

                if ($node->left === null) {
                    $node->left = new Node($kernel, $next = $this->nextNumber++);

                    return $next;
                }

                $node = $node->left;

            } elseif ($kernel > $node->kernel) {

                if ($node->right === null) {
                    $node->right = new Node($kernel, $next = $this->nextNumber++);

                    return $next;
                }

                $node = $node->right;

            } else {
                return $node->number;
            }
        }
    }

    /**
     * Hashes a state kernel using a pairing function.
     *
     * @param array $kernel The kernel.
     *
     * @return array The hashed kernel.
     */
    public static function hashKernel(array $kernel)
    {
        $kernel = array_map(function ($tuple) {
            list ($car, $cdr) = $tuple;

            return ($car + $cdr) * ($car + $cdr + 1) / 2 + $cdr;
        }, $kernel);

        sort($kernel);

        return $kernel;
    }
}
