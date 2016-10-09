<?php

namespace Shrink0r\SuffixTree\Tests\Unit;

use Shrink0r\SuffixTree\InternalNode;
use Shrink0r\SuffixTree\Tests\Unit\TestCase;

class InternalNodeTest extends TestCase
{
    private $internal_node;

    protected function setUp()
    {
        $this->internal_node = new InternalNode(3, 5);
    }

    public function testGetSuffixLink()
    {
        $this->assertNull($this->internal_node->getSuffixLink());
    }

    public function getSuffixIdx()
    {
        $this->assertEquals(-1, $this->internal_node->getSuffixIdx());
    }

    public function testGetStart()
    {
        $this->assertEquals(3, $this->internal_node->getStart());
    }

    public function testGetEnd()
    {
        $this->assertEquals(5, $this->internal_node->getEnd());
    }

    public function testGetChildren()
    {
        $this->assertEmpty($this->internal_node->getChildren());
    }

    public function testGetEdgeSize()
    {
        $this->assertEquals(3, $this->internal_node->getEdgeSize());
    }
}
