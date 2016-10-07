<?php

namespace Shrink0r\SuffixTree\Builder;

use Shrink0r\SuffixTree\Builder\InternalNode;
use Shrink0r\SuffixTree\Builder\LeafNode;
use Shrink0r\SuffixTree\Builder\RootNode;
use Shrink0r\SuffixTree\NodeInterface;
use Shrink0r\SuffixTree\SuffixTree;
use Shrink0r\SuffixTree\LeafNode as Leaf;
use Shrink0r\SuffixTree\InternalNode as Internal;
use Shrink0r\SuffixTree\RootNode as Root;

final class SuffixTreeBuilder
{
    // active-point state
    private $active_edge = -1;
    private $active_length = 0;
    private $active_node;
    // is used to update all leafs, when applying rule nr. 1
    private $leaf_pos = -1;
    // length of our input S
    private $length;
    // previously created internal-node
    private $prev_int_node;
    // the tree's root-node
    private $root;
    // position at which we split an active-edge when applying rule nr. 2
    private $split_pos = -1;
    // holds the number of (atm implicit)extension-suffixes pending to be added
    private $suffixes_to_add = 0;
    // holds the input string
    private $S;

    public function __construct(string $S)
    {
        $this->S = $S;
        $this->length = strlen($S);
        $this->root = new RootNode;
    }

    public function build()
    {
        $this->active_node = $this->root;
        for ($i = 0; $i < $this->length; $i++) {
            $this->appendSuffix($i);
        }

        $suffix_link_map = [];
        $children = $this->buildFixSubtrees($this->root, $suffix_link_map);
        $root = new Root($this->root->start, $this->root->end, $children);

        return new SuffixTree($root, $this->S);
    }

    public function getLeafPos()
    {
        return $this->leaf_pos;
    }

    private function appendSuffix(int $i)
    {
        // initialize phase state
        $this->prev_int_node = null;
        $this->suffixes_to_add++;
        // extension rule nr. 1: append suffix($i) to leaf nodes
        $this->leaf_pos = $i;
        // add remaining suffixes to tree by applying extension rules nr. 2 and 3
        while ($this->suffixes_to_add > 0) {
            if ($this->active_length === 0) {
                $this->active_edge = $i;
            }
            if (!isset($this->active_node->children[$this->S{$this->active_edge}])) {
                // extension rule nr. 2: current suffix is not yet in tree
                // so create new leaf node for current (final) extension-suffix
                $this->active_node->children[$this->S{$this->active_edge}] = new LeafNode($i, $this);
                // create a suffix-link from internal-node of previous extension to root
                if ($this->prev_int_node !== null) {
                    $this->prev_int_node->suffix_link = $this->active_node;
                    $this->prev_int_node = null;
                }
            } else {
                // current suffix start is in tree, check if the whole thing is or if we'll fall off an edge
                $next = $this->active_node->children[$this->S{$this->active_edge}];
                if ($this->skipCountEdge($next)) {
                    continue;
                }
                if ($this->S{$next->start + $this->active_length} === $this->S{$i}) {
                    // extension rule nr- 3: current suffix is already in tree
                    // do nothing and end current phase after adding suffix links
                    if ($this->prev_int_node !== null && $this->active_node !== $this->root) {
                        $this->prev_int_node->suffix_link = $this->active_node;
                        $this->prev_int_node = null;
                    }
                    $this->active_length++;
                    break;
                }
                // we fell off the tree, so we need to split the edge at the "fall off position"
                $this->split_pos = $next->start + $this->active_length - 1;
                // insert a new edge(internal-node), that ends at the "fall off pos" in order to split the active-edge
                $split = new InternalNode($next->start, $this->split_pos);
                $this->active_node->children[$this->S{$this->active_edge}] = $split;
                // insert a new leaf node for current extension-suffix
                $split->children[$this->S{$i}] = new LeafNode($i, $this);
                $next->start += $this->active_length;
                $split->children[$this->S{$next->start}] = $next;
                // create a suffix-link to any prev. created internal-node to new internal-node
                if ($this->prev_int_node !== null) {
                    $this->prev_int_node->suffix_link = $split;
                }
                // setup prev. internal-node for next extension
                $this->prev_int_node = $split;
            }
            // decrement suffix count after successfully appending extension-suffix,
            // then prep state for next extension phase
            $this->suffixes_to_add--;
            if ($this->active_node === $this->root && $this->active_length > 0) {
                $this->active_length--;
                $this->active_edge = $i - $this->suffixes_to_add + 1;
            } else if ($this->active_node !== $this->root) {
                $this->active_node = $this->active_node->suffix_link ?: $this->root;
            }
        }
    }

    private function skipCountEdge(NodeInterface $node): bool
    {
        // skip-count-trick:
        // skip nodes with a label-size smaller than active-length,
        // then adjust active-length relative to the next node.
        $edge_size = $node->getEdgeSize();
        if ($this->active_length >= $edge_size) {
            $this->active_edge += $edge_size;
            $this->active_length -= $edge_size;
            $this->active_node = $node;

            return true;
        }

        return false;
    }

    private function buildFixSubtrees(NodeInterface $node, array &$suffix_link_map, int $path_size = 0)
    {
        $children = [];
        foreach ($node->children as $edge => $child_node) {
            if ($child_node instanceof LeafNode) {
                $children[$edge] = new Leaf(
                    $child_node->start,
                    $child_node->end,
                    [],
                    $this->length - ($path_size + $child_node->getEdgeSize()) + 1
                );
            } else { // InternalNode
                $grand_children = $this->buildFixSubtrees(
                    $child_node,
                    $suffix_link_map,
                    $path_size + $child_node->getEdgeSize()
                );
                $children[$edge] = new Internal(
                    $child_node->start,
                    $child_node->end,
                    $grand_children,
                    -1
                );
                if ($child_node->suffix_link) {
                    $suffix_link_map[(string)$child_node->suffix_link] = $children[$edge];
                }
                if (isset($suffix_link_map[(string)$child_node])) {
                    // todo: find out way to prevent mutating node state here, need to get the new node ref
                    // into the proper place within the new tree
                    $suffix_link_map[(string)$child_node]->withSuffixLink($children[$edge]);
                }
            }
        }

        return $children;
    }
}
