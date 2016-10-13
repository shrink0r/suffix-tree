<?php

namespace Shrink0r\SuffixTree\Tests\Unit;

use Shrink0r\SuffixTree\Builder\SuffixTreeBuilder;
use Shrink0r\SuffixTree\Tests\Unit\TestCase;

class SuffixTreeTest extends TestCase
{
    private $tree_builder;

    protected function setUp()
    {
        $this->tree_builder = new SuffixTreeBuilder;
    }

    /**
     * @dataProvider provideFlowFixtures
     */
    public function testOverallFlow(string $s, string $lrs, array $suffix, array $substring)
    {
        $suffix_tree = $this->tree_builder->build($s);
        $this->assertEquals($lrs, $suffix_tree->findLongestRepetition());
        $this->assertTrue($suffix_tree->hasSuffix($suffix['true']));
        $this->assertFalse($suffix_tree->hasSuffix($suffix['false']));
        $this->assertTrue($suffix_tree->hasSubstring($substring['true']));
        $this->assertFalse($suffix_tree->hasSubstring($substring['false']));
    }

    public function testGetLongestRepetition()
    {
        $suffix_tree = $this->tree_builder->build('mississippi$');
        $this->assertEquals('issi', $suffix_tree->findLongestRepetition(true));
    }

    /**
     * @codeCoverageIgnore
     */
    public static function provideFlowFixtures()
    {
        return [
            [
                's' => 'mississippi$',
                'lrs' => 'iss',
                'suffix' => [ 'true' => 'sippi$', 'false' => 'miss$' ],
                'substring' => [ 'true' => 'iss', 'false' => 'issm' ]
            ],
            [
                's' => 'GEEKSFORGEEKS$',
                'lrs' => 'GEEKS',
                'suffix' => [ 'true' => 'EKS$', 'false' => 'GE$' ],
                'substring' => [ 'true' => 'EEK', 'false' => 'SKG' ]
            ],
            [
                's' => 'xabxac$',
                'lrs' => 'xa',
                'suffix' => [ 'true' => 'ac$', 'false' => 'xa$' ],
                'substring' => [ 'true' => 'bxa', 'false' => 'cba' ]
            ]
        ];
    }
}
