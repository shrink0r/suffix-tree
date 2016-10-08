<?php

namespace Shrink0r\SuffixTree\Renderer;

use Shrink0r\SuffixTree\SuffixTree;

interface SuffixTreeRendererInterface
{
    public function render(SuffixTree $tree);
}
