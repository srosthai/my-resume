<script setup lang="ts">
import type { Component } from 'vue'
import TimelineCard from './TimelineCard.vue'
import SectionHeader from './SectionHeader.vue'

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
        <SectionHeader :title="title" :icon="icon" />

        <div class="relative space-y-4">
            <!-- Timeline line -->
            <div
                class="absolute bottom-0 left-[5px] top-0 hidden w-px bg-gradient-to-b from-primary/50 via-border to-transparent md:block"
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
