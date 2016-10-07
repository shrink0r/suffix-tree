<?php

namespace Shrink0r\SuffixTree\Builder;

trait NodeTrait
{
    public function getEdgeSize(): int
    {
        return $this->end - $this->start + 1;
    }

    public function getSuffixLink()
    {
        return $this->suffix_link;
    }

    public function getSuffixIdx(): int
    {
        return $this->suffix_idx;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    public function __toString()
    {
        return spl_object_hash($this);
    }
}
