<?php

namespace Shrink0r\SuffixTree;

use Shrink0r\SuffixTree\NodeInterface;
use Shrink0r\SuffixTree\NodeTrait;

final class RootNode implements NodeInterface
{
    use NodeTrait;

    /**
     * @param array $children
     */
    public function __construct(array $children = [])
    {
        $this->children = $children;

        $this->start = -1;
        $this->end = -1;
        $this->suffix_idx = -1;
        $this->suffix_link = null;
        $this->min_suffix_idx = -1;
        $this->max_suffix_idx = -1;
    }
}
