<?php

namespace Shrink0r\SuffixTree;

trait NodeTrait
{
    private $start;
    private $end;
    private $children;
    private $suffix_link;
    private $suffix_idx;

    public function __construct(int $start, int $end, array $children, int $suffix_idx = -1, $suffix_link = null)
    {
        $this->start = $start;
        $this->end = $end;
        $this->children = $children;
        $this->suffix_idx = $suffix_idx;
        $this->suffix_link = $suffix_link;
    }

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
}
