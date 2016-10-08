<?php

namespace Shrink0r\SuffixTree\Builder;

trait NodeTrait
{
    /**
     * @return int
     */
    public function getEdgeSize(): int
    {
        return $this->end - $this->start + 1;
    }

    /**
     * @return NodeInterface|null
     */
    public function getSuffixLink()
    {
        return $this->suffix_link;
    }

    /**
     * @return int
     */
    public function getSuffixIdx(): int
    {
        return $this->suffix_idx;
    }

    /**
     * @return NodeInterface[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @return int
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * @return int
     */
    public function getEnd(): int
    {
        return $this->end;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return spl_object_hash($this);
    }
}
