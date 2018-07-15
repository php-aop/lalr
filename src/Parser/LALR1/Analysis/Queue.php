<?php

declare(strict_types=1);

namespace Aop\LALR\Parser\LALR1\Analysis;

final class Queue extends \SplQueue
{
    public function __construct(array $elements = [])
    {
        foreach ($elements as $element) {
            parent::enqueue($element);
        }
    }
}
