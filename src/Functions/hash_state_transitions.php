<?php

namespace Aop\LALR\Functions;

/**
 * Hashes a state transitions using a pairing function.
 *
 * @param array $transitionMap The transition map.
 *
 * @return array The hashed transition map.
 */
function hash_state_transitions(array $transitionMap): array
{
    $hashed = array_map(function($transition) {
        [$car, $cdr] = $transition;

        return ($car + $cdr) * ($car + $cdr + 1) / 2 + $cdr;
    }, $transitionMap);

    sort($hashed);

    return $hashed;
}
