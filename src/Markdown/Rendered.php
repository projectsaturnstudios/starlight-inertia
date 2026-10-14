<?php

namespace ProjectSaturnStudios\Themes\Starlight\Markdown;

final readonly class Rendered
{
    /** @param list<array{depth:int,slug:string,text:string}> $toc */
    public function __construct(public string $html, public array $toc) {}
}
