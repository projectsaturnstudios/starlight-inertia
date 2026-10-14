<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Icon from './icon.vue';

const props = withDefaults(
    defineProps<{
        title: string;
        href?: string | null;
        icon?: string | null;
    }>(),
    { href: null, icon: null },
);
const external = computed(
    () =>
        props.href !== null && /^(?:[a-z][a-z0-9+.-]*:|\/\/)/i.test(props.href),
);
const tag = computed(() =>
    props.href === null ? 'div' : external.value ? 'a' : Link,
);
const attrs = computed(() =>
    external.value
        ? { href: props.href, target: '_blank', rel: 'noopener' }
        : props.href
          ? { href: props.href }
          : {},
);
</script>

<template>
    <component :is="tag" v-bind="attrs" class="sl-card">
        <span class="title">
            <Icon
                v-if="icon ?? (external ? 'external' : null)"
                :name="icon ?? 'external'"
                :size="20"
            />
            {{ title }}
        </span>
        <span v-if="$slots.default"><slot /></span>
    </component>
</template>
