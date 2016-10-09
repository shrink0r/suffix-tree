<?php

namespace Shrink0r\SuffixTree\Tests\Unit;

use Shrink0r\SuffixTree\RootNode;
use Shrink0r\SuffixTree\Tests\Unit\TestCase;

class RootNodeTest extends TestCase
{
    private $root_node;

    protected function setUp()
    {
        $this->root_node = new RootNode;
    }

    public function testGetSuffixLink()
    {
        $this->assertNull($this->root_node->getSuffixLink());
    }

    public function getSuffixIdx()
    {
        $this->assertEquals(-1, $this->root_node->getSuffixIdx());
    }

    public function testGetStart()
    {
        $this->assertEquals(-1, $this->root_node->getStart());
    }

    public function testGetEnd()
    {
        $this->assertEquals(-1, $this->root_node->getEnd());
    }

    public function testGetChildren()
    {
        $this->assertEmpty($this->root_node->getChildren());
    }

    public function testGetEdgeSize()
    {
        $this->assertEquals(1, $this->root_node->getEdgeSize());
    }
}
