<?php

namespace Shrink0r\SuffixTree\Builder;

use Shrink0r\SuffixTree\Builder\BuilderInterface;
use Shrink0r\SuffixTree\Builder\InternalNode;
use Shrink0r\SuffixTree\Builder\LeafNode;
use Shrink0r\SuffixTree\Builder\RootNode;
use Shrink0r\SuffixTree\InternalNode as Internal;
use Shrink0r\SuffixTree\LeafNode as Leaf;
use Shrink0r\SuffixTree\Builder\SuffixTreeBuilder;
use Shrink0r\SuffixTree\RootNode as Root;
use Shrink0r\SuffixTree\SuffixTree;

final class SuffixTreeBuilder implements BuilderInterface
{
    // active-point state
    /**
     * @var int $active_edge
     */
    private $active_edge = -1;
    /**
     * @var int $active_length
     */
    private $active_length = 0;
    /**
     * @var NodeInterface $active_node
     */
    private $active_node;
    /**
     * is used to update all leafs, when applying rule 1
     * @var int $leaf_pos
     */
    private $leaf_pos = -1;
    /**
     * length of our input S
     * @var int $length
     */
    private $length;
    /**
     * previously created internal-node
     * @var NodeInterface $active_node
     */
    private $prev_int_node;
    /**
     * the tree's root-node
     * @var RootNode $root
     */
    private $root;
    /**
     * position at which we split an active-edge when creating a new internal node (applying rule 2)
     * @var integer $split_pos
     */
    private $split_pos = -1;
    /**
     * holds the number of (atm implicit)extension-suffixes pending to be added
     * @var integer $suffixes_to_add
     */
    private $suffixes_to_add = 0;
    /**
     * holds the input string
     * @var string $S
     */
    private $S;

    /**
     * @return SuffixTree
     */
    public function build(string $S): SuffixTree
    {
        $this->S = $S;
        $this->length = strlen($S);
        $this->root = new RootNode;
        $this->suffixes_to_add = 0;
        $this->active_length = 0;
        $this->split_pos = -1;
        $this->leaf_pos = -1;
        $this->active_edge = -1;

        $this->active_node = $this->root;
        for ($i = 0; $i < $this->length; $i++) {
            $this->appendSuffix($i);
        }
        list($children, $smin, $smax) = $this->transferChildren($this->root);

        return new SuffixTree(new Root($children), $this->S);
    }

    /**
     * @return int
     */
    public function getLeafPos(): int
    {
        return $this->leaf_pos;
    }

    /**
     * @param int $i
     */
    private function appendSuffix(int $i)
    {
        // initialize phase state
        $this->prev_int_node = null;
        $this->suffixes_to_add++;
        // -> extension rule 1 <-
        // append suffix($i) to leaf nodes
        $this->leaf_pos = $i;
        // add remaining suffixes to tree by applying extension rules 2 and 3
        while ($this->suffixes_to_add > 0) {
            if ($this->active_length === 0) {
                $this->active_edge = $i;
            }
            if (!isset($this->active_node->children[$this->S{$this->active_edge}])) {
                // -> extension rule 2 <-
                // current suffix is not yet in tree
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
                    // -> extension rule 3 <-
                    // current suffix is already in tree; do nothing and end current phase after adding suffix-link
                    if ($this->prev_int_node !== null && $this->active_node !== $this->root) {
                        $this->prev_int_node->suffix_link = $this->active_node;
                        $this->prev_int_node = null;
                    }
                    $this->active_length++;
                    break;
                }
                // -> extension rule 2 <-
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
            } elseif ($this->active_node !== $this->root) {
                $this->active_node = $this->active_node->suffix_link ?: $this->root;
            }
        }
    }

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
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

    /**
     * @param NodeInterface $node
     * @param array $node_map
     * @param array $lazy_links
     * @param int $path_size
     *
     * @return NodeInterface[]
     */
    private function transferChildren(
        NodeInterface $node,
        array &$node_map = [],
        array &$lazy_links = [],
        int $path_size = 0
    ): array {
        $children = [];
        $suffix_min = -1;
        $suffix_max = $suffix_min;
        foreach ($node->children as $edge => $child_node) {
            if ($child_node instanceof LeafNode) {
                $new_node = new Leaf(
                    $child_node->start,
                    $child_node->end,
                    $this->length - ($path_size + $child_node->getEdgeSize()) + 1
                );
                if ($suffix_min === -1) {
                    $suffix_min = $new_node->getSuffixIdx();
                    $suffix_max = $suffix_min;
                } else {
                    $suffix_min = min($suffix_min, $new_node->getSuffixIdx());
                    $suffix_max = max($suffix_max, $new_node->getSuffixIdx());
                }
            } else {
                list($grand_children, $smin, $smax) = $this->transferChildren(
                    $child_node,
                    $node_map,
                    $lazy_links,
                    $path_size + $child_node->getEdgeSize()
                );
                $suffix_node = null;
                if ($child_node->suffix_link !== null) {
                    $suffix_hash = (string)$child_node->suffix_link;
                    $suffix_node = isset($node_map[$suffix_hash]) ? $node_map[$suffix_hash] : null;
                }
                $new_node = new Internal(
                    $child_node->start,
                    $child_node->end,
                    $smin,
                    $smax,
                    $grand_children,
                    $suffix_node
                );
                if ($suffix_node === null && $child_node->suffix_link !== null) {
                    $lazy_links[$suffix_hash] = $new_node;
                }
                $child_hash = (string)$child_node;
                if (isset($lazy_links[$child_hash])) {
                    // todo: find out way to prevent mutating node state here, need to get the new node ref
                    // into the proper place within the new tree
                    $lazy_links[$child_hash]->withSuffixLink($new_node);
                }
                if ($suffix_min === -1) {
                    $suffix_min = $new_node->getMinSuffixIdx();
                    $suffix_max = $new_node->getMaxSuffixIdx();
                } else {
                    $suffix_min = min($suffix_min, $new_node->getMinSuffixIdx());
                    $suffix_max = max($suffix_max, $new_node->getMaxSuffixIdx());
                }
                $node_map[$child_hash] = $new_node;
            }
            $children[$edge] = $new_node;
        }

        return [ $children, $suffix_min, $suffix_max ];
    }
}
