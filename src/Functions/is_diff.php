<?php

namespace Aop\LALR\Functions;

/**
 * Determines whether two sets have a difference.
 *
 * @param array $first The first set.
 * @param array $second The second set.
 *
 * @return boolean Whether there is a difference.
 */
function is_diff(array $first, array $second): bool {
    return count(array_diff($first, $second)) !== 0;
}
