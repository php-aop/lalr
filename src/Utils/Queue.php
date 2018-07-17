<?php

declare(strict_types=1);

namespace Aop\LALR\Utils;

final class Queue extends \SplQueue
{
    public function __construct(array $elements = [])
    {
        foreach ($elements as $element) {
            parent::enqueue($element);
        }
    }
}
