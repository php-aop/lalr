<?php

namespace Aop\LALR\Exception;

class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface, \Psr\SimpleCache\InvalidArgumentException
{
    public static function create($expected, $received): InvalidArgumentException
    {
        $expected = (array) $expected;
        $received = \is_object($received) ? \get_class($received) : \gettype($received);

        return new static(sprintf('Expected "%s", received "%s".', implode('", "', $expected), $received));
    }
}
