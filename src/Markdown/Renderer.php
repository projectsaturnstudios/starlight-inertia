<?php

namespace ProjectSaturnStudios\Themes\Starlight\Markdown;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkProcessor;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Node;
use League\CommonMark\Node\StringContainerInterface;
use Phiki\Adapters\CommonMark\PhikiExtension;
use Phiki\Grammar\Grammar;
use Phiki\Grammar\ParsedGrammar;
use Phiki\Phiki;
use Phiki\Theme\Theme;

/**
 * Markdown → the HTML the Starlight theme styles: GFM, footnotes, heading ids
 * (h2–h4), `:::note|tip|caution|danger[Title]` asides, GitHub `> [!NOTE]`
 * alerts, and Phiki code blocks carrying a light and a dark theme at once.
 */
class Renderer
{
    private MarkdownConverter $converter;

    /**
     * @param  list<string>  $internalHosts  Hosts whose links stay in-tab; others open in a new window.
     * @param  'allow'|'escape'|'strip'  $htmlInput  What raw HTML in the markdown becomes. `allow` passes it through, for trusted authors only.
     * @param  string  $lightTheme  A Phiki theme id, or the path to a TextMate/VS Code theme `.json` file.
     * @param  string  $darkTheme  A Phiki theme id, or the path to a TextMate/VS Code theme `.json` file.
     */
    public function __construct(array $internalHosts = [], string $lightTheme = 'github-light', string $darkTheme = 'github-dark', string $htmlInput = 'allow')
    {
        if (! in_array($htmlInput, ['allow', 'escape', 'strip'], true)) {
            throw new \InvalidArgumentException("html_input must be allow, escape or strip; got \"$htmlInput\".");
        }

        $environment = new Environment([
            'html_input' => $htmlInput,
            'allow_unsafe_links' => false,
            'heading_permalink' => [
                'insert' => HeadingPermalinkProcessor::INSERT_NONE,
                'apply_id_to_heading' => true,
                'id_prefix' => '',
                'fragment_prefix' => '',
                'min_heading_level' => 2,
                'max_heading_level' => 4,
            ],
            'external_link' => ['internal_hosts' => $internalHosts, 'open_in_new_window' => true],
            'slug_normalizer' => ['unique' => 'document'],
        ]);

        $phiki = self::withPhpOpenTags(new Phiki);

        $environment->addExtension(new CommonMarkCoreExtension)
            ->addExtension(new GithubFlavoredMarkdownExtension)
            ->addExtension(new HeadingPermalinkExtension)
            ->addExtension(new ExternalLinkExtension)
            ->addExtension(new FootnoteExtension)
            ->addExtension(new PhikiExtension(['light' => self::theme($phiki, $lightTheme), 'dark' => self::theme($phiki, $darkTheme)], $phiki))
            ->addBlockStartParser(new AdmonitionStartParser, 100)
            ->addRenderer(Admonition::class, new AdmonitionRenderer)
            ->addRenderer(BlockQuote::class, new AlertBlockQuoteRenderer, 10);

        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * Phiki's PHP grammar is the embedded `source.php` one, with no rule for the
     * open tag, so `<?php` reads as `<`, `?` and a constant. Two leading rules
     * give `<?php`, `<?=` and `?>` their usual punctuation.section.embedded scope.
     */
    private static function withPhpOpenTags(Phiki $phiki): Phiki
    {
        $grammar = json_decode((string) file_get_contents(Grammar::Php->path()), true, flags: JSON_THROW_ON_ERROR);

        array_unshift(
            $grammar['patterns'],
            ['match' => '<\\?(?:php\\b|=)?', 'name' => 'punctuation.section.embedded.begin.php'],
            ['match' => '\\?>', 'name' => 'punctuation.section.embedded.end.php'],
        );

        return $phiki->grammar('php', ParsedGrammar::fromArray($grammar));
    }

    /** A built-in theme id resolves to Phiki's enum; a `.json` path is registered under its file name. */
    private static function theme(Phiki $phiki, string $theme): Theme|string
    {
        if (str_ends_with(strtolower($theme), '.json')) {
            if (! is_file($theme)) {
                throw new \InvalidArgumentException("Code theme file \"$theme\" does not exist.");
            }

            $name = 'starlight-'.pathinfo($theme, PATHINFO_FILENAME);
            $phiki->theme($name, $theme);

            return $name;
        }

        return Theme::tryFrom($theme)
            ?? throw new \InvalidArgumentException("\"$theme\" is not a Phiki theme id or a path to a theme .json file.");
    }

    public function render(string $markdown): Rendered
    {
        $result = $this->converter->convert($markdown);

        $toc = [];
        foreach ($result->getDocument()->iterator() as $node) {
            if ($node instanceof Heading && $node->getLevel() >= 2 && $node->getLevel() <= 3 && $node->data->has('attributes/id')) {
                $toc[] = ['depth' => $node->getLevel(), 'slug' => $node->data->get('attributes/id'), 'text' => $this->text($node)];
            }
        }

        return new Rendered($result->getContent(), $toc);
    }

    private function text(Node $node): string
    {
        $out = '';
        foreach ($node->iterator() as $child) {
            if ($child instanceof StringContainerInterface) {
                $out .= $child->getLiteral();
            }
        }

        return trim($out);
    }
}
