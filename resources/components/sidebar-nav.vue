<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import SidebarGroup from './sidebar-group.vue';
import type { NavNode } from '../types';

defineProps<{ nav: NavNode[] }>();
const emit = defineEmits<{ navigate: [] }>();
const page = usePage();
const current = computed(() => page.url.split(/[?#]/)[0]);
</script>

<template>
    <div class="sl-nav">
        <ul>
            <SidebarGroup
                v-for="node in nav"
                :key="node.id"
                :node="node"
                :current="current"
                @navigate="emit('navigate')"
            />
        </ul>
    </div>
</template>
