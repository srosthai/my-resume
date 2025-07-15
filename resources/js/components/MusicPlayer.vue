<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Maximize2, Minimize2, Music, Pause, Play, SkipBack, SkipForward, X } from 'lucide-vue-next';
import { onMounted, onUnmounted, ref, watch } from 'vue';

interface Song {
    id: number;
    title: string;
    artist: string;
    src: string;
    duration: number;
}

const musicLibrary = ref<Song[]>([
    {
        id: 1,
        title: 'បងក្រ',
        artist: 'Tena Feat. YCN Rakhie Original Version',
        src: 'https://www.youtube.com/watch?v=-IQcA1jmb3I&list=RD-IQcA1jmb3I&start_radio=1',
        duration: 250,
    },
]);

const isPlaying = ref(false);
const currentSongIndex = ref(0);
const currentTime = ref(0);
const isMinimized = ref(true);
const isExpanded = ref(false);
const youtubePlayer = ref<any>(null);
const playerReady = ref(false);

const currentSong = ref<Song>(musicLibrary.value[0]);

const formatTime = (seconds: number): string => {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

const togglePlay = () => {
    if (!playerReady.value || !youtubePlayer.value) return;

    if (isPlaying.value) {
        youtubePlayer.value.pauseVideo();
    } else {
        youtubePlayer.value.playVideo();
    }
};

const previousSong = () => {
    if (currentSongIndex.value > 0) {
        currentSongIndex.value--;
    } else {
        currentSongIndex.value = musicLibrary.value.length - 1;
    }
    loadCurrentSong();
};

const nextSong = () => {
    if (currentSongIndex.value < musicLibrary.value.length - 1) {
        currentSongIndex.value++;
    } else {
        currentSongIndex.value = 0;
    }
    loadCurrentSong();
};

const loadCurrentSong = () => {
    if (musicLibrary.value.length === 0) {
        console.warn('No songs in music library');
        return;
    }

    if (currentSongIndex.value >= musicLibrary.value.length) {
        console.warn('Invalid song index:', currentSongIndex.value);
        currentSongIndex.value = 0;
    }

    currentSong.value = musicLibrary.value[currentSongIndex.value];
    isPlaying.value = false;
    currentTime.value = 0;

    console.log('Loading song:', currentSong.value);

    if (playerReady.value && youtubePlayer.value && currentSong.value?.src) {
        const videoId = extractYouTubeId(currentSong.value.src);
        if (videoId) {
            console.log('Loading YouTube video:', videoId);
            try {
                youtubePlayer.value.loadVideoById(videoId);
            } catch (error) {
                console.error('Error loading video:', error);
            }
        } else {
            console.error('Cannot extract video ID from:', currentSong.value.src);
        }
    }
};

const openYouTube = () => {
    if (currentSong.value?.src) {
        window.open(currentSong.value.src, '_blank');
    } else {
        console.warn('No current song URL to open');
    }
};

const openCurrentSongYouTube = () => {
    if (currentSong.value?.src) {
        window.open(currentSong.value.src, '_blank');
    } else {
        console.warn('No current song URL to open');
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
        togglePlay();
    }
};

// YouTube Player API functions
const initYouTubePlayer = () => {
    if (!window.YT) {
        const tag = document.createElement('script');
        tag.src = 'https://www.youtube.com/iframe_api';
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode?.insertBefore(tag, firstScriptTag);

        window.onYouTubeIframeAPIReady = onYouTubeIframeAPIReady;
    } else {
        onYouTubeIframeAPIReady();
    }
};

const onYouTubeIframeAPIReady = () => {
    if (!currentSong.value || !currentSong.value.src) {
        console.error('No current song or src available for YouTube player');
        return;
    }

    const videoId = extractYouTubeId(currentSong.value.src);
    if (!videoId) {
        console.error('Cannot extract YouTube video ID from:', currentSong.value.src);
        return;
    }

    console.log('Initializing YouTube player with video ID:', videoId);

    try {
        youtubePlayer.value = new window.YT.Player('youtube-player', {
            height: '0',
            width: '0',
            videoId: videoId,
            playerVars: {
                autoplay: 0,
                controls: 0,
                disablekb: 1,
                fs: 0,
                iv_load_policy: 3,
                modestbranding: 1,
                rel: 0,
                showinfo: 0,
            },
            events: {
                onReady: onPlayerReady,
                onStateChange: onPlayerStateChange,
            },
        });
    } catch (error) {
        console.error('Error creating YouTube player:', error);
    }
};

const onPlayerReady = () => {
    playerReady.value = true;
};

const onPlayerStateChange = (event: any) => {
    if (event.data === window.YT.PlayerState.PLAYING) {
        isPlaying.value = true;
        startTimeUpdate();
    } else if (event.data === window.YT.PlayerState.PAUSED) {
        isPlaying.value = false;
    } else if (event.data === window.YT.PlayerState.ENDED) {
        isPlaying.value = false;
        nextSong();
    }
};

let timeUpdateInterval: number | null = null;

const startTimeUpdate = () => {
    if (timeUpdateInterval) clearInterval(timeUpdateInterval);

    timeUpdateInterval = setInterval(() => {
        if (youtubePlayer.value && isPlaying.value) {
            currentTime.value = Math.floor(youtubePlayer.value.getCurrentTime());
        }
    }, 1000);
};

const updateProgress = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const newTime = parseFloat(target.value);
    currentTime.value = newTime;
    if (youtubePlayer.value && playerReady.value) {
        youtubePlayer.value.seekTo(newTime);
    }
};

const extractYouTubeId = (url: string | undefined): string | null => {
    if (!url || typeof url !== 'string') {
        console.warn('Invalid YouTube URL:', url);
        return null;
    }

    try {
        const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/);
        return match ? match[1] : null;
    } catch (error) {
        console.error('Error extracting YouTube ID from URL:', url, error);
        return null;
    }
};

const loadSongs = async () => {
    try {
        const response = await fetch('/api/popular-songs');
        if (response.ok) {
            const songs = await response.json();
            if (songs && songs.length > 0) {
                console.log('Loaded songs from API:', songs); // Debug log

                // Map API response to expected format with validation
                musicLibrary.value = songs
                    .map((song: any) => {
                        const mappedSong = {
                            id: song.id || 0,
                            title: song.title || 'Unknown Title',
                            artist: song.artist || 'Unknown Artist',
                            src: song.url || song.src || '', // Check both 'url' and 'src' fields
                            duration: song.duration || 180,
                        };

                        // Validate YouTube URL
                        if (!mappedSong.src || !extractYouTubeId(mappedSong.src)) {
                            console.warn('Invalid YouTube URL for song:', mappedSong.title, mappedSong.src);
                        }

                        return mappedSong;
                    })
                    .filter((song) => extractYouTubeId(song.src)); // Only keep songs with valid YouTube URLs

                if (musicLibrary.value.length > 0) {
                    currentSong.value = musicLibrary.value[0];
                    currentSongIndex.value = 0;
                    console.log('Current song set to:', currentSong.value); // Debug log
                } else {
                    console.warn('No valid YouTube URLs found in songs');
                }
            }
        }
    } catch (error) {
        console.warn('Failed to load songs from API:', error);
    }
};

onMounted(async () => {
    // Load songs from API first
    await loadSongs();

    // Initialize YouTube player
    initYouTubePlayer();
});

onUnmounted(() => {
    if (timeUpdateInterval) {
        clearInterval(timeUpdateInterval);
    }
    if (youtubePlayer.value) {
        youtubePlayer.value.destroy();
    }
});

watch(currentSongIndex, () => {
    loadCurrentSong();
});
</script>

<template>
    <div class="pointer-events-auto fixed right-6 bottom-6 z-50">
        <TooltipProvider>
            <Tooltip>
                <TooltipTrigger asChild>
                    <Button
                        @click="handleMusicButtonClick"
                        size="lg"
                        class="h-14 w-14 rounded-full bg-primary/90 text-primary-foreground shadow-xl transition-all duration-300 hover:scale-110 hover:bg-primary hover:shadow-2xl active:scale-105 active:bg-primary/80"
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
    <div v-if="isExpanded" class="pointer-events-none fixed right-4 bottom-4 left-4 z-40">
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
                                <Button @click="togglePlay" size="sm" variant="ghost" class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent">
                                    <component :is="isPlaying ? Pause : Play" class="h-4 w-4" />
                                </Button>
                            </div>
                            <div class="flex items-center gap-1">
                                <Tooltip>
                                    <TooltipTrigger asChild>
                                        <Button
                                            @click="toggleMinimize"
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
                                        <h3 class="truncate font-medium text-foreground">{{ currentSong.title }}</h3>
                                        <p class="truncate text-sm text-muted-foreground">{{ currentSong.artist }}</p>
                                    </div>
                                    <Badge variant="secondary" class="hidden sm:flex"> {{ currentSongIndex + 1 }} / {{ musicLibrary.length }} </Badge>
                                </div>

                                <div class="flex items-center gap-1">
                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <Button
                                                @click="toggleMinimize"
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
                                    :max="currentSong.duration"
                                    :value="currentTime"
                                    @input="updateProgress"
                                    class="slider h-2 flex-1 cursor-pointer appearance-none rounded-lg bg-accent"
                                />
                                <span class="w-10">{{ formatTime(currentSong.duration) }}</span>
                            </div>

                            <!-- Control Buttons -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1">
                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <Button
                                                @click="previousSong"
                                                size="sm"
                                                variant="ghost"
                                                class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                                                :disabled="musicLibrary.length <= 1"
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
                                                size="sm"
                                                class="h-10 w-10 rounded-full bg-primary text-primary-foreground hover:bg-primary/90 active:bg-primary/80"
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
                                                size="sm"
                                                variant="ghost"
                                                class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                                                :disabled="musicLibrary.length <= 1"
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
                                    <Badge variant="secondary" class="text-xs"> {{ currentSongIndex + 1 }}/{{ musicLibrary.length }} </Badge>
                                    <Tooltip>
                                        <TooltipTrigger asChild>
                                            <Button
                                                @click="openCurrentSongYouTube"
                                                size="sm"
                                                variant="ghost"
                                                class="h-8 w-8 rounded-full text-red-600 hover:bg-accent active:bg-accent"
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
    <div id="youtube-player" style="display: none"></div>
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
