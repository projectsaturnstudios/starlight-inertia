<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import SidebarNav from '../components/sidebar-nav.vue';
import SiteHeader from '../components/site-header.vue';
import type { NavNode } from '../types';

/**
 * The Starlight page frame: fixed header, collapsible sidebar (drawer below
 * 64rem), content frame, and a transient toast for `notice`.
 */
const props = withDefaults(
    defineProps<{
        title: string;
        nav?: NavNode[];
        sidebar?: boolean;
        homeHref?: string;
        logoSrc?: string | null;
        badge?: string | null;
        githubUrl?: string | null;
        searchUrl?: string | null;
        notice?: string | null;
    }>(),
    {
        nav: () => [],
        sidebar: true,
        homeHref: '/',
        logoSrc: null,
        badge: null,
        githubUrl: null,
        searchUrl: '/search',
        notice: null,
    },
);

const open = ref(false);
const toast = ref<string | null>(null);
let toastTimer: ReturnType<typeof setTimeout> | undefined;

watch(
    () => props.notice,
    (message) => {
        clearTimeout(toastTimer);
        toast.value = message ?? null;
        if (message) {
            toastTimer = setTimeout(() => (toast.value = null), 5000);
        }
    },
    { immediate: true },
);

router.on('navigate', () => {
    open.value = false;
});
</script>

<template>
    <div class="sl-page" :data-has-sidebar="sidebar">
        <SiteHeader
            :title="title"
            :home-href="homeHref"
            :logo-src="logoSrc"
            :badge="badge"
            :github-url="githubUrl"
            :search-url="searchUrl"
            :menu="sidebar"
            :menu-open="open"
            @toggle-menu="open = !open"
        >
            <template v-if="$slots.logo" #logo><slot name="logo" /></template>
            <template v-if="$slots.actions" #actions
                ><slot name="actions"
            /></template>
        </SiteHeader>
        <nav
            v-if="sidebar"
            class="sl-sidebar"
            :class="{ open }"
            aria-label="Main"
        >
            <slot name="sidebar-top" />
            <SidebarNav :nav="nav" @navigate="open = false" />
            <slot name="sidebar-bottom" />
        </nav>
        <div class="sl-main-frame">
            <slot />
        </div>
        <div v-if="toast" class="sl-toast" role="status" @click="toast = null">
            {{ toast }}
        </div>
    </div>
</template>
