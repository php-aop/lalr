<?php

namespace Aop\LALR\Parser\LALR1\Analysis\KernelSet;

final class Node
{
    public $kernel;
    public $number;

    public $left;
    public $right;

    public function __construct(array $hashedKernel, $number)
    {
        $this->kernel = $hashedKernel;
        $this->number = $number;
    }
}
