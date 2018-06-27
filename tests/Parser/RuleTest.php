<?php

namespace Aop\LALR\Tests\Parser;

use Aop\LALR\Parser\Rule;
use PHPUnit\Framework\TestCase;

final class RuleTest extends TestCase
{
    /**
     * @test
     */
    public function getComponentShouldReturnNullIfAskedForComponentOutOfRange()
    {
        $rule = new Rule(1, 'Foo', ['x', 'y']);

        $this->assertEquals('y', $rule->getComponent(1));
        $this->assertNull($rule->getComponent(2));
    }
}
