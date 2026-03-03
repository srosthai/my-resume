<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'

interface Props {
    name: string
    logo?: string | null
    type: string
    description: string
}

defineProps<Props>()

const getInitials = (name: string): string => {
    return name.slice(0, 2).toUpperCase()
}
</script>

<template>
    <Card
        class="group h-full border-border/40 bg-card/30 transition-all duration-300 hover:border-primary/30 hover:bg-card/50 hover:shadow-md"
    >
        <CardContent class="flex h-full flex-col p-4">
            <div class="mb-3 flex items-start gap-3">
                <Avatar class="size-10 shrink-0 rounded-lg">
                    <AvatarImage v-if="logo" :src="logo" :alt="name" class="object-contain p-1.5" />
                    <AvatarFallback class="rounded-lg bg-primary/10 text-xs font-bold text-primary">
                        {{ getInitials(name) }}
                    </AvatarFallback>
                </Avatar>

                <div class="min-w-0 flex-1">
                    <h3
                        class="truncate text-sm font-semibold text-foreground transition-colors duration-300 group-hover:text-primary"
                    >
                        {{ name }}
                    </h3>
                    <Badge variant="outline" class="mt-1 border-primary/20 bg-primary/5 px-2 py-0 text-[10px] text-primary">
                        {{ type }}
                    </Badge>
                </div>
            </div>

            <p class="line-clamp-2 flex-1 text-xs leading-relaxed text-muted-foreground">
                {{ description }}
            </p>
        </CardContent>
    </Card>
</template>
