<script setup lang="ts">
import type { Component } from 'vue'
import { cn } from '@/lib/utils'

interface Props {
    class?: string
    title?: string
    description?: string
    icon?: Component
}

const props = defineProps<Props>()
</script>

<template>
    <div
        :class="
            cn(
                'group/bento relative flex cursor-pointer flex-col overflow-hidden rounded-xl',
                'border border-border/50 bg-card/80 p-3 sm:p-4',
                'transition-all duration-300',
                'hover:border-primary/40 hover:bg-card hover:shadow-xl hover:-translate-y-0.5',
                'focus:outline-none focus:ring-2 focus:ring-primary/50',
                props.class
            )
        "
        tabindex="0"
    >
        <!-- Header Slot (Image area) -->
        <div class="relative mb-3 aspect-[4/3] w-full flex-shrink-0 overflow-hidden rounded-lg">
            <slot name="header" />
        </div>

        <!-- Content Area -->
        <div class="flex flex-col gap-2">
            <!-- Title Row -->
            <div v-if="icon || title" class="flex items-center gap-2">
                <component
                    :is="icon"
                    v-if="icon"
                    class="size-4 shrink-0 text-primary"
                />
                <h3
                    v-if="title"
                    class="line-clamp-1 text-sm font-semibold text-foreground sm:text-base"
                >
                    {{ title }}
                </h3>
            </div>

            <!-- Description -->
            <p
                v-if="description"
                class="line-clamp-2 text-xs leading-relaxed text-muted-foreground sm:text-sm"
            >
                {{ description }}
            </p>
        </div>
    </div>
</template>