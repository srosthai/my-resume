<script setup lang="ts">
import { computed } from 'vue';

/**
 * Meteor-shower backdrop (magicui-style) that follows the site theme:
 * every streak is mixed from the theme foreground, so it reads as dark
 * ink on the light drafting sheet and as pale light on the dark theme.
 *
 * Positions, trail length and timing are deterministic pseudo-random so
 * the server and the client render the exact same field (no hydration
 * mismatch), while still looking scattered.
 */
interface Props {
    /** Number of meteors in the field. */
    count?: number;
}

const props = withDefaults(defineProps<Props>(), {
    count: 16,
});

/** Stable pseudo-random in [0, 1) from a numeric seed. */
const noise = (seed: number) => {
    const value = Math.sin(seed * 999.7) * 43758.5453;
    return value - Math.floor(value);
};

const meteors = computed(() =>
    Array.from({ length: props.count }, (_, index) => {
        const seed = index + 1;

        return {
            // Start high and toward the right so the full drop crosses the stage.
            left: `${(noise(seed * 3.1) * 88 + 12).toFixed(1)}%`,
            top: `${(noise(seed * 7.7) * 55 - 15).toFixed(1)}%`,
            '--meteor-trail': `${Math.round(50 + noise(seed * 13.7) * 60)}px`,
            // Negative delay: the shower is mid-flight the moment the page opens.
            animationDelay: `-${(noise(seed * 5.3) * 8).toFixed(2)}s`,
            animationDuration: `${(5 + noise(seed * 11.3) * 4).toFixed(2)}s`,
        };
    }),
);
</script>

<template>
    <div class="meteors" aria-hidden="true">
        <span v-for="(meteor, index) in meteors" :key="index" class="meteor" :style="meteor" />
    </div>
</template>

<style scoped>
.meteors {
    /* Theme-aware ink: dark on the light theme, light on the dark theme. */
    --meteor-ink: color-mix(in oklab, var(--color-foreground) 42%, transparent);

    position: absolute;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
}

.meteor {
    position: absolute;
    width: 2px;
    height: 2px;
    border-radius: 9999px;
    background: var(--meteor-ink);
    box-shadow: 0 0 0 1px color-mix(in oklab, var(--meteor-ink) 30%, transparent);
    transform: rotate(325deg);
    animation-name: meteor-fall;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
}

/* The tail trails behind: the head travels down-left, the tail points up-right. */
.meteor::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    width: var(--meteor-trail, 54px);
    height: 1px;
    transform: translateY(-50%);
    background: linear-gradient(to right, var(--meteor-ink), transparent);
}

@keyframes meteor-fall {
    0% {
        transform: rotate(325deg) translateX(0);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    70% {
        opacity: 1;
    }
    100% {
        /* Falls far enough to cross the whole stage before the loop resets. */
        transform: rotate(325deg) translateX(-1750px);
        opacity: 0;
    }
}

@media (prefers-reduced-motion: reduce) {
    .meteors {
        display: none;
    }
}
</style>
