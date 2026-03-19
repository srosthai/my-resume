<script setup lang="ts">
import type { Component } from 'vue'

interface Props {
    title: string
    subtitle?: string
    icon?: Component
    centered?: boolean
}

withDefaults(defineProps<Props>(), {
    centered: false,
})
</script>

<template>
    <div class="mb-8">
        <div :class="['flex items-center gap-3', centered && 'justify-center']">
            <div v-if="icon" class="flex size-8 items-center justify-center rounded-lg bg-muted/60">
                <component :is="icon" class="size-4 text-muted-foreground" />
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl">
                {{ title }}
            </h2>
        </div>
        <p
            v-if="subtitle"
            :class="[
                'mt-2 text-sm text-muted-foreground',
                centered && 'mx-auto max-w-2xl text-center',
            ]"
        >
            {{ subtitle }}
        </p>
        <div :class="['mt-4 h-px w-12 bg-border/60', centered && 'mx-auto']"></div>
    </div>
</template>
