<?php

namespace Shrink0r\SuffixTree;

interface NodeInterface
{
    public function getEdgeSize(): int;

    public function getStart(): int;

    public function getEnd(): int;

    public function getSuffixLink();

    public function getSuffixIdx(): int;

    public function getChildren(): array;
}
