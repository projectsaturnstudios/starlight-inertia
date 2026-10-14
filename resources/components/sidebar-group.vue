<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Icon from './icon.vue';
import type { NavNode } from '../types';

const props = defineProps<{ node: NavNode; current: string }>();
const emit = defineEmits<{ navigate: [] }>();

function contains(node: NavNode): boolean {
    return (
        node.pages.some((p) => p.href === props.current) ||
        node.children.some(contains)
    );
}

const open = computed(
    () => props.node.collapsed === false || contains(props.node),
);
</script>

<template>
    <li v-if="node.url">
        <a :href="node.url" target="_blank" rel="noopener">
            <span class="group-label">{{ node.title }}</span>
            <Icon name="external" :size="14" />
        </a>
    </li>
    <li v-else>
        <details :open="open">
            <summary>
                <span class="group-label">{{ node.title }}</span>
                <Icon name="chevron-right" :size="16" class="caret" />
            </summary>
            <ul>
                <li v-for="page in node.pages" :key="page.id">
                    <Link
                        :href="page.href"
                        :aria-current="
                            page.href === current ? 'page' : undefined
                        "
                        @click="emit('navigate')"
                    >
                        {{ page.title }}
                    </Link>
                </li>
                <SidebarGroup
                    v-for="child in node.children"
                    :key="child.id"
                    :node="child"
                    :current="current"
                    @navigate="emit('navigate')"
                />
            </ul>
        </details>
    </li>
</template>
