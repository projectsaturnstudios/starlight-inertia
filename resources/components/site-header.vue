<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Icon from './icon.vue';
import SearchDialog from './search-dialog.vue';
import ThemeSelect from './theme-select.vue';

withDefaults(
    defineProps<{
        title: string;
        homeHref?: string;
        logoSrc?: string | null;
        badge?: string | null;
        githubUrl?: string | null;
        searchUrl?: string | null;
        menu?: boolean;
        menuOpen?: boolean;
    }>(),
    {
        homeHref: '/',
        logoSrc: null,
        badge: null,
        githubUrl: null,
        searchUrl: '/search',
        menu: true,
        menuOpen: false,
    },
);
defineEmits<{ toggleMenu: [] }>();

const search = ref<InstanceType<typeof SearchDialog>>();
const isMac =
    typeof navigator !== 'undefined' &&
    /Mac|iPhone|iPad/.test(navigator.platform);
</script>

<template>
    <header class="sl-header">
        <button
            v-if="menu"
            type="button"
            class="sl-icon-link menu-button"
            :aria-expanded="menuOpen"
            aria-label="Menu"
            @click="$emit('toggleMenu')"
        >
            <Icon :name="menuOpen ? 'close' : 'menu'" :size="20" />
        </button>

        <Link :href="homeHref" class="site-title">
            <slot name="logo">
                <img
                    v-if="logoSrc"
                    :src="logoSrc"
                    alt=""
                    width="24"
                    height="24"
                />
            </slot>
            {{ title }}
        </Link>

        <span v-if="badge" class="sl-badge">{{ badge }}</span>

        <div class="sl-header-center">
            <button
                v-if="searchUrl"
                type="button"
                class="search-button"
                @click="search?.open()"
            >
                <Icon name="search" :size="16" />
                <span>Search</span>
                <kbd>{{ isMac ? '⌘' : 'Ctrl' }} K</kbd>
            </button>
        </div>

        <div class="sl-header-actions">
            <button
                v-if="searchUrl"
                type="button"
                class="sl-icon-link search-trigger-compact"
                aria-label="Search"
                @click="search?.open()"
            >
                <Icon name="search" :size="20" />
            </button>

            <slot name="actions" />

            <a
                v-if="githubUrl"
                :href="githubUrl"
                class="sl-icon-link github-link"
                rel="noopener"
                target="_blank"
                aria-label="GitHub"
            >
                <Icon name="github" :size="20" />
            </a>

            <ThemeSelect />
        </div>

        <SearchDialog v-if="searchUrl" ref="search" :url="searchUrl" />
    </header>
</template>
