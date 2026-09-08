import { ref } from 'vue';

/**
 * Page reveal state shared by the public pages.
 *
 * Content is available the moment the component renders (Inertia props are
 * already loaded and pages may be server-rendered), so there is no artificial
 * skeleton delay: `isLoading` is always false and `isVisible` always true.
 * The composable is kept so page templates keep one entry point for a future
 * real loading state (for example, driven by Inertia's router events).
 *
 * @param _delayMs kept for call-site compatibility; no longer used.
 */
// eslint-disable-next-line @typescript-eslint/no-unused-vars
export function usePageReveal(_delayMs = 0) {
    const isLoading = ref(false);
    const isVisible = ref(true);

    return { isLoading, isVisible };
}
