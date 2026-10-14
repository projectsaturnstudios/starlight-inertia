<?php

namespace ProjectSaturnStudios\Themes\Starlight\Markdown;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

/** Starlight fences: `:::note`, `:::tip[Custom title]`, closed by `:::`. */
class AdmonitionStartParser implements BlockStartParserInterface
{
    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->isIndented() || $cursor->getNextNonSpaceCharacter() !== ':') {
            return BlockStart::none();
        }

        $match = $cursor->match('/^:::([a-zA-Z]+)(?:\[([^\]]*)\])?\s*$/');
        if ($match === null) {
            return BlockStart::none();
        }

        preg_match('/^:::([a-zA-Z]+)(?:\[([^\]]*)\])?/', $match, $parts);

        return BlockStart::of(new AdmonitionParser(Admonition::normalize($parts[1]), $parts[2] ?? null))->at($cursor);
    }
}
