<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        href: string;
        variant?: 'primary' | 'minimal';
        small?: boolean;
    }>(),
    { variant: 'primary', small: false },
);
const external = computed(() =>
    /^(?:[a-z][a-z0-9+.-]*:|\/\/)/i.test(props.href),
);
</script>

<template>
    <a
        v-if="external"
        :href="href"
        class="sl-button"
        :class="[variant, { small }]"
        target="_blank"
        rel="noopener"
    >
        <slot />
    </a>
    <Link v-else :href="href" class="sl-button" :class="[variant, { small }]">
        <slot />
    </Link>
</template>
