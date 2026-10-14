---
okf_version: "0.2"
---

# projectsaturnstudios/starlight-inertia

* [Assets](architecture/assets.md) - Source-shipped Vue + CSS copied into the app by `starlight:update-assets`; why copy beats importing from vendor; the `@starlight` alias.
* [Theming](architecture/theming.md) - Every colour, size and font is a `--sl-*` custom property; base = Starlight indigo; presets override after the import; the `layer(components)` rule under Tailwind.
* [Markdown](architecture/markdown.md) - The renderer pipeline, the HTML contract the CSS targets, `html_input` modes, TOC extraction.

# API

* [Components](api/components.md) - Props, slots and events of the layout, article, splash and chrome components, the composable and the types.
