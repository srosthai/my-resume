<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
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
            class="timeline-dot absolute -left-16 top-6 hidden size-3 rounded-full border-2 border-primary bg-background transition-all duration-300 group-hover:scale-150 group-hover:bg-primary md:block"
        />

        <Card
            class="timeline-card border-border/40 bg-card/30 transition-all duration-300 hover:border-primary/30 hover:bg-card/50 hover:shadow-md"
        >
            <CardContent class="p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex-1 space-y-2">
                        <h4 class="text-lg font-semibold text-foreground">
                            {{ title }}
                        </h4>
                        <p class="text-sm font-medium text-primary">
                            {{ subtitle }}
                        </p>
                        <p class="text-sm leading-relaxed text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>

                    <Badge variant="secondary" class="w-fit shrink-0 gap-1.5 px-3 py-1.5 text-xs font-medium">
                        <Calendar class="size-3" />
                        {{ dateFrom }} - {{ dateTo }}
                    </Badge>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
