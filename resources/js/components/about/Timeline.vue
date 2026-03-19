<script setup lang="ts">
import type { Component } from 'vue'
import TimelineCard from './TimelineCard.vue'

interface TimelineItem {
    id: number
    title: string
    company?: string
    institution?: string
    description: string
    from: string
    to: string
}

interface Props {
    title: string
    icon: Component
    items: TimelineItem[]
    subtitleKey?: 'company' | 'institution'
}

withDefaults(defineProps<Props>(), {
    subtitleKey: 'company',
})
</script>

<template>
    <div class="mb-12">
        <div class="mb-5 flex items-center gap-2.5">
            <div class="flex size-8 items-center justify-center rounded-lg bg-muted/60">
                <component :is="icon" class="size-4 text-muted-foreground" />
            </div>
            <h3 class="text-base font-semibold tracking-tight text-foreground">
                {{ title }}
            </h3>
        </div>

        <div class="relative space-y-3">
            <!-- Timeline line -->
            <div
                class="absolute bottom-0 left-[5px] top-0 hidden w-px bg-gradient-to-b from-border/60 via-border/30 to-transparent md:block"
            />

            <TimelineCard
                v-for="(item, index) in items"
                :key="item.id"
                :title="item.title"
                :subtitle="item[subtitleKey] || ''"
                :description="item.description"
                :date-from="item.from"
                :date-to="item.to"
                :index="index"
            />
        </div>
    </div>
</template>
