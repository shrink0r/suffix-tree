<?php

namespace Shrink0r\SuffixTree;

interface NodeInterface
{
    /**
     * @return int
     */
    public function getEdgeSize(): int;

    /**
     * @return int
     */
    public function getStart(): int;

    /**
     * @return int
     */
    public function getEnd(): int;

    /**
     * @return NodeInterface
     */
    public function getSuffixLink();

    /**
     * @return int
     */
    public function getSuffixIdx(): int;

    /**
     * @return NodeInterface[]
     */
    public function getChildren(): array;
}
