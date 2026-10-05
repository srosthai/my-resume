<script setup lang="ts">
import { extractYouTubeId, formatTime, useYouTubePlayer } from '@/composables/useYouTubePlayer';
import FrontendLayout from '@/layouts/FrontendLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Pause, Play, SkipBack, SkipForward } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface LibrarySong {
    id: number;
    title: string;
    artist: string;
    src: string;
    duration: number;
}

const props = withDefaults(
    defineProps<{
        title?: string;
        description?: string;
        songs?: LibrarySong[];
    }>(),
    {
        title: 'Music',
        description: 'Songs from the library, the same list as the music player.',
        songs: () => [],
    },
);

const PLAYER_ELEMENT_ID = 'more-youtube-player';

const tracks = computed(() =>
    props.songs.flatMap((song) => {
        const videoId = extractYouTubeId(song.src);

        return videoId ? [{ ...song, videoId }] : [];
    }),
);

const currentIndex = ref(0);
const track = computed(() => tracks.value[currentIndex.value] ?? null);

const { ready, isPlaying, currentTime, duration, error, play, pause, seekTo, loadVideoById } = useYouTubePlayer({
    elementId: PLAYER_ELEMENT_ID,
    onEnded: () => {
        if (currentIndex.value < tracks.value.length - 1) {
            selectTrack(currentIndex.value + 1);
        }
    },
});

const togglePlay = async () => {
    if (!track.value) return;
    if (isPlaying.value) {
        pause();
        return;
    }
    await play(track.value.videoId);
};

const selectTrack = (index: number) => {
    if (tracks.value.length === 0) return;
    currentIndex.value = ((index % tracks.value.length) + tracks.value.length) % tracks.value.length;
    if (ready.value && track.value) {
        void loadVideoById(track.value.videoId);
    }
};

const updateProgress = (event: Event) => {
    const target = event.target as HTMLInputElement;
    seekTo(parseFloat(target.value));
};

const progressMax = computed(() => (duration.value > 0 ? duration.value : track.value?.duration || 0));
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="description" />
        <meta property="og:title" :content="title" />
        <meta property="og:description" :content="description" />
        <meta property="og:image" content="/og-image.png" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="title" />
        <meta name="twitter:description" :content="description" />
        <meta name="twitter:image" content="/og-image.png" />
    </Head>

    <FrontendLayout current-route="/more">
        <section class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
            <header class="mb-8">
                <h1 class="font-serif text-4xl tracking-tight text-foreground sm:text-5xl">Music</h1>
                <p class="mt-3 max-w-xl text-sm leading-relaxed text-muted-foreground sm:text-base">
                    {{ description }}
                </p>
            </header>

            <div :id="PLAYER_ELEMENT_ID" class="sr-only" aria-hidden="true"></div>

            <div v-if="tracks.length === 0" class="rounded-2xl border border-dashed border-border px-6 py-16 text-center">
                <p class="text-sm text-muted-foreground">No playable songs in the library yet.</p>
            </div>

            <div v-else class="overflow-hidden rounded-2xl border border-border/70 bg-card">
                <div class="border-b border-border/70 px-5 py-5 sm:px-6">
                    <p class="text-xs tracking-wide text-muted-foreground uppercase">Now playing</p>
                    <h2 class="mt-1 text-xl font-medium text-foreground">{{ track?.title }}</h2>
                    <p class="text-sm text-muted-foreground">{{ track?.artist }}</p>

                    <p v-if="error" class="mt-3 text-sm text-destructive" role="alert">{{ error }}</p>

                    <label class="mt-5 block">
                        <span class="sr-only">Playback position</span>
                        <input
                            type="range"
                            class="w-full accent-foreground"
                            min="0"
                            :max="progressMax"
                            step="1"
                            :value="currentTime"
                            :disabled="!ready"
                            @input="updateProgress"
                        />
                    </label>
                    <div class="mt-1 flex justify-between font-mono text-xs text-muted-foreground tabular-nums">
                        <span>{{ formatTime(currentTime) }}</span>
                        <span>{{ formatTime(progressMax) }}</span>
                    </div>

                    <div class="mt-4 flex items-center gap-2">
                        <button
                            type="button"
                            class="inline-flex size-10 items-center justify-center rounded-full border border-border text-foreground hover:bg-muted disabled:opacity-40"
                            :disabled="tracks.length < 2"
                            aria-label="Previous song"
                            @click="selectTrack(currentIndex - 1)"
                        >
                            <SkipBack class="size-4" />
                        </button>
                        <button
                            type="button"
                            class="inline-flex size-12 items-center justify-center rounded-full bg-foreground text-background hover:opacity-90"
                            :aria-label="isPlaying ? 'Pause' : 'Play'"
                            @click="togglePlay"
                        >
                            <Pause v-if="isPlaying" class="size-5" />
                            <Play v-else class="size-5" />
                        </button>
                        <button
                            type="button"
                            class="inline-flex size-10 items-center justify-center rounded-full border border-border text-foreground hover:bg-muted disabled:opacity-40"
                            :disabled="tracks.length < 2"
                            aria-label="Next song"
                            @click="selectTrack(currentIndex + 1)"
                        >
                            <SkipForward class="size-4" />
                        </button>
                    </div>
                </div>

                <ul class="divide-y divide-border/70">
                    <li v-for="(song, index) in tracks" :key="song.id">
                        <button
                            type="button"
                            class="flex w-full items-center gap-4 px-5 py-3 text-left hover:bg-muted/50 sm:px-6"
                            :aria-current="index === currentIndex ? 'true' : undefined"
                            @click="selectTrack(index)"
                        >
                            <span class="w-6 font-mono text-xs text-muted-foreground tabular-nums">{{ index + 1 }}</span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-medium text-foreground">{{ song.title }}</span>
                                <span class="block truncate text-xs text-muted-foreground">{{ song.artist }}</span>
                            </span>
                            <span class="font-mono text-xs text-muted-foreground tabular-nums">{{ formatTime(song.duration) }}</span>
                        </button>
                    </li>
                </ul>
            </div>
        </section>
    </FrontendLayout>
</template>
