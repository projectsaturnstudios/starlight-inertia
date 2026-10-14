<?php

use ProjectSaturnStudios\Themes\Starlight\Markdown\Renderer;

beforeEach(fn () => $this->renderer = new Renderer);

test('headings get ids and feed the table of contents', function () {
    $rendered = $this->renderer->render("# Title\n\n## First\n\ntext\n\n### Nested *em*\n\n#### Too deep\n");

    expect($rendered->html)->toContain('<h2 id="first">First</h2>')
        ->and($rendered->toc)->toBe([
            ['depth' => 2, 'slug' => 'first', 'text' => 'First'],
            ['depth' => 3, 'slug' => 'nested-em', 'text' => 'Nested em'],
        ]);
});

test('starlight fences become asides with nested markdown', function () {
    $html = $this->renderer->render(":::tip[Do this]\nInner **bold**.\n:::\n")->html;

    expect($html)->toContain('<aside aria-label="Do this" class="aside aside-tip">')
        ->toContain('<p class="aside-title" aria-hidden="true">Do this</p>')
        ->toContain('<strong>bold</strong>');
});

test('aside types normalise and default to note', function (string $fence, string $class) {
    expect($this->renderer->render(":::$fence\nx\n:::\n")->html)->toContain("aside-$class");
})->with([['note', 'note'], ['warning', 'caution'], ['important', 'tip'], ['error', 'danger'], ['whatever', 'note']]);

test('github alerts render as asides and plain quotes stay quotes', function () {
    $html = $this->renderer->render("> [!WARNING]\n> Careful.\n\n> plain\n")->html;

    expect($html)->toContain('aside-caution')->toContain('Careful.')
        ->toContain('<blockquote>')->toContain('plain');
});

test('code blocks are highlighted for both themes', function () {
    $html = $this->renderer->render("```php\n<?php echo 1;\n```\n")->html;

    expect($html)->toContain('class="phiki language-php github-light phiki-themes github-dark"')
        ->toContain('--phiki-dark-color');
});

test('unknown fence languages fall back to plain text', function () {
    $html = $this->renderer->render("```nope\nx\n```\n")->html;

    expect($html)->toStartWith('<pre class="phiki ')->not->toContain('language-nope')->toContain('<span class="token">x');
});

test('footnotes and tables render', function () {
    $html = $this->renderer->render("Text.[^a]\n\n| a | b |\n|---|---|\n| 1 | 2 |\n\n[^a]: Note\n")->html;

    expect($html)->toContain('class="footnotes"')->toContain('<table>');
});

test('phiki themes are configurable by id', function () {
    $html = (new Renderer([], 'nord', 'one-dark-pro'))->render("```php\n\$x = 1;\n```\n")->html;

    expect($html)->toContain('nord')->toContain('one-dark-pro');
});

test('links to internal hosts open in-tab, others in a new window', function () {
    $html = (new Renderer(['docs.test']))->render('[a](https://docs.test/x) [b](https://example.com/y)')->html;

    expect($html)->toContain('href="https://docs.test/x">a')->toContain('target="_blank" href="https://example.com/y"');
});

test('raw html is passed through by default, escaped or stripped on request', function () {
    $md = "<b>bold</b> text\n";

    expect((new Renderer)->render($md)->html)->toContain('<b>bold</b>')
        ->and((new Renderer(htmlInput: 'escape'))->render($md)->html)->toContain('&lt;b&gt;bold&lt;/b&gt;')
        ->and((new Renderer(htmlInput: 'strip'))->render($md)->html)->not->toContain('bold</b>');
});

test('unsafe link targets are removed whatever the html mode', function () {
    expect((new Renderer)->render('[x](javascript:alert(1))')->html)->not->toContain('javascript:');
});

test('an unknown html mode is rejected', function () {
    new Renderer(htmlInput: 'yolo');
})->throws(InvalidArgumentException::class);

test('a code theme can be a path to a theme json file', function () {
    $path = sys_get_temp_dir().'/starlight-test-theme-'.uniqid().'.json';
    file_put_contents($path, json_encode([
        'name' => 'test-dark',
        'type' => 'dark',
        'colors' => ['editor.background' => '#2b2b2b', 'editor.foreground' => '#a9b7c6'],
        'tokenColors' => [['scope' => ['keyword', 'storage'], 'settings' => ['foreground' => '#cc7832']]],
    ]));

    try {
        $html = (new Renderer(darkTheme: $path))->render("```php\n<?php function f() {}\n```\n")->html;
    } finally {
        unlink($path);
    }

    expect($html)->toContain('--phiki-dark-background-color: #2b2b2b')
        ->toContain('--phiki-dark-color: #cc7832');
});

test('an unknown code theme fails with the name in the message', function () {
    expect(fn () => new Renderer(darkTheme: 'no-such-theme'))->toThrow(InvalidArgumentException::class, 'no-such-theme');
    expect(fn () => new Renderer(darkTheme: '/nope/missing.json'))->toThrow(InvalidArgumentException::class, '/nope/missing.json');
});

test('the php open and close tags are one token each, not operators and a constant', function () {
    $html = $this->renderer->render("```php\n<?php echo \$a ? 1 : 2; ?>\n```\n")->html;

    expect($html)->toMatch('/<span class="token"[^>]*>&lt;\\?php<\\/span>/')
        ->toMatch('/<span class="token"[^>]*>\\?&gt;<\\/span>/');
});
