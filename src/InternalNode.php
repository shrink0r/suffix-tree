<?php

namespace Shrink0r\SuffixTree;

use Shrink0r\SuffixTree\NodeInterface;
use Shrink0r\SuffixTree\NodeTrait;

final class InternalNode implements NodeInterface
{
    use NodeTrait;

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
