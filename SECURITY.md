# Security Policy

## Supported versions

starlight-inertia is pre-1.0. No 0.x release receives security fixes or advisories; fixes land in the
next release line. Security support starts with 1.0.

| Version | Security fixes |
|---------|----------------|
| < 1.0   | No             |

## Reporting a vulnerability

Please don't open a public issue for a security problem.

Report it privately through GitHub: the **Report a vulnerability** button on the
[projectsaturnstudios/starlight-inertia](https://github.com/projectsaturnstudios/starlight-inertia)
repository's **Security** tab. If that isn't available, email **info@projectsaturnstudios.com**.

Include what you found, the affected version, the Laravel and Inertia versions, and steps to
reproduce. Reports are read and weighed for the release line in development; before 1.0 there is
no response-time commitment.

## Security model

The package has two surfaces: a markdown renderer that runs in the application, and Vue components
that put the renderer's output into the page.

- **Rendered HTML is trusted by design.** `Renderer::render()` returns HTML that `article.vue`
  places with `v-html`, unescaped. The renderer never adds escaping to what the author wrote: with
  the default `html_input = allow`, raw HTML in the markdown reaches the page as written, so a
  `<script>` or an `onerror=` attribute written by an author runs for every reader. That default
  exists for trusted authors (an admin editing their own docs, README imports from repositories you
  control). For content from anyone else, set `starlight.markdown.html_input` to `escape` or `strip`
  before it is rendered; the setting applies at construction, so content rendered and cached under
  `allow` stays as it was.
- **Link targets.** CommonMark's `allow_unsafe_links` is off and cannot be turned on through this
  package: `javascript:`, `vbscript:`, `file:` and `data:` URLs in links and images are dropped in
  every `html_input` mode.
- **Asides, alerts, code.** Aside titles (`:::note[Title]`) are HTML-escaped. Alert markers and
  fence info strings select a type or grammar and are never echoed. Phiki escapes code block
  contents; the inline `style` attributes it emits are colours from the theme files, not from the
  markdown.
- **`starlight:update-assets`** deletes and rewrites the directory named by
  `starlight.assets_path`, resolved under `base_path()`. The command refuses an empty path, `.`,
  a path containing `..` or an absolute path, so a mis-set config cannot point the wipe outside the
  application. It copies only the package's own `resources/` tree and reads nothing else.
- **Search dialog.** Results come from the URL the application passes in and are rendered through
  Vue interpolation, never as HTML; a result's `href` is followed with Inertia's router. Anything
  else about the search endpoint (authorisation, rate limits, what it indexes) belongs to the
  application.
- **Theme state.** The only client-side storage is the `theme` key in `localStorage`, read into
  a `data-theme` attribute after validation against `light` / `dark`.

A report is in scope when the renderer emits markup the author did not write, when a link target
the renderer should drop survives, when an `escape` or `strip` mode lets raw HTML through, when the
asset command can be made to touch a path outside the configured directory, or when a component
renders application data as HTML. Content an author wrote under `allow` reaching the page is the
documented behaviour.
