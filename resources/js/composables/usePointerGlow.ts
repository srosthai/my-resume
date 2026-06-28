import { onBeforeUnmount, onMounted, ref } from 'vue';

export interface PointerPosition {
    x: number;
    y: number;
}

/**
 * Tracks the pointer position as a percentage of the viewport, for cursor-follow
 * glow / parallax effects. The listener is rAF-throttled so it fires at most once
 * per frame instead of on every raw pointermove event.
 *
 * SSR-safe: the listener only attaches in the browser.
 */
export function usePointerGlow(initial: PointerPosition = { x: 50, y: 50 }) {
    const pointer = ref<PointerPosition>({ ...initial });
    let frame = 0;

    const handlePointer = (e: PointerEvent) => {
        if (frame) return;
        frame = requestAnimationFrame(() => {
            pointer.value = {
                x: (e.clientX / window.innerWidth) * 100,
                y: (e.clientY / window.innerHeight) * 100,
            };
            frame = 0;
        });
    };

    onMounted(() => {
        window.addEventListener('pointermove', handlePointer, { passive: true });
    });

    onBeforeUnmount(() => {
        window.removeEventListener('pointermove', handlePointer);
        if (frame) cancelAnimationFrame(frame);
    });

    return { pointer };
}
