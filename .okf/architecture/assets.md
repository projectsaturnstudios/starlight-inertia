---
type: Architecture
title: Assets
description: The theme ships as Vue and CSS source under resources/, copied into the application by starlight:update-assets and reached through a Vite alias.
resource: src/Console/Commands/UpdateAssets.php
tags: [assets, vite, inertia, package]
status: stable
generated: { by: claude/fable-5.1, at: 2026-10-12T20:00:00Z }
sources:
  - id: command
    resource: src/Console/Commands/UpdateAssets.php
    title: UpdateAssets command
  - id: config
    resource: config/starlight.php
    title: starlight config
  - id: rdm
    resource: "scope: rdm-integrations/rdm-cms-theme, the Hybridly theme this package copies its distribution model from"
    title: rdm-cms-theme
---

# Overview

No npm package. `resources/` (components, layouts, composables, css, types, `index.ts`) is the deliverable and travels with the Composer package. `php artisan starlight:update-assets` deletes and rewrites `config('starlight.assets_path')` (default `resources/themes/starlight`) under `base_path()` with a copy of that tree.[^command][^config] The app aliases `@starlight` → that directory in Vite and `tsconfig` paths and imports from the copy.

Same model as rdm-cms-theme: `rdm-cms:update-assets` + an `RDM` alias on `resources/themes/rdm-cms`.[^rdm]

# Why copy, not import from vendor

- Vite, Tailwind's auto-source scan and vue-tsc all treat `resources/` as project code; `vendor/` needs allow-lists and `@source` lines.
- The app can commit the copy → builds from a clean clone without running the command.
- An app may patch a component locally; the next `update-assets` is a visible overwrite in its diff, not a silent vendor bump.

# Rules for the shipped source

- Only Vue 3, `@inertiajs/vue3` and the DOM. No Tailwind utilities (a consumer may not run it), no app routes or shared-prop names: the app passes everything via props/slots (see [Components](/api/components.md)).
- Kebab-case file names; `resources/index.ts` re-exports every public component.
- CSS imports are relative to the copy: `../themes/starlight/css/starlight.css` from `resources/css/app.css`.

# Guard

Target must be a relative path inside the app: empty, `.`, `..` segments and absolute paths → `INVALID` before any delete.[^command]

[^command]: UpdateAssets command
[^config]: starlight config
[^rdm]: rdm-cms-theme
