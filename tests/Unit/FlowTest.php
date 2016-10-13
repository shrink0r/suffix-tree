<?php

namespace Shrink0r\SuffixTree\Tests\Unit;

use Shrink0r\SuffixTree\Builder\SuffixTreeBuilder;
use Shrink0r\SuffixTree\Tests\Unit\TestCase;

class FlowTest extends TestCase
{
    private $tree_builder;

    protected function setUp()
    {
        $this->tree_builder = new SuffixTreeBuilder;
    }

    /**
     * @dataProvider provideFlowFixtures
     */
    public function testOverallFlow(string $s, string $lrs, string $lrs_no, array $suffix, array $substring)
    {
        $suffix_tree = $this->tree_builder->build($s);
        $this->assertEquals($lrs, $suffix_tree->findLrs());
        $this->assertEquals($lrs_no, $suffix_tree->findNonOverlappingLrs());
        $this->assertTrue($suffix_tree->hasSuffix($suffix['true']));
        $this->assertFalse($suffix_tree->hasSuffix($suffix['false']));
        $this->assertTrue($suffix_tree->hasSubstring($substring['true']));
        $this->assertFalse($suffix_tree->hasSubstring($substring['false']));
    }

    /**
     * @codeCoverageIgnore
     */
    public static function provideFlowFixtures()
    {
        return [
            [
                's' => 'mississippi$',
                'lrs' => 'issi',
                'lrs_no' => 'ssi',
                'suffix' => [ 'true' => 'sippi$', 'false' => 'miss$' ],
                'substring' => [ 'true' => 'iss', 'false' => 'issm' ]
            ],
            [
                's' => 'GEEKSFORGEEKS$',
                'lrs' => 'GEEKS',
                'lrs_no' => 'GEEKS',
                'suffix' => [ 'true' => 'EKS$', 'false' => 'GE$' ],
                'substring' => [ 'true' => 'EEK', 'false' => 'SKG' ]
            ],
            [
                's' => 'xabxac$',
                'lrs' => 'xa',
                'lrs_no' => 'xa',
                'suffix' => [ 'true' => 'ac$', 'false' => 'xa$' ],
                'substring' => [ 'true' => 'bxa', 'false' => 'cba' ]
            ]
        ];
    }
}
