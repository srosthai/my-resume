<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { extractYouTubeId, formatTime, useYouTubePlayer } from '@/composables/useYouTubePlayer';
import FrontendLayout from '@/layouts/FrontendLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Award, Camera, FileText, Heart, ImageIcon, Music, Pause, Play, Repeat, Shuffle, SkipBack, SkipForward, Volume2 } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

defineProps<{
    title: string;
    description: string;
}>();

const isVisible = ref(false);
const activeTab = ref('certificate');

interface PlaylistSource {
    id: number;
    title: string;
    artist: string;
    youtubeUrl: string;
    duration: string;
    genre: string;
}

interface PlaylistTrack extends PlaylistSource {
    videoId: string;
    albumArt: string;
}

const PLAYER_ELEMENT_ID = 'youtube-player-music';

// Music playlist; the video id and thumbnail are derived from the URL.
const playlistSource: PlaylistSource[] = [
    {
        id: 1,
        title: 'បងក្រ',
        artist: 'Tena - បងក្រ Feat. YCN Rakhie',
        youtubeUrl: 'https://www.youtube.com/watch?v=-IQcA1jmb3I&list=RD-IQcA1jmb3I&start_radio=1',
        duration: '0:04:10',
        genre: 'Song',
    },
    {
        id: 2,
        title: '360',
        artist: 'Vannda',
        youtubeUrl: 'https://www.youtube.com/watch?v=VangtodgL0Y&list=RDVangtodgL0Y&start_radio=1',
        duration: '0:03:41',
        genre: 'Song',
    },
    {
        id: 3,
        title: 'យប់ស្ងាត់/QUIET NIGHT',
        artist: 'TEPPISETH',
        youtubeUrl: 'https://www.youtube.com/watch?v=JLevKPoa6BI&list=RD8oLi5b4w4PQ&index=2',
        duration: '0:02:26',
        genre: 'Song',
    },
    {
        id: 4,
        title: 'រៀនចប់',
        artist: 'All3rgy & Chan Sreykhouch',
        youtubeUrl: 'https://www.youtube.com/watch?v=8oLi5b4w4PQ&list=RD8oLi5b4w4PQ&start_radio=1&rv=-IQcA1jmb3I',
        duration: '0:03:47',
        genre: 'Song',
    },
];

const playlist: PlaylistTrack[] = playlistSource.flatMap((track) => {
    const videoId = extractYouTubeId(track.youtubeUrl);

    return videoId ? [{ ...track, videoId, albumArt: `https://img.youtube.com/vi/${videoId}/hqdefault.jpg` }] : [];
});

// Music player state
const currentTrack = ref(0);
const volume = ref(70);
const isLiked = ref(false);
const isShuffled = ref(false);
const repeatMode = ref(0); // 0: no repeat, 1: repeat all, 2: repeat one

const track = computed(() => playlist[currentTrack.value]);

const { ready, isPlaying, currentTime, duration, error, play, pause, seekTo, loadVideoById } = useYouTubePlayer({
    elementId: PLAYER_ELEMENT_ID,
    onEnded: () => handleEnded(),
});

const togglePlay = async () => {
    if (isPlaying.value) {
        pause();
        return;
    }

    if (track.value) {
        await play(track.value.videoId);
    }
};

/** Switch tracks; only talks to YouTube once the player exists (i.e. after the first play). */
const selectTrack = (index: number) => {
    if (playlist.length === 0) {
        return;
    }

    currentTrack.value = ((index % playlist.length) + playlist.length) % playlist.length;

    if (ready.value && track.value) {
        void loadVideoById(track.value.videoId);
    }
};

const previousTrack = () => selectTrack(currentTrack.value - 1);

const nextTrack = () => {
    if (isShuffled.value && playlist.length > 1) {
        let next = currentTrack.value;

        while (next === currentTrack.value) {
            next = Math.floor(Math.random() * playlist.length);
        }

        selectTrack(next);
        return;
    }

    selectTrack(currentTrack.value + 1);
};

const handleEnded = () => {
    if (repeatMode.value === 2) {
        seekTo(0);
        void play();
    } else if (repeatMode.value === 1 || isShuffled.value || currentTrack.value < playlist.length - 1) {
        nextTrack();
    }
};

const updateProgress = (event: Event) => {
    const target = event.target as HTMLInputElement;
    seekTo(parseFloat(target.value));
};

const toggleLike = () => {
    isLiked.value = !isLiked.value;
};

const toggleShuffle = () => {
    isShuffled.value = !isShuffled.value;
};

const toggleRepeat = () => {
    repeatMode.value = (repeatMode.value + 1) % 3;
};

const tabs = [
    { id: 'certificate', name: 'My Certificate', icon: FileText, route: '/more?tab=certificate' },
    { id: 'gallery', name: 'My Gallery', icon: ImageIcon, route: '/more?tab=gallery' },
    { id: 'music', name: 'My Music', icon: Music, route: '/more?tab=music' },
];

onMounted(() => {
    // Check URL parameters for active tab
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam && ['certificate', 'gallery', 'music'].includes(tabParam)) {
        activeTab.value = tabParam;
    }

    setTimeout(() => {
        isVisible.value = true;
    }, 100);
});
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="description" />
        <meta name="keywords" content="certificates, gallery, music, achievements, creative work, professional development" />
        <meta name="author" content="Software Developer" />

        <!-- Open Graph Meta Tags -->
        <meta property="og:title" :content="title" />
        <meta property="og:description" :content="description" />
        <meta property="og:image" content="/more-og-image.jpg" />
        <meta property="og:url" :content="$page.url" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="More About Me - Portfolio" />

        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="title" />
        <meta name="twitter:description" :content="description" />
        <meta name="twitter:image" content="/more-og-image.jpg" />

        <!-- Additional SEO Meta Tags -->
        <meta name="robots" content="index, follow" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="canonical" :href="$page.url" />
    </Head>

    <FrontendLayout currentRoute="/more">
        <div
            class="min-h-screen overflow-x-hidden bg-gradient-to-br from-background via-slate-50/5 to-background pt-16 font-sans text-foreground transition-all duration-300"
        >
            <!-- More Content Section -->
            <section class="relative mx-auto min-h-screen max-w-6xl px-4 py-20">
                <!-- Background decoration -->
                <div class="pointer-events-none absolute inset-0 overflow-hidden">
                    <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-gradient-to-br from-primary/8 to-accent/8 blur-3xl"></div>
                    <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-gradient-to-br from-secondary/8 to-primary/8 blur-3xl"></div>
                    <div class="absolute top-1/3 left-1/3 h-72 w-72 rounded-full bg-gradient-to-br from-accent/4 to-muted/4 blur-2xl"></div>
                </div>

                <div class="relative z-10 mx-auto max-w-4xl space-y-8 lg:space-y-12">
                    <!-- Header -->
                    <div class="space-y-6 text-center lg:space-y-8" :class="{ 'fade-in-up': isVisible }">
                        <div class="relative">
                            <h1 class="mb-2 text-4xl leading-tight font-bold sm:text-5xl lg:text-6xl">
                                <span class="bg-gradient-to-br from-foreground via-primary/80 to-accent/80 bg-clip-text text-transparent">
                                    More About Me
                                </span>
                            </h1>
                            <div class="absolute -top-2 -right-2 h-4 w-4 rounded-full bg-primary/20 blur-sm"></div>
                            <div class="absolute -bottom-2 -left-2 h-3 w-3 rounded-full bg-accent/20 blur-sm"></div>
                        </div>

                        <p class="mx-auto max-w-2xl text-lg leading-relaxed text-muted-foreground lg:text-xl">
                            Discover my professional journey through certifications, creative projects, and personal interests
                        </p>
                    </div>

                    <!-- Tab Navigation -->
                    <div class="flex justify-center" :class="{ 'fade-in-up': isVisible }">
                        <div class="card-3d flex rounded-[1.25rem] border border-border/60 bg-card/80 p-1.5 backdrop-blur-xl">
                            <Link
                                v-for="tab in tabs"
                                :key="tab.id"
                                :href="tab.route"
                                :class="[
                                    'btn-3d flex items-center gap-3 rounded-xl px-6 py-4 font-semibold',
                                    activeTab === tab.id
                                        ? 'bg-primary text-primary-foreground'
                                        : 'text-muted-foreground hover:bg-muted/70 hover:text-foreground',
                                ]"
                            >
                                <component :is="tab.icon" class="h-5 w-5" />
                                <span class="text-sm font-medium">{{ tab.name }}</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="transition-all duration-300" :class="{ 'fade-in-up': isVisible }">
                        <!-- My Certificate Tab -->
                        <div v-if="activeTab === 'certificate'" class="space-y-8">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8 xl:grid-cols-4">
                                <!-- Web Development Certificate -->
                                <div
                                    class="group relative aspect-[4/3] transform cursor-pointer overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/15 via-purple-500/15 to-indigo-600/15 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-primary/25"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center text-white/95">
                                            <div class="mb-4 rounded-3xl border border-white/20 bg-white/10 p-6 backdrop-blur-sm">
                                                <Award class="mx-auto h-16 w-16 drop-shadow-lg" />
                                            </div>
                                            <div class="text-sm font-semibold tracking-widest uppercase opacity-90">Full Stack Web Development</div>
                                            <div class="mt-2 text-xs font-medium opacity-80">Professional Certificate</div>
                                        </div>
                                    </div>
                                    <!-- Modern overlay pattern -->
                                    <div class="absolute top-6 right-6 h-24 w-24 animate-pulse rounded-full border border-white/15"></div>
                                    <div
                                        class="absolute bottom-6 left-6 h-16 w-16 animate-pulse rounded-full border border-white/15"
                                        style="animation-delay: 0.5s"
                                    ></div>
                                    <div
                                        class="absolute top-1/2 left-1/2 h-32 w-32 -translate-x-1/2 -translate-y-1/2 transform animate-pulse rounded-full border border-white/10"
                                        style="animation-delay: 1s"
                                    ></div>
                                </div>

                                <!-- UI/UX Design Certificate -->
                                <div
                                    class="group relative aspect-[4/3] transform cursor-pointer overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/15 via-teal-500/15 to-green-600/15 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-primary/25"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center text-white/95">
                                            <div class="mb-4 rounded-3xl border border-white/20 bg-white/10 p-6 backdrop-blur-sm">
                                                <Award class="mx-auto h-16 w-16 drop-shadow-lg" />
                                            </div>
                                            <div class="text-sm font-semibold tracking-widest uppercase opacity-90">UI/UX Design Professional</div>
                                            <div class="mt-2 text-xs font-medium opacity-80">Advanced Design Certificate</div>
                                        </div>
                                    </div>
                                    <!-- Modern overlay pattern -->
                                    <div class="absolute top-6 right-6 h-24 w-24 animate-pulse rounded-full border border-white/15"></div>
                                    <div
                                        class="absolute bottom-6 left-6 h-16 w-16 animate-pulse rounded-full border border-white/15"
                                        style="animation-delay: 0.5s"
                                    ></div>
                                    <div
                                        class="absolute top-1/2 left-1/2 h-32 w-32 -translate-x-1/2 -translate-y-1/2 transform animate-pulse rounded-full border border-white/10"
                                        style="animation-delay: 1s"
                                    ></div>
                                </div>

                                <!-- JavaScript Development Certificate -->
                                <div
                                    class="group relative aspect-[4/3] transform cursor-pointer overflow-hidden rounded-2xl bg-gradient-to-br from-yellow-500/20 via-orange-500/20 to-red-500/20 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-primary/25"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center text-white/95">
                                            <div class="mb-4 rounded-3xl border border-white/20 bg-white/10 p-6 backdrop-blur-sm">
                                                <Award class="mx-auto h-16 w-16 drop-shadow-lg" />
                                            </div>
                                            <div class="text-sm font-semibold tracking-widest uppercase opacity-90">
                                                Advanced JavaScript Developer
                                            </div>
                                            <div class="mt-2 text-xs font-medium opacity-80">ES6+ & Modern Frameworks</div>
                                        </div>
                                    </div>
                                    <!-- Modern overlay pattern -->
                                    <div class="absolute top-6 right-6 h-24 w-24 animate-pulse rounded-full border border-white/15"></div>
                                    <div
                                        class="absolute bottom-6 left-6 h-16 w-16 animate-pulse rounded-full border border-white/15"
                                        style="animation-delay: 0.5s"
                                    ></div>
                                    <div
                                        class="absolute top-1/2 left-1/2 h-32 w-32 -translate-x-1/2 -translate-y-1/2 transform animate-pulse rounded-full border border-white/10"
                                        style="animation-delay: 1s"
                                    ></div>
                                </div>

                                <!-- Cloud Computing Certificate -->
                                <div
                                    class="group relative aspect-[4/3] transform cursor-pointer overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500/20 via-purple-500/20 to-pink-500/20 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-primary/25"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center text-white/95">
                                            <div class="mb-4 rounded-3xl border border-white/20 bg-white/10 p-6 backdrop-blur-sm">
                                                <Award class="mx-auto h-16 w-16 drop-shadow-lg" />
                                            </div>
                                            <div class="text-sm font-semibold tracking-widest uppercase opacity-90">Cloud Computing Specialist</div>
                                            <div class="mt-2 text-xs font-medium opacity-80">AWS Solutions Architecture</div>
                                        </div>
                                    </div>
                                    <!-- Modern overlay pattern -->
                                    <div class="absolute top-6 right-6 h-24 w-24 animate-pulse rounded-full border border-white/15"></div>
                                    <div
                                        class="absolute bottom-6 left-6 h-16 w-16 animate-pulse rounded-full border border-white/15"
                                        style="animation-delay: 0.5s"
                                    ></div>
                                    <div
                                        class="absolute top-1/2 left-1/2 h-32 w-32 -translate-x-1/2 -translate-y-1/2 transform animate-pulse rounded-full border border-white/10"
                                        style="animation-delay: 1s"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <!-- My Gallery Tab -->
                        <div v-if="activeTab === 'gallery'" class="space-y-10">
                            <!-- Modern Gallery Grid -->
                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 sm:gap-6 lg:grid-cols-4 lg:gap-8">
                                <!-- Portfolio Website -->
                                <div
                                    class="group relative aspect-square transform cursor-pointer overflow-hidden rounded-3xl bg-gradient-to-br from-blue-500/20 via-purple-500/20 to-indigo-600/20 transition-all duration-700 hover:scale-110 hover:rotate-2 hover:shadow-2xl hover:shadow-primary/30"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-black/15"></div>
                                    <div
                                        class="absolute inset-0 flex items-center justify-center opacity-80 transition-opacity duration-300 group-hover:opacity-100"
                                    >
                                        <div class="rounded-2xl border border-white/20 bg-white/10 p-4 backdrop-blur-sm">
                                            <Camera class="h-8 w-8 text-white drop-shadow-lg sm:h-12 sm:w-12" />
                                        </div>
                                    </div>
                                    <!-- Floating elements -->
                                    <div
                                        class="absolute top-4 right-4 h-6 w-6 rounded-full bg-white/20 transition-transform duration-500 group-hover:scale-150"
                                    ></div>
                                    <div
                                        class="absolute bottom-4 left-4 h-8 w-8 rounded-full border border-white/30 transition-transform duration-500 group-hover:rotate-45"
                                    ></div>
                                    <div
                                        class="absolute top-1/2 left-1/2 h-20 w-20 -translate-x-1/2 -translate-y-1/2 transform rounded-full border border-white/10 transition-transform duration-700 group-hover:scale-125"
                                    ></div>
                                </div>

                                <!-- E-commerce App -->
                                <div
                                    class="group relative aspect-square transform cursor-pointer overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/30 via-teal-500/30 to-green-600/30 transition-all duration-500 hover:scale-105 hover:-rotate-1 hover:shadow-2xl hover:shadow-primary/25"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                    <div
                                        class="absolute inset-0 flex items-center justify-center opacity-80 transition-opacity duration-300 group-hover:opacity-100"
                                    >
                                        <Camera class="h-8 w-8 text-white drop-shadow-lg sm:h-12 sm:w-12" />
                                    </div>
                                    <div
                                        class="absolute top-3 right-3 h-4 w-4 rounded-full bg-white/20 transition-transform duration-300 group-hover:scale-150"
                                    ></div>
                                    <div
                                        class="absolute bottom-3 left-3 h-6 w-6 rounded-full border border-white/30 transition-transform duration-300 group-hover:rotate-45"
                                    ></div>
                                </div>

                                <!-- Mobile App Design -->
                                <div
                                    class="group relative aspect-square transform cursor-pointer overflow-hidden rounded-2xl bg-gradient-to-br from-orange-500/30 via-red-500/30 to-pink-600/30 transition-all duration-500 hover:scale-105 hover:rotate-1 hover:shadow-2xl hover:shadow-primary/25"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                    <div
                                        class="absolute inset-0 flex items-center justify-center opacity-80 transition-opacity duration-300 group-hover:opacity-100"
                                    >
                                        <Camera class="h-8 w-8 text-white drop-shadow-lg sm:h-12 sm:w-12" />
                                    </div>
                                    <div
                                        class="absolute top-3 right-3 h-4 w-4 rounded-full bg-white/20 transition-transform duration-300 group-hover:scale-150"
                                    ></div>
                                    <div
                                        class="absolute bottom-3 left-3 h-6 w-6 rounded-full border border-white/30 transition-transform duration-300 group-hover:rotate-45"
                                    ></div>
                                </div>

                                <!-- Dashboard UI -->
                                <div
                                    class="group relative aspect-square transform cursor-pointer overflow-hidden rounded-2xl bg-gradient-to-br from-violet-500/30 via-purple-500/30 to-indigo-600/30 transition-all duration-500 hover:scale-105 hover:-rotate-1 hover:shadow-2xl hover:shadow-primary/25"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                    <div
                                        class="absolute inset-0 flex items-center justify-center opacity-80 transition-opacity duration-300 group-hover:opacity-100"
                                    >
                                        <Camera class="h-8 w-8 text-white drop-shadow-lg sm:h-12 sm:w-12" />
                                    </div>
                                    <div
                                        class="absolute top-3 right-3 h-4 w-4 rounded-full bg-white/20 transition-transform duration-300 group-hover:scale-150"
                                    ></div>
                                    <div
                                        class="absolute bottom-3 left-3 h-6 w-6 rounded-full border border-white/30 transition-transform duration-300 group-hover:rotate-45"
                                    ></div>
                                </div>

                                <!-- Landing Page -->
                                <div
                                    class="group relative aspect-square transform cursor-pointer overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-500/30 via-blue-500/30 to-indigo-600/30 transition-all duration-500 hover:scale-105 hover:rotate-1 hover:shadow-2xl hover:shadow-primary/25"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                    <div
                                        class="absolute inset-0 flex items-center justify-center opacity-80 transition-opacity duration-300 group-hover:opacity-100"
                                    >
                                        <Camera class="h-8 w-8 text-white drop-shadow-lg sm:h-12 sm:w-12" />
                                    </div>
                                    <div
                                        class="absolute top-3 right-3 h-4 w-4 rounded-full bg-white/20 transition-transform duration-300 group-hover:scale-150"
                                    ></div>
                                    <div
                                        class="absolute bottom-3 left-3 h-6 w-6 rounded-full border border-white/30 transition-transform duration-300 group-hover:rotate-45"
                                    ></div>
                                </div>

                                <!-- Web Application -->
                                <div
                                    class="group relative aspect-square transform cursor-pointer overflow-hidden rounded-2xl bg-gradient-to-br from-lime-500/30 via-green-500/30 to-emerald-600/30 transition-all duration-500 hover:scale-105 hover:-rotate-1 hover:shadow-2xl hover:shadow-primary/25"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                    <div
                                        class="absolute inset-0 flex items-center justify-center opacity-80 transition-opacity duration-300 group-hover:opacity-100"
                                    >
                                        <Camera class="h-8 w-8 text-white drop-shadow-lg sm:h-12 sm:w-12" />
                                    </div>
                                    <div
                                        class="absolute top-3 right-3 h-4 w-4 rounded-full bg-white/20 transition-transform duration-300 group-hover:scale-150"
                                    ></div>
                                    <div
                                        class="absolute bottom-3 left-3 h-6 w-6 rounded-full border border-white/30 transition-transform duration-300 group-hover:rotate-45"
                                    ></div>
                                </div>

                                <!-- Brand Identity -->
                                <div
                                    class="group relative aspect-square transform cursor-pointer overflow-hidden rounded-2xl bg-gradient-to-br from-rose-500/30 via-pink-500/30 to-purple-600/30 transition-all duration-500 hover:scale-105 hover:rotate-1 hover:shadow-2xl hover:shadow-primary/25"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                    <div
                                        class="absolute inset-0 flex items-center justify-center opacity-80 transition-opacity duration-300 group-hover:opacity-100"
                                    >
                                        <Camera class="h-8 w-8 text-white drop-shadow-lg sm:h-12 sm:w-12" />
                                    </div>
                                    <div
                                        class="absolute top-3 right-3 h-4 w-4 rounded-full bg-white/20 transition-transform duration-300 group-hover:scale-150"
                                    ></div>
                                    <div
                                        class="absolute bottom-3 left-3 h-6 w-6 rounded-full border border-white/30 transition-transform duration-300 group-hover:rotate-45"
                                    ></div>
                                </div>

                                <!-- API Documentation -->
                                <div
                                    class="group relative aspect-square transform cursor-pointer overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500/30 via-yellow-500/30 to-orange-600/30 transition-all duration-500 hover:scale-105 hover:-rotate-1 hover:shadow-2xl hover:shadow-primary/25"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                    <div
                                        class="absolute inset-0 flex items-center justify-center opacity-80 transition-opacity duration-300 group-hover:opacity-100"
                                    >
                                        <Camera class="h-8 w-8 text-white drop-shadow-lg sm:h-12 sm:w-12" />
                                    </div>
                                    <div
                                        class="absolute top-3 right-3 h-4 w-4 rounded-full bg-white/20 transition-transform duration-300 group-hover:scale-150"
                                    ></div>
                                    <div
                                        class="absolute bottom-3 left-3 h-6 w-6 rounded-full border border-white/30 transition-transform duration-300 group-hover:rotate-45"
                                    ></div>
                                </div>
                            </div>

                            <!-- Large Featured Images -->
                            <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-2 lg:gap-8">
                                <!-- Featured Project 1 -->
                                <div
                                    class="group hover:shadow-3xl relative aspect-video transform cursor-pointer overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600/30 via-purple-600/30 to-pink-600/30 transition-all duration-700 hover:scale-[1.03] hover:shadow-primary/35"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                    <div
                                        class="absolute inset-0 flex items-center justify-center opacity-80 transition-opacity duration-300 group-hover:opacity-100"
                                    >
                                        <div class="rounded-3xl border border-white/20 bg-white/10 p-6 backdrop-blur-sm">
                                            <Camera class="h-16 w-16 text-white drop-shadow-2xl" />
                                        </div>
                                    </div>
                                    <!-- Animated decorative elements -->
                                    <div
                                        class="absolute top-8 right-8 h-10 w-10 rounded-full bg-white/20 transition-all duration-500 group-hover:scale-125 group-hover:rotate-180"
                                    ></div>
                                    <div
                                        class="absolute bottom-8 left-8 h-14 w-14 rounded-full border-2 border-white/30 transition-transform duration-500 group-hover:rotate-90"
                                    ></div>
                                    <div
                                        class="absolute top-1/2 left-1/2 h-28 w-28 -translate-x-1/2 -translate-y-1/2 transform rounded-full border border-white/20 transition-transform duration-700 group-hover:scale-150"
                                    ></div>
                                </div>

                                <!-- Featured Project 2 -->
                                <div
                                    class="group hover:shadow-3xl relative aspect-video transform cursor-pointer overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600/40 via-teal-600/40 to-cyan-600/40 transition-all duration-700 hover:scale-[1.02] hover:shadow-primary/30"
                                >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-black/30"></div>
                                    <div
                                        class="absolute inset-0 flex items-center justify-center opacity-80 transition-opacity duration-300 group-hover:opacity-100"
                                    >
                                        <Camera class="h-16 w-16 text-white drop-shadow-2xl" />
                                    </div>
                                    <!-- Animated decorative elements -->
                                    <div
                                        class="absolute top-6 right-6 h-8 w-8 rounded-full bg-white/20 transition-all duration-500 group-hover:scale-125 group-hover:rotate-180"
                                    ></div>
                                    <div
                                        class="absolute bottom-6 left-6 h-12 w-12 rounded-full border-2 border-white/30 transition-transform duration-500 group-hover:rotate-90"
                                    ></div>
                                    <div
                                        class="absolute top-1/2 left-1/2 h-24 w-24 -translate-x-1/2 -translate-y-1/2 transform rounded-full border border-white/20 transition-transform duration-700 group-hover:scale-150"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <!-- My Music Tab -->
                        <div v-if="activeTab === 'music' && track" class="space-y-8">
                            <!-- Hidden YouTube Player (only created once playback starts) -->
                            <div :id="PLAYER_ELEMENT_ID" class="hidden"></div>
                            <!-- Current Playing Card -->
                            <Card
                                class="overflow-hidden rounded-3xl border border-border/60 bg-gradient-to-br from-purple-500/15 via-pink-500/15 to-indigo-500/15 shadow-2xl backdrop-blur-xl"
                            >
                                <CardContent class="p-0">
                                    <div class="flex flex-col lg:flex-row">
                                        <!-- Album Art & Track Info -->
                                        <div class="flex flex-col p-8 sm:flex-row lg:w-1/2 lg:flex-col xl:flex-row">
                                            <div class="relative mb-6 sm:mr-8 sm:mb-0 lg:mr-0 lg:mb-6 xl:mr-8 xl:mb-0">
                                                <div
                                                    class="h-28 w-28 overflow-hidden rounded-3xl shadow-2xl ring-2 ring-white/20 sm:h-36 sm:w-36 lg:h-44 lg:w-44 xl:h-36 xl:w-36"
                                                >
                                                    <img
                                                        :src="track.albumArt"
                                                        :alt="track.title"
                                                        class="h-full w-full object-cover transition-transform duration-500"
                                                        :class="{ 'scale-110': isPlaying }"
                                                    />
                                                </div>
                                                <!-- Playing Animation -->
                                                <div v-if="isPlaying" class="absolute inset-0 flex items-center justify-center">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-black/50 backdrop-blur-sm">
                                                        <div class="flex space-x-1">
                                                            <div class="h-4 w-1 animate-pulse rounded-full bg-white"></div>
                                                            <div
                                                                class="h-3 w-1 animate-pulse rounded-full bg-white"
                                                                style="animation-delay: 0.1s"
                                                            ></div>
                                                            <div
                                                                class="h-5 w-1 animate-pulse rounded-full bg-white"
                                                                style="animation-delay: 0.2s"
                                                            ></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <h3 class="mb-3 truncate text-2xl font-bold text-foreground">
                                                    {{ track.title }}
                                                </h3>
                                                <p class="mb-3 truncate text-lg text-muted-foreground">
                                                    {{ track.artist }}
                                                </p>
                                                <div class="mb-6 flex items-center gap-3">
                                                    <Badge
                                                        variant="secondary"
                                                        class="border-purple-200 bg-purple-100 px-3 py-1 text-sm text-purple-700"
                                                    >
                                                        {{ track.genre }}
                                                    </Badge>
                                                    <span class="text-sm text-muted-foreground">
                                                        {{ track.duration }}
                                                    </span>
                                                </div>

                                                <!-- Progress Bar -->
                                                <div class="space-y-2">
                                                    <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                                        <span class="w-10 text-right">{{ formatTime(currentTime) }}</span>
                                                        <input
                                                            type="range"
                                                            :min="0"
                                                            :max="duration > 0 ? duration : 300"
                                                            :value="currentTime"
                                                            @input="updateProgress"
                                                            class="slider h-2 flex-1 cursor-pointer appearance-none rounded-lg bg-accent"
                                                        />
                                                        <span class="w-10">{{ duration > 0 ? formatTime(duration) : track.duration }}</span>
                                                    </div>
                                                    <p v-if="error" class="text-xs text-destructive">{{ error }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Controls -->
                                        <div class="flex flex-col justify-center border-t border-border/60 p-8 lg:w-1/2 lg:border-t-0 lg:border-l">
                                            <!-- Main Controls -->
                                            <div class="mb-8 flex items-center justify-center gap-6">
                                                <button
                                                    @click="toggleShuffle"
                                                    class="rounded-full p-2 transition-colors duration-200"
                                                    :class="
                                                        isShuffled
                                                            ? 'bg-primary text-primary-foreground'
                                                            : 'text-muted-foreground hover:text-foreground'
                                                    "
                                                >
                                                    <Shuffle class="h-4 w-4" />
                                                </button>

                                                <button @click="previousTrack" class="btn-3d rounded-full bg-card p-3 text-foreground hover:bg-muted">
                                                    <SkipBack class="h-5 w-5" />
                                                </button>

                                                <button
                                                    @click="togglePlay"
                                                    class="btn-3d rounded-full bg-primary p-4 text-primary-foreground hover:bg-primary/90"
                                                >
                                                    <Play v-if="!isPlaying" class="ml-0.5 h-6 w-6" />
                                                    <Pause v-else class="h-6 w-6" />
                                                </button>

                                                <button @click="nextTrack" class="btn-3d rounded-full bg-card p-3 text-foreground hover:bg-muted">
                                                    <SkipForward class="h-5 w-5" />
                                                </button>

                                                <button
                                                    @click="toggleRepeat"
                                                    class="rounded-full p-2 transition-colors duration-200"
                                                    :class="
                                                        repeatMode > 0
                                                            ? 'bg-primary text-primary-foreground'
                                                            : 'text-muted-foreground hover:text-foreground'
                                                    "
                                                >
                                                    <Repeat class="h-4 w-4" />
                                                </button>
                                            </div>

                                            <!-- Secondary Controls -->
                                            <div class="flex items-center justify-between">
                                                <button
                                                    @click="toggleLike"
                                                    class="rounded-full p-2 transition-colors duration-200"
                                                    :class="
                                                        isLiked ? 'text-red-500 hover:text-red-600' : 'text-muted-foreground hover:text-foreground'
                                                    "
                                                >
                                                    <Heart class="h-4 w-4" :class="{ 'fill-current': isLiked }" />
                                                </button>

                                                <div class="flex items-center gap-2">
                                                    <Volume2 class="h-4 w-4 text-muted-foreground" />
                                                    <div class="h-1 w-20 overflow-hidden rounded-full bg-muted">
                                                        <div
                                                            class="h-full rounded-full bg-primary transition-all duration-300"
                                                            :style="{ width: volume + '%' }"
                                                        ></div>
                                                    </div>
                                                </div>

                                                <a
                                                    :href="track.youtubeUrl"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="btn-3d rounded-full bg-red-500 px-3 py-1 text-xs text-white hover:bg-red-600"
                                                >
                                                    YouTube
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            <!-- Playlist -->
                            <Card class="card-3d rounded-[1.5rem] border border-border/60 bg-card/60 backdrop-blur-xl">
                                <CardHeader class="pb-4">
                                    <CardTitle class="flex items-center gap-3 text-xl">
                                        <Music class="h-6 w-6" />
                                        Coding Playlist
                                    </CardTitle>
                                    <CardDescription class="text-base">{{ playlist.length }} tracks • Perfect for focus sessions</CardDescription>
                                </CardHeader>
                                <CardContent class="p-0">
                                    <div class="space-y-1">
                                        <div
                                            v-for="(item, index) in playlist"
                                            :key="item.id"
                                            @click="selectTrack(index)"
                                            class="group flex cursor-pointer items-center gap-4 p-4 transition-colors duration-200 hover:bg-muted/50"
                                            :class="{ 'bg-primary/10': currentTrack === index }"
                                        >
                                            <div class="relative">
                                                <div class="h-12 w-12 overflow-hidden rounded-lg">
                                                    <img :src="item.albumArt" :alt="item.title" class="h-full w-full object-cover" />
                                                </div>
                                                <div
                                                    v-if="currentTrack === index && isPlaying"
                                                    class="absolute inset-0 flex items-center justify-center rounded-lg bg-black/50"
                                                >
                                                    <div class="flex space-x-0.5">
                                                        <div class="h-3 w-0.5 animate-pulse rounded-full bg-white"></div>
                                                        <div
                                                            class="h-2 w-0.5 animate-pulse rounded-full bg-white"
                                                            style="animation-delay: 0.1s"
                                                        ></div>
                                                        <div
                                                            class="h-4 w-0.5 animate-pulse rounded-full bg-white"
                                                            style="animation-delay: 0.2s"
                                                        ></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <h4
                                                    class="mb-1 truncate font-medium text-foreground"
                                                    :class="{ 'text-primary': currentTrack === index }"
                                                >
                                                    {{ item.title }}
                                                </h4>
                                                <p class="truncate text-sm text-muted-foreground">
                                                    {{ item.artist }}
                                                </p>
                                            </div>

                                            <div class="flex items-center gap-3">
                                                <Badge variant="outline" class="text-xs">
                                                    {{ item.genre }}
                                                </Badge>
                                                <span class="text-sm text-muted-foreground">
                                                    {{ item.duration }}
                                                </span>
                                                <button
                                                    @click.stop="selectTrack(index)"
                                                    class="rounded-full p-1 opacity-0 transition-all duration-200 group-hover:opacity-100 hover:bg-muted"
                                                >
                                                    <Play class="h-4 w-4" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </FrontendLayout>
</template>

<style scoped>
/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 1s ease-out forwards;
}

/* Custom slider styling for music player */
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
    height: 10px;
    border-radius: 5px;
}

/* Button hover effects */
button {
    transition: all 0.2s ease;
}

/* Glass morphism effect for cards */
.backdrop-blur-xl {
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
}

/* Enhanced shadows */
.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.shadow-3xl {
    box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.3);
}

/* Ring utilities */
.ring-2 {
    box-shadow: 0 0 0 2px;
}

.ring-white\/20 {
    --tw-ring-color: rgb(255 255 255 / 0.2);
}
</style>
