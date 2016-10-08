<?php

namespace Shrink0r\SuffixTree;

use Shrink0r\SuffixTree\LeafNode;
use Shrink0r\SuffixTree\NodeInterface;
use Shrink0r\SuffixTree\NodeTrait;

final class InternalNode implements NodeInterface
{
    use NodeTrait;

    /**
     * @param int $start
     * @param int $end
     * @param array $children
     * @param NodeInterface $suffix_link
     */
    public function __construct(int $start, int $end, array $children = [], $suffix_link = null)
    {
        if ($suffix_link !== null && $suffix_link->getSuffixIdx() !== -1) {
            throw new \Exception("Trying to link non-internal/root node.");
        }
        $this->start = $start;
        $this->end = $end;
        $this->children = $children;
        $this->suffix_link = $suffix_link;

        $this->suffix_idx = -1;
    }

    /**
     * todo: need to make this return a cloned instance rather than mutating state,
     * but the TreeBuilder relies on this effect atm
     *
     * @param  InternalNode $link_target [description]
     *
     * @return self
     */
    public function withSuffixLink(InternalNode $link_target): self
    {
        if ($this->suffix_link !== null) {
            throw new \Exception("Trying to link node more than once.");
        }
        $this->suffix_link = $link_target;

        return $this;
    }
}
