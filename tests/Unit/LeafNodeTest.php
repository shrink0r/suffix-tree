<?php

namespace Shrink0r\SuffixTree\Tests\Unit;

use Shrink0r\SuffixTree\LeafNode;
use Shrink0r\SuffixTree\Tests\Unit\TestCase;

class LeafNodeTest extends TestCase
{
    protected function setUp()
    {
        $this->leaf_node = new LeafNode(7, 8, 2);
    }

    public function testGetSuffixLink()
    {
        $this->assertNull($this->leaf_node->getSuffixLink());
    }

    public function getSuffixIdx()
    {
        $this->assertEquals(-1, $this->leaf_node->getSuffixIdx());
    }

    public function testGetStart()
    {
        $this->assertEquals(7, $this->leaf_node->getStart());
    }

    public function testGetEnd()
    {
        $this->assertEquals(8, $this->leaf_node->getEnd());
    }

    public function testGetChildren()
    {
        $this->assertEmpty($this->leaf_node->getChildren());
    }

    public function testGetEdgeSize()
    {
        $this->assertEquals(2, $this->leaf_node->getEdgeSize());
    }
}
