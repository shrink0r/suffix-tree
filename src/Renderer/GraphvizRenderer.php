<?php

namespace Shrink0r\SuffixTree\Renderer;

use Shrink0r\SuffixTree\InternalNode;
use Shrink0r\SuffixTree\LeafNode;
use Shrink0r\SuffixTree\NodeInterface;
use Shrink0r\SuffixTree\RootNode;
use Shrink0r\SuffixTree\SuffixTree;

final class GraphvizRenderer implements SuffixTreeRendererInterface
{
    const NODE_PROPS = 'label="%s" fontname="Arial" fontcolor="#000000" color="#7f8c8d"';

    const LEAF_PROPS = 'shape="circle" width="0.45" fixedsize="true" fontsize="8"';

    const ROOT_PROPS = 'shape="point" width="0.1" fontsize="0.3" fontsize="8"';

    const INTERNAL_PROPS = 'shape="circle" width="0.2" fillcolor="#ecf0f1" style="filled" ';

    const BASE_EDGE_PROPS = 'label="%s" fontname="Arial" fontcolor="#000000"';

    const EDGE_PROPS = 'fontsize="12" color="#7f8c8d"';

    const SUFFIX_LINK_PROPS = 'arrowhead="vee" arrowsize="0.7" style="dashed" fontsize="8" color="#2980b9"';

    public function render(SuffixTree $tree)
    {
        $node_map = [];
        $rendered_nodes = [];
        foreach ($this->collectNodes($tree->getRoot(), []) as $node_id => $node) {
            $rendered_nodes[] = $this->renderNode($node, $node_id);
            $node_map[$node_id] = $node;
        }

        $nodes = implode("\n    ", $rendered_nodes);
        $edges = $this->renderEdges($tree, $node_map);

        return sprintf("digraph \"suffix-tree\" {\n    %s\n\n    %s\n}", $nodes, $edges);
    }

    private function renderNode(NodeInterface $node, int $node_id): string
    {
        if ($node instanceof LeafNode) {
            return $this->renderLeafNode($node, $node_id);
        } elseif ($node instanceof RootNode) {
            return $this->renderRootNode($node, $node_id);
        }
        return $this->renderInternalNode($node, $node_id);
    }

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

    private function renderLeafNode(LeafNode $leaf, int $node_id): string
    {
        return sprintf('%s [ '.self::NODE_PROPS.' '.self::LEAF_PROPS.' ];', $node_id, $leaf->getSuffixIdx());
    }

    private function renderRootNode(RootNode $root, int $node_id): string
    {
        return sprintf('%s [ '.self::NODE_PROPS.' '.self::ROOT_PROPS.' ];', $node_id, 'root');
    }

    private function renderInternalNode(InternalNode $internal_node, int $node_id): string
    {
        return sprintf('%s [ '.self::NODE_PROPS.' '.self::INTERNAL_PROPS.' ];', $node_id, '');
    }

    private function renderEdges(SuffixTree $tree, array $node_map): string
    {
        $rendered_edges = [];
        foreach ($node_map as $id => $node) {
            foreach ($node->getChildren() as $child) {
                $rendered_edges[] = $this->renderEdge($node, $child, $node_map, $tree->getS());
            }
            if ($node->getSuffixLink() !== null && $node->getSuffixLink() !== $tree->getRoot()) {
                $rendered_edges[] = $this->renderSuffixLink($node, $node_map);
            }
        }

        return implode(PHP_EOL, $rendered_edges);
    }

    private function renderEdge(NodeInterface $node, NodeInterface $child, array $node_map, string $S): string
    {
        return sprintf(
            '%s -> %s [ '.self::BASE_EDGE_PROPS.' '.self::EDGE_PROPS.' ];',
            array_search($node, $node_map, true),
            array_search($child, $node_map, true),
            substr($S, $child->getStart(), $child->getEnd() - $child->getStart() + 1)
        );
    }

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