# Agent rules for starlight-inertia

Read [`.okf/index.md`](.okf/index.md) first; every rule here points at the concept that explains it.

1. **`resources/` is the product.** Components, layout, composables, CSS and types ship as source and are copied into an application by `starlight:update-assets`. They use only what every consumer has: Vue 3, `@inertiajs/vue3`, the DOM. No Tailwind classes, no app-specific routes or shared-prop names; everything an application decides comes in through props and slots. See `.okf/architecture/assets.md`.
2. **CSS is custom properties.** New colours, sizes and fonts are `--sl-*` variables on `:root` and `:root[data-theme='dark']`, so a scheme stays a file of overrides. The base ships Starlight's indigo; presets live in `css/themes/`. See `.okf/architecture/theming.md`.
3. **The renderer and the stylesheet are one contract.** A class the CSS targets (`aside`, `aside-title`, `phiki`, `footnotes`, heading ids) is produced by `src/Markdown`; change both sides together and add a Pest case. See `.okf/architecture/markdown.md`.
4. **Raw HTML passes through on purpose.** `html_input` defaults to `allow` for trusted authors and is the application's knob; never add escaping inside the renderer that the mode doesn't ask for. See `SECURITY.md`.
5. **Files are kebab-case under `resources/`**, PSR-4 under `src/`. The barrel `resources/index.ts` re-exports every public component; add new ones there.
6. **Versioning.** `composer.json` carries the version; commits read `(x.y.z) - Imperative summary`. Bumps are the maintainer's.
7. **Tests.** `tests/` is plain Pest without Laravel; the renderer is constructed directly. Run `composer install && vendor/bin/pest`, then remove `vendor/` and `composer.lock` (both ignored).
