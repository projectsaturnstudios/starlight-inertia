<?php

namespace ProjectSaturnStudios\Themes\Starlight\Markdown;

use League\CommonMark\Node\Block\AbstractBlock;

class Admonition extends AbstractBlock
{
    public const TYPES = ['note', 'tip', 'caution', 'danger'];

    public function __construct(public readonly string $type, public readonly ?string $title = null)
    {
        parent::__construct();
    }

    public static function normalize(string $type): string
    {
        return match (strtolower($type)) {
            'tip', 'hint', 'important' => 'tip',
            'caution', 'warning' => 'caution',
            'danger', 'error' => 'danger',
            default => 'note',
        };
    }
}
