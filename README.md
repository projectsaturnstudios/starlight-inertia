<div align="center" id="header">

<h1>Starlight Inertia</h1>

<h3>A Starlight-shaped documentation theme for Laravel + Inertia (Vue 3)</h3>

[![CI](https://img.shields.io/github/actions/workflow/status/projectsaturnstudios/starlight-inertia/ci.yml?style=flat-square&label=CI)](https://github.com/projectsaturnstudios/starlight-inertia/actions/workflows/ci.yml)
[![Packagist](https://img.shields.io/packagist/v/projectsaturnstudios/starlight-inertia?style=flat-square)](https://packagist.org/packages/projectsaturnstudios/starlight-inertia)
[![PHP](https://img.shields.io/packagist/dependency-v/projectsaturnstudios/starlight-inertia/php?style=flat-square&label=PHP)](https://packagist.org/packages/projectsaturnstudios/starlight-inertia)
[![Laravel](https://img.shields.io/badge/Laravel-12%20%7C%2013-ff2d20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![License](https://img.shields.io/github/license/projectsaturnstudios/starlight-inertia?style=flat-square)](LICENSE)
[![Frontend](https://img.shields.io/badge/Inertia_3-Vue_3-4fc08d?style=flat-square&logo=vuedotjs&logoColor=white)](https://inertiajs.com)
[![OKF](https://img.shields.io/badge/OKF-0.2-6f42c1?style=flat-square&label=knowledge%20bundle)](.okf/index.md)
</div>

[Starlight](https://starlight.astro.build) is the documentation look everyone recognises: a fixed header with a search box, a collapsible sidebar on the left, the page in the middle, "<b>On this page</b>" on the right, previous/next cards at the bottom, light and dark themes that just work. It gets that look from a few hundred lines of CSS custom properties and a handful of components. The catch is that Starlight is an Astro integration that builds static pages from markdown files on disk.

If your docs live in a database, behind a login, with an editor, in a Laravel app that already renders through Inertia, you can't use Starlight. You can only miss it.

<b>This package is the Starlight look, rebuilt for Laravel + Inertia.</b> The same token system, the same chrome, the same markdown styling, as Vue components and plain CSS you compose your own pages from. It also ships the markdown renderer that produces the exact HTML those styles expect, so a page stored as markdown in your database comes out looking like a Starlight page. No Tailwind dependency, no Astro, and a colour scheme is six variables in a file.

## Basic usage

Most ways you will interface with the theme are in two parts -

Compose the page from the layout and the article component, passing what your app knows through props:

```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import StarlightLayout from '@starlight/layouts/starlight-layout.vue';
import Article from '@starlight/components/article.vue';
import type { NavNode, PageLink, TocEntry } from '@starlight/types';

defineProps<{
    nav: NavNode[];
    page: { title: string; description: string | null; html: string; toc: TocEntry[]; updated_at: string | null };
    prev: PageLink | null;
    next: PageLink | null;
}>();
</script>

<template>
    <Head :title="page.title" />

    <StarlightLayout title="My Docs" :nav="nav" badge="v1.0" github-url="https://github.com/me/docs">
        <Article
            :title="page.title"
            :description="page.description"
            :overline="['Guide']"
            :html="page.html"
            :toc="page.toc"
            :prev="prev"
            :next="next"
            :updated-at="page.updated_at"
        />
    </StarlightLayout>
</template>
```

Render the markdown on the server with the bundled renderer and hand the result to that page:

```php
use ProjectSaturnStudios\Themes\Starlight\Markdown\Renderer;

$rendered = app(Renderer::class)->render($page->body);

return Inertia::render('Docs/Show', [
    'nav' => $nav,
    'page' => [
        'title' => $page->title,
        'description' => $page->description,
        'html' => $rendered->html, // GFM, footnotes, heading ids, :::note asides, > [!TIP] alerts, dual-theme code blocks
        'toc' => $rendered->toc,   // [['depth' => 2, 'slug' => 'intro', 'text' => 'Intro'], ...]
        'updated_at' => $page->updated_at,
    ],
    'prev' => $prev,
    'next' => $next,
]);
```

The sidebar is a tree of `{ id, title, url?, collapsed?, pages: [{ id, title, href }], children: [...] }`; a node with a `url` renders as an external link. The search dialog (⌘K / Ctrl+K / `/`) calls `GET /search?q=` on your app and expects `{ results: [{ title, href, section?, snippet? }] }`; pass `:search-url="null"` to the layout to leave it out.

### Markdown syntax

Everything GitHub-flavored, plus Starlight's asides and GitHub's alerts, which render the same way:

```markdown
:::tip[Custom title]
Inner **markdown** works.
:::

> [!WARNING]
> Alerts map onto the same four asides: note, tip, caution, danger.
```

Code fences are highlighted server-side by [Phiki](https://github.com/phikiphp/phiki) with a light and a dark theme in one `<pre>`, so switching themes never re-renders the page.

## Requirements

| | |
|---|---|
| PHP | 8.3 or newer. |
| Laravel | 12 or 13 (`illuminate/support`). |
| Frontend | Inertia 3 with the Vue 3 adapter, built by Vite. The shipped components import only `vue` and `@inertiajs/vue3`. |
| CSS | None. Plain CSS on custom properties; works with or without Tailwind. |
| Libraries | `league/commonmark` ^2.6 and `phiki/phiki` ^2.2, pulled in by Composer. |

## Installation

### With Composer

```bash
composer require projectsaturnstudios/starlight-inertia
```

The service provider is auto-discovered. Publish the config only if you need to change the asset directory, the Phiki themes or the HTML policy:

```bash
php artisan vendor:publish --tag=starlight-config
```

### Copy the frontend into your app

The theme's Vue, TypeScript and CSS ship as source. Copy them into the application, where Vite, Tailwind and your editor already look:

```bash
php artisan starlight:update-assets
```

This writes `resources/themes/starlight/` (components, layouts, composables, css, types, `index.ts`) and overwrites it on every run, so re-run it after updating the package. The destination is `assets_path` in `config/starlight.php`.

### Wire Vite and the stylesheet

Alias the copy in `vite.config.ts`, and in `tsconfig.json` `compilerOptions.paths` for editor support:

```ts
export default defineConfig({
    resolve: {
        alias: {
            '@starlight': '/resources/themes/starlight',
        },
    },
});
```

Import the base stylesheet and an accent preset into your app CSS. Under Tailwind 4, put both in the `components` layer so utilities keep precedence:

```css
@import 'tailwindcss';
@import '../themes/starlight/css/starlight.css' layer(components);
@import '../themes/starlight/css/themes/venus.css' layer(components);
```

Add the no-flash theme script to the `<head>` of your root Blade view, and apply the stored theme once at app start:

```blade
@include('starlight::head')
```

```ts
import { applyTheme } from '@starlight/composables/useTheme';

applyTheme();
```

### Verify

```console
$ php artisan starlight:update-assets
Starlight theme assets copied to resources/themes/starlight.

$ php artisan tinker --execute='echo app(ProjectSaturnStudios\Themes\Starlight\Markdown\Renderer::class)->render(":::tip\nhi\n:::")->html;'
<aside aria-label="Tip" class="aside aside-tip"><p class="aside-title" aria-hidden="true">Tip</p><div class="aside-content"><p>hi</p></div></aside>
```

Then `npm run build`: a page that renders `StarlightLayout` shows the header, sidebar and theme select.

## API

### Components

Import each from `@starlight/...`, or everything from `@starlight/index.ts`.

| Component | Props | Slots |
|---|---|---|
| `layouts/starlight-layout.vue` | `title`, `nav`, `sidebar` (default true), `homeHref`, `logoSrc`, `badge`, `githubUrl`, `searchUrl` (null hides search), `notice` (toast) | `logo`, `actions`, `sidebar-top`, `sidebar-bottom`, default |
| `components/article.vue` | `title`, `description`, `overline` (breadcrumb words), `html` or the default slot, `toc`, `prev`, `next`, `updatedAt`, `editHref`, `editLabel`, `draft`, `wide` | `header`, `meta`, default |
| `components/hero.vue` | `title`, `tagline` | `eyebrow`, `actions`, `image` |
| `components/card-grid.vue`, `card.vue` | card: `title`, `href`, `icon`. Absolute URL → `<a target="_blank">`, else Inertia `Link` | default |
| `components/link-button.vue` | `href`, `variant` (`primary` \| `minimal`), `small` | default |
| `components/search-dialog.vue` | `url`, `placeholder`; exposes `open()` | |
| `components/sidebar-nav.vue` | `nav`; emits `navigate` | |
| `components/table-of-contents.vue` | `toc` | |
| `components/theme-select.vue`, `icon.vue` | icon: `name`, `size` | |

Wrap a splash page's hero and cards in an element with class `sl-splash` for the centred column.

### Composable

`@starlight/composables/useTheme`: `applyTheme(theme?)`, `setTheme('light' | 'dark' | 'auto')`, `storedTheme()`, `resolve(theme)`. The preference lives in `localStorage` under `theme`; the resolved value is the `data-theme` attribute on `<html>`.

### Renderer

`ProjectSaturnStudios\Themes\Starlight\Markdown\Renderer` is a container singleton. `render(string $markdown): Rendered` returns `html` and `toc` (h2 and h3 entries). Construct it directly to choose: internal link hosts (stay in-tab; the app URL's host is always one), the light and dark code themes (a Phiki theme id, or the path to a TextMate / VS Code theme `.json` file), and `html_input`.

| `html_input` | Raw HTML in the markdown |
|---|---|
| `allow` (default) | Passed through unchanged. For trusted authors only; see [SECURITY.md](SECURITY.md). |
| `escape` | Shown as text. |
| `strip` | Removed. |

`javascript:`, `data:` and similar link targets are removed in every mode.

### Colour schemes

`css/starlight.css` defines every colour as a custom property on `:root` (light) and `:root[data-theme='dark']`. A scheme is a file that redefines some of them after the base import. Two presets ship in `css/themes/`: `starlight.css` (the stock indigo, also the base default) and `venus.css` (warm amber). Your own needs six lines:

```css
:root {
    --sl-color-accent-low: #d9def7;
    --sl-color-accent: #3b57d6;
    --sl-color-accent-high: #1a2a6b;
}

:root[data-theme='dark'] {
    --sl-color-accent-low: #1b2650;
    --sl-color-accent: #6d84f0;
    --sl-color-accent-high: #c4cfff;
}
```

The grey scale (`--sl-color-white` … `--sl-color-black`, inverted between themes as Starlight does), the aside colours (`--sl-color-tip*`, `caution*`, `danger*`), fonts (`--sl-font`, `--sl-font-mono`) and layout (`--sl-nav-height`, `--sl-sidebar-width`, `--sl-content-width`) override the same way.

## Packages you might use with this one

* [`league/commonmark`](https://commonmark.thephpleague.com) and [`phiki/phiki`](https://github.com/phikiphp/phiki): the renderer is an environment built on these; add your own CommonMark extensions by extending `Renderer`.
* [Inertia](https://inertiajs.com): the components use its `Link`, `router` and `usePage`, nothing more.

## AI-assisted development

This codebase was built with LLM assistance under human direction. Prompts, reviews, and architectural decisions were owned by a professional software engineer — the model accelerates drafting and exploration; it does not replace judgment on APIs, dependency direction, or what ships.

## Agentic Documentation

This repository carries an [Open Knowledge Format](https://github.com/GoogleCloudPlatform/knowledge-catalog/blob/main/okf/SPEC.md) bundle at [`.okf/`](.okf/): Markdown with YAML frontmatter, readable by you and loadable by a coding agent. It records what the README doesn't: why the frontend is copied into the app instead of imported from `vendor/`, how the token set and the dark theme fit together, the exact HTML contract between the renderer and the stylesheet, and every component's props and slots.

Start at [`.okf/index.md`](.okf/index.md). [`AGENTS.md`](AGENTS.md) is the short rule sheet an agent reads before touching the components, the CSS or the renderer; each rule points into the bundle for the reason behind it.

Point your agent at the repo and it has the same context the maintainer has. Point yourself at it when the README's answer is "it does X" and you want "because".

## Resources

* [Starlight](https://starlight.astro.build): the design this theme reproduces. Its [CSS custom properties](https://starlight.astro.build/guides/css-and-tailwind/) are the token names used here.
* [Inertia](https://inertiajs.com) and the [Vue adapter](https://inertiajs.com/client-side-setup): the frontend runtime the components assume.
* [CommonMark PHP](https://commonmark.thephpleague.com) and [Phiki](https://github.com/phikiphp/phiki): the markdown and highlighting engines behind the renderer.
* [Open Knowledge Format](https://github.com/GoogleCloudPlatform/knowledge-catalog/blob/main/okf/SPEC.md): the spec the [`.okf/`](.okf/) bundle follows.

## Running the test suite

The suite is Pest and needs no Laravel application: the renderer is constructed directly and its HTML is asserted.

```bash
git clone https://github.com/projectsaturnstudios/starlight-inertia.git
cd starlight-inertia
composer install
vendor/bin/pest
```

```console
Tests:    16 passed (30 assertions)
Duration: 0.23s
```

The frontend has no test runner of its own; it is type-checked and built by the consuming application's `vue-tsc` and Vite.

## Contributing

Open an issue first for anything beyond a typo. Pull requests that change a class the stylesheet targets change the renderer and `css/starlight.css` together and add a Pest case; new components go in `resources/index.ts` and the components concept in the bundle. CI must be green on every PHP version in the matrix. Read [`AGENTS.md`](AGENTS.md) for the rules the code follows.

## Security

The renderer passes raw HTML through by default, for trusted authors; `escape` and `strip` modes exist for everyone else. See [SECURITY.md](SECURITY.md) for the model and how to report a vulnerability.

## License

MIT. See [LICENSE](LICENSE).
