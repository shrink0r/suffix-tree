<?php

namespace Shrink0r\SuffixTree\Builder;

use Shrink0r\SuffixTree\Builder\NodeTrait;
use Shrink0r\SuffixTree\NodeInterface;

final class RootNode implements NodeInterface
{
    use NodeTrait;

    /**
     * @var NodeInterface[] $children
     */
    public $children = [];

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function __get(string $property)
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
