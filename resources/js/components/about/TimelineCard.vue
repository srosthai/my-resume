<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import { Calendar } from 'lucide-vue-next'

interface Props {
    title: string
    subtitle: string
    description: string
    dateFrom: string
    dateTo: string
    index?: number
}

withDefaults(defineProps<Props>(), {
    index: 0,
})
</script>

<template>
    <div class="timeline-item group relative md:ml-12">
        <!-- Timeline dot -->
        <div
            class="timeline-dot absolute -left-16 top-6 hidden size-2.5 rounded-full border-2 border-border bg-background transition-all duration-300 group-hover:border-primary group-hover:bg-primary md:block"
        />

        <div
            class="rounded-xl border border-border/30 bg-card/20 p-5 transition-all duration-300 hover:border-border/50 hover:bg-card/40"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex-1 space-y-1.5">
                    <h4 class="text-base font-semibold text-foreground">
                        {{ title }}
                    </h4>
                    <p class="text-sm font-medium text-primary/80">
                        {{ subtitle }}
                    </p>
                    <p class="pt-1 text-sm leading-relaxed text-muted-foreground">
                        {{ description }}
                    </p>
                </div>

                <Badge
                    variant="outline"
                    class="w-fit shrink-0 gap-1.5 border-border/40 bg-muted/30 px-2.5 py-1 text-[11px] font-normal text-muted-foreground"
                >
                    <Calendar class="size-3 opacity-50" />
                    {{ dateFrom }} - {{ dateTo }}
                </Badge>
            </div>
        </div>
    </div>
</template>
