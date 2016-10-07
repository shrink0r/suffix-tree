<?php

namespace Shrink0r\SuffixTree\Builder;

use Shrink0r\SuffixTree\Builder\NodeTrait;
use Shrink0r\SuffixTree\Builder\TreeBuilder;
use Shrink0r\SuffixTree\NodeInterface;

final class LeafNode implements NodeInterface
{
    use NodeTrait;

    public $start;
    public $suffix_idx = -1;

    private $builder;

    public function __construct(int $start, TreeBuilder $builder)
    {
        $this->builder = $builder;
        $this->start = $start;
    }

    public function __get($property)
    {
        if ($property === 'end') {
            return $this->builder->getLeafPos();
        }
        if ($property === 'suffix_link') {
            return null;
        }
        if ($property === 'children') {
            return [];
        }
    }
}
