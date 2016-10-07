<?php

namespace Shrink0r\SuffixTree;

use Shrink0r\SuffixTree\InternalNode;
use Shrink0r\SuffixTree\RootNode;

final class SuffixTree
{
    private $S;
    private $root;
    private $length;

    public function __construct(RootNode $root, string $S)
    {
        $this->S = $S;
        $this->root = $root;
        $this->length = strlen($this->S);
    }

    public function hasSubstring(string $substring): bool
    {
        return $this->matchPath($this->getRoot(), $substring, -1) !== -1;
    }

    public function hasSuffix(string $suffix): bool
    {
        return $this->matchPath($this->getRoot(), $suffix, -1) === 2;
    }

    public function findLongestRepetition(): string
    {
        list($node_depth, $substring_start) = $this->dfsDeeptestInternalNode($this->getRoot(), 0, 0, 0);

        if ($substring_start - $node_depth > 0) {
            return substr($this->getS(), $substring_start, $node_depth);
        }
        return '';
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function getS()
    {
        return $this->S;
    }

    public function getLength()
    {
        return $this->length;
    }

    private function matchPath(NodeInterface $node, string $path, int $idx): int
    {
        $match_result = -1;
        if ($node->getStart() !== -1) {
            $match_result = $this->walkEdge($path, $idx, $node->getStart(), $node->getEnd());
            if ($match_result !== 0) {
                return $match_result;
            }
        }

        $idx = $idx + $node->getEdgeSize();
        $children = $node->getChildren();
        if (isset($children[$path[$idx]])) {
            return $this->matchPath($children[$path[$idx]], $path, $idx);
        } else {
            return -1;  // no match
        }
    }

    // return -1 = 'no match', 0 = 'more edges', 1 = 'edge-match', 2 = 'leaf-match'
    private function walkEdge(string $s, int $idx, int $start, int $end): int
    {
        $text = $this->getS();
        for ($k = $start; $k <= $end && $idx < strlen($s); $k++, $idx++) {
            if ($text{$k} !== $s{$idx}) {
                return -1;
            }
        }
        if (strlen($s) === $idx) {
            return ($k <= $end) ? 1 : 2;
        }
        return 0;
    }

    private function dfsDeeptestInternalNode(NodeInterface $node, int $path_size, int $max_depth, int $start_pos): array
    {
        if ($node->getSuffixIdx() === -1) {
            foreach ($node->getChildren() as $child_node) {
                list($max_depth, $start_pos) = $this->dfsDeeptestInternalNode(
                    $child_node,
                    $path_size + $child_node->getEdgeSize(),
                    $max_depth,
                    $start_pos
                );
            }
        } elseif ($node->getSuffixIdx() > -1 && ($max_depth < $path_size - $node->getEdgeSize()) ) {
            $max_depth = $path_size - $node->getEdgeSize();
            $start_pos = $node->getSuffixIdx() - 1;
        }

        return [ $max_depth, $start_pos ];
    }
}
