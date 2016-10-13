<?php

namespace Shrink0r\SuffixTree;

trait NodeTrait
{
    /**
     * @var int $start
     */
    private $start;
    /**
     * @var int $end
     */
    private $end;
    /**
     * @var NodeInterface[] $children
     */
    private $children;
    /**
     * @var NodeInterface $suffix_link
     */
    private $suffix_link;
    /**
     * @var int $suffix_idx
     */
    private $suffix_idx;
    /**
     * @var int $min_suffix_idx
     */
    private $min_suffix_idx;
    /**
     * @var int $max_suffix_idx
     */
    private $max_suffix_idx;

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
     * @return int
     */
    public function getMinSuffixIdx(): int
    {
        return $this->min_suffix_idx;
    }

    /**
     * @return int
     */
    public function getMaxSuffixIdx(): int
    {
        return $this->max_suffix_idx;
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
}
