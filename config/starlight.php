<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Asset copy
    |--------------------------------------------------------------------------
    |
    | `php artisan starlight:update-assets` copies the package's resources/
    | (components, layouts, composables, css, types) into this directory of
    | the application. Point a Vite alias at it (see README) and import from
    | there; re-run the command after updating the package.
    |
    */

    'assets_path' => 'resources/themes/starlight',

    /*
    |--------------------------------------------------------------------------
    | Markdown
    |--------------------------------------------------------------------------
    |
    | The renderer emits the HTML the theme's markdown styles expect: heading
    | ids for the table of contents, `:::note` asides, GitHub alerts, and
    | Phiki code blocks carrying both themes so the page can switch without a
    | re-render. Each theme is a Phiki theme id or the path to a TextMate /
    | VS Code theme `.json` file (resource_path('...') works). `internal_hosts` lists hosts
    | whose links stay in-tab; the application URL's host is always included.
    |
    | `html_input` decides what raw HTML inside the markdown becomes: `allow`
    | passes it through (README badges, tables with markup), `escape` shows it
    | as text, `strip` drops it. Keep `allow` only while every author is
    | trusted; the rendered HTML goes into the page unescaped. `javascript:`
    | and `data:` link targets are always removed.
    |
    */

    'markdown' => [
        'themes' => [
            'light' => 'github-light',
            'dark' => 'github-dark',
        ],
        'internal_hosts' => [],
        'html_input' => 'allow',
    ],

];
