<script setup lang="ts">
import GridPattern from '@/components/magicui/GridPattern.vue';
import Meteors from '@/components/magicui/Meteors.vue';

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

        <!-- Ink streaks across the whole sheet; seeded client-side, hidden under reduced motion -->
        <Meteors :number="16" :min-delay="0" :max-delay="8" :min-duration="4" :max-duration="12" />
    </div>
</template>
