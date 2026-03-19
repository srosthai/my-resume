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
    <div class="group flex items-center gap-2.5 cursor-default">
        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-muted/60 transition-colors duration-200 group-hover:bg-primary/10">
            <component :is="icon" class="size-4 text-muted-foreground transition-colors duration-200 group-hover:text-primary" />
        </div>
        <div class="min-w-0">
            <p class="text-[10px] font-medium uppercase tracking-[0.15em] text-muted-foreground/60">
                {{ label }}
            </p>
            <p class="truncate text-sm font-semibold text-foreground">
                {{ value || 'Not specified' }}
            </p>
        </div>
    </div>
</template>
