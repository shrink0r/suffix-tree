<?php

namespace Shrink0r\SuffixTree\Builder;

use Shrink0r\SuffixTree\Builder\NodeTrait;
use Shrink0r\SuffixTree\NodeInterface;

final class InternalNode implements NodeInterface
{
    use NodeTrait;

    public $children = [];
    public $suffix_link;
    public $start;
    public $end;

    public function __construct(int $start, int $end)
    {
        $this->suffix_link = null;
        $this->start = $start;
        $this->end = $end;
    }

    public function __get($property)
    {
        if ($property === 'suffix_idx') {
            return -1;
        }
    }
}
