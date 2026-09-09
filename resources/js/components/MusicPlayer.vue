<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { extractYouTubeId, formatTime, useYouTubePlayer } from '@/composables/useYouTubePlayer';
import type { PlayerSong, PopularSong } from '@/types';
import { Maximize2, Minimize2, Music, Pause, Play, SkipBack, SkipForward, X } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

/** Raw row from /api/popular-songs; tolerate either `url` or `src` for the YouTube link. */
type ApiSong = Partial<PopularSong> & { src?: string };

const PLAYER_ELEMENT_ID = 'youtube-player';

const musicLibrary = ref<PlayerSong[]>([]);
const currentSongIndex = ref(0);
const isMinimized = ref(true);
const isExpanded = ref(false);
const libraryError = ref<string | null>(null);

const currentSong = computed<PlayerSong | null>(() => musicLibrary.value[currentSongIndex.value] ?? null);
const hasSongs = computed(() => musicLibrary.value.length > 0);
const canSkip = computed(() => musicLibrary.value.length > 1);

const {
    ready,
    isPlaying,
    currentTime,
    duration,
    error: playerError,
    play,
    pause,
    seekTo,
    loadVideoById,
} = useYouTubePlayer({
    elementId: PLAYER_ELEMENT_ID,
    onEnded: () => nextSong(),
});

/** Prefer the duration reported by YouTube, fall back to the stored one. */
const songDuration = computed(() => (duration.value > 0 ? duration.value : (currentSong.value?.duration ?? 0)));
const statusMessage = computed(() => playerError.value ?? libraryError.value);

const togglePlay = async () => {
    const song = currentSong.value;

    if (!song) {
        return;
    }

    if (isPlaying.value) {
        pause();
        return;
    }

    const videoId = extractYouTubeId(song.src);

    if (!videoId) {
        return;
    }

    await play(videoId);
};

/** Switch to another song; only touches YouTube once the player already exists. */
const selectSong = (index: number) => {
    const total = musicLibrary.value.length;

    if (total === 0) {
        return;
    }

    currentSongIndex.value = ((index % total) + total) % total;

    const videoId = extractYouTubeId(currentSong.value?.src);

    if (ready.value && videoId) {
        void loadVideoById(videoId);
    }
};

const previousSong = () => selectSong(currentSongIndex.value - 1);
const nextSong = () => selectSong(currentSongIndex.value + 1);

const openCurrentSongYouTube = () => {
    const src = currentSong.value?.src;

    if (src) {
        window.open(src, '_blank', 'noopener');
    }
};

const toggleMinimize = () => {
    isMinimized.value = !isMinimized.value;
};

const toggleExpand = () => {
    isExpanded.value = !isExpanded.value;
    if (!isExpanded.value) {
        isMinimized.value = true;
    }
};

const handleMusicButtonClick = () => {
    if (!isExpanded.value) {
        isExpanded.value = true;
        isMinimized.value = false;
    } else {
        void togglePlay();
    }
};

const updateProgress = (event: Event) => {
    const target = event.target as HTMLInputElement;
    seekTo(parseFloat(target.value));
};

const loadSongs = async () => {
    try {
        const response = await fetch('/api/popular-songs', { headers: { Accept: 'application/json' } });

        if (!response.ok) {
            libraryError.value = 'Playlist is unavailable right now';
            return;
        }

        const payload: unknown = await response.json();
        const rows: ApiSong[] = Array.isArray(payload) ? payload : [];

        musicLibrary.value = rows
            .map(
                (song): PlayerSong => ({
                    id: song.id ?? 0,
                    title: song.title || 'Unknown Title',
                    artist: song.artist || 'Unknown Artist',
                    src: song.url || song.src || '',
                    duration: song.duration ?? 0,
                }),
            )
            .filter((song) => extractYouTubeId(song.src) !== null);

        if (rows.length > 0 && musicLibrary.value.length === 0) {
            libraryError.value = 'The playlist has no YouTube links yet.';
        }

        currentSongIndex.value = 0;
    } catch {
        libraryError.value = 'Playlist is unavailable right now';
    }
};

onMounted(() => {
    void loadSongs();
});
</script>

<template>
    <div class="pointer-events-auto fixed right-5 bottom-24 z-40 md:right-6 md:bottom-6 md:z-50">
        <TooltipProvider>
            <Tooltip>
                <TooltipTrigger asChild>
                    <Button
                        @click="handleMusicButtonClick"
                        :aria-label="isExpanded ? (isPlaying ? 'Pause music' : 'Play music') : 'Open music player'"
                        size="lg"
                        class="btn-3d h-14 w-14 rounded-full bg-primary/90 text-primary-foreground hover:bg-primary active:bg-primary/80"
                        :class="{
                            'animate-pulse': isPlaying && !isExpanded,
                            'bg-primary': isExpanded,
                        }"
                    >
                        <component :is="isExpanded ? (isPlaying ? Pause : Play) : Music" class="h-6 w-6" />
                    </Button>
                </TooltipTrigger>
                <TooltipContent>
                    <p>{{ isExpanded ? (isPlaying ? 'Pause Music' : 'Play Music') : 'Open Music Player' }}</p>
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>
    </div>

    <!-- Expanded Music Player -->
    <div v-if="isExpanded" class="pointer-events-none fixed right-4 bottom-28 left-4 z-40 md:bottom-4">
        <div class="pointer-events-auto mx-auto max-w-6xl">
            <TooltipProvider>
                <Card class="music-player border-border/50 bg-background/95 shadow-2xl transition-all duration-300">
                    <CardContent class="p-3">
                        <!-- Minimized View -->
                        <div v-if="isMinimized" class="flex items-center gap-2">
                            <div class="flex items-center gap-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10">
                                    <Music class="h-4 w-4 text-primary" />
                                </div>
                                <Button
                                    @click="togglePlay"
                                    :aria-label="isPlaying ? 'Pause' : 'Play'"
                                    size="sm"
                                    variant="ghost"
                                    class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                                    :disabled="!hasSongs"
                                >
                                    <component :is="isPlaying ? Pause : Play" class="h-4 w-4" />
                                </Button>
                            </div>
                            <div class="flex items-center gap-1">
                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <Button
                                            @click="toggleMinimize"
                                            aria-label="Expand player"
                                            size="sm"
                                            variant="ghost"
                                            class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                                        >
                                            <Maximize2 class="h-3 w-3" />
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>Expand Player</p>
                                    </TooltipContent>
                                </Tooltip>

                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <Button
                                            @click="toggleExpand"
                                            aria-label="Close player"
                                            size="sm"
                                            variant="ghost"
                                            class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                                        >
                                            <X class="h-3 w-3" />
                                        </Button>
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>Close Player</p>
                                    </TooltipContent>
                                </Tooltip>
                            </div>
                        </div>

                        <!-- Full View -->
                        <div v-else class="space-y-3">
                            <!-- Song Info and Controls -->
                            <div class="flex items-center justify-between">
                                <div class="flex min-w-0 flex-1 items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-primary/20 to-primary/10"
                                    >
                                        <Music class="h-5 w-5 text-primary" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="truncate font-medium text-foreground">
                                            {{ currentSong?.title ?? (libraryError ? 'Nothing to play' : 'No songs yet') }}
                                        </h3>
                                        <p class="truncate text-sm text-muted-foreground">
                                            {{ currentSong?.artist ?? 'Add songs in the admin panel to start listening' }}
                                        </p>
                                    </div>
                                    <Badge variant="secondary" class="hidden sm:flex">
                                        {{ hasSongs ? currentSongIndex + 1 : 0 }} / {{ musicLibrary.length }}
                                    </Badge>
                                </div>

                                <div class="flex items-center gap-1">
                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <Button
                                                @click="toggleMinimize"
                                                aria-label="Minimize player"
                                                size="sm"
                                                variant="ghost"
                                                class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                                            >
                                                <Minimize2 class="h-3 w-3" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>Minimize Player</p>
                                        </TooltipContent>
                                    </Tooltip>

                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <Button
                                                @click="toggleExpand"
                                                aria-label="Close player"
                                                size="sm"
                                                variant="ghost"
                                                class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                                            >
                                                <X class="h-3 w-3" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>Close Player</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                <span class="w-10 text-right">{{ formatTime(currentTime) }}</span>
                                <input
                                    type="range"
                                    :min="0"
                                    :max="songDuration"
                                    :value="currentTime"
                                    :disabled="!hasSongs"
                                    @input="updateProgress"
                                    class="slider h-2 flex-1 cursor-pointer appearance-none rounded-lg bg-accent disabled:cursor-not-allowed disabled:opacity-50"
                                />
                                <span class="w-10">{{ formatTime(songDuration) }}</span>
                            </div>
                            <p v-if="statusMessage" class="text-xs text-destructive">{{ statusMessage }}</p>

                            <!-- Control Buttons -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <Button
                                                @click="previousSong"
                                                aria-label="Previous song"
                                                size="sm"
                                                variant="ghost"
                                                class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                                                :disabled="!canSkip"
                                            >
                                                <SkipBack class="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>Previous Song</p>
                                        </TooltipContent>
                                    </Tooltip>

                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <Button
                                                @click="togglePlay"
                                                :aria-label="isPlaying ? 'Pause' : 'Play'"
                                                size="sm"
                                                class="h-10 w-10 rounded-full bg-primary text-primary-foreground hover:bg-primary/90 active:bg-primary/80"
                                                :disabled="!hasSongs"
                                            >
                                                <component :is="isPlaying ? Pause : Play" class="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{{ isPlaying ? 'Pause' : 'Play' }}</p>
                                        </TooltipContent>
                                    </Tooltip>

                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <Button
                                                @click="nextSong"
                                                aria-label="Next song"
                                                size="sm"
                                                variant="ghost"
                                                class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                                                :disabled="!canSkip"
                                            >
                                                <SkipForward class="h-4 w-4" />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>Next Song</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </div>

                                <!-- Song Info -->
                                <div class="hidden items-center gap-2 sm:flex">
                                    <Badge variant="secondary" class="text-xs">
                                        {{ hasSongs ? currentSongIndex + 1 : 0 }}/{{ musicLibrary.length }}
                                    </Badge>
                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <Button
                                                @click="openCurrentSongYouTube"
                                                aria-label="Open on YouTube"
                                                size="sm"
                                                variant="ghost"
                                                class="h-8 w-8 rounded-full text-red-600 hover:bg-accent active:bg-accent"
                                                :disabled="!hasSongs"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path
                                                        d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"
                                                    />
                                                </svg>
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>Watch on YouTube</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </TooltipProvider>
        </div>
    </div>

    <!-- Hidden YouTube Player -->
    <div :id="PLAYER_ELEMENT_ID" class="hidden"></div>
</template>

<style scoped>
/* Custom slider styling */
.slider {
    background: hsl(var(--accent));
}

.slider::-webkit-slider-thumb {
    appearance: none;
    width: 16px;
    height: 16px;
    background: hsl(var(--primary));
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid hsl(var(--background));
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.slider::-moz-range-thumb {
    width: 16px;
    height: 16px;
    background: hsl(var(--primary));
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid hsl(var(--background));
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.slider::-webkit-slider-track {
    background: hsl(var(--accent));
    height: 8px;
    border-radius: 4px;
}

.slider::-moz-range-track {
    background: hsl(var(--accent));
    height: 8px;
    border-radius: 4px;
}

/* Music button pulse animation */
@keyframes music-pulse {
    0%,
    100% {
        box-shadow: 0 0 0 0 hsl(var(--primary) / 0.7);
    }
    50% {
        box-shadow: 0 0 0 10px hsl(var(--primary) / 0);
    }
}

.animate-pulse {
    animation: music-pulse 2s infinite;
}
</style>
