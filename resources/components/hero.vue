<script setup lang="ts">
defineProps<{ title: string; tagline?: string | null }>();
</script>

<template>
    <section class="sl-hero">
        <div>
            <p v-if="$slots.eyebrow" class="eyebrow">
                <slot name="eyebrow" />
            </p>
            <h1>{{ title }}</h1>
            <p v-if="tagline" class="tagline">{{ tagline }}</p>
            <div v-if="$slots.actions" class="actions">
                <slot name="actions" />
            </div>
        </div>
        <div v-if="$slots.image" class="sl-hero-image">
            <slot name="image" />
        </div>
    </section>
</template>
