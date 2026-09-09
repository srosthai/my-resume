<script setup lang="ts">
import GridPattern from '@/components/magicui/GridPattern.vue';

// Sparse filled cells, as [column, row]. Read as pencil marks on graph
// paper rather than a pattern — kept off-centre so they never sit
// directly behind the hero card.
const marks: [number, number][] = [
    [2, 3],
    [5, 8],
    [3, 13],
    [8, 17],
    [1, 20],
    [28, 2],
    [32, 6],
    [30, 11],
    [34, 15],
    [27, 19],
];
</script>

<template>
    <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden" aria-hidden="true">
        <GridPattern
            :width="44"
            :height="44"
            :squares="marks"
            class="[mask-image:radial-gradient(135%_105%_at_50%_0%,black_35%,transparent_88%)] fill-[var(--grid-cell)] stroke-[var(--grid-line)]"
        />
    </div>
</template>
