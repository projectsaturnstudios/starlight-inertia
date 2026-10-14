<?php

namespace ProjectSaturnStudios\Themes\Starlight\Markdown;

use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Renderer\Block\BlockQuoteRenderer;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

/** GitHub alerts (`> [!NOTE]`, as the Laravel docs use) render as Starlight asides. */
class AlertBlockQuoteRenderer implements NodeRendererInterface
{
    public function __construct(private readonly BlockQuoteRenderer $fallback = new BlockQuoteRenderer, private readonly AdmonitionRenderer $aside = new AdmonitionRenderer) {}

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): mixed
    {
        assert($node instanceof BlockQuote);

        $paragraph = $node->firstChild();
        $text = $paragraph instanceof Paragraph ? $paragraph->firstChild() : null;

        if (! $text instanceof Text || ! preg_match('/^\[!([A-Z]+)\]\s*/', $text->getLiteral(), $m)) {
            return $this->fallback->render($node, $childRenderer);
        }

        $text->setLiteral(ltrim(substr($text->getLiteral(), strlen($m[0]))));
        if ($text->getLiteral() === '' && $text->next() === null) {
            $paragraph->detach();
        }

        $aside = new Admonition(Admonition::normalize($m[1]));
        foreach ($node->children() as $child) {
            $aside->appendChild($child);
        }

        return $this->aside->render($aside, $childRenderer);
    }
}
