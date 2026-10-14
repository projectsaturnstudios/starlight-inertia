---
type: Architecture
title: Markdown
description: Renderer pipeline (CommonMark + Phiki + Starlight asides), the HTML contract the stylesheet targets, html_input modes, TOC extraction.
resource: src/Markdown/Renderer.php
tags: [markdown, commonmark, phiki, security]
status: stable
generated: { by: claude/fable-5.1, at: 2026-10-12T20:00:00Z }
sources:
  - id: renderer
    resource: src/Markdown/Renderer.php
    title: Renderer
  - id: provider
    resource: src/Providers/StarlightServiceProvider.php
    title: Service provider
  - id: asides
    resource: src/Markdown/AdmonitionStartParser.php
    title: Admonition block parser
  - id: alerts
    resource: src/Markdown/AlertBlockQuoteRenderer.php
    title: GitHub alert renderer
  - id: tests
    resource: tests/RendererTest.php
    title: Renderer tests
---

# Overview

`Renderer::render(string): Rendered { html, toc }`. Environment: CommonMark core + GFM (tables, strikethrough, autolinks, task lists) + footnotes + heading permalinks with `insert: none` (ids only, h2–h4, document-unique slugs) + external links (`target=_blank` unless host ∈ `internal_hosts`) + Phiki with `['light' => …, 'dark' => …]` + the aside block parser + a blockquote renderer that turns `> [!NOTE]` into an aside.[^renderer]

Provider binds it as a singleton from `config('starlight.markdown.*')`, always adding the app URL's host to `internal_hosts`.[^provider]

# HTML contract (what the CSS targets)

| Markdown | HTML |
|---|---|
| `## Heading` | `<h2 id="heading">` (h2–h4 get ids; h2–h3 go in `toc`) |
| `:::tip[Title]` … `:::` | `<aside class="aside aside-tip" aria-label="Title"><p class="aside-title">Title</p><div class="aside-content">…</div></aside>` |
| `> [!WARNING]` | same aside shape; `NOTE→note`, `TIP/HINT/IMPORTANT→tip`, `WARNING/CAUTION→caution`, `DANGER/ERROR→danger`, unknown→note |
| fenced code | `<pre class="phiki language-x github-light phiki-themes github-dark" style="…;--phiki-dark-*">`; unknown language → plain `txt` tokens |
| footnote reference + definition | `<sup class="footnote-ref">` + `<div class="footnotes">` |

Aside parser: a line `:::type` or `:::type[Title]` opens a container block; `:::` closes; inner markdown parses normally. Title is HTML-escaped.[^asides] Alert renderer strips the `[!TYPE]` text node and re-parents the quote's children into an `Admonition` node.[^alerts]

# html_input

Constructor `$htmlInput`: `allow` (default; raw HTML passes), `escape`, `strip`. Anything else → `InvalidArgumentException`. `allow_unsafe_links` is hard-coded false → `javascript:`/`data:` targets dropped in every mode.[^renderer] `allow` is for trusted authors; see `SECURITY.md`.

# TOC

After conversion the document AST is walked; `Heading` nodes at level 2–3 with an `attributes/id` yield `['depth', 'slug', 'text']`, text = concatenated string literals (inline markup dropped).[^renderer]

# Tests

Plain Pest, no Laravel: headings/toc, asides and type normalisation, alerts vs plain quotes, dual-theme code, unknown fence fallback, footnotes/tables, configurable Phiki themes, internal hosts, html modes, unsafe links.[^tests]

[^renderer]: Renderer
[^provider]: Service provider
[^asides]: Admonition block parser
[^alerts]: GitHub alert renderer
[^tests]: Renderer tests
