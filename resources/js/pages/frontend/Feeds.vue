<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import axios from 'axios'
import { Badge } from '@/components/ui/badge'
import { Skeleton } from '@/components/ui/skeleton'
import {
    Search,
    MapPin,
    Heart,
    Eye,
    Pin,
    ChevronLeft,
    ChevronRight,
    X,
    Compass,
    Utensils,
    Users,
    Briefcase,
    CalendarHeart,
    Sparkles,
    Coffee,
    ScanSearch,
} from 'lucide-vue-next'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import MusicPlayer from '@/components/MusicPlayer.vue'

const props = defineProps({
    title: {
        type: String,
        default: 'My Feeds',
    },
    description: {
        type: String,
        default: 'Follow my lifestyle, hangouts, and adventures',
    },
    feeds: {
        type: Array,
        default: () => [],
    },
    activityTypes: {
        type: Array,
        default: () => [],
    },
})

const isLoading = ref(true)
const isVisible = ref(false)
const searchQuery = ref('')
const selectedFilter = ref('All')
const expandedImages = ref(null)
const currentImageIndex = ref(0)
const searchFocused = ref(false)

// Track likes and views locally
const likedFeeds = ref(new Set(JSON.parse(localStorage.getItem('liked_feeds') || '[]')))
const feedStats = reactive({})
const likingInProgress = ref(new Set())

// Initialize feed stats from props
const initFeedStats = () => {
    props.feeds.forEach(feed => {
        feedStats[feed.id] = {
            likes_count: feed.likes_count,
            views: feed.views,
        }
    })
}

const getLikes = (feed) => feedStats[feed.id]?.likes_count ?? feed.likes_count
const getViews = (feed) => feedStats[feed.id]?.views ?? feed.views
const isLiked = (feedId) => likedFeeds.value.has(feedId)

const toggleLike = async (feed) => {
    if (likingInProgress.value.has(feed.id)) return
    likingInProgress.value.add(feed.id)

    // Optimistic update
    const wasLiked = likedFeeds.value.has(feed.id)
    if (wasLiked) {
        likedFeeds.value.delete(feed.id)
        if (feedStats[feed.id]) feedStats[feed.id].likes_count--
    } else {
        likedFeeds.value.add(feed.id)
        if (feedStats[feed.id]) feedStats[feed.id].likes_count++
    }
    localStorage.setItem('liked_feeds', JSON.stringify([...likedFeeds.value]))

    try {
        const { data } = await axios.post(`/api/feeds/${feed.id}/like`)
        if (feedStats[feed.id]) {
            feedStats[feed.id].likes_count = data.likes_count
        }
        // Sync liked state with server response
        if (data.liked) {
            likedFeeds.value.add(feed.id)
        } else {
            likedFeeds.value.delete(feed.id)
        }
        localStorage.setItem('liked_feeds', JSON.stringify([...likedFeeds.value]))
    } catch {
        // Revert on error
        if (wasLiked) {
            likedFeeds.value.add(feed.id)
            if (feedStats[feed.id]) feedStats[feed.id].likes_count++
        } else {
            likedFeeds.value.delete(feed.id)
            if (feedStats[feed.id]) feedStats[feed.id].likes_count--
        }
        localStorage.setItem('liked_feeds', JSON.stringify([...likedFeeds.value]))
    } finally {
        likingInProgress.value.delete(feed.id)
    }
}

const trackView = async (feed) => {
    const viewedKey = `feed_viewed_${feed.id}`
    if (sessionStorage.getItem(viewedKey)) return

    sessionStorage.setItem(viewedKey, '1')
    try {
        const { data } = await axios.post(`/api/feeds/${feed.id}/view`)
        if (feedStats[feed.id]) {
            feedStats[feed.id].views = data.views
        }
    } catch {
        // Silent fail for view tracking
    }
}

const handleKeydown = (e) => {
    if (!expandedImages.value) return
    if (e.key === 'Escape') closeImageViewer()
    if (e.key === 'ArrowRight') nextImage()
    if (e.key === 'ArrowLeft') prevImage()
}

onMounted(() => {
    initFeedStats()

    setTimeout(() => {
        isLoading.value = false
        setTimeout(() => {
            isVisible.value = true
            // Track views for all visible feeds after render
            props.feeds.forEach(feed => trackView(feed))
        }, 50)
    }, 800)

    window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown)
    document.body.style.overflow = ''
})

const activities = computed(() => {
    const types = ['All', ...new Set(props.activityTypes)]
    return types
})

const filteredFeeds = computed(() => {
    let filtered = props.feeds

    if (selectedFilter.value !== 'All') {
        filtered = filtered.filter((feed) => feed.activity_type === selectedFilter.value)
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(
            (feed) =>
                (feed.title && feed.title.toLowerCase().includes(query)) ||
                feed.body.toLowerCase().includes(query) ||
                (feed.location && feed.location.toLowerCase().includes(query)) ||
                (feed.tags && feed.tags.some((tag) => tag.toLowerCase().includes(query))),
        )
    }

    return filtered
})

const getActivityIcon = (type) => {
    const icons = {
        hangout: Users,
        travel: Compass,
        food: Utensils,
        lifestyle: Coffee,
        work: Briefcase,
        event: CalendarHeart,
    }
    return icons[type] || Sparkles
}

const getActivityAccent = (type) => {
    const accents = {
        hangout: { bg: 'bg-sky-500/8', text: 'text-sky-600 dark:text-sky-400', border: 'border-sky-500/15', dot: 'bg-sky-500' },
        travel: { bg: 'bg-emerald-500/8', text: 'text-emerald-600 dark:text-emerald-400', border: 'border-emerald-500/15', dot: 'bg-emerald-500' },
        food: { bg: 'bg-amber-500/8', text: 'text-amber-600 dark:text-amber-400', border: 'border-amber-500/15', dot: 'bg-amber-500' },
        lifestyle: { bg: 'bg-rose-500/8', text: 'text-rose-600 dark:text-rose-400', border: 'border-rose-500/15', dot: 'bg-rose-500' },
        work: { bg: 'bg-violet-500/8', text: 'text-violet-600 dark:text-violet-400', border: 'border-violet-500/15', dot: 'bg-violet-500' },
        event: { bg: 'bg-teal-500/8', text: 'text-teal-600 dark:text-teal-400', border: 'border-teal-500/15', dot: 'bg-teal-500' },
    }
    return accents[type] || { bg: 'bg-muted/50', text: 'text-muted-foreground', border: 'border-border/30', dot: 'bg-muted-foreground' }
}

const getMoodEmoji = (mood) => {
    const emojis = {
        happy: '😊',
        excited: '🎉',
        relaxed: '😌',
        grateful: '🙏',
        adventurous: '🏔️',
        thoughtful: '💭',
        creative: '🎨',
        energetic: '⚡',
    }
    return emojis[mood] || ''
}

const timeAgo = (date) => {
    const now = new Date()
    const past = new Date(date)
    const diffMs = now - past
    const diffSecs = Math.floor(diffMs / 1000)
    const diffMins = Math.floor(diffSecs / 60)
    const diffHours = Math.floor(diffMins / 60)
    const diffDays = Math.floor(diffHours / 24)
    const diffWeeks = Math.floor(diffDays / 7)
    const diffMonths = Math.floor(diffDays / 30)

    if (diffSecs < 60) return 'just now'
    if (diffMins < 60) return `${diffMins}m ago`
    if (diffHours < 24) return `${diffHours}h ago`
    if (diffDays < 7) return `${diffDays}d ago`
    if (diffWeeks < 4) return `${diffWeeks}w ago`
    if (diffMonths < 12) return `${diffMonths}mo ago`
    return past.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
    })
}

const openImageViewer = (feed, index) => {
    expandedImages.value = feed
    currentImageIndex.value = index
    document.body.style.overflow = 'hidden'
}

const closeImageViewer = () => {
    expandedImages.value = null
    currentImageIndex.value = 0
    document.body.style.overflow = ''
}

const nextImage = () => {
    if (expandedImages.value && currentImageIndex.value < expandedImages.value.images.length - 1) {
        currentImageIndex.value++
    }
}

const prevImage = () => {
    if (currentImageIndex.value > 0) {
        currentImageIndex.value--
    }
}
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="description" />
        <meta name="keywords" content="lifestyle, hangout, travel, food, activities, daily life, social feed" />
        <meta property="og:title" :content="title" />
        <meta property="og:description" :content="description" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="title" />
        <meta name="twitter:description" :content="description" />
        <meta name="robots" content="index, follow" />
    </Head>

    <FrontendLayout currentRoute="/feeds">
        <MusicPlayer />

        <div class="min-h-screen text-foreground overflow-x-hidden transition-all duration-300 pt-16">
            <div class="max-w-[680px] mx-auto px-4 sm:px-6 py-8 sm:py-12">

                <!-- ==================== SKELETON ==================== -->
                <template v-if="isLoading">
                    <div class="mb-10 sm:mb-14">
                        <Skeleton class="h-3 w-20 rounded-full mb-4" />
                        <Skeleton class="h-9 w-3/4 max-w-[224px] rounded-lg mb-3" />
                        <Skeleton class="h-4 w-full max-w-[288px] rounded-lg" />
                    </div>
                    <div class="mb-8">
                        <Skeleton class="h-10 w-full rounded-xl" />
                    </div>
                    <div class="flex gap-2 mb-10 overflow-hidden">
                        <Skeleton v-for="i in 4" :key="i" class="h-8 w-20 shrink-0 rounded-full" />
                    </div>
                    <div class="space-y-8">
                        <div v-for="i in 3" :key="i" class="space-y-3">
                            <div class="flex items-center gap-3">
                                <Skeleton class="h-9 w-9 rounded-full shrink-0" />
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <Skeleton class="h-3.5 w-20 rounded" />
                                    <Skeleton class="h-3 w-28 rounded" />
                                </div>
                            </div>
                            <Skeleton class="h-44 sm:h-52 w-full rounded-2xl" />
                            <Skeleton class="h-3.5 w-3/4 rounded" />
                            <Skeleton class="h-3.5 w-1/2 rounded" />
                        </div>
                    </div>
                </template>

                <!-- ==================== CONTENT ==================== -->
                <template v-else>

                    <!-- Header -->
                    <div class="mb-10 sm:mb-14 reveal" :class="{ 'is-visible': isVisible }"
                        :style="{ animationDelay: '0ms' }">
                        <span
                            class="inline-block text-[11px] font-medium tracking-[0.2em] uppercase text-muted-foreground/60 mb-3">
                            Journal
                        </span>
                        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-foreground mb-3">
                            {{ title }}
                        </h1>
                        <p class="text-sm sm:text-base lg:text-lg text-muted-foreground/80 max-w-md leading-relaxed">
                            {{ description }}
                        </p>
                    </div>

                    <!-- Search -->
                    <div class="mb-5 reveal" :class="{ 'is-visible': isVisible }"
                        :style="{ animationDelay: '80ms' }">
                        <div class="relative">
                            <Search
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground/50 transition-colors duration-200"
                                :class="{ 'text-foreground/70': searchFocused }" />
                            <input v-model="searchQuery" @focus="searchFocused = true"
                                @blur="searchFocused = false" type="text"
                                placeholder="Search posts, places, tags..."
                                class="w-full pl-10 pr-4 py-2.5 bg-muted/30 border border-border/40 rounded-xl text-sm text-foreground placeholder:text-muted-foreground/40 focus:outline-none focus:bg-background focus:border-border/80 focus:ring-1 focus:ring-ring/20 transition-all duration-300" />
                        </div>
                    </div>

                    <!-- Filter Pills -->
                    <div class="mb-8 sm:mb-10 reveal" :class="{ 'is-visible': isVisible }"
                        :style="{ animationDelay: '140ms' }">
                        <div
                            class="flex items-center gap-1.5 overflow-x-auto pb-1 -mx-4 px-4 sm:mx-0 sm:px-0 sm:flex-wrap sm:overflow-visible scrollbar-hide">
                            <button v-for="activity in activities" :key="activity"
                                @click="selectedFilter = activity"
                                class="filter-pill shrink-0 capitalize"
                                :class="selectedFilter === activity ? 'filter-pill--active' : 'filter-pill--idle'">
                                {{ activity }}
                            </button>
                        </div>
                    </div>

                    <!-- ==================== FEED TIMELINE ==================== -->
                    <div class="relative">
                        <!-- Timeline line (hidden on mobile) -->
                        <div v-if="filteredFeeds.length > 0"
                            class="absolute left-[19px] top-2 bottom-2 w-px bg-gradient-to-b from-border/60 via-border/30 to-transparent hidden sm:block" />

                        <div class="space-y-6 sm:space-y-10">
                            <article v-for="(feed, index) in filteredFeeds" :key="feed.id"
                                class="feed-card relative reveal" :class="{ 'is-visible': isVisible }"
                                :style="{ animationDelay: `${200 + index * 80}ms` }">

                                <!-- Timeline dot (hidden on mobile) -->
                                <div class="hidden sm:flex absolute left-0 top-7 w-10 items-center justify-center z-10">
                                    <div class="timeline-dot"
                                        :class="feed.activity_type ? getActivityAccent(feed.activity_type).dot : 'bg-muted-foreground/40'" />
                                </div>

                                <!-- Card -->
                                <div class="sm:ml-14 group">
                                    <div
                                        class="rounded-2xl border border-border/40 bg-card/60 backdrop-blur-sm overflow-hidden transition-all duration-300 hover:border-border/60 hover:shadow-lg hover:shadow-black/[0.03] dark:hover:shadow-black/20">

                                        <!-- Card header row -->
                                        <div class="p-4 sm:px-5 sm:pt-5 sm:pb-3">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                                    <!-- Activity icon -->
                                                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center shrink-0 transition-transform duration-300 group-hover:scale-110"
                                                        :class="feed.activity_type ? getActivityAccent(feed.activity_type).bg : 'bg-muted/50'">
                                                        <component :is="getActivityIcon(feed.activity_type)"
                                                            class="h-3.5 w-3.5 sm:h-4 sm:w-4"
                                                            :class="feed.activity_type ? getActivityAccent(feed.activity_type).text : 'text-muted-foreground'" />
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                                            <span v-if="feed.activity_type"
                                                                class="text-[11px] sm:text-xs font-semibold capitalize"
                                                                :class="feed.activity_type ? getActivityAccent(feed.activity_type).text : 'text-muted-foreground'">
                                                                {{ feed.activity_type }}
                                                            </span>
                                                            <span v-if="feed.mood" class="text-xs sm:text-sm leading-none"
                                                                :title="feed.mood">
                                                                {{ getMoodEmoji(feed.mood) }}
                                                            </span>
                                                        </div>
                                                        <p class="text-[10px] sm:text-[11px] text-muted-foreground/50 mt-0.5 font-medium tracking-wide truncate">
                                                            {{ formatDate(feed.published_at || feed.created_at) }}
                                                            <span class="mx-0.5 sm:mx-1 opacity-30">&middot;</span>
                                                            {{ timeAgo(feed.published_at || feed.created_at) }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <Badge v-if="feed.is_pinned" variant="outline"
                                                    class="text-[10px] font-medium bg-amber-500/8 text-amber-600 dark:text-amber-400 border-amber-500/15 gap-0.5 sm:gap-1 shrink-0 px-1.5 sm:px-2 py-0.5">
                                                    <Pin class="h-2.5 w-2.5" />
                                                    <span class="hidden xs:inline">Pinned</span>
                                                </Badge>
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="px-4 sm:px-5 pb-2">
                                            <h3 v-if="feed.title"
                                                class="text-base sm:text-lg md:text-xl font-bold text-foreground mb-1.5 sm:mb-2 leading-snug tracking-tight">
                                                {{ feed.title }}
                                            </h3>
                                            <p class="text-sm sm:text-[15px] text-foreground/75 leading-relaxed whitespace-pre-line">
                                                {{ feed.body }}
                                            </p>
                                        </div>

                                        <!-- Images -->
                                        <div v-if="feed.images && feed.images.length > 0" class="px-4 sm:px-5 pt-3 pb-1">
                                            <!-- Single image -->
                                            <div v-if="feed.images.length === 1"
                                                class="rounded-xl overflow-hidden cursor-pointer img-hover"
                                                @click="openImageViewer(feed, 0)">
                                                <img :src="feed.images[0]"
                                                    :alt="feed.title || 'Feed photo'"
                                                    class="w-full max-h-[320px] sm:max-h-[420px] object-cover" loading="lazy" />
                                            </div>

                                            <!-- Two images -->
                                            <div v-else-if="feed.images.length === 2"
                                                class="grid grid-cols-2 gap-1 sm:gap-1.5 rounded-xl overflow-hidden h-48 sm:h-64 md:h-72">
                                                <div v-for="(img, idx) in feed.images" :key="idx"
                                                    class="cursor-pointer img-hover overflow-hidden"
                                                    @click="openImageViewer(feed, idx)">
                                                    <img :src="img" :alt="`Photo ${idx + 1}`"
                                                        class="w-full h-full object-cover" loading="lazy" />
                                                </div>
                                            </div>

                                            <!-- Three or more images -->
                                            <div v-else
                                                class="flex gap-1 sm:gap-1.5 rounded-xl overflow-hidden h-52 sm:h-72 md:h-80">
                                                <!-- Large image (left ~65%) -->
                                                <div class="flex-[2] cursor-pointer img-hover overflow-hidden"
                                                    @click="openImageViewer(feed, 0)">
                                                    <img :src="feed.images[0]" alt="Photo 1"
                                                        class="w-full h-full object-cover"
                                                        loading="lazy" />
                                                </div>
                                                <!-- Right column (~35%) -->
                                                <div class="flex-[1] flex flex-col gap-1 sm:gap-1.5">
                                                    <div class="flex-1 cursor-pointer img-hover overflow-hidden"
                                                        @click="openImageViewer(feed, 1)">
                                                        <img :src="feed.images[1]" alt="Photo 2"
                                                            class="w-full h-full object-cover"
                                                            loading="lazy" />
                                                    </div>
                                                    <div class="relative flex-1 cursor-pointer img-hover overflow-hidden"
                                                        @click="openImageViewer(feed, 2)">
                                                        <img :src="feed.images[2]" alt="Photo 3"
                                                            class="w-full h-full object-cover"
                                                            loading="lazy" />
                                                        <div v-if="feed.images.length > 3"
                                                            class="absolute inset-0 bg-black/40 backdrop-blur-[2px] flex items-center justify-center">
                                                            <span class="text-white text-base sm:text-lg font-semibold">+{{
                                                                feed.images.length - 3 }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Location -->
                                        <div v-if="feed.location"
                                            class="px-4 sm:px-5 pt-3 flex items-center gap-1.5">
                                            <MapPin class="h-3.5 w-3.5 text-muted-foreground/40 shrink-0" />
                                            <span class="text-xs sm:text-[13px] text-muted-foreground/60 font-medium truncate">
                                                {{ feed.location }}
                                            </span>
                                        </div>

                                        <!-- Tags -->
                                        <div v-if="feed.tags && feed.tags.length"
                                            class="px-4 sm:px-5 pt-2 sm:pt-2.5 flex flex-wrap gap-x-2 gap-y-1">
                                            <span v-for="tag in feed.tags" :key="tag"
                                                class="text-[11px] sm:text-[12px] font-medium text-primary/60 hover:text-primary transition-colors cursor-default">
                                                #{{ tag }}
                                            </span>
                                        </div>

                                        <!-- Footer -->
                                        <div class="px-4 sm:px-5 py-3 sm:py-4 mt-2 flex items-center gap-4 sm:gap-5 border-t border-border/20">
                                            <button
                                                @click="toggleLike(feed)"
                                                class="flex items-center gap-1.5 text-xs sm:text-[13px] font-medium transition-all duration-200 group/like"
                                                :class="isLiked(feed.id) ? 'text-rose-500' : 'text-muted-foreground/50 hover:text-rose-400'"
                                                :disabled="likingInProgress.has(feed.id)">
                                                <Heart class="h-3.5 w-3.5 transition-transform duration-200 group-hover/like:scale-110 group-active/like:scale-95"
                                                    :class="{ 'fill-current': isLiked(feed.id) }" />
                                                {{ getLikes(feed) }}
                                            </button>
                                            <span
                                                class="flex items-center gap-1.5 text-xs sm:text-[13px] text-muted-foreground/50 font-medium">
                                                <Eye class="h-3.5 w-3.5" />
                                                {{ getViews(feed) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="filteredFeeds.length === 0"
                        class="text-center py-16 sm:py-20 reveal" :class="{ 'is-visible': isVisible }"
                        :style="{ animationDelay: '200ms' }">
                        <div
                            class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-muted/40 flex items-center justify-center mx-auto mb-4 sm:mb-5">
                            <ScanSearch class="h-6 w-6 sm:h-7 sm:w-7 text-muted-foreground/40" />
                        </div>
                        <h3 class="text-sm sm:text-base font-semibold text-foreground/70 mb-1.5">
                            {{ searchQuery || selectedFilter !== 'All' ? 'No matching posts' : 'No feeds yet' }}
                        </h3>
                        <p class="text-xs sm:text-sm text-muted-foreground/50 max-w-xs mx-auto">
                            {{ searchQuery || selectedFilter !== 'All'
                                ? 'Try adjusting your search or filter to find what you\'re looking for'
                                : 'Stay tuned — new posts are on the way!' }}
                        </p>
                    </div>
                </template>
            </div>
        </div>

        <!-- ==================== IMAGE LIGHTBOX ==================== -->
        <Teleport to="body">
            <Transition name="lightbox">
                <div v-if="expandedImages"
                    class="fixed inset-0 z-[100] flex items-center justify-center"
                    @click.self="closeImageViewer">

                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/90 sm:bg-black/85 backdrop-blur-xl" />

                    <!-- Close (larger touch target on mobile) -->
                    <button @click="closeImageViewer"
                        class="absolute top-3 right-3 sm:top-5 sm:right-5 z-20 w-11 h-11 sm:w-10 sm:h-10 rounded-full bg-white/10 hover:bg-white/20 active:bg-white/30 flex items-center justify-center transition-all duration-200">
                        <X class="h-5 w-5 text-white/80" />
                    </button>

                    <!-- Prev button -->
                    <button v-if="currentImageIndex > 0" @click="prevImage"
                        class="absolute left-2 sm:left-6 z-20 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white/10 hover:bg-white/20 active:bg-white/30 flex items-center justify-center transition-all duration-200">
                        <ChevronLeft class="h-5 w-5 text-white/80" />
                    </button>

                    <!-- Image -->
                    <div class="relative z-10 w-full max-w-[94vw] sm:max-w-[90vw] max-h-[80vh] sm:max-h-[88vh] flex items-center justify-center px-12 sm:px-16">
                        <img :src="expandedImages.images[currentImageIndex]"
                            :alt="`Photo ${currentImageIndex + 1}`"
                            class="max-w-full max-h-[80vh] sm:max-h-[88vh] object-contain rounded-lg shadow-2xl"
                            :key="currentImageIndex" />
                    </div>

                    <!-- Next button -->
                    <button v-if="expandedImages && currentImageIndex < expandedImages.images.length - 1"
                        @click="nextImage"
                        class="absolute right-2 sm:right-6 z-20 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white/10 hover:bg-white/20 active:bg-white/30 flex items-center justify-center transition-all duration-200">
                        <ChevronRight class="h-5 w-5 text-white/80" />
                    </button>

                    <!-- Dot indicators -->
                    <div class="absolute bottom-4 sm:bottom-6 z-20 flex items-center gap-1.5 sm:gap-2">
                        <div v-for="(_, idx) in expandedImages.images" :key="idx"
                            class="h-1.5 rounded-full transition-all duration-300"
                            :class="idx === currentImageIndex ? 'bg-white w-4' : 'bg-white/30 w-1.5'" />
                    </div>
                </div>
            </Transition>
        </Teleport>
    </FrontendLayout>
</template>

<style scoped>
/* ===== Reveal Animation ===== */
.reveal {
    opacity: 0;
    transform: translateY(16px);
}

.reveal.is-visible {
    animation: revealUp 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

@keyframes revealUp {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ===== Timeline Dot ===== */
.timeline-dot {
    width: 10px;
    height: 10px;
    border-radius: 9999px;
    box-shadow: 0 0 0 3px var(--color-background);
    transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}

.feed-card:hover .timeline-dot {
    transform: scale(1.3);
}

/* ===== Filter Pills ===== */
.filter-pill {
    padding: 5px 14px;
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 0.01em;
    transition: all 0.25s cubic-bezier(0.22, 1, 0.36, 1);
    white-space: nowrap;
}

@media (min-width: 640px) {
    .filter-pill {
        padding: 6px 16px;
        font-size: 13px;
    }
}

.filter-pill--idle {
    color: var(--color-muted-foreground);
    background: transparent;
    border: 1px solid transparent;
}

.filter-pill--idle:hover {
    color: var(--color-foreground);
    background: var(--color-muted);
}

.filter-pill--active {
    background: var(--color-foreground);
    color: var(--color-background);
    border: 1px solid transparent;
}

/* ===== Image Hover ===== */
.img-hover img {
    transition: transform 0.5s cubic-bezier(0.22, 1, 0.36, 1);
}

.img-hover:hover img {
    transform: scale(1.03);
}

/* ===== Lightbox Transition ===== */
.lightbox-enter-active {
    transition: opacity 0.3s ease;
}

.lightbox-leave-active {
    transition: opacity 0.2s ease;
}

.lightbox-enter-from,
.lightbox-leave-to {
    opacity: 0;
}

/* ===== Scrollbar Hide ===== */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* ===== Reduced Motion ===== */
@media (prefers-reduced-motion: reduce) {
    .reveal {
        opacity: 1;
        transform: none;
    }

    .reveal.is-visible {
        animation: none;
    }

    .img-hover img {
        transition: none;
    }

    .filter-pill {
        transition: none;
    }
}
</style>
