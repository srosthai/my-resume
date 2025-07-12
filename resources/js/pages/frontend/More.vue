<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { FileText, ImageIcon, Music, Award, Camera, Play, Pause, SkipBack, SkipForward, Volume2, Heart, Shuffle, Repeat } from 'lucide-vue-next'
import DockNavigation from '@/components/DockNavigation.vue'
import MusicPlayer from '@/components/MusicPlayer.vue'

const props = defineProps({
    title: String,
    description: String
})

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
        genre: "Lo-fi"
    },
    {
        id: 2,
        title: "360",
        artist: "Vannda",
        albumArt: "https://img.youtube.com/vi/VangtodgL0Y/hqdefault.jpg",
        youtubeUrl: "https://www.youtube.com/watch?v=VangtodgL0Y&list=RDVangtodgL0Y&start_radio=1",
        youtubeId: "VangtodgL0Y",
        duration: "0:03:41",
        genre: "Lo-fi"
    },
    {
        id: 3,
        title: "យប់ស្ងាត់/QUIET NIGHT",
        artist: "TEPPISETH",
        albumArt: "https://img.youtube.com/vi/JLevKPoa6BI/hqdefault.jpg",
        youtubeUrl: "https://www.youtube.com/watch?v=JLevKPoa6BI&list=RD8oLi5b4w4PQ&index=2",
        youtubeId: "JLevKPoa6BI",
        duration: "0:02:26",
        genre: "Lo-fi"
    },
    {
        id: 4,
        title: "រៀនចប់",
        artist: "All3rgy & Chan Sreykhouch",
        albumArt: "https://img.youtube.com/vi/8oLi5b4w4PQ/hqdefault.jpg",
        youtubeUrl: "https://www.youtube.com/watch?v=8oLi5b4w4PQ&list=RD8oLi5b4w4PQ&start_radio=1&rv=-IQcA1jmb3I",
        youtubeId: "8oLi5b4w4PQ",
        duration: "0:03:47",
        genre: "Lo-fi"
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
    { id: 'certificate', name: 'My Certificate', icon: FileText },
    { id: 'gallery', name: 'My Gallery', icon: ImageIcon },
    { id: 'music', name: 'My Music', icon: Music }
]

onMounted(() => {
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

    <div class="min-h-screen bg-gradient-to-br from-background via-background/95 to-background text-foreground font-sans overflow-x-hidden transition-all duration-300 pt-16">
        <DockNavigation currentRoute="/more" />
        
        <!-- Music Player -->
        <MusicPlayer />

        <!-- More Content Section -->
        <section class="min-h-screen py-20 px-4 max-w-6xl mx-auto relative">
            <!-- Background decoration -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
            </div>
            
            <div class="max-w-4xl mx-auto space-y-8 lg:space-y-12 relative z-10">
                <!-- Header -->
                <div class="text-center space-y-4 lg:space-y-6" :class="{ 'fade-in-up': isVisible }">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight">
                        <span class="bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                            More About Me
                        </span>
                    </h1>
                    
                    <p class="text-base lg:text-lg text-muted-foreground leading-relaxed">
                        Explore my certificates, gallery, and music collection
                    </p>
                </div>

                <!-- Tab Navigation -->
                <div class="flex justify-center" :class="{ 'fade-in-up': isVisible }">
                    <div class="flex bg-muted/50 backdrop-blur-sm border border-border/50 rounded-lg p-1">
                        <button 
                            v-for="tab in tabs" 
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                'flex items-center gap-2 px-4 py-3 rounded-md font-medium transition-all duration-200',
                                activeTab === tab.id 
                                    ? 'bg-background text-foreground shadow-sm' 
                                    : 'text-muted-foreground hover:text-foreground hover:bg-background/50'
                            ]"
                        >
                            <component :is="tab.icon" class="w-4 h-4" />
                            {{ tab.name }}
                        </button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="transition-all duration-300" :class="{ 'fade-in-up': isVisible }">
                    <!-- My Certificate Tab -->
                    <div v-if="activeTab === 'certificate'" class="space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Web Development Certificate -->
                            <Card class="group bg-card/50 backdrop-blur-sm border border-border/50 hover:bg-card/80 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/20 overflow-hidden">
                                <!-- Certificate Image -->
                                <div class="relative h-48 sm:h-56 bg-gradient-to-br from-blue-500/20 via-purple-500/20 to-blue-600/20 overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center text-white/90">
                                            <Award class="w-16 h-16 mx-auto mb-3 drop-shadow-lg" />
                                            <div class="text-sm font-medium uppercase tracking-widest opacity-80">Certificate</div>
                                        </div>
                                    </div>
                                    <!-- Modern overlay pattern -->
                                    <div class="absolute top-4 right-4 w-20 h-20 border border-white/20 rounded-full"></div>
                                    <div class="absolute bottom-4 left-4 w-12 h-12 border border-white/20 rounded-full"></div>
                                </div>
                                
                                <CardContent class="p-6 space-y-4">
                                    <!-- Title & Subtitle -->
                                    <div class="space-y-2">
                                        <h3 class="text-xl font-bold text-foreground group-hover:text-primary transition-colors duration-300">
                                            Full Stack Web Development
                                        </h3>
                                        <p class="text-base text-primary font-medium">
                                            Professional Certification Program
                                        </p>
                                    </div>
                                    
                                    <!-- Description -->
                                    <p class="text-sm text-muted-foreground leading-relaxed">
                                        Comprehensive certification covering modern web development technologies including Vue.js, Laravel, Node.js, database management, API development, and deployment strategies. Completed with practical projects and real-world applications.
                                    </p>
                                    
                                    <!-- Year & Technologies -->
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                                        <div class="flex items-center gap-2">
                                            <Badge variant="default" class="bg-primary/10 text-primary border-primary/20 font-medium">
                                                2023 - 2024
                                            </Badge>
                                            <Badge variant="outline" class="text-xs">
                                                Certified
                                            </Badge>
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            <Badge variant="secondary" class="text-xs">Vue.js</Badge>
                                            <Badge variant="secondary" class="text-xs">Laravel</Badge>
                                            <Badge variant="secondary" class="text-xs">Node.js</Badge>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            <!-- UI/UX Design Certificate -->
                            <Card class="group bg-card/50 backdrop-blur-sm border border-border/50 hover:bg-card/80 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/20 overflow-hidden">
                                <!-- Certificate Image -->
                                <div class="relative h-48 sm:h-56 bg-gradient-to-br from-emerald-500/20 via-teal-500/20 to-green-600/20 overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center text-white/90">
                                            <Award class="w-16 h-16 mx-auto mb-3 drop-shadow-lg" />
                                            <div class="text-sm font-medium uppercase tracking-widest opacity-80">Certificate</div>
                                        </div>
                                    </div>
                                    <!-- Modern overlay pattern -->
                                    <div class="absolute top-4 right-4 w-20 h-20 border border-white/20 rounded-full"></div>
                                    <div class="absolute bottom-4 left-4 w-12 h-12 border border-white/20 rounded-full"></div>
                                </div>
                                
                                <CardContent class="p-6 space-y-4">
                                    <!-- Title & Subtitle -->
                                    <div class="space-y-2">
                                        <h3 class="text-xl font-bold text-foreground group-hover:text-primary transition-colors duration-300">
                                            UI/UX Design Professional
                                        </h3>
                                        <p class="text-base text-primary font-medium">
                                            Advanced Design Certification
                                        </p>
                                    </div>
                                    
                                    <!-- Description -->
                                    <p class="text-sm text-muted-foreground leading-relaxed">
                                        Advanced certification in user interface and user experience design principles, covering design thinking, prototyping, user research, accessibility standards, and modern design tools including Figma, Adobe XD, and Sketch.
                                    </p>
                                    
                                    <!-- Year & Technologies -->
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                                        <div class="flex items-center gap-2">
                                            <Badge variant="default" class="bg-primary/10 text-primary border-primary/20 font-medium">
                                                2022 - 2023
                                            </Badge>
                                            <Badge variant="outline" class="text-xs">
                                                Certified
                                            </Badge>
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            <Badge variant="secondary" class="text-xs">Figma</Badge>
                                            <Badge variant="secondary" class="text-xs">Adobe XD</Badge>
                                            <Badge variant="secondary" class="text-xs">Sketch</Badge>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            <!-- JavaScript Development Certificate -->
                            <Card class="group bg-card/50 backdrop-blur-sm border border-border/50 hover:bg-card/80 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/20 overflow-hidden">
                                <!-- Certificate Image -->
                                <div class="relative h-48 sm:h-56 bg-gradient-to-br from-yellow-500/20 via-orange-500/20 to-red-500/20 overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center text-white/90">
                                            <Award class="w-16 h-16 mx-auto mb-3 drop-shadow-lg" />
                                            <div class="text-sm font-medium uppercase tracking-widest opacity-80">Certificate</div>
                                        </div>
                                    </div>
                                    <!-- Modern overlay pattern -->
                                    <div class="absolute top-4 right-4 w-20 h-20 border border-white/20 rounded-full"></div>
                                    <div class="absolute bottom-4 left-4 w-12 h-12 border border-white/20 rounded-full"></div>
                                </div>
                                
                                <CardContent class="p-6 space-y-4">
                                    <!-- Title & Subtitle -->
                                    <div class="space-y-2">
                                        <h3 class="text-xl font-bold text-foreground group-hover:text-primary transition-colors duration-300">
                                            Advanced JavaScript Developer
                                        </h3>
                                        <p class="text-base text-primary font-medium">
                                            ES6+ & Modern Frameworks
                                        </p>
                                    </div>
                                    
                                    <!-- Description -->
                                    <p class="text-sm text-muted-foreground leading-relaxed">
                                        Specialized certification in advanced JavaScript development including ES6+, async programming, testing frameworks, performance optimization, and modern JavaScript frameworks with emphasis on React and Vue.js ecosystems.
                                    </p>
                                    
                                    <!-- Year & Technologies -->
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                                        <div class="flex items-center gap-2">
                                            <Badge variant="default" class="bg-primary/10 text-primary border-primary/20 font-medium">
                                                2023 - 2024
                                            </Badge>
                                            <Badge variant="outline" class="text-xs">
                                                Certified
                                            </Badge>
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            <Badge variant="secondary" class="text-xs">ES6+</Badge>
                                            <Badge variant="secondary" class="text-xs">React</Badge>
                                            <Badge variant="secondary" class="text-xs">Vue.js</Badge>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            <!-- Cloud Computing Certificate -->
                            <Card class="group bg-card/50 backdrop-blur-sm border border-border/50 hover:bg-card/80 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/20 overflow-hidden">
                                <!-- Certificate Image -->
                                <div class="relative h-48 sm:h-56 bg-gradient-to-br from-indigo-500/20 via-purple-500/20 to-pink-500/20 overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center text-white/90">
                                            <Award class="w-16 h-16 mx-auto mb-3 drop-shadow-lg" />
                                            <div class="text-sm font-medium uppercase tracking-widest opacity-80">Certificate</div>
                                        </div>
                                    </div>
                                    <!-- Modern overlay pattern -->
                                    <div class="absolute top-4 right-4 w-20 h-20 border border-white/20 rounded-full"></div>
                                    <div class="absolute bottom-4 left-4 w-12 h-12 border border-white/20 rounded-full"></div>
                                </div>
                                
                                <CardContent class="p-6 space-y-4">
                                    <!-- Title & Subtitle -->
                                    <div class="space-y-2">
                                        <h3 class="text-xl font-bold text-foreground group-hover:text-primary transition-colors duration-300">
                                            Cloud Computing Specialist
                                        </h3>
                                        <p class="text-base text-primary font-medium">
                                            AWS Solutions Architecture
                                        </p>
                                    </div>
                                    
                                    <!-- Description -->
                                    <p class="text-sm text-muted-foreground leading-relaxed">
                                        Professional certification in cloud computing and AWS solutions architecture, covering serverless applications, containerization, microservices, cloud security, and scalable infrastructure design and deployment.
                                    </p>
                                    
                                    <!-- Year & Technologies -->
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                                        <div class="flex items-center gap-2">
                                            <Badge variant="default" class="bg-primary/10 text-primary border-primary/20 font-medium">
                                                2024 - Present
                                            </Badge>
                                            <Badge variant="outline" class="text-xs">
                                                In Progress
                                            </Badge>
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            <Badge variant="secondary" class="text-xs">AWS</Badge>
                                            <Badge variant="secondary" class="text-xs">Docker</Badge>
                                            <Badge variant="secondary" class="text-xs">Kubernetes</Badge>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </div>

                    <!-- My Gallery Tab -->
                    <div v-if="activeTab === 'gallery'" class="space-y-8">
                        <!-- Modern Gallery Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
                            <!-- Portfolio Website -->
                            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/30 via-purple-500/30 to-indigo-600/30 aspect-square cursor-pointer transform transition-all duration-500 hover:scale-105 hover:rotate-1 hover:shadow-2xl hover:shadow-primary/25">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                                    <Camera class="w-8 h-8 sm:w-12 sm:h-12 text-white drop-shadow-lg" />
                                </div>
                                <!-- Floating elements -->
                                <div class="absolute top-3 right-3 w-4 h-4 bg-white/20 rounded-full group-hover:scale-150 transition-transform duration-300"></div>
                                <div class="absolute bottom-3 left-3 w-6 h-6 border border-white/30 rounded-full group-hover:rotate-45 transition-transform duration-300"></div>
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6 mt-8">
                            <!-- Featured Project 1 -->
                            <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600/40 via-purple-600/40 to-pink-600/40 aspect-video cursor-pointer transform transition-all duration-700 hover:scale-[1.02] hover:shadow-3xl hover:shadow-primary/30">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-black/30"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                                    <Camera class="w-16 h-16 text-white drop-shadow-2xl" />
                                </div>
                                <!-- Animated decorative elements -->
                                <div class="absolute top-6 right-6 w-8 h-8 bg-white/20 rounded-full group-hover:scale-125 group-hover:rotate-180 transition-all duration-500"></div>
                                <div class="absolute bottom-6 left-6 w-12 h-12 border-2 border-white/30 rounded-full group-hover:rotate-90 transition-transform duration-500"></div>
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-24 h-24 border border-white/20 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
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
                    <div v-if="activeTab === 'music'" class="space-y-6">
                        <!-- Hidden YouTube Player -->
                        <div id="youtube-player-music" style="display: none;"></div>
                        <!-- Current Playing Card -->
                        <Card class="bg-gradient-to-br from-purple-500/10 via-pink-500/10 to-indigo-500/10 backdrop-blur-sm border border-border/50 overflow-hidden">
                            <CardContent class="p-0">
                                <div class="flex flex-col lg:flex-row">
                                    <!-- Album Art & Track Info -->
                                    <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row p-6 lg:w-1/2">
                                        <div class="relative mb-4 sm:mb-0 sm:mr-6 lg:mr-0 lg:mb-4 xl:mr-6 xl:mb-0">
                                            <div class="w-24 h-24 sm:w-32 sm:h-32 lg:w-40 lg:h-40 xl:w-32 xl:h-32 rounded-2xl overflow-hidden shadow-xl">
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
                                            <h3 class="text-xl font-bold text-foreground mb-2 truncate">
                                                {{ playlist[currentTrack].title }}
                                            </h3>
                                            <p class="text-muted-foreground mb-2 truncate">
                                                {{ playlist[currentTrack].artist }}
                                            </p>
                                            <div class="flex items-center gap-2 mb-4">
                                                <Badge variant="secondary" class="text-xs">
                                                    {{ playlist[currentTrack].genre }}
                                                </Badge>
                                                <span class="text-xs text-muted-foreground">
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
                                    <div class="flex flex-col justify-center p-6 lg:w-1/2 border-t lg:border-t-0 lg:border-l border-border/50">
                                        <!-- Main Controls -->
                                        <div class="flex items-center justify-center gap-4 mb-6">
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
                        <Card class="bg-card/50 backdrop-blur-sm border border-border/50">
                            <CardHeader>
                                <CardTitle class="flex items-center gap-2">
                                    <Music class="w-5 h-5" />
                                    Coding Playlist
                                </CardTitle>
                                <CardDescription>{{ playlist.length }} tracks • Perfect for focus sessions</CardDescription>
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
    height: 8px;
    border-radius: 4px;
}
</style>