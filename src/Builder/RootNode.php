<?php

namespace Shrink0r\SuffixTree\Builder;

use Shrink0r\SuffixTree\Builder\NodeTrait;
use Shrink0r\SuffixTree\NodeInterface;

final class RootNode implements NodeInterface
{
    use NodeTrait;

    public $children = [];

    public function __get($property)
    {
        if ($property === 'end') {
            return -1;
        }
        if (in_array($property, [ 'start', 'end', 'suffix_idx'])) {
            return -1;
        }
        if ($property === 'suffix_link') {
            return null;
        }
    }
}
