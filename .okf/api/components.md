---
type: API
title: Components
description: Props, slots and events of every shipped Vue component, the composable and the shared types.
resource: resources/index.ts
tags: [vue, inertia, components, api]
status: stable
generated: { by: claude/fable-5.1, at: 2026-10-12T20:00:00Z }
sources:
  - id: layout
    resource: resources/layouts/starlight-layout.vue
    title: starlight-layout
  - id: article
    resource: resources/components/article.vue
    title: article
  - id: search
    resource: resources/components/search-dialog.vue
    title: search-dialog
  - id: types
    resource: resources/types/index.ts
    title: types
---

# Layout

`layouts/starlight-layout.vue`[^layout]

| Prop | Type | Default | Role |
|---|---|---|---|
| `title` | string | required | Header wordmark |
| `nav` | `NavNode[]` | `[]` | Sidebar tree |
| `sidebar` | boolean | true | false = no sidebar, no menu button (splash pages) |
| `homeHref` | string | `/` | Wordmark link |
| `logoSrc` | string\|null | null | 24px image before the title (or use slot `logo`) |
| `badge` | string\|null | null | Pill after the title (version) |
| `githubUrl` | string\|null | null | Header icon link |
| `searchUrl` | string\|null | `/search` | null hides search entirely |
| `notice` | string\|null | null | Toast, auto-dismissed after 5 s |

Slots: `logo`, `actions` (extra header buttons, before GitHub/theme), `sidebar-top`, `sidebar-bottom`, default. Drawer closes on every Inertia `navigate`.

# Article

`components/article.vue`[^article]: `title`, `description`, `overline: string[]` (breadcrumb words), `html` (rendered markdown, `v-html`) or default slot, `toc: TocEntry[]`, `prev`/`next: PageLink`, `updatedAt` (ISO), `editHref`, `editLabel`, `draft`, `wide`. Slots `header`, `meta`. Renders the mobile `<details>` TOC and the sticky right TOC when `toc` is non-empty. Click on a same-origin link inside `html` → `router.visit`; hash-only, modified-click, `_blank` and download links are left to the browser.

# Splash

`hero.vue` (`title`, `tagline`; slots `actions`, `image`), `card-grid.vue` (slot), `card.vue` (`title`, `href`, `icon`; absolute URL → `<a target=_blank>`, else `Link`, no href → `<div>`), `link-button.vue` (`href`, `variant: primary|minimal`, `small`; same URL rule). Wrap in `.sl-splash` for the centred column.

# Chrome

- `site-header.vue`: used by the layout; same header props + `menu`, `menuOpen`, emits `toggleMenu`.
- `sidebar-nav.vue` (`nav`, emits `navigate`) → recursive `sidebar-group.vue` (`node`, `current`): `<details>` open when `collapsed === false` or the group holds the current URL; a node with `url` renders as an external link.
- `search-dialog.vue` (`url`, `placeholder`): native `<dialog>`; ⌘K / Ctrl+K anywhere, `/` outside inputs; debounced 150 ms, aborts stale fetches, ≥2 chars; expects `{ results: SearchResult[] }`; ↑↓ Enter Esc; exposes `open()`.[^search]
- `table-of-contents.vue` (`toc`): scroll-spy by last heading above nav height + 32 px.
- `theme-select.vue`: dark/light/auto `<select>` with icon.
- `icon.vue` (`name`, `size`): inline stroke icons: search, menu, close, chevron-left/right, external, github, sun, moon, pencil, clock, book, plus, up, down, eye, settings, logout.

# Composable and types

`useTheme.ts`: `applyTheme(theme?)` (call once at app start), `setTheme(theme)`, `storedTheme()`, `resolve(theme)`, `useTheme()` bundling them. Types[^types]: `NavNode { id, title, url?, collapsed?, pages: NavPage[], children: NavNode[] }`, `NavPage/PageLink { title, href }`, `TocEntry { depth, slug, text }`, `SearchResult { title, href, section?, snippet? }`.

[^layout]: starlight-layout
[^article]: article
[^search]: search-dialog
[^types]: types
