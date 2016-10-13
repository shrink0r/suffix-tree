<?php

namespace Shrink0r\SuffixTree\Renderer;

use Shrink0r\SuffixTree\InternalNode;
use Shrink0r\SuffixTree\LeafNode;
use Shrink0r\SuffixTree\NodeInterface;
use Shrink0r\SuffixTree\RootNode;
use Shrink0r\SuffixTree\SuffixTree;

final class GraphvizRenderer implements SuffixTreeRendererInterface
{
    // basic graph layout
    const GRAPH_TPL = "digraph \"suffix-tree\" {\n\t%s\n\n\t%s\n}";

    // node styles
    const NODE_PROPS = 'label="%s" fontname="Arial" fontcolor="#000000" color="#7f8c8d"';
    const LEAF_NODE = 'shape="circle" width="0.45" fixedsize="true" fontsize="8"';
    const ROOT_NODE = 'shape="point" width="0.1" fontsize="0.3" fontsize="8"';
    const INTERN_NODE = 'shape="circle" width="0.35" fixedsize="true" fontsize="6" fillcolor="#ecf0f1" style="filled"';

    // edge styles
    const BASE_EDGE_PROPS = 'label="%s" fontname="Arial" fontcolor="#000000"';
    const EDGE_PROPS = 'fontsize="12" color="#7f8c8d"';
    const SUFFIX_LINK_PROPS = 'arrowhead="vee" arrowsize="0.7" style="dashed" fontsize="8" color="#2980b9"';

    /**
     * @param SuffixTree $tree
     *
     * @return string
     */
    public function render(SuffixTree $tree): string
    {
        $node_map = [];

        $nodes = [];
        foreach ($this->collectNodes($tree->getRoot(), []) as $node_id => $node) {
            $nodes[] = $this->renderNode($node, $node_id);
            $node_map[$node_id] = $node;
        }

        $edges = [];
        foreach ($node_map as $node) {
            foreach ($node->getChildren() as $child) {
                $edges[] = $this->renderEdge($node, $child, $node_map, $tree->getS());
            }
            if ($node->getSuffixLink() !== null && $node->getSuffixLink() !== $tree->getRoot()) {
                $edges[] = $this->renderSuffixLink($node, $node_map);
            }
        }

        return sprintf(self::GRAPH_TPL, implode("\n\t", $nodes), implode("\n\t", $edges));
    }

    /**
     * @param NodeInterface $node
     * @param int $node_id
     *
     * @return string
     */
    private function renderNode(NodeInterface $node, int $node_id): string
    {
        if ($node instanceof LeafNode) {
            return $this->renderLeafNode($node, $node_id);
        } elseif ($node instanceof RootNode) {
            return $this->renderRootNode($node_id);
        }
        return $this->renderInternalNode($node, $node_id);
    }

    /**
     * @param NodeInterface $node
     * @param array $visited_nodes
     *
     * @return NodeInterface[]
     */
    private function collectNodes(NodeInterface $node, array $visited_nodes): array
    {
        if (in_array($node, $visited_nodes, true)) {
            return $visited_nodes;
        }

        $visited_nodes[] = $node;
        foreach ($node->getChildren() as $child) {
            $visited_nodes = $this->collectNodes($child, $visited_nodes);
        }

        return $visited_nodes;
    }

    /**
     * @param LeafNode $leaf
     * @param int $node_id
     *
     * @return string
     */
    private function renderLeafNode(LeafNode $leaf, int $node_id): string
    {
        return sprintf('%s [ '.self::NODE_PROPS.' '.self::LEAF_NODE.' ];', $node_id, $leaf->getSuffixIdx());
    }

    /**
     * @param int $node_id
     *
     * @return string
     */
    private function renderRootNode(int $node_id): string
    {
        return sprintf('%s [ '.self::NODE_PROPS.' '.self::ROOT_NODE.' ];', $node_id, 'root');
    }

    /**
     * @param int $node_id
     *
     * @return string
     */
    private function renderInternalNode(InternalNode $node, int $node_id): string
    {
        $label = $node->getMinSuffixIdx().', '.$node->getMaxSuffixIdx();
        return sprintf('%s [ '.self::NODE_PROPS.' '.self::INTERN_NODE.' ];', $node_id, $label);
    }

    /**
     * @param NodeInterface $node
     * @param NodeInterface $child
     * @param array $node_map
     * @param string $S
     *
     * @return string
     */
    private function renderEdge(NodeInterface $node, NodeInterface $child, array $node_map, string $S): string
    {
        return sprintf(
            '%s -> %s [ '.self::BASE_EDGE_PROPS.' '.self::EDGE_PROPS.' ];',
            array_search($node, $node_map, true),
            array_search($child, $node_map, true),
            substr($S, $child->getStart(), $child->getEnd() - $child->getStart() + 1)
        );
    }

    /**
     * @param NodeInterface $node
     * @param array $node_map
     *
     * @return string
     */
    private function renderSuffixLink(NodeInterface $node, array $node_map): string
    {
        return sprintf(
            '%s -> %s [ '.self::BASE_EDGE_PROPS.' '.self::SUFFIX_LINK_PROPS.' ];',
            array_search($node, $node_map, true),
            array_search($node->getSuffixLink(), $node_map, true),
            ''
        );
    }
}
