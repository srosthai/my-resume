<script setup lang="ts">
import type { Component } from 'vue'

interface Props {
    icon: Component
    label: string
    value: string
}

defineProps<Props>()
</script>

<template>
    <div class="flex items-center gap-3">
        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10">
            <component :is="icon" class="size-4 text-primary" />
        </div>
        <div class="min-w-0">
            <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">
                {{ label }}
            </p>
            <p class="truncate text-sm font-semibold text-foreground">
                {{ value || 'Not specified' }}
            </p>
        </div>
    </div>
</template>
