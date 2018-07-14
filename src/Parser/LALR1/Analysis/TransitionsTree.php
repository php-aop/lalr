<?php

declare(strict_types=1);

namespace Aop\LALR\Parser\LALR1\Analysis;

use function Aop\LALR\Functions\hash_state_transitions;

/**
 * A BST implementation for more efficient lookup
 * of states by their transition map items.
 *
 * @internal
 */
final class TransitionsTree
{
    /**
     * @var int
     */
    private $next = 0;

    /**
     * @var \stdClass
     */
    private $root;

    /**
     * Inserts a new node in the BST and returns
     * the number of the new state if no such state
     * exists. Otherwise, returns the number of the
     * existing state.
     *
     * @param array $transitionsMap The transitions map.
     *
     * @return int The state number.
     */
    public function insert(array $transitionsMap): int
    {
        $hash = hash_state_transitions($transitionsMap);
        $node = (object) [
            'hash'  => $hash,
            'next'  => $this->next,
            'left'  => null,
            'right' => null,
        ];

        if ($this->root === null) {

            $this->root = $node;

            $this->next++;

            return $this->root->next;
        }

        $current = $this->root;

        while (true) {

            if ($hash < $current->hash) {

                if ($current->left === null) {

                    $node->next    = $this->next;
                    $current->left = $node;

                    $this->next++;

                    return $current->left->next;
                }

                $current = $current->left;

                continue;

            }

            if ($hash > $current->hash) {

                if ($current->right === null) {

                    $node->next    = $this->next;
                    $current->right = $node;

                    $this->next++;

                    return $current->right->next;
                }

                $current = $current->right;

                continue;
            }

            return $current->next;
        }
    }
}
