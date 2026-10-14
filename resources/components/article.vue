<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import Icon from './icon.vue';
import TableOfContents from './table-of-contents.vue';
import type { PageLink, TocEntry } from '../types';

/**
 * A documentation article: overline, title, description, mobile + desktop
 * table of contents, the rendered body, meta line and prev/next pagination.
 * Pass `html` for server-rendered markdown or use the default slot.
 */
const props = withDefaults(
    defineProps<{
        title: string;
        description?: string | null;
        overline?: string[];
        html?: string | null;
        toc?: TocEntry[];
        prev?: PageLink | null;
        next?: PageLink | null;
        updatedAt?: string | null;
        editHref?: string | null;
        editLabel?: string;
        draft?: boolean;
        wide?: boolean;
    }>(),
    {
        description: null,
        overline: () => [],
        html: null,
        toc: () => [],
        prev: null,
        next: null,
        updatedAt: null,
        editHref: null,
        editLabel: 'Edit this page',
        draft: false,
        wide: false,
    },
);

const updated = computed(() =>
    props.updatedAt
        ? new Date(props.updatedAt).toLocaleDateString(undefined, {
              year: 'numeric',
              month: 'short',
              day: 'numeric',
          })
        : null,
);

/** Same-origin links inside rendered markdown stay inside the SPA. */
function onContentClick(event: MouseEvent) {
    const anchor = (event.target as HTMLElement).closest('a');
    if (
        !anchor ||
        event.defaultPrevented ||
        event.button !== 0 ||
        event.metaKey ||
        event.ctrlKey ||
        event.shiftKey ||
        event.altKey
    ) {
        return;
    }
    if (
        anchor.target === '_blank' ||
        anchor.origin !== window.location.origin ||
        anchor.hasAttribute('download')
    ) {
        return;
    }
    if (anchor.pathname === window.location.pathname && anchor.hash) {
        return;
    }
    event.preventDefault();
    router.visit(anchor.href);
}
</script>

<template>
    <div class="sl-main">
        <article :class="{ 'sl-wide': wide }">
            <header class="sl-article-header">
                <p v-if="overline.length || draft" class="sl-overline">
                    <template v-for="(crumb, index) in overline" :key="index">
                        <span v-if="index > 0" aria-hidden="true">›</span>
                        <span>{{ crumb }}</span>
                    </template>
                    <span v-if="draft" class="sl-chip draft">Draft</span>
                </p>
                <h1 class="sl-title">{{ title }}</h1>
                <p v-if="description" class="sl-description">
                    {{ description }}
                </p>
                <slot name="header" />
            </header>

            <details v-if="toc.length" class="sl-toc-mobile">
                <summary>On this page</summary>
                <TableOfContents :toc="toc" />
            </details>

            <div
                v-if="html !== null"
                class="sl-markdown-content"
                @click="onContentClick"
                v-html="html"
            />
            <slot v-else />

            <footer
                v-if="editHref || updated || prev || next || $slots.meta"
                class="sl-article-footer"
            >
                <div class="sl-meta">
                    <Link v-if="editHref" :href="editHref"
                        ><Icon name="pencil" :size="14" /> {{ editLabel }}</Link
                    >
                    <span v-if="updated" class="sl-meta-item"
                        ><Icon name="clock" :size="14" /> Last updated
                        {{ updated }}</span
                    >
                    <slot name="meta" />
                </div>
                <nav
                    v-if="prev || next"
                    class="sl-pagination"
                    aria-label="Pagination"
                >
                    <Link v-if="prev" :href="prev.href" class="prev" rel="prev">
                        <Icon name="chevron-left" :size="20" />
                        <span
                            ><span class="label">Previous</span
                            ><span class="link-title">{{
                                prev.title
                            }}</span></span
                        >
                    </Link>
                    <Link v-if="next" :href="next.href" class="next" rel="next">
                        <span
                            ><span class="label">Next</span
                            ><span class="link-title">{{
                                next.title
                            }}</span></span
                        >
                        <Icon name="chevron-right" :size="20" />
                    </Link>
                </nav>
            </footer>
        </article>

        <aside
            v-if="toc.length"
            class="sl-right-sidebar"
            aria-label="Table of contents"
        >
            <div>
                <TableOfContents :toc="toc" />
            </div>
        </aside>
    </div>
</template>
