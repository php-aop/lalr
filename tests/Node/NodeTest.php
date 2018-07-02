<?php

namespace Aop\LALR\Tests\Node;

use Aop\LALR\Contract\NodeInterface;
use Aop\LALR\Node\Node;
use PHPUnit\Framework\TestCase;

final class NodeTest extends TestCase
{
    /**
     * @var \Aop\LALR\Contract\NodeInterface
     */
    private $node;

    public function setUp(): void
    {
        $this->node = new Node([
            'attr_1' => 1,
            'attr_2' => 2,
        ], [
            'left'  => new Node([
                'sub_child' => new Node(),
            ]),
            'right' => new Node(),
        ]);
    }

    /**
     * @test
     */
    public function getNodesReturnsDirectSiblings(): void
    {
        $this->assertCount(2, $this->node->getNodes());
    }

    /**
     * @test
     */
    public function hasNodeChecksForChildExistence(): void
    {
        $this->assertTrue($this->node->hasNode('left'));
        $this->assertTrue($this->node->hasNode('right'));
        $this->assertFalse($this->node->hasNode('middle'));
    }

    /**
     * @test
     */
    public function getNodeWillReturnChild(): void
    {
        $this->assertInstanceOf(NodeInterface::class, $this->node->getNode('left'));
    }

    /**
     * @test
     * @expectedException \Aop\LALR\Exception\RuntimeException
     */
    public function getNodeWillThrowExceptionIfThereIsNoNode(): void
    {
        $this->node->getNode('middle');
    }
}
