<?php

namespace Shrink0r\SuffixTree;

use Shrink0r\SuffixTree\InternalNode;
use Shrink0r\SuffixTree\RootNode;

final class SuffixTree
{
    private $S;
    private $root;
    private $length;
    private $longest_repetiton;

    public function __construct(RootNode $root, string $S)
    {
        $this->S = $S;
        $this->root = $root;
        $this->length = strlen($this->S);
    }

    public function hasSubstring(string $substring): bool
    {
        return $this->matchSuffixPath($this->getRoot(), $substring, -1) !== -1;
    }

    public function hasSuffix(string $suffix): bool
    {
        return $this->matchSuffixPath($this->getRoot(), $suffix, -1) === 2;
    }

    public function findLongestRepetition(): string
    {
        if ($this->longest_repetiton === null) {
            list($node_depth, $substring_start) = $this->dfsDeeptestInternalNode($this->getRoot(), 0, 0, 0);
            $this->longest_repetiton = substr($this->getS(), $substring_start, $node_depth);
        }
        return $this->longest_repetiton;
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

    private function matchSuffixPath(NodeInterface $node, string $path, int $i): int
    {
        $match_result = -1;
        if (!$node instanceof RootNode) {
            $match_result = $this->walkEdge($path, $i, $node->getStart(), $node->getEnd());
            if ($match_result !== 0) {
                return $match_result;
            }
        }

        $i = $i + $node->getEdgeSize();
        $children = $node->getChildren();
        if (isset($children[$path[$i]])) {
            return $this->matchSuffixPath($children[$path[$i]], $path, $i);
        } else {
            return -1;
        }
    }

    // return -1 = 'fell-off', 0 = 'not-finished', 1 = 'partial-match', 2 = 'full-match'
    private function walkEdge(string $s, int $i, int $start, int $end): int
    {
        $text = $this->getS();
        for ($k = $start; $k <= $end && $i < strlen($s); $k++, $i++) {
            if ($text{$k} !== $s{$i}) {
                return -1;
            }
        }
        if (strlen($s) === $i) {
            return $k > $end ? 2 : 1;
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
