<script setup lang="ts">
import { cn } from '@/lib/utils';

interface DockIconProps {
    class?: string;
}

defineProps<DockIconProps>();
</script>

<template>
    <div :class="cn('group flex aspect-square cursor-pointer items-center justify-center rounded-full border', $props.class)">
        <slot />
    </div>
</template>
