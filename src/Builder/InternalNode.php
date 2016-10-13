<?php

namespace Shrink0r\SuffixTree\Builder;

use Shrink0r\SuffixTree\Builder\NodeTrait;
use Shrink0r\SuffixTree\Builder\NodeInterface;

final class InternalNode implements NodeInterface
{
    use NodeTrait;

    /**
     * @var NodeInterface[]
     */
    public $children = [];
    /**
     * @var NodeInterface
     */
    public $suffix_link;
    /**
     * @var int
     */
    public $start;
    /**
     * @var int
     */
    public $end;

    /**
     * @param int $start
     * @param int $end
     */
    public function __construct(int $start, int $end)
    {
        $this->suffix_link = null;
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if ($property === 'suffix_idx') {
            return -1;
        }
    }
}
