<?php

declare(strict_types=1);

namespace Aop\LALR\Tests\Parser\LALR1\Analysis;

use Aop\LALR\Parser\LALR1\Analysis\TransitionsTree;
use PHPUnit\Framework\TestCase;

use function Aop\LALR\Functions\hash_state_transitions;

final class TransitionsTreeTest extends TestCase
{
    /**
     * @test
     */
    public function kernelsShouldBeProperlyHashedAndOrdered(): void
    {
        $this->assertEquals([1, 3, 6, 7], hash_state_transitions([
            [2, 1],
            [1, 0],
            [2, 0],
            [3, 0],
        ]));
    }

    /**
     * @test
     */
    public function insertShouldInsertANewNodeIfNoIdenticalKernelExists(): void
    {
        $set = new TransitionsTree();

        $this->assertEquals(0, $set->insert([
            [2, 1],
        ]));

        $this->assertEquals(1, $set->insert([
            [2, 2],
        ]));

        $this->assertEquals(2, $set->insert([
            [1, 1],
        ]));

        $this->assertEquals(0, $set->insert([
            [2, 1],
        ]));
    }
}
