<?php

namespace Shrink0r\SuffixTree;

use Shrink0r\SuffixTree\NodeInterface;
use Shrink0r\SuffixTree\NodeTrait;

final class LeafNode implements NodeInterface
{
    use NodeTrait;

    /**
     * @param int $start
     * @param int $end
     * @param int $suffix_idx
     */
    public function __construct(int $start, int $end, int $suffix_idx = -1)
    {
        $this->start = $start;
        $this->end = $end;
        $this->suffix_idx = $suffix_idx;

        $this->children = [];
        $this->suffix_link = null;
    }
}
