<script setup lang="ts">
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
    <div
        class="group flex h-full flex-col rounded-xl border border-border/30 bg-card/20 p-3.5 transition-all duration-300 hover:border-border/50 hover:bg-card/40"
    >
        <div class="mb-2.5 flex items-center gap-2.5">
            <Avatar class="size-8 shrink-0 rounded-lg">
                <AvatarImage v-if="logo" :src="logo" :alt="name" class="object-contain p-1" />
                <AvatarFallback
                    class="rounded-lg bg-muted/60 text-[10px] font-semibold text-muted-foreground"
                >
                    {{ getInitials(name) }}
                </AvatarFallback>
            </Avatar>

            <h3
                class="truncate text-sm font-semibold text-foreground"
            >
                {{ name }}
            </h3>
        </div>

        <p class="line-clamp-2 flex-1 text-xs leading-relaxed text-muted-foreground/80">
            {{ description }}
        </p>
    </div>
</template>
