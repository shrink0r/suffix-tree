<?php

namespace Shrink0r\SuffixTree\Builder;

use Shrink0r\SuffixTree\Builder\NodeInterface;
use Shrink0r\SuffixTree\Builder\NodeTrait;
use Shrink0r\SuffixTree\Builder\SuffixTreeBuilder;

final class LeafNode implements NodeInterface
{
    use NodeTrait;

    /**
     * @var int
     */
    public $start;
    /**
     * @var int
     */
    public $suffix_idx = -1;
    /**
     * @var SuffixTreeBuilder
     */
    private $builder;

    /**
     * @param int $start
     * @param SuffixTreeBuilder $builder
     */
    public function __construct(int $start, SuffixTreeBuilder $builder)
    {
        $this->builder = $builder;
        $this->start = $start;
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
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
