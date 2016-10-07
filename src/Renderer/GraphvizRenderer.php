<?php

namespace Shrink0r\SuffixTree\Renderer;

use Shrink0r\SuffixTree\LeafNode;
use Shrink0r\SuffixTree\NodeInterface;
use Shrink0r\SuffixTree\RootNode;
use Shrink0r\SuffixTree\SuffixTree;

final class GraphvizRenderer
{
    public function render(SuffixTree $tree)
    {
        list($rendered_nodes, $node_map) = $this->renderNodes($tree->getRoot());

        return sprintf(
            "digraph \"suffix-tree\" {\n    %s\n\n    %s\n}",
            implode("\n    ", $rendered_nodes),
            implode("\n    ", $this->renderEdges($tree, $node_map))
        );
    }

    private function collectNodes(NodeInterface $node, array $visited_nodes)
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

    private function renderNodes(RootNode $root)
    {
        $dot_nodes = [];
        $node_map = [];
        foreach ($this->collectNodes($root, []) as $id => $node) {
            if ($node instanceof LeafNode) {
                $dot_nodes[] = sprintf(
                    '%s [label="%s" shape="circle" fontname="Arial" width="0.45" fixedsize="true" fontsize="8" fontcolor="#000000" color="#7f8c8d"];',
                    $id,
                    sprintf('%d', $node->getSuffixIdx())
                );
            } elseif ($node instanceof RootNode) {
                $dot_nodes[] = sprintf(
                    '%s [label="" shape="point" fontname="Arial" width="0.1" fontsize="3" fontcolor="#000000" color="#7f8c8d"];',
                    $id
                );
            } else { // InternalNode
                $dot_nodes[] = sprintf(
                    '%s [label="" shape="circle" fillcolor="#ecf0f1" style="filled" fontname="Arial" width="0.2" fontsize="8" fontcolor="#000000" color="#7f8c8d"];',
                    $id
                );
            }
            $node_map[$id] = $node;
        }

        return [ $dot_nodes, $node_map ];
    }

    private function renderEdges(SuffixTree $tree, array $node_map)
    {
        $S = $tree->getS();
        $root = $tree->getRoot();
        $dot_edges = [];

        foreach ($node_map as $id => $node) {
            foreach ($node->getChildren() as $child) {
                $dot_edges[] = sprintf(
                    '%s -> %s [label="%s" fontname="Arial" fontsize="12" fontcolor="#000000" color="#7f8c8d"];',
                    $id,
                    array_search($child, $node_map, true),
                    substr($S, $child->getStart(), $child->getEnd() - $child->getStart() + 1)
                );
            }

            if (!empty($node->getChildren()) && $node->getSuffixLink() !== null && $node->getSuffixLink() !== $root) {
                $dot_edges[] = sprintf(
                    '%s -> %s [label="%s" arrowhead="vee" arrowsize="0.7" style="dashed" fontname="Arial" fontsize="8" fontcolor="#000000" color="#2980b9"];',
                    $id,
                    array_search($node->getSuffixLink(), $node_map, true),
                    ''
                );
            }
        }

        return $dot_edges;
    }
}