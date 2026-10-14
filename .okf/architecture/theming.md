---
type: Architecture
title: Theming
description: Starlight's token set as CSS custom properties; a colour scheme is a file of overrides imported after the base.
resource: resources/css/starlight.css
tags: [css, tokens, dark-mode, theme]
status: stable
generated: { by: claude/fable-5.1, at: 2026-10-12T20:00:00Z }
sources:
  - id: base
    resource: resources/css/starlight.css
    title: Base stylesheet
  - id: venus
    resource: resources/css/themes/venus.css
    title: Venus preset
  - id: use-theme
    resource: resources/composables/useTheme.ts
    title: useTheme composable
  - id: head
    resource: resources/views/head.blade.php
    title: starlight::head partial
---

# Overview

`starlight.css` = tokens + chrome + markdown + search + splash + forms (~1300 lines, plain CSS, no `@layer`). Tokens on `:root` (light) and `:root[data-theme='dark']`: grey scale `--sl-color-white … --sl-color-black` (inverted per theme, as in Starlight), accent `low/accent/high`, aside colours `tip/caution/danger` ×3, text/bg/hairline aliases, `--sl-text-*` scale, `--sl-nav-height`, `--sl-sidebar-width`, `--sl-content-width`, fonts (system stacks).[^base]

Base accent = Starlight indigo. A scheme = file that redefines variables after the base import; `css/themes/venus.css` redefines the six accent variables (amber) and nothing else.[^venus]

# Theme switching

`data-theme` on `<html>` is always resolved to `light` or `dark`; `auto` is a stored preference, resolved against `prefers-color-scheme` and re-resolved on media change.[^use-theme] `starlight::head` runs the same resolution inline before first paint → no flash.[^head] Storage key `theme` in `localStorage`; absent = auto.

Phiki code blocks carry both themes: inline light colours + `--phiki-dark-*` variables; `:root[data-theme='dark'] pre.phiki-themes span` switches to the dark variables. No re-render on toggle.[^base]

# Under Tailwind 4

Tailwind's utilities live in `@layer utilities`; unlayered CSS beats any layer. Import the theme into a layer so utilities keep winning:

```css
@import 'tailwindcss';
@import '../themes/starlight/css/starlight.css' layer(components);
@import '../themes/starlight/css/themes/venus.css' layer(components);
```

Without Tailwind, plain `@import` is right.

[^base]: Base stylesheet
[^venus]: Venus preset
[^use-theme]: useTheme composable
[^head]: starlight::head partial
