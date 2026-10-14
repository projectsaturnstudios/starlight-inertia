<?php

namespace ProjectSaturnStudios\Themes\Starlight\Markdown;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

class AdmonitionRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        assert($node instanceof Admonition);

        $title = new HtmlElement('p', ['class' => 'aside-title', 'aria-hidden' => 'true'], htmlspecialchars($node->title ?? ucfirst($node->type)));
        $content = new HtmlElement('div', ['class' => 'aside-content'], $childRenderer->renderNodes($node->children()));

        return new HtmlElement('aside', ['aria-label' => $node->title ?? ucfirst($node->type), 'class' => 'aside aside-'.$node->type], $title.$content);
    }
}
