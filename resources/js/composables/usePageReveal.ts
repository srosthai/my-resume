import { onBeforeUnmount, onMounted, ref } from 'vue';

/**
 * Drives the shared page-load reveal: shows a skeleton (`isLoading`) for a short
 * beat, then flips `isVisible` on the next frame to trigger the entrance animation.
 * The timeout is cleared on unmount so it never fires after the component is gone.
 *
 * @param delayMs skeleton duration before reveal; default 450ms.
 */
export function usePageReveal(delayMs = 450) {
    const isLoading = ref(true);
    const isVisible = ref(false);
    let timer: ReturnType<typeof setTimeout> | null = null;
    let frame = 0;

    onMounted(() => {
        timer = setTimeout(() => {
            isLoading.value = false;
            frame = requestAnimationFrame(() => {
                isVisible.value = true;
            });
        }, delayMs);
    });

    onBeforeUnmount(() => {
        if (timer) clearTimeout(timer);
        if (frame) cancelAnimationFrame(frame);
    });

    return { isLoading, isVisible };
}
