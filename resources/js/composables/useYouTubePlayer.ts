import { getCurrentScope, onScopeDispose, ref, type Ref } from 'vue';

const YOUTUBE_API_SRC = 'https://www.youtube.com/iframe_api';

/** Shared promise so that several components can wait for the same script load. */
let apiPromise: Promise<typeof YT> | null = null;

/**
 * Extract the 11 character YouTube video id from a watch / share / embed / shorts URL.
 * A bare id is returned unchanged. Extra query parameters (playlists, timestamps) are ignored.
 */
export function extractYouTubeId(url: string | null | undefined): string | null {
    if (!url) {
        return null;
    }

    const trimmed = url.trim();

    if (/^[A-Za-z0-9_-]{11}$/.test(trimmed)) {
        return trimmed;
    }

    const match = trimmed.match(
        /(?:youtube(?:-nocookie)?\.com\/(?:watch\?(?:[^#]*&)?v=|embed\/|shorts\/|live\/|v\/)|youtu\.be\/)([A-Za-z0-9_-]{11})(?![A-Za-z0-9_-])/,
    );

    return match ? match[1] : null;
}

/** Format seconds as m:ss for the progress readouts. */
export function formatTime(seconds: number): string {
    const safe = Number.isFinite(seconds) && seconds > 0 ? Math.floor(seconds) : 0;
    const mins = Math.floor(safe / 60);
    const secs = safe % 60;

    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

const isApiReady = (): boolean => typeof window !== 'undefined' && typeof window.YT?.Player === 'function';

/**
 * Lazily inject the IFrame API script and resolve once `YT.Player` is usable.
 * Only one script tag is ever injected; concurrent callers share the same promise and any
 * previously registered `onYouTubeIframeAPIReady` handler is still invoked.
 */
export function loadYouTubeApi(): Promise<typeof YT> {
    if (typeof window === 'undefined' || typeof document === 'undefined') {
        return Promise.reject(new Error('YouTube player is only available in the browser'));
    }

    if (isApiReady() && window.YT) {
        return Promise.resolve(window.YT);
    }

    if (apiPromise) {
        return apiPromise;
    }

    apiPromise = new Promise<typeof YT>((resolve, reject) => {
        const previousHandler = window.onYouTubeIframeAPIReady;

        window.onYouTubeIframeAPIReady = () => {
            previousHandler?.();

            if (window.YT) {
                resolve(window.YT);
            } else {
                apiPromise = null;
                reject(new Error('YouTube API did not initialise'));
            }
        };

        const alreadyInjected = Array.from(document.scripts).some((script) => script.src.startsWith(YOUTUBE_API_SRC));

        if (alreadyInjected) {
            return;
        }

        const tag = document.createElement('script');
        tag.src = YOUTUBE_API_SRC;
        tag.async = true;
        tag.onerror = () => {
            apiPromise = null;
            tag.remove();
            reject(new Error('Could not load the YouTube player'));
        };

        document.head.appendChild(tag);
    });

    return apiPromise;
}

const PLAYER_ERROR_MESSAGES: Record<number, string> = {
    2: 'This video link is invalid',
    5: 'This video cannot be played in this browser',
    100: 'This video is unavailable',
    101: 'This video cannot be played outside YouTube',
    150: 'This video cannot be played outside YouTube',
};

export interface UseYouTubePlayerOptions {
    /** Id of the (hidden) element the iframe replaces. It must exist in the DOM when playback starts. */
    elementId: string;
    /** Called when the current video finishes. */
    onEnded?: () => void;
    /** Extra `playerVars` merged over the hidden-player defaults. */
    playerVars?: Record<string, string | number>;
    /** Polling interval for `currentTime` / `duration` while playing, in ms. */
    pollInterval?: number;
}

export interface UseYouTubePlayerReturn {
    /** True once the underlying `YT.Player` fired `onReady`. */
    ready: Ref<boolean>;
    /** True while the API script or the player is being created. */
    loading: Ref<boolean>;
    isPlaying: Ref<boolean>;
    /** Whole seconds, refreshed while playing and on state changes. */
    currentTime: Ref<number>;
    /** Whole seconds, 0 until the player reports it. */
    duration: Ref<number>;
    /** Id of the video currently loaded in the player, if any. */
    videoId: Ref<string | null>;
    /** Human readable failure, or null. Cleared when a new video is loaded. */
    error: Ref<string | null>;
    /** Load (and by default start) a video. Creates the player, injecting the API, on first use. */
    loadVideoById: (videoId: string, autoplay?: boolean) => Promise<void>;
    /** Cue a video without starting it. */
    load: (videoId: string) => Promise<void>;
    /** Play the given video (loading it if it is not the current one) or resume the current one. */
    play: (videoId?: string) => Promise<void>;
    pause: () => void;
    /** Pause when playing, otherwise play the given/current video. */
    toggle: (videoId?: string) => Promise<void>;
    seekTo: (seconds: number) => void;
    /** Tear down the player and timers. Also runs automatically when the owning scope is disposed. */
    destroy: () => void;
}

const DEFAULT_PLAYER_VARS: Record<string, string | number> = {
    autoplay: 0,
    controls: 0,
    disablekb: 1,
    fs: 0,
    iv_load_policy: 3,
    modestbranding: 1,
    playsinline: 1,
    rel: 0,
};

/**
 * Wraps a hidden YouTube IFrame player. Nothing is loaded until the first `play()` / `load()` call,
 * so mounting a component that uses this composable does not fetch anything from YouTube.
 */
export function useYouTubePlayer(options: UseYouTubePlayerOptions): UseYouTubePlayerReturn {
    const ready = ref(false);
    const loading = ref(false);
    const isPlaying = ref(false);
    const currentTime = ref(0);
    const duration = ref(0);
    const videoId = ref<string | null>(null);
    const error = ref<string | null>(null);

    let api: typeof YT | null = null;
    let player: YT.Player | null = null;
    let creating: Promise<YT.Player | null> | null = null;
    let pollTimer: ReturnType<typeof setInterval> | null = null;
    let disposed = false;

    const stopPolling = () => {
        if (pollTimer !== null) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    };

    const syncTimes = () => {
        if (!player) {
            return;
        }

        const time = player.getCurrentTime();
        const total = player.getDuration();

        if (Number.isFinite(time)) {
            currentTime.value = Math.floor(time);
        }

        if (Number.isFinite(total) && total > 0) {
            duration.value = Math.floor(total);
        }
    };

    const startPolling = () => {
        stopPolling();
        pollTimer = setInterval(syncTimes, options.pollInterval ?? 500);
    };

    const handleStateChange = (event: YT.OnStateChangeEvent) => {
        if (!api) {
            return;
        }

        const state = event.data;

        if (state === api.PlayerState.PLAYING) {
            isPlaying.value = true;
            error.value = null;
            syncTimes();
            startPolling();
        } else if (state === api.PlayerState.PAUSED) {
            isPlaying.value = false;
            stopPolling();
            syncTimes();
        } else if (state === api.PlayerState.ENDED) {
            isPlaying.value = false;
            stopPolling();
            syncTimes();
            options.onEnded?.();
        } else if (state === api.PlayerState.CUED) {
            isPlaying.value = false;
            stopPolling();
            syncTimes();
        }
    };

    const handleError = (event: YT.OnErrorEvent) => {
        isPlaying.value = false;
        stopPolling();
        error.value = PLAYER_ERROR_MESSAGES[event.data] ?? 'This video cannot be played';
    };

    const createPlayer = (loadedApi: typeof YT, initialVideoId: string): Promise<YT.Player | null> =>
        new Promise((resolve) => {
            if (!document.getElementById(options.elementId)) {
                error.value = 'Player container is missing';
                resolve(null);
                return;
            }

            try {
                new loadedApi.Player(options.elementId, {
                    height: '0',
                    width: '0',
                    videoId: initialVideoId,
                    playerVars: { ...DEFAULT_PLAYER_VARS, ...options.playerVars },
                    events: {
                        onReady: (event) => {
                            if (disposed) {
                                event.target.destroy();
                                resolve(null);
                                return;
                            }

                            player = event.target;
                            ready.value = true;
                            resolve(player);
                        },
                        onStateChange: handleStateChange,
                        onError: handleError,
                    },
                });
            } catch {
                error.value = 'Could not start the YouTube player';
                resolve(null);
            }
        });

    /** Resolve the live player, creating it (and loading the API) on first use. */
    const ensurePlayer = async (initialVideoId: string): Promise<YT.Player | null> => {
        if (player) {
            return player;
        }

        if (creating) {
            return creating;
        }

        disposed = false;
        loading.value = true;

        creating = (async () => {
            try {
                api = await loadYouTubeApi();

                if (disposed) {
                    return null;
                }

                const instance = await createPlayer(api, initialVideoId);

                if (instance) {
                    videoId.value = initialVideoId;
                }

                return instance;
            } catch (cause) {
                error.value = cause instanceof Error ? cause.message : 'Could not load the YouTube player';
                return null;
            } finally {
                loading.value = false;
                creating = null;
            }
        })();

        return creating;
    };

    const loadVideoById = async (id: string, autoplay = true): Promise<void> => {
        error.value = null;
        currentTime.value = 0;
        duration.value = 0;

        const hadPlayer = player !== null;
        const instance = await ensurePlayer(id);

        if (!instance || disposed) {
            return;
        }

        videoId.value = id;

        if (hadPlayer) {
            if (autoplay) {
                instance.loadVideoById(id);
            } else {
                instance.cueVideoById(id);
            }

            return;
        }

        // The player was just created with this video already cued.
        if (autoplay) {
            instance.playVideo();
        }
    };

    const load = (id: string): Promise<void> => loadVideoById(id, false);

    const play = async (id?: string): Promise<void> => {
        const target = id ?? videoId.value;

        if (!target) {
            return;
        }

        if (!player || target !== videoId.value) {
            await loadVideoById(target, true);
            return;
        }

        error.value = null;
        player.playVideo();
    };

    const pause = () => {
        player?.pauseVideo();
    };

    const toggle = async (id?: string): Promise<void> => {
        if (isPlaying.value && (id === undefined || id === videoId.value)) {
            pause();
            return;
        }

        await play(id);
    };

    const seekTo = (seconds: number) => {
        const target = Math.max(0, Math.floor(seconds));
        currentTime.value = target;
        player?.seekTo(target, true);
    };

    const destroy = () => {
        disposed = true;
        stopPolling();

        if (player) {
            player.destroy();
            player = null;
        }

        ready.value = false;
        loading.value = false;
        isPlaying.value = false;
        videoId.value = null;
    };

    if (getCurrentScope()) {
        onScopeDispose(destroy);
    }

    return {
        ready,
        loading,
        isPlaying,
        currentTime,
        duration,
        videoId,
        error,
        loadVideoById,
        load,
        play,
        pause,
        toggle,
        seekTo,
        destroy,
    };
}
