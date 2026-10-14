<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import Icon from './icon.vue';
import type { SearchResult } from '../types';

const props = withDefaults(
    defineProps<{ url?: string; placeholder?: string }>(),
    { url: '/search', placeholder: 'Search the docs…' },
);

const dialog = ref<HTMLDialogElement>();
const input = ref<HTMLInputElement>();
const query = ref('');
const results = ref<SearchResult[]>([]);
const selected = ref(0);
const searched = ref(false);
let timer: ReturnType<typeof setTimeout> | undefined;
let controller: AbortController | undefined;

function open() {
    if (dialog.value && !dialog.value.open) {
        dialog.value.showModal();
        void nextTick(() => input.value?.select());
    }
}

function close() {
    dialog.value?.close();
}

async function search(term: string) {
    controller?.abort();
    if (term.trim().length < 2) {
        results.value = [];
        searched.value = false;
        return;
    }
    controller = new AbortController();
    try {
        const separator = props.url.includes('?') ? '&' : '?';
        const response = await fetch(
            `${props.url}${separator}q=${encodeURIComponent(term)}`,
            {
                headers: { Accept: 'application/json' },
                signal: controller.signal,
            },
        );
        const data = (await response.json()) as { results: SearchResult[] };
        results.value = data.results;
        selected.value = 0;
        searched.value = true;
    } catch (error) {
        if (!(error instanceof DOMException && error.name === 'AbortError')) {
            results.value = [];
            searched.value = true;
        }
    }
}

watch(query, (value) => {
    clearTimeout(timer);
    timer = setTimeout(() => void search(value), 150);
});

function go(result: SearchResult) {
    close();
    router.visit(result.href);
}

function onKeydown(event: KeyboardEvent) {
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        selected.value = Math.min(selected.value + 1, results.value.length - 1);
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        selected.value = Math.max(selected.value - 1, 0);
    } else if (event.key === 'Enter' && results.value[selected.value]) {
        event.preventDefault();
        go(results.value[selected.value]);
    }
}

function onGlobalKeydown(event: KeyboardEvent) {
    const typing = (event.target as HTMLElement | null)?.closest(
        'input, textarea, select, [contenteditable]',
    );
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        open();
    } else if (event.key === '/' && !typing) {
        event.preventDefault();
        open();
    }
}

function onBackdropClick(event: MouseEvent) {
    if (event.target === dialog.value) {
        close();
    }
}

onMounted(() => window.addEventListener('keydown', onGlobalKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', onGlobalKeydown));

defineExpose({ open });
</script>

<template>
    <dialog
        ref="dialog"
        class="sl-search-dialog"
        aria-label="Search"
        @click="onBackdropClick"
    >
        <div class="input-row">
            <Icon name="search" />
            <input
                ref="input"
                v-model="query"
                type="text"
                role="searchbox"
                :placeholder="placeholder"
                autocomplete="off"
                spellcheck="false"
                @keydown="onKeydown"
            />
            <button
                type="button"
                class="sl-icon-link"
                aria-label="Close search"
                @click="close"
            >
                <Icon name="close" :size="16" />
            </button>
        </div>
        <ul v-if="results.length" class="results" role="listbox">
            <li
                v-for="(result, index) in results"
                :key="result.href"
                role="option"
                :aria-selected="index === selected"
            >
                <a
                    :href="result.href"
                    :aria-selected="index === selected"
                    @click.prevent="go(result)"
                    @mousemove="selected = index"
                >
                    <div class="result-section">{{ result.section }}</div>
                    <div class="result-title">{{ result.title }}</div>
                    <div class="result-snippet">{{ result.snippet }}</div>
                </a>
            </li>
        </ul>
        <p v-else-if="searched" class="empty">No results for “{{ query }}”.</p>
        <p v-else class="empty">
            Type at least two characters. ↑↓ to move, Enter to open, Esc to
            close.
        </p>
    </dialog>
</template>
