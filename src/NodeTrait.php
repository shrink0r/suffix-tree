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
     * @param int $start
     * @param int $end
     * @param array $children
     * @param int $suffix_idx
     * @param NodeInterface $suffix_link
     */
    public function __construct(int $start, int $end, array $children, int $suffix_idx = -1, $suffix_link = null)
    {
        $this->start = $start;
        $this->end = $end;
        $this->children = $children;
        $this->suffix_idx = $suffix_idx;
        $this->suffix_link = $suffix_link;
    }

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
}
