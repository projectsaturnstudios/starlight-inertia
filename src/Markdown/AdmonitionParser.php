<?php

namespace ProjectSaturnStudios\Themes\Starlight\Markdown;

use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

class AdmonitionParser extends AbstractBlockContinueParser
{
    private Admonition $block;

    public function __construct(string $type, ?string $title)
    {
        $this->block = new Admonition($type, $title !== '' ? $title : null);
    }

    public function getBlock(): Admonition
    {
        return $this->block;
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function canContain(AbstractBlock $childBlock): bool
    {
        return true;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        if (! $cursor->isIndented() && $cursor->match('/^:::\s*$/') !== null) {
            return BlockContinue::finished();
        }

        return BlockContinue::at($cursor);
    }
}
