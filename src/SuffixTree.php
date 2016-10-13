<?php

namespace Shrink0r\SuffixTree;

use Shrink0r\SuffixTree\InternalNode;
use Shrink0r\SuffixTree\LeafNode;
use Shrink0r\SuffixTree\RootNode;

final class SuffixTree
{
    /**
     * @var string $S
     */
    private $S;
    /**
     * @var RootNode $root
     */
    private $root;
    /**
     * @var int $length
     */
    private $length;
    /**
     * @var string $lrs
     */
    private $lrs;
    /**
     * @var string $non_overlapping_lrs
     */
    private $non_overlapping_lrs;
    /**
     * @var string[] $suffix_array
     */
    private $suffix_array;

    /**
     * @param RootNode $root
     * @param string $S
     */
    public function __construct(RootNode $root, string $S)
    {
        $this->S = $S;
        $this->root = $root;
        $this->length = strlen($this->S);
    }

    /**
     * @param  string $substring
     *
     * @return bool
     */
    public function hasSubstring(string $substring): bool
    {
        return $this->matchSuffixPath($this->getRoot(), $substring, -1) !== -1;
    }

    /**
     * @param  string $suffix
     *
     * @return bool
     */
    public function hasSuffix(string $suffix): bool
    {
        return $this->matchSuffixPath($this->getRoot(), $suffix, -1) === 2;
    }

    /**
     * @return string
     */
    public function findNonOverlappingLrs(): string
    {
        if ($this->non_overlapping_lrs === null) {
            list($start_pos, $length) = $this->dfsLongestRepetition($this->getRoot(), 0, [ 0, 0 ]);
            $this->non_overlapping_lrs = substr($this->getS(), $start_pos - 1, $length);
        }
        return $this->non_overlapping_lrs;
    }

    /**
     * @return string
     */
    public function findLrs(): string
    {
        if ($this->lrs === null) {
            list($start_pos, $length) = $this->dfsLongestRepetition($this->getRoot(), 0, [ 0, 0 ], true);
            $this->lrs = substr($this->getS(), $start_pos - 1, $length);
        }
        return $this->lrs;
    }

    /**
     * @return string[]
     */
    public function getSuffixArray(): array
    {
        if ($this->suffix_array === null) {
            $this->suffix_array = $this->dfsSuffixes($this->getRoot(), 0);
        }
        return $this->suffix_array;
    }

    /**
     * @return RootNode
     */
    public function getRoot(): RootNode
    {
        return $this->root;
    }

    /**
     * @return string
     */
    public function getS(): string
    {
        return $this->S;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param  NodeInterface $node
     * @param  string $path
     * @param  int $i
     *
     * @return int
     */
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

    /**
     * @param string $s
     * @param int $i
     * @param int $start
     * @param int $end
     *
     * @return int -1 = 'fell-off', 0 = 'not-finished', 1 = 'partial-match', 2 = 'full-match'
     */
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

    /**
     * @param  NodeInterface $node
     * @param  int $path_size
     * @param  int $max_depth
     * @param  int $start_pos
     *
     * @return int[] int tuple containing max_depth and start_pos
     */
    private function dfsLongestRepetition(NodeInterface $node, int $path_size, array $slice, $overlap = false): array
    {
        if ($node instanceof LeafNode && $slice[1] < $path_size - $node->getEdgeSize()) {
            $slice = [ $node->getSuffixIdx(), $path_size - $node->getEdgeSize() ];
        } else {
            $prev_slice = $slice;
            foreach ($node->getChildren() as $child_node) {
                $slice = $this->dfsLongestRepetition(
                    $child_node,
                    $path_size + $child_node->getEdgeSize(),
                    $slice,
                    $overlap
                );
            }
            if (!$overlap && $slice[1] > $prev_slice[1] && $node instanceof InternalNode
                && $path_size > $node->getMaxSuffixIdx() - $node->getMinSuffixIdx()
            ) { // prevent overlap when not allowed
                $slice = $prev_slice;
            }
        }

        return $slice;
    }

    /**
     * @param  NodeInterface $node
     * @param  int $path_size
     * @param  string[] $suffixes
     *
     * @return string[]
     */
    private function dfsSuffixes(NodeInterface $node, int $path_size, array $suffixes = []): array
    {
        if ($node instanceof LeafNode) {
            $suffixes[$node->getSuffixIdx() - 1] = substr($this->getS(), $node->getSuffixIdx() - 1, $path_size);
        } else {
            foreach ($node->getChildren() as $child_node) {
                $suffixes = $this->dfsSuffixes(
                    $child_node,
                    $path_size + $child_node->getEdgeSize(),
                    $suffixes
                );
            }
        }

        return $suffixes;
    }
}
