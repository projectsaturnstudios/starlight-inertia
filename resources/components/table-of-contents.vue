<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import type { TocEntry } from '../types';

const props = defineProps<{ toc: TocEntry[] }>();
const current = ref<string | null>(null);
let ticking = false;

function update() {
    ticking = false;
    const navHeight =
        parseFloat(
            getComputedStyle(document.documentElement).getPropertyValue(
                '--sl-nav-height',
            ),
        ) * 16 || 56;
    let active: string | null = props.toc[0]?.slug ?? null;
    for (const entry of props.toc) {
        const el = document.getElementById(entry.slug);
        if (el && el.getBoundingClientRect().top <= navHeight + 32) {
            active = entry.slug;
        }
    }
    current.value = active;
}

function onScroll() {
    if (!ticking) {
        ticking = true;
        requestAnimationFrame(update);
    }
}

onMounted(() => {
    update();
    window.addEventListener('scroll', onScroll, { passive: true });
});
onBeforeUnmount(() => window.removeEventListener('scroll', onScroll));
watch(() => props.toc, update);
</script>

<template>
    <nav class="sl-toc" aria-labelledby="toc-heading">
        <h2 id="toc-heading">On this page</h2>
        <ul>
            <li v-for="entry in toc" :key="entry.slug">
                <a
                    :href="`#${entry.slug}`"
                    :data-depth="entry.depth"
                    :aria-current="entry.slug === current ? 'true' : undefined"
                >
                    {{ entry.text }}
                </a>
            </li>
        </ul>
    </nav>
</template>
