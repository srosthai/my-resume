<script setup lang="ts">
import { cn } from '@/lib/utils';
import { onMounted, ref, watch, type CSSProperties } from 'vue';

interface Props {
    /** How many streaks are in flight at once. */
    number?: number;
    /** Seconds before a meteor's first pass, picked between min and max. */
    minDelay?: number;
    maxDelay?: number;
    /** Seconds for one full pass, picked between min and max. */
    minDuration?: number;
    maxDuration?: number;
    /** Direction of travel in degrees. 215 falls from top-left toward bottom-right. */
    angle?: number;
    /** How far a meteor travels before it fades. Any CSS length. */
    travel?: string;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    number: 20,
    minDelay: 0.2,
    maxDelay: 1.2,
    minDuration: 2,
    maxDuration: 10,
    angle: 215,
    travel: '-120vmax',
});

// Positions are randomised on the client only, so SSR markup and the
// hydrated DOM never disagree.
const meteors = ref<CSSProperties[]>([]);

const rand = (min: number, max: number) => Math.random() * (max - min) + min;

const seed = () => {
    meteors.value = Array.from({ length: props.number }, () => {
        const style: Record<string, string> = {
            '--angle': `${props.angle}deg`,
            '--travel': props.travel,
            '--delay': `${rand(props.minDelay, props.maxDelay).toFixed(2)}s`,
            '--duration': `${Math.floor(rand(props.minDuration, props.maxDuration))}s`,
            '--tail': `${Math.floor(rand(60, 180))}px`,
            top: '-5%',
            // Start some off the left edge so the whole width sees traffic.
            left: `${Math.floor(rand(-50, 100))}%`,
        };
        return style as CSSProperties;
    });
};

onMounted(seed);
watch(() => [props.number, props.angle, props.travel, props.minDelay, props.maxDelay, props.minDuration, props.maxDuration], seed);
</script>

<template>
    <div :class="cn('pointer-events-none absolute inset-0 overflow-hidden', props.class)" aria-hidden="true">
        <span v-for="(style, i) in meteors" :key="i" :style="style" class="meteor animate-meteor">
            <!-- The tail trails behind the head along the line of travel -->
            <span class="meteor-tail"></span>
        </span>
    </div>
</template>

<style scoped>
.meteor {
    position: absolute;
    width: 2px;
    height: 2px;
    border-radius: 9999px;
    background: var(--meteor-ink);
    box-shadow: 0 0 0 1px var(--meteor-glow);
    will-change: transform, opacity;
}

.meteor-tail {
    position: absolute;
    top: 50%;
    left: 0;
    width: var(--tail, 60px);
    height: 1px;
    transform: translateY(-50%);
    background: linear-gradient(90deg, var(--meteor-ink), transparent);
}
</style>
