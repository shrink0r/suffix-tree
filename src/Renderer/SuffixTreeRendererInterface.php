<?php

namespace Shrink0r\SuffixTree\Renderer;

use Shrink0r\SuffixTree\SuffixTree;

interface SuffixTreeRendererInterface
{
    /**
     * @param  SuffixTree $tree
     *
     * @return string
     */
    public function render(SuffixTree $tree): string;
}
