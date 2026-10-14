<script setup lang="ts">
import { ref } from 'vue';
import Icon from './icon.vue';
import { setTheme, storedTheme, type Theme } from '../composables/useTheme';

const theme = ref<Theme>(storedTheme());

function change(event: Event) {
    theme.value = (event.target as HTMLSelectElement).value as Theme;
    setTheme(theme.value);
}
</script>

<template>
    <label class="theme-select">
        <span class="sr-only">Theme</span>
        <Icon :name="theme === 'light' ? 'sun' : 'moon'" :size="16" />
        <select :value="theme" @change="change">
            <option value="dark">Dark</option>
            <option value="light">Light</option>
            <option value="auto">Auto</option>
        </select>
    </label>
</template>
