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

    /**
     * @test
     */
    public function setAndRemoveNode(): void
    {
        $this->assertFalse($this->node->hasNode('test'));
        $this->node->setNode('test', new Node());
        $this->assertTrue($this->node->hasNode('test'));
        $this->node->removeNode('test');
        $this->assertFalse($this->node->hasNode('test'));
    }

    /**
     * @test
     */
    public function getAttributeReturnsAttribute(): void
    {
        $this->assertEquals(1, $this->node->getAttribute('attr_1'));
        $this->assertEquals(2, $this->node->getAttribute('attr_2'));
    }

    /**
     * @test
     * @expectedException \Aop\LALR\Exception\RuntimeException
     */
    public function getAttributeThrowsExceptionIfThereIsNoAttribute(): void
    {
        $this->node->getAttribute('not_existing');
    }
}
