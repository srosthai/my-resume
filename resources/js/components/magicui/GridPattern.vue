<script setup lang="ts">
import { cn } from '@/lib/utils';
import { computed, useId } from 'vue';

interface Props {
    /** Cell size in px. */
    width?: number;
    height?: number;
    /** Pattern origin offset — -1 keeps the stroke crisp on the edge. */
    x?: number;
    y?: number;
    strokeDasharray?: string | number;
    /** Cells to fill, as [column, row] pairs. */
    squares?: [number, number][];
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    width: 40,
    height: 40,
    x: -1,
    y: -1,
    strokeDasharray: 0,
});

// Unique per instance so two grids on one page can't share a <pattern>.
const patternId = computed(() => `grid-pattern-${useId()}`);
</script>

<template>
    <svg
        aria-hidden="true"
        :class="
            cn(
                'pointer-events-none absolute inset-0 h-full w-full fill-foreground/[0.045] stroke-foreground/[0.07]',
                props.class,
            )
        "
    >
        <defs>
            <pattern :id="patternId" :width="width" :height="height" patternUnits="userSpaceOnUse" :x="x" :y="y">
                <path :d="`M.5 ${height}V.5H${width}`" fill="none" :stroke-dasharray="strokeDasharray" />
            </pattern>
        </defs>

        <rect width="100%" height="100%" stroke-width="0" :fill="`url(#${patternId})`" />

        <svg v-if="squares" :x="x" :y="y" class="overflow-visible">
            <rect
                v-for="([sx, sy], i) in squares"
                :key="`${sx}-${sy}-${i}`"
                stroke-width="0"
                :width="width - 1"
                :height="height - 1"
                :x="sx * width + 1"
                :y="sy * height + 1"
            />
        </svg>
    </svg>
</template>
