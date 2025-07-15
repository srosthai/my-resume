<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { FileText, ImageIcon, Music, Award, Camera, Play, Pause, SkipBack, SkipForward, Volume2, Heart, Shuffle, Repeat } from 'lucide-vue-next'
import DockNavigation from '@/components/DockNavigation.vue'
import MusicPlayer from '@/components/MusicPlayer.vue'

defineProps<{
    title: string
    description: string
}>()

const isVisible = ref(false)
const activeTab = ref('certificate')

// Music player state
const currentTrack = ref(0)
const isPlaying = ref(false)
const currentTime = ref(0)
const duration = ref(0)
const volume = ref(70)
const isLiked = ref(false)
const isShuffled = ref(false)
const repeatMode = ref(0) // 0: no repeat, 1: repeat all, 2: repeat one
const player = ref(null)
const playerReady = ref(false)

// Extract YouTube video ID from URL
const extractYouTubeId = (url) => {
    const regex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/
    const match = url.match(regex)
    return match ? match[1] : null
}

// Music playlist with YouTube URLs
const playlist = ref([
    {
        id: 1,
        title: "បងក្រ",
        artist: "Tena - បងក្រ Feat. YCN Rakhie",
        albumArt: "https://img.youtube.com/vi/-IQcA1jmb3I/hqdefault.jpg",
        youtubeUrl: "https://www.youtube.com/watch?v=-IQcA1jmb3I&list=RD-IQcA1jmb3I&start_radio=1",
        youtubeId: "-IQcA1jmb3I&list",
        duration: "0:04:10",
        genre: "Song"
    },
    {
        id: 2,
        title: "360",
        artist: "Vannda",
        albumArt: "https://img.youtube.com/vi/VangtodgL0Y/hqdefault.jpg",
        youtubeUrl: "https://www.youtube.com/watch?v=VangtodgL0Y&list=RDVangtodgL0Y&start_radio=1",
        youtubeId: "VangtodgL0Y",
        duration: "0:03:41",
        genre: "Song"
    },
    {
        id: 3,
        title: "យប់ស្ងាត់/QUIET NIGHT",
        artist: "TEPPISETH",
        albumArt: "https://img.youtube.com/vi/JLevKPoa6BI/hqdefault.jpg",
        youtubeUrl: "https://www.youtube.com/watch?v=JLevKPoa6BI&list=RD8oLi5b4w4PQ&index=2",
        youtubeId: "JLevKPoa6BI",
        duration: "0:02:26",
        genre: "Song"
    },
    {
        id: 4,
        title: "រៀនចប់",
        artist: "All3rgy & Chan Sreykhouch",
        albumArt: "https://img.youtube.com/vi/8oLi5b4w4PQ/hqdefault.jpg",
        youtubeUrl: "https://www.youtube.com/watch?v=8oLi5b4w4PQ&list=RD8oLi5b4w4PQ&start_radio=1&rv=-IQcA1jmb3I",
        youtubeId: "8oLi5b4w4PQ",
        duration: "0:03:47",
        genre: "Song"
    },
])

// Format time (same as working MusicPlayer)
const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60)
    const secs = Math.floor(seconds % 60)
    return `${mins}:${secs.toString().padStart(2, '0')}`
}

// YouTube Player API functions (adapted from working MusicPlayer)
const initYouTubePlayer = () => {
    if (!window.YT) {
        const tag = document.createElement('script')
        tag.src = 'https://www.youtube.com/iframe_api'
        const firstScriptTag = document.getElementsByTagName('script')[0]
        firstScriptTag.parentNode?.insertBefore(tag, firstScriptTag)
        
        window.onYouTubeIframeAPIReady = onYouTubeIframeAPIReady
    } else {
        onYouTubeIframeAPIReady()
    }
}

const onYouTubeIframeAPIReady = () => {
    if (!playlist.value[currentTrack.value] || !playlist.value[currentTrack.value].youtubeUrl) {
        console.error('No current song or src available for YouTube player')
        return
    }
    
    const videoId = extractYouTubeId(playlist.value[currentTrack.value].youtubeUrl)
    if (!videoId) {
        console.error('Cannot extract YouTube video ID from:', playlist.value[currentTrack.value].youtubeUrl)
        return
    }
    
    console.log('Initializing YouTube player with video ID:', videoId)
    
    try {
        player.value = new window.YT.Player('youtube-player-music', {
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
                showinfo: 0
            },
            events: {
                onReady: onPlayerReady,
                onStateChange: onPlayerStateChange
            }
        })
    } catch (error) {
        console.error('Error creating YouTube player:', error)
    }
}

const onPlayerReady = () => {
    playerReady.value = true
    console.log('YouTube player ready')
}

const onPlayerStateChange = (event) => {
    if (event.data === window.YT.PlayerState.PLAYING) {
        isPlaying.value = true
        startTimeUpdate()
    } else if (event.data === window.YT.PlayerState.PAUSED) {
        isPlaying.value = false
    } else if (event.data === window.YT.PlayerState.ENDED) {
        isPlaying.value = false
        if (repeatMode.value === 2) {
            // Repeat current track
            player.value.seekTo(0)
            player.value.playVideo()
        } else if (repeatMode.value === 1 || currentTrack.value < playlist.value.length - 1) {
            nextTrack()
        }
    }
}

let timeUpdateInterval = null

const startTimeUpdate = () => {
    if (timeUpdateInterval) clearInterval(timeUpdateInterval)
    
    timeUpdateInterval = setInterval(() => {
        if (player.value && isPlaying.value) {
            currentTime.value = Math.floor(player.value.getCurrentTime())
            if (player.value.getDuration() > 0) {
                duration.value = Math.floor(player.value.getDuration())
            }
        }
    }, 1000)
}

// Music player functions (adapted from working MusicPlayer)
const togglePlay = () => {
    if (!playerReady.value || !player.value) return
    
    if (isPlaying.value) {
        player.value.pauseVideo()
    } else {
        player.value.playVideo()
    }
}

const previousTrack = () => {
    if (currentTrack.value > 0) {
        currentTrack.value--
    } else {
        currentTrack.value = playlist.value.length - 1
    }
    loadCurrentTrack()
}

const nextTrack = () => {
    if (currentTrack.value < playlist.value.length - 1) {
        currentTrack.value++
    } else {
        currentTrack.value = 0
    }
    loadCurrentTrack()
}

const selectTrack = (index) => {
    currentTrack.value = index
    loadCurrentTrack()
}

const loadCurrentTrack = () => {
    if (!playerReady.value || !player.value) return
    
    const track = playlist.value[currentTrack.value]
    const videoId = extractYouTubeId(track.youtubeUrl)
    if (videoId) {
        console.log('Loading YouTube video:', videoId)
        try {
            player.value.loadVideoById(videoId)
        } catch (error) {
            console.error('Error loading video:', error)
        }
    } else {
        console.error('Cannot extract video ID from:', track.youtubeUrl)
    }
}

const updateProgress = (event) => {
    const target = event.target
    const newTime = parseFloat(target.value)
    currentTime.value = newTime
    if (player.value && playerReady.value) {
        player.value.seekTo(newTime)
    }
}

const toggleLike = () => {
    isLiked.value = !isLiked.value
}

const toggleShuffle = () => {
    isShuffled.value = !isShuffled.value
}

const toggleRepeat = () => {
    repeatMode.value = (repeatMode.value + 1) % 3
}

const tabs = [
    { id: 'certificate', name: 'My Certificate', icon: FileText, route: '/more?tab=certificate' },
    { id: 'gallery', name: 'My Gallery', icon: ImageIcon, route: '/more?tab=gallery' },
    { id: 'music', name: 'My Music', icon: Music, route: '/more?tab=music' }
]

onMounted(() => {
    // Check URL parameters for active tab
    const urlParams = new URLSearchParams(window.location.search)
    const tabParam = urlParams.get('tab')
    if (tabParam && ['certificate', 'gallery', 'music'].includes(tabParam)) {
        activeTab.value = tabParam
    }
    
    setTimeout(() => {
        isVisible.value = true
    }, 100)
    
    // Initialize YouTube player when music tab is active
    if (activeTab.value === 'music') {
        initYouTubePlayer()
    }
})

// Watch for tab changes to initialize player
watch(activeTab, (newTab) => {
    if (newTab === 'music' && !playerReady.value) {
        setTimeout(() => {
            initYouTubePlayer()
        }, 100)
    }
})

// Watch currentTrack changes to load new song
watch(currentTrack, () => {
    loadCurrentTrack()
})

onUnmounted(() => {
    if (timeUpdateInterval) {
        clearInterval(timeUpdateInterval)
    }
    if (player.value) {
        player.value.destroy()
    }
})
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="description" />
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-background via-slate-50/5 to-background text-foreground font-sans overflow-x-hidden transition-all duration-300 pt-16">
        <DockNavigation currentRoute="/more" />
        
        <!-- Music Player -->
        <MusicPlayer />

        <!-- More Content Section -->
        <section class="min-h-screen py-20 px-4 max-w-6xl mx-auto relative">
            <!-- Background decoration -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-96 h-96 bg-gradient-to-br from-primary/8 to-accent/8 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-br from-secondary/8 to-primary/8 rounded-full blur-3xl"></div>
                <div class="absolute top-1/3 left-1/3 w-72 h-72 bg-gradient-to-br from-accent/4 to-muted/4 rounded-full blur-2xl"></div>
            </div>
            
            <div class="max-w-4xl mx-auto space-y-8 lg:space-y-12 relative z-10">
                <!-- Header -->
                <div class="text-center space-y-6 lg:space-y-8" :class="{ 'fade-in-up': isVisible }">
                    <div class="relative">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-2">
                            <span class="bg-gradient-to-br from-foreground via-primary/80 to-accent/80 bg-clip-text text-transparent">
                                More About Me
                            </span>
                        </h1>
                        <div class="absolute -top-2 -right-2 w-4 h-4 bg-primary/20 rounded-full blur-sm"></div>
                        <div class="absolute -bottom-2 -left-2 w-3 h-3 bg-accent/20 rounded-full blur-sm"></div>
                    </div>
                    
                    <p class="text-lg lg:text-xl text-muted-foreground leading-relaxed max-w-2xl mx-auto">
                        Discover my professional journey through certifications, creative projects, and personal interests
                    </p>
                </div>

                <!-- Tab Navigation -->
                <div class="flex justify-center" :class="{ 'fade-in-up': isVisible }">
                    <div class="flex bg-card/80 backdrop-blur-xl border border-border/60 rounded-2xl p-1.5 shadow-lg">
                        <Link
                            v-for="tab in tabs" 
                            :key="tab.id"
                            :href="tab.route"
                            :class="[
                                'flex items-center gap-3 px-6 py-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-[1.02] hover:shadow-md',
                                activeTab === tab.id 
                                    ? 'bg-primary text-primary-foreground shadow-lg shadow-primary/25' 
                                    : 'text-muted-foreground hover:text-foreground hover:bg-muted/70'
                            ]"
                        >
                            <component :is="tab.icon" class="w-5 h-5" />
                            <span class="text-sm font-medium">{{ tab.name }}</span>
                        </Link>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="transition-all duration-300" :class="{ 'fade-in-up': isVisible }">
                    <!-- My Certificate Tab -->
                    <div v-if="activeTab === 'certificate'" class="space-y-8">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
                            <!-- Web Development Certificate -->
                            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/15 via-purple-500/15 to-indigo-600/15 aspect-[4/3] cursor-pointer transform transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-primary/25">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-center text-white/95">
                                        <div class="p-6 rounded-3xl bg-white/10 backdrop-blur-sm border border-white/20 mb-4">
                                            <Award class="w-16 h-16 mx-auto drop-shadow-lg" />
                                        </div>
                                        <div class="text-sm font-semibold uppercase tracking-widest opacity-90">Full Stack Web Development</div>
                                        <div class="text-xs font-medium opacity-80 mt-2">Professional Certificate</div>
                                    </div>
                                </div>
                                <!-- Modern overlay pattern -->
                                <div class="absolute top-6 right-6 w-24 h-24 border border-white/15 rounded-full animate-pulse"></div>
                                <div class="absolute bottom-6 left-6 w-16 h-16 border border-white/15 rounded-full animate-pulse" style="animation-delay: 0.5s"></div>
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-32 h-32 border border-white/10 rounded-full animate-pulse" style="animation-delay: 1s"></div>
                            </div>

                            <!-- UI/UX Design Certificate -->
                            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/15 via-teal-500/15 to-green-600/15 aspect-[4/3] cursor-pointer transform transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-primary/25">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-center text-white/95">
                                        <div class="p-6 rounded-3xl bg-white/10 backdrop-blur-sm border border-white/20 mb-4">
                                            <Award class="w-16 h-16 mx-auto drop-shadow-lg" />
                                        </div>
                                        <div class="text-sm font-semibold uppercase tracking-widest opacity-90">UI/UX Design Professional</div>
                                        <div class="text-xs font-medium opacity-80 mt-2">Advanced Design Certificate</div>
                                    </div>
                                </div>
                                <!-- Modern overlay pattern -->
                                <div class="absolute top-6 right-6 w-24 h-24 border border-white/15 rounded-full animate-pulse"></div>
                                <div class="absolute bottom-6 left-6 w-16 h-16 border border-white/15 rounded-full animate-pulse" style="animation-delay: 0.5s"></div>
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-32 h-32 border border-white/10 rounded-full animate-pulse" style="animation-delay: 1s"></div>
                            </div>

                            <!-- JavaScript Development Certificate -->
                            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-yellow-500/20 via-orange-500/20 to-red-500/20 aspect-[4/3] cursor-pointer transform transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-primary/25">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-center text-white/95">
                                        <div class="p-6 rounded-3xl bg-white/10 backdrop-blur-sm border border-white/20 mb-4">
                                            <Award class="w-16 h-16 mx-auto drop-shadow-lg" />
                                        </div>
                                        <div class="text-sm font-semibold uppercase tracking-widest opacity-90">Advanced JavaScript Developer</div>
                                        <div class="text-xs font-medium opacity-80 mt-2">ES6+ & Modern Frameworks</div>
                                    </div>
                                </div>
                                <!-- Modern overlay pattern -->
                                <div class="absolute top-6 right-6 w-24 h-24 border border-white/15 rounded-full animate-pulse"></div>
                                <div class="absolute bottom-6 left-6 w-16 h-16 border border-white/15 rounded-full animate-pulse" style="animation-delay: 0.5s"></div>
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-32 h-32 border border-white/10 rounded-full animate-pulse" style="animation-delay: 1s"></div>
                            </div>

                            <!-- Cloud Computing Certificate -->
                            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500/20 via-purple-500/20 to-pink-500/20 aspect-[4/3] cursor-pointer transform transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-primary/25">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-center text-white/95">
                                        <div class="p-6 rounded-3xl bg-white/10 backdrop-blur-sm border border-white/20 mb-4">
                                            <Award class="w-16 h-16 mx-auto drop-shadow-lg" />
                                        </div>
                                        <div class="text-sm font-semibold uppercase tracking-widest opacity-90">Cloud Computing Specialist</div>
                                        <div class="text-xs font-medium opacity-80 mt-2">AWS Solutions Architecture</div>
                                    </div>
                                </div>
                                <!-- Modern overlay pattern -->
                                <div class="absolute top-6 right-6 w-24 h-24 border border-white/15 rounded-full animate-pulse"></div>
                                <div class="absolute bottom-6 left-6 w-16 h-16 border border-white/15 rounded-full animate-pulse" style="animation-delay: 0.5s"></div>
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-32 h-32 border border-white/10 rounded-full animate-pulse" style="animation-delay: 1s"></div>
                            </div>
                        </div>
                    </div>

                    <!-- My Gallery Tab -->
                    <div v-if="activeTab === 'gallery'" class="space-y-10">
                        <!-- Modern Gallery Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                            <!-- Portfolio Website -->
                            <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-500/20 via-purple-500/20 to-indigo-600/20 aspect-square cursor-pointer transform transition-all duration-700 hover:scale-110 hover:rotate-2 hover:shadow-2xl hover:shadow-primary/30">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-black/15"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20">
                                        <Camera class="w-8 h-8 sm:w-12 sm:h-12 text-white drop-shadow-lg" />
                                    </div>
                                </div>
                                <!-- Floating elements -->
                                <div class="absolute top-4 right-4 w-6 h-6 bg-white/20 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                                <div class="absolute bottom-4 left-4 w-8 h-8 border border-white/30 rounded-full group-hover:rotate-45 transition-transform duration-500"></div>
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-20 h-20 border border-white/10 rounded-full group-hover:scale-125 transition-transform duration-700"></div>
                            </div>

                            <!-- E-commerce App -->
                            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/30 via-teal-500/30 to-green-600/30 aspect-square cursor-pointer transform transition-all duration-500 hover:scale-105 hover:-rotate-1 hover:shadow-2xl hover:shadow-primary/25">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                                    <Camera class="w-8 h-8 sm:w-12 sm:h-12 text-white drop-shadow-lg" />
                                </div>
                                <div class="absolute top-3 right-3 w-4 h-4 bg-white/20 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                                <div class="absolute bottom-3 left-3 w-6 h-6 border border-white/30 rounded-full group-hover:rotate-45 transition-transform duration-300"></div>
                            </div>

                            <!-- Mobile App Design -->
                            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-orange-500/30 via-red-500/30 to-pink-600/30 aspect-square cursor-pointer transform transition-all duration-500 hover:scale-105 hover:rotate-1 hover:shadow-2xl hover:shadow-primary/25">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                                    <Camera class="w-8 h-8 sm:w-12 sm:h-12 text-white drop-shadow-lg" />
                                </div>
                                <div class="absolute top-3 right-3 w-4 h-4 bg-white/20 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                                <div class="absolute bottom-3 left-3 w-6 h-6 border border-white/30 rounded-full group-hover:rotate-45 transition-transform duration-300"></div>
                            </div>

                            <!-- Dashboard UI -->
                            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-500/30 via-purple-500/30 to-indigo-600/30 aspect-square cursor-pointer transform transition-all duration-500 hover:scale-105 hover:-rotate-1 hover:shadow-2xl hover:shadow-primary/25">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                                    <Camera class="w-8 h-8 sm:w-12 sm:h-12 text-white drop-shadow-lg" />
                                </div>
                                <div class="absolute top-3 right-3 w-4 h-4 bg-white/20 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                                <div class="absolute bottom-3 left-3 w-6 h-6 border border-white/30 rounded-full group-hover:rotate-45 transition-transform duration-300"></div>
                            </div>

                            <!-- Landing Page -->
                            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-500/30 via-blue-500/30 to-indigo-600/30 aspect-square cursor-pointer transform transition-all duration-500 hover:scale-105 hover:rotate-1 hover:shadow-2xl hover:shadow-primary/25">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                                    <Camera class="w-8 h-8 sm:w-12 sm:h-12 text-white drop-shadow-lg" />
                                </div>
                                <div class="absolute top-3 right-3 w-4 h-4 bg-white/20 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                                <div class="absolute bottom-3 left-3 w-6 h-6 border border-white/30 rounded-full group-hover:rotate-45 transition-transform duration-300"></div>
                            </div>

                            <!-- Web Application -->
                            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-lime-500/30 via-green-500/30 to-emerald-600/30 aspect-square cursor-pointer transform transition-all duration-500 hover:scale-105 hover:-rotate-1 hover:shadow-2xl hover:shadow-primary/25">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                                    <Camera class="w-8 h-8 sm:w-12 sm:h-12 text-white drop-shadow-lg" />
                                </div>
                                <div class="absolute top-3 right-3 w-4 h-4 bg-white/20 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                                <div class="absolute bottom-3 left-3 w-6 h-6 border border-white/30 rounded-full group-hover:rotate-45 transition-transform duration-300"></div>
                            </div>

                            <!-- Brand Identity -->
                            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-500/30 via-pink-500/30 to-purple-600/30 aspect-square cursor-pointer transform transition-all duration-500 hover:scale-105 hover:rotate-1 hover:shadow-2xl hover:shadow-primary/25">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                                    <Camera class="w-8 h-8 sm:w-12 sm:h-12 text-white drop-shadow-lg" />
                                </div>
                                <div class="absolute top-3 right-3 w-4 h-4 bg-white/20 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                                <div class="absolute bottom-3 left-3 w-6 h-6 border border-white/30 rounded-full group-hover:rotate-45 transition-transform duration-300"></div>
                            </div>

                            <!-- API Documentation -->
                            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500/30 via-yellow-500/30 to-orange-600/30 aspect-square cursor-pointer transform transition-all duration-500 hover:scale-105 hover:-rotate-1 hover:shadow-2xl hover:shadow-primary/25">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                                    <Camera class="w-8 h-8 sm:w-12 sm:h-12 text-white drop-shadow-lg" />
                                </div>
                                <div class="absolute top-3 right-3 w-4 h-4 bg-white/20 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                                <div class="absolute bottom-3 left-3 w-6 h-6 border border-white/30 rounded-full group-hover:rotate-45 transition-transform duration-300"></div>
                            </div>
                        </div>

                        <!-- Large Featured Images -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 mt-12">
                            <!-- Featured Project 1 -->
                            <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600/30 via-purple-600/30 to-pink-600/30 aspect-video cursor-pointer transform transition-all duration-700 hover:scale-[1.03] hover:shadow-3xl hover:shadow-primary/35">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="p-6 rounded-3xl bg-white/10 backdrop-blur-sm border border-white/20">
                                        <Camera class="w-16 h-16 text-white drop-shadow-2xl" />
                                    </div>
                                </div>
                                <!-- Animated decorative elements -->
                                <div class="absolute top-8 right-8 w-10 h-10 bg-white/20 rounded-full group-hover:scale-125 group-hover:rotate-180 transition-all duration-500"></div>
                                <div class="absolute bottom-8 left-8 w-14 h-14 border-2 border-white/30 rounded-full group-hover:rotate-90 transition-transform duration-500"></div>
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-28 h-28 border border-white/20 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                            </div>

                            <!-- Featured Project 2 -->
                            <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600/40 via-teal-600/40 to-cyan-600/40 aspect-video cursor-pointer transform transition-all duration-700 hover:scale-[1.02] hover:shadow-3xl hover:shadow-primary/30">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-black/30"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                                    <Camera class="w-16 h-16 text-white drop-shadow-2xl" />
                                </div>
                                <!-- Animated decorative elements -->
                                <div class="absolute top-6 right-6 w-8 h-8 bg-white/20 rounded-full group-hover:scale-125 group-hover:rotate-180 transition-all duration-500"></div>
                                <div class="absolute bottom-6 left-6 w-12 h-12 border-2 border-white/30 rounded-full group-hover:rotate-90 transition-transform duration-500"></div>
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-24 h-24 border border-white/20 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                            </div>
                        </div>
                    </div>

                    <!-- My Music Tab -->
                    <div v-if="activeTab === 'music'" class="space-y-8">
                        <!-- Hidden YouTube Player -->
                        <div id="youtube-player-music" style="display: none;"></div>
                        <!-- Current Playing Card -->
                        <Card class="bg-gradient-to-br from-purple-500/15 via-pink-500/15 to-indigo-500/15 backdrop-blur-xl border border-border/60 overflow-hidden rounded-3xl shadow-2xl">
                            <CardContent class="p-0">
                                <div class="flex flex-col lg:flex-row">
                                    <!-- Album Art & Track Info -->
                                    <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row p-8 lg:w-1/2">
                                        <div class="relative mb-6 sm:mb-0 sm:mr-8 lg:mr-0 lg:mb-6 xl:mr-8 xl:mb-0">
                                            <div class="w-28 h-28 sm:w-36 sm:h-36 lg:w-44 lg:h-44 xl:w-36 xl:h-36 rounded-3xl overflow-hidden shadow-2xl ring-2 ring-white/20">
                                                <img 
                                                    :src="playlist[currentTrack].albumArt" 
                                                    :alt="playlist[currentTrack].title"
                                                    class="w-full h-full object-cover transition-transform duration-500"
                                                    :class="{ 'scale-110': isPlaying }"
                                                />
                                            </div>
                                            <!-- Playing Animation -->
                                            <div v-if="isPlaying" class="absolute inset-0 flex items-center justify-center">
                                                <div class="w-8 h-8 bg-black/50 rounded-full flex items-center justify-center backdrop-blur-sm">
                                                    <div class="flex space-x-1">
                                                        <div class="w-1 h-4 bg-white rounded-full animate-pulse"></div>
                                                        <div class="w-1 h-3 bg-white rounded-full animate-pulse" style="animation-delay: 0.1s"></div>
                                                        <div class="w-1 h-5 bg-white rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-2xl font-bold text-foreground mb-3 truncate">
                                                {{ playlist[currentTrack].title }}
                                            </h3>
                                            <p class="text-muted-foreground mb-3 truncate text-lg">
                                                {{ playlist[currentTrack].artist }}
                                            </p>
                                            <div class="flex items-center gap-3 mb-6">
                                                <Badge variant="secondary" class="text-sm px-3 py-1 bg-purple-100 text-purple-700 border-purple-200">
                                                    {{ playlist[currentTrack].genre }}
                                                </Badge>
                                                <span class="text-sm text-muted-foreground">
                                                    {{ playlist[currentTrack].duration }}
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
                                                        class="flex-1 h-2 bg-accent rounded-lg appearance-none cursor-pointer slider"
                                                    />
                                                    <span class="w-10">{{ duration > 0 ? formatTime(duration) : playlist[currentTrack].duration }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Controls -->
                                    <div class="flex flex-col justify-center p-8 lg:w-1/2 border-t lg:border-t-0 lg:border-l border-border/60">
                                        <!-- Main Controls -->
                                        <div class="flex items-center justify-center gap-6 mb-8">
                                            <button 
                                                @click="toggleShuffle"
                                                class="p-2 rounded-full transition-colors duration-200"
                                                :class="isShuffled ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground'"
                                            >
                                                <Shuffle class="w-4 h-4" />
                                            </button>
                                            
                                            <button 
                                                @click="previousTrack"
                                                class="p-3 rounded-full text-foreground hover:bg-muted transition-colors duration-200"
                                            >
                                                <SkipBack class="w-5 h-5" />
                                            </button>
                                            
                                            <button 
                                                @click="togglePlay"
                                                class="p-4 bg-primary text-primary-foreground rounded-full hover:bg-primary/90 transition-all duration-200 hover:scale-105"
                                            >
                                                <Play v-if="!isPlaying" class="w-6 h-6 ml-0.5" />
                                                <Pause v-else class="w-6 h-6" />
                                            </button>
                                            
                                            <button 
                                                @click="nextTrack"
                                                class="p-3 rounded-full text-foreground hover:bg-muted transition-colors duration-200"
                                            >
                                                <SkipForward class="w-5 h-5" />
                                            </button>
                                            
                                            <button 
                                                @click="toggleRepeat"
                                                class="p-2 rounded-full transition-colors duration-200"
                                                :class="repeatMode > 0 ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground'"
                                            >
                                                <Repeat class="w-4 h-4" />
                                            </button>
                                        </div>
                                        
                                        <!-- Secondary Controls -->
                                        <div class="flex items-center justify-between">
                                            <button 
                                                @click="toggleLike"
                                                class="p-2 rounded-full transition-colors duration-200"
                                                :class="isLiked ? 'text-red-500 hover:text-red-600' : 'text-muted-foreground hover:text-foreground'"
                                            >
                                                <Heart class="w-4 h-4" :class="{ 'fill-current': isLiked }" />
                                            </button>
                                            
                                            <div class="flex items-center gap-2">
                                                <Volume2 class="w-4 h-4 text-muted-foreground" />
                                                <div class="w-20 h-1 bg-muted rounded-full overflow-hidden">
                                                    <div class="h-full bg-primary rounded-full transition-all duration-300" :style="{ width: volume + '%' }"></div>
                                                </div>
                                            </div>
                                            
                                            <a 
                                                :href="playlist[currentTrack].youtubeUrl" 
                                                target="_blank"
                                                class="px-3 py-1 text-xs bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors duration-200"
                                            >
                                                YouTube
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Playlist -->
                        <Card class="bg-card/60 backdrop-blur-xl border border-border/60 rounded-3xl shadow-xl">
                            <CardHeader class="pb-4">
                                <CardTitle class="flex items-center gap-3 text-xl">
                                    <Music class="w-6 h-6" />
                                    Coding Playlist
                                </CardTitle>
                                <CardDescription class="text-base">{{ playlist.length }} tracks • Perfect for focus sessions</CardDescription>
                            </CardHeader>
                            <CardContent class="p-0">
                                <div class="space-y-1">
                                    <div 
                                        v-for="(track, index) in playlist" 
                                        :key="track.id"
                                        @click="selectTrack(index)"
                                        class="flex items-center gap-4 p-4 hover:bg-muted/50 cursor-pointer transition-colors duration-200 group"
                                        :class="{ 'bg-primary/10': currentTrack === index }"
                                    >
                                        <div class="relative">
                                            <div class="w-12 h-12 rounded-lg overflow-hidden">
                                                <img 
                                                    :src="track.albumArt" 
                                                    :alt="track.title"
                                                    class="w-full h-full object-cover"
                                                />
                                            </div>
                                            <div v-if="currentTrack === index && isPlaying" class="absolute inset-0 flex items-center justify-center bg-black/50 rounded-lg">
                                                <div class="flex space-x-0.5">
                                                    <div class="w-0.5 h-3 bg-white rounded-full animate-pulse"></div>
                                                    <div class="w-0.5 h-2 bg-white rounded-full animate-pulse" style="animation-delay: 0.1s"></div>
                                                    <div class="w-0.5 h-4 bg-white rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-medium text-foreground truncate mb-1" :class="{ 'text-primary': currentTrack === index }">
                                                {{ track.title }}
                                            </h4>
                                            <p class="text-sm text-muted-foreground truncate">
                                                {{ track.artist }}
                                            </p>
                                        </div>
                                        
                                        <div class="flex items-center gap-3">
                                            <Badge variant="outline" class="text-xs">
                                                {{ track.genre }}
                                            </Badge>
                                            <span class="text-sm text-muted-foreground">
                                                {{ track.duration }}
                                            </span>
                                            <button 
                                                @click.stop
                                                class="opacity-0 group-hover:opacity-100 p-1 rounded-full hover:bg-muted transition-all duration-200"
                                            >
                                                <Play class="w-4 h-4" />
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

button:hover {
    transform: translateY(-1px);
}

button:active {
    transform: translateY(0);
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