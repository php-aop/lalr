<?php

namespace Aop\LALR\Functions;

/**
 * Merges two or more sets by values.
 *
 * {a, b} union {b, c} = {a, b, c}
 *
 * @return array The union of given sets.
 */
function union(): array
{
    return array_unique(call_user_func_array('array_merge', func_get_args()));
}
