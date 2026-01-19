<script setup lang="ts">
import { cn } from '@/lib/utils'

interface Props {
    class?: string
}

const props = defineProps<Props>()
</script>

<template>
    <div
        :class="
            cn(
                'grid w-full gap-4 sm:gap-5',
                'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
                props.class
            )
        "
    >
        <slot />
    </div>
</template>