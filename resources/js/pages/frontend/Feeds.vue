<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, onBeforeUnmount } from 'vue'
import { Head } from '@inertiajs/vue3'
import axios from 'axios'
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
    ArrowUpRight,
} from 'lucide-vue-next'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

const props = defineProps({
    title: { type: String, default: 'My Feeds' },
    description: { type: String, default: 'Follow my lifestyle, hangouts, and adventures' },
    feeds: { type: Array, default: () => [] },
    activityTypes: { type: Array, default: () => [] },
})

const isLoading = ref(true)
const isVisible = ref(false)
const searchQuery = ref('')
const selectedFilter = ref('All')
const searchFocused = ref(false)
const expandedImages = ref(null)
const currentImageIndex = ref(0)

// Like / view tracking
const likedFeeds = ref(
    new Set(
        typeof window !== 'undefined'
            ? JSON.parse(localStorage.getItem('liked_feeds') || '[]')
            : [],
    ),
)
const feedStats = reactive({})
const likingInProgress = ref(new Set())

// Clock for meta strip
const now = ref(new Date())
let clockTimer = null

const dateString = computed(() => {
    try {
        return new Intl.DateTimeFormat('en-US', {
            timeZone: 'Asia/Phnom_Penh',
            weekday: 'short',
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        }).format(now.value)
    } catch (e) {
        return ''
    }
})

// Mouse-reactive glow
const pointer = ref({ x: 50, y: 50 })
const handlePointer = (e) => {
    pointer.value = {
        x: (e.clientX / window.innerWidth) * 100,
        y: (e.clientY / window.innerHeight) * 100,
    }
}

const initFeedStats = () => {
    props.feeds.forEach((feed) => {
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
        if (data.liked) {
            likedFeeds.value.add(feed.id)
        } else {
            likedFeeds.value.delete(feed.id)
        }
        localStorage.setItem('liked_feeds', JSON.stringify([...likedFeeds.value]))
    } catch {
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
        // silent
    }
}

const handleKeydown = (e) => {
    if (!expandedImages.value) return
    if (e.key === 'Escape') closeImageViewer()
    if (e.key === 'ArrowRight') nextImage()
    if (e.key === 'ArrowLeft') prevImage()
}

const activities = computed(() => {
    const types = ['All', ...new Set(props.activityTypes)]
    return types
})

const countByActivity = computed(() => {
    const m = { All: props.feeds.length }
    for (const t of props.activityTypes || []) {
        m[t] = props.feeds.filter((f) => f.activity_type === t).length
    }
    return m
})

const filteredFeeds = computed(() => {
    let filtered = props.feeds || []
    if (selectedFilter.value !== 'All') {
        filtered = filtered.filter((f) => f.activity_type === selectedFilter.value)
    }
    const q = searchQuery.value.trim().toLowerCase()
    if (q) {
        filtered = filtered.filter(
            (f) =>
                (f.title && f.title.toLowerCase().includes(q)) ||
                (f.body && f.body.toLowerCase().includes(q)) ||
                (f.location && f.location.toLowerCase().includes(q)) ||
                (f.tags && f.tags.some((t) => t.toLowerCase().includes(q))),
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
    const past = new Date(date)
    const diffMs = now.value - past
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
    return past.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    })
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
    if (
        expandedImages.value &&
        currentImageIndex.value < expandedImages.value.images.length - 1
    ) {
        currentImageIndex.value++
    }
}

const prevImage = () => {
    if (currentImageIndex.value > 0) {
        currentImageIndex.value--
    }
}

onMounted(() => {
    initFeedStats()

    setTimeout(() => {
        isLoading.value = false
        requestAnimationFrame(() => {
            isVisible.value = true
            props.feeds.forEach((feed) => trackView(feed))
        })
    }, 400)

    clockTimer = setInterval(() => {
        now.value = new Date()
    }, 60000)

    window.addEventListener('keydown', handleKeydown)
    window.addEventListener('pointermove', handlePointer, { passive: true })
})

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown)
    document.body.style.overflow = ''
})

onBeforeUnmount(() => {
    if (clockTimer) clearInterval(clockTimer)
    window.removeEventListener('pointermove', handlePointer)
})
</script>

<template>
    <FrontendLayout current-route="/feeds">
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta
                name="keywords"
                content="lifestyle, hangout, travel, food, activities, journal, dispatches"
            />
            <meta property="og:title" :content="title" />
            <meta property="og:description" :content="description" />
            <meta property="og:type" content="website" />
            <link
                href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500;600&display=swap"
                rel="stylesheet"
            />
        </Head>

        <!-- Skeleton -->
        <section
            v-if="isLoading"
            class="mx-auto w-full max-w-3xl px-3 py-6 sm:px-6 sm:py-8"
        >
            <Skeleton class="h-56 w-full rounded-3xl mb-5" />
            <Skeleton class="h-16 w-full rounded-2xl mb-6" />
            <div class="space-y-6">
                <Skeleton v-for="i in 3" :key="i" class="h-72 w-full rounded-2xl" />
            </div>
        </section>

        <section
            v-else
            class="relative mx-auto w-full max-w-3xl px-3 py-6 sm:px-6 sm:py-8"
            :class="{ 'is-visible': isVisible }"
        >
            <!-- Ambient + grain -->
            <div
                class="pointer-events-none fixed inset-0 -z-10 overflow-hidden"
                aria-hidden="true"
            >
                <div
                    class="ambient-blob"
                    :style="{ left: pointer.x + '%', top: pointer.y + '%' }"
                ></div>
                <div class="grain-overlay"></div>
            </div>

            <!-- Top meta strip -->
            <div
                class="reveal mb-4 flex flex-wrap items-center justify-between gap-x-4 gap-y-2 font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground/70 sm:mb-5 sm:text-[10px] md:text-xs"
                style="--d: 0ms"
            >
                <span class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-foreground/40"></span>
                    Chapter 08
                </span>
                <span class="hidden md:inline">Dispatches / 2026</span>
                <span class="tabular-nums">{{ dateString }}</span>
            </div>

            <!-- MASTHEAD -->
            <article
                class="reveal relative overflow-hidden rounded-[1.5rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-3xl sm:p-8 md:p-10"
                style="--d: 80ms"
            >
                <div
                    class="pointer-events-none absolute -right-24 -top-24 h-64 w-64 rotate-45 bg-gradient-to-br from-foreground/[0.04] to-transparent"
                    aria-hidden="true"
                ></div>

                <div
                    class="flex items-center justify-between font-mono text-[9px] uppercase tracking-[0.25em] text-muted-foreground sm:text-[10px] md:text-xs"
                >
                    <span class="inline-flex items-center gap-2">
                        <span class="h-px w-5 bg-foreground/40 sm:w-6"></span>
                        Field journal
                    </span>
                    <span>{{ String(feeds.length).padStart(2, '0') }} entries</span>
                </div>

                <h1 class="mt-5 font-serif leading-[0.9] tracking-tight sm:mt-6">
                    <span
                        class="block text-[clamp(2.5rem,9vw,5.5rem)] font-normal text-foreground"
                    >
                        Dispatches.
                    </span>
                </h1>

                <p
                    class="mt-5 max-w-xl text-sm leading-relaxed text-muted-foreground sm:mt-6 sm:text-[15px] md:text-base"
                >
                    Notes from the road — hangouts, travel, meals, work. A running personal
                    journal kept in real time.
                </p>
            </article>

            <!-- SEARCH + FILTERS -->
            <div class="reveal mt-4 sm:mt-5" style="--d: 160ms">
                <div
                    class="rounded-[1.25rem] border border-border/60 bg-card/60 p-3 backdrop-blur-xl sm:rounded-2xl sm:p-4"
                >
                    <div class="relative">
                        <Search
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground/50"
                        />
                        <input
                            v-model="searchQuery"
                            @focus="searchFocused = true"
                            @blur="searchFocused = false"
                            type="text"
                            placeholder="Search dispatches, places, tags…"
                            class="search-input"
                        />
                        <button
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground/60 transition-colors hover:text-foreground"
                            aria-label="Clear search"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    </div>

                    <div class="relative mt-3">
                        <div
                            class="scroll-row flex gap-2 overflow-x-auto pb-1 sm:flex-wrap sm:overflow-visible sm:pb-0"
                        >
                            <button
                                v-for="activity in activities"
                                :key="activity"
                                class="filter-pill shrink-0 capitalize"
                                :class="{
                                    'filter-pill-active': selectedFilter === activity,
                                }"
                                @click="selectedFilter = activity"
                            >
                                <span>{{ activity }}</span>
                                <span class="filter-count">{{ countByActivity[activity] || 0 }}</span>
                            </button>
                        </div>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 w-8 bg-gradient-to-l from-card/80 to-transparent sm:hidden"
                            aria-hidden="true"
                        ></div>
                    </div>

                    <div
                        class="mt-3 flex items-center justify-between font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground"
                    >
                        <span>
                            {{ String(filteredFeeds.length).padStart(2, '0') }} /
                            {{ String(feeds.length).padStart(2, '0') }} dispatches
                        </span>
                        <span v-if="selectedFilter !== 'All'" class="hidden sm:inline">
                            filed under {{ selectedFilter }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- FEED TIMELINE -->
            <div class="mt-6 sm:mt-8">
                <!-- Empty state -->
                <div
                    v-if="filteredFeeds.length === 0"
                    class="reveal rounded-[1.25rem] border border-dashed border-border/60 bg-card/30 px-6 py-16 text-center backdrop-blur-xl sm:rounded-3xl sm:py-24"
                    style="--d: 240ms"
                >
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-border/60 bg-muted/40"
                    >
                        <ScanSearch class="h-5 w-5 text-muted-foreground/60" />
                    </div>
                    <h3 class="mt-5 font-serif text-2xl text-foreground sm:text-3xl">
                        {{ searchQuery || selectedFilter !== 'All'
                            ? 'Nothing filed yet.'
                            : 'No dispatches yet.' }}
                    </h3>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-muted-foreground">
                        <template v-if="searchQuery || selectedFilter !== 'All'">
                            Try a different search term or filter.
                        </template>
                        <template v-else>Stay tuned — new dispatches on the way.</template>
                    </p>
                </div>

                <!-- Timeline -->
                <div v-else class="relative">
                    <!-- Timeline rail (desktop only) -->
                    <div
                        class="absolute left-[11px] top-3 bottom-3 w-px bg-gradient-to-b from-border/80 via-border/40 to-transparent hidden sm:block"
                        aria-hidden="true"
                    ></div>

                    <div class="space-y-5 sm:space-y-7">
                        <article
                            v-for="(feed, index) in filteredFeeds"
                            :key="feed.id"
                            class="feed-card reveal relative"
                            :style="{ '--d': 240 + index * 70 + 'ms' }"
                        >
                            <!-- Timeline dot -->
                            <div
                                class="timeline-dot hidden sm:flex"
                                aria-hidden="true"
                            >
                                <span></span>
                            </div>

                            <!-- Card -->
                            <div class="sm:ml-10">
                                <div
                                    class="overflow-hidden rounded-2xl border border-border/60 bg-card/60 backdrop-blur-xl transition-all duration-300 hover:border-foreground/20"
                                >
                                    <!-- Header row -->
                                    <div class="px-4 pt-4 pb-2 sm:px-6 sm:pt-5 sm:pb-3">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex min-w-0 items-center gap-2.5">
                                                <span
                                                    class="activity-chip"
                                                >
                                                    <component
                                                        :is="getActivityIcon(feed.activity_type)"
                                                        class="h-3 w-3"
                                                    />
                                                </span>
                                                <div class="min-w-0 flex items-center gap-2">
                                                    <span
                                                        v-if="feed.activity_type"
                                                        class="font-mono text-[10px] uppercase tracking-[0.22em] text-foreground"
                                                    >
                                                        {{ feed.activity_type }}
                                                    </span>
                                                    <span
                                                        v-if="feed.mood"
                                                        class="text-xs leading-none"
                                                        :title="feed.mood"
                                                    >
                                                        {{ getMoodEmoji(feed.mood) }}
                                                    </span>
                                                    <span
                                                        class="font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground"
                                                    >
                                                        · {{ timeAgo(feed.published_at || feed.created_at) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <span
                                                v-if="feed.is_pinned"
                                                class="pinned-chip"
                                            >
                                                <Pin class="h-2.5 w-2.5" />
                                                <span class="hidden xs:inline">Pinned</span>
                                            </span>
                                        </div>
                                        <div
                                            class="mt-1 ml-[34px] font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground/60"
                                        >
                                            {{ formatDate(feed.published_at || feed.created_at) }}
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="px-4 sm:px-6">
                                        <h3
                                            v-if="feed.title"
                                            class="mt-1 font-serif text-2xl leading-tight tracking-tight text-foreground sm:text-[28px]"
                                        >
                                            {{ feed.title }}
                                        </h3>
                                        <p
                                            class="mt-2.5 whitespace-pre-line text-sm leading-relaxed text-muted-foreground sm:text-[15px]"
                                        >
                                            {{ feed.body }}
                                        </p>
                                    </div>

                                    <!-- Images -->
                                    <div
                                        v-if="feed.images && feed.images.length > 0"
                                        class="mt-4 px-4 sm:px-6"
                                    >
                                        <!-- Single -->
                                        <div
                                            v-if="feed.images.length === 1"
                                            class="group/img overflow-hidden rounded-2xl cursor-pointer"
                                            @click="openImageViewer(feed, 0)"
                                        >
                                            <img
                                                :src="feed.images[0]"
                                                :alt="feed.title || 'Feed photo'"
                                                class="w-full max-h-[420px] object-cover transition-transform duration-700 group-hover/img:scale-[1.03]"
                                                loading="lazy"
                                            />
                                        </div>

                                        <!-- Two -->
                                        <div
                                            v-else-if="feed.images.length === 2"
                                            class="grid grid-cols-2 gap-1 overflow-hidden rounded-2xl h-52 sm:h-64"
                                        >
                                            <div
                                                v-for="(img, idx) in feed.images"
                                                :key="idx"
                                                class="group/img cursor-pointer overflow-hidden"
                                                @click="openImageViewer(feed, idx)"
                                            >
                                                <img
                                                    :src="img"
                                                    :alt="`Photo ${idx + 1}`"
                                                    class="h-full w-full object-cover transition-transform duration-700 group-hover/img:scale-[1.05]"
                                                    loading="lazy"
                                                />
                                            </div>
                                        </div>

                                        <!-- Three or more -->
                                        <div
                                            v-else
                                            class="flex gap-1 overflow-hidden rounded-2xl h-56 sm:h-72"
                                        >
                                            <div
                                                class="group/img flex-[2] cursor-pointer overflow-hidden"
                                                @click="openImageViewer(feed, 0)"
                                            >
                                                <img
                                                    :src="feed.images[0]"
                                                    alt="Photo 1"
                                                    class="h-full w-full object-cover transition-transform duration-700 group-hover/img:scale-[1.04]"
                                                    loading="lazy"
                                                />
                                            </div>
                                            <div class="flex flex-[1] flex-col gap-1">
                                                <div
                                                    class="group/img flex-1 cursor-pointer overflow-hidden"
                                                    @click="openImageViewer(feed, 1)"
                                                >
                                                    <img
                                                        :src="feed.images[1]"
                                                        alt="Photo 2"
                                                        class="h-full w-full object-cover transition-transform duration-700 group-hover/img:scale-[1.05]"
                                                        loading="lazy"
                                                    />
                                                </div>
                                                <div
                                                    class="group/img relative flex-1 cursor-pointer overflow-hidden"
                                                    @click="openImageViewer(feed, 2)"
                                                >
                                                    <img
                                                        :src="feed.images[2]"
                                                        alt="Photo 3"
                                                        class="h-full w-full object-cover transition-transform duration-700 group-hover/img:scale-[1.05]"
                                                        loading="lazy"
                                                    />
                                                    <div
                                                        v-if="feed.images.length > 3"
                                                        class="absolute inset-0 flex items-center justify-center bg-black/45 backdrop-blur-[2px]"
                                                    >
                                                        <span class="font-serif text-2xl text-white">
                                                            +{{ feed.images.length - 3 }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location + tags -->
                                    <div
                                        v-if="feed.location || (feed.tags && feed.tags.length)"
                                        class="px-4 pt-3 sm:px-6 sm:pt-4"
                                    >
                                        <div
                                            v-if="feed.location"
                                            class="flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-[0.18em] text-muted-foreground"
                                        >
                                            <MapPin class="h-3 w-3 shrink-0 opacity-70" />
                                            <span class="truncate">{{ feed.location }}</span>
                                        </div>

                                        <div
                                            v-if="feed.tags && feed.tags.length"
                                            class="mt-2 flex flex-wrap gap-1.5"
                                        >
                                            <span
                                                v-for="tag in feed.tags"
                                                :key="tag"
                                                class="tag-chip"
                                            >
                                                #{{ tag }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Footer -->
                                    <div
                                        class="mt-4 flex items-center gap-4 border-t border-border/50 px-4 py-3 sm:gap-5 sm:px-6 sm:py-4"
                                    >
                                        <button
                                            @click="toggleLike(feed)"
                                            :disabled="likingInProgress.has(feed.id)"
                                            class="group/like flex items-center gap-1.5 font-mono text-[11px] uppercase tracking-[0.15em] transition-colors duration-200"
                                            :class="
                                                isLiked(feed.id)
                                                    ? 'text-rose-500'
                                                    : 'text-muted-foreground hover:text-rose-400'
                                            "
                                            :aria-label="isLiked(feed.id) ? 'Unlike dispatch' : 'Like dispatch'"
                                        >
                                            <Heart
                                                class="h-3.5 w-3.5 transition-transform duration-200 group-hover/like:scale-110 group-active/like:scale-95"
                                                :class="{ 'fill-current': isLiked(feed.id) }"
                                            />
                                            <span class="tabular-nums">{{ getLikes(feed) }}</span>
                                        </button>
                                        <span
                                            class="flex items-center gap-1.5 font-mono text-[11px] uppercase tracking-[0.15em] text-muted-foreground"
                                        >
                                            <Eye class="h-3.5 w-3.5 opacity-70" />
                                            <span class="tabular-nums">{{ getViews(feed) }}</span>
                                        </span>
                                        <span
                                            class="ml-auto font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground/50"
                                        >
                                            #{{ String(index + 1).padStart(2, '0') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="reveal mt-10 flex flex-col items-start justify-between gap-1.5 font-mono text-[9px] uppercase tracking-[0.2em] text-muted-foreground/60 sm:mt-14 sm:flex-row sm:items-center sm:gap-2 sm:text-[10px] sm:tracking-[0.22em] md:text-xs"
                style="--d: 900ms"
            >
                <span>© {{ new Date().getFullYear() }} · Field journal</span>
                <span>End of transmission →</span>
            </div>
        </section>

        <!-- IMAGE LIGHTBOX -->
        <Teleport to="body">
            <Transition name="lightbox">
                <div
                    v-if="expandedImages"
                    class="fixed inset-0 z-[100] flex items-center justify-center"
                    @click.self="closeImageViewer"
                >
                    <div class="absolute inset-0 bg-black/90 backdrop-blur-xl sm:bg-black/85"></div>

                    <button
                        @click="closeImageViewer"
                        class="absolute right-3 top-3 z-20 flex h-11 w-11 items-center justify-center rounded-full bg-white/10 transition-all duration-200 hover:bg-white/20 active:bg-white/30 sm:right-5 sm:top-5 sm:h-10 sm:w-10"
                        aria-label="Close"
                    >
                        <X class="h-5 w-5 text-white/90" />
                    </button>

                    <button
                        v-if="currentImageIndex > 0"
                        @click="prevImage"
                        class="absolute left-2 z-20 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 transition-all duration-200 hover:bg-white/20 active:bg-white/30 sm:left-6 sm:h-11 sm:w-11"
                        aria-label="Previous image"
                    >
                        <ChevronLeft class="h-5 w-5 text-white/90" />
                    </button>

                    <div
                        class="relative z-10 flex h-full max-h-[80vh] w-full max-w-[94vw] items-center justify-center px-12 sm:max-h-[88vh] sm:max-w-[90vw] sm:px-16"
                    >
                        <img
                            :src="expandedImages.images[currentImageIndex]"
                            :alt="`Photo ${currentImageIndex + 1}`"
                            class="max-h-[80vh] max-w-full rounded-lg object-contain shadow-2xl sm:max-h-[88vh]"
                            :key="currentImageIndex"
                        />
                    </div>

                    <button
                        v-if="expandedImages && currentImageIndex < expandedImages.images.length - 1"
                        @click="nextImage"
                        class="absolute right-2 z-20 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 transition-all duration-200 hover:bg-white/20 active:bg-white/30 sm:right-6 sm:h-11 sm:w-11"
                        aria-label="Next image"
                    >
                        <ChevronRight class="h-5 w-5 text-white/90" />
                    </button>

                    <div
                        class="absolute bottom-4 z-20 flex items-center gap-1.5 sm:bottom-6 sm:gap-2"
                    >
                        <div
                            v-for="(_, idx) in expandedImages.images"
                            :key="idx"
                            class="h-1.5 rounded-full transition-all duration-300"
                            :class="idx === currentImageIndex ? 'w-4 bg-white' : 'w-1.5 bg-white/30'"
                        ></div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </FrontendLayout>
</template>

<style scoped>
.reveal {
    opacity: 0;
    transform: translateY(18px);
}
.is-visible .reveal {
    animation: revealUp 0.9s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    animation-delay: var(--d, 0ms);
}
@keyframes revealUp {
    from {
        opacity: 0;
        transform: translateY(18px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Typography */
h1,
h3,
.font-serif {
    font-family: 'Instrument Serif', 'Iowan Old Style', 'Apple Garamond', Georgia, serif;
    font-feature-settings: 'ss01', 'liga';
}
.font-mono {
    font-family: 'JetBrains Mono', ui-monospace, 'SFMono-Regular', Menlo, monospace;
}

/* Ambient */
.ambient-blob {
    position: absolute;
    width: 40rem;
    height: 40rem;
    border-radius: 9999px;
    transform: translate(-50%, -50%);
    background: radial-gradient(
        closest-side,
        color-mix(in oklab, var(--color-foreground) 7%, transparent),
        transparent 70%
    );
    filter: blur(60px);
    transition:
        left 600ms cubic-bezier(0.22, 1, 0.36, 1),
        top 600ms cubic-bezier(0.22, 1, 0.36, 1);
    will-change: left, top;
}
.grain-overlay {
    position: absolute;
    inset: 0;
    opacity: 0.035;
    mix-blend-mode: overlay;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.9'/%3E%3C/svg%3E");
    pointer-events: none;
}

/* Search input */
.search-input {
    width: 100%;
    height: 2.75rem;
    padding: 0 2.5rem;
    background: color-mix(in oklab, var(--color-muted) 40%, transparent);
    border: 1px solid var(--color-border);
    border-radius: 9999px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.8rem;
    letter-spacing: 0.02em;
    color: var(--color-foreground);
    transition:
        border-color 0.2s ease,
        background-color 0.2s ease;
}
.search-input::placeholder {
    color: color-mix(in oklab, var(--color-muted-foreground) 80%, transparent);
    font-size: 0.75rem;
    letter-spacing: 0.05em;
}
.search-input:focus {
    outline: none;
    border-color: color-mix(in oklab, var(--color-foreground) 40%, var(--color-border));
    background: var(--color-background);
}

/* Filter pills */
.filter-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.85rem;
    border-radius: 9999px;
    border: 1px solid var(--color-border);
    background: color-mix(in oklab, var(--color-card) 60%, transparent);
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.72rem;
    letter-spacing: 0.08em;
    color: var(--color-foreground);
    white-space: nowrap;
    cursor: pointer;
    transition:
        transform 0.2s ease,
        border-color 0.2s ease,
        background-color 0.2s ease,
        color 0.2s ease;
}
.filter-pill:hover {
    transform: translateY(-1.5px);
    border-color: color-mix(in oklab, var(--color-foreground) 35%, var(--color-border));
}
.filter-pill-active {
    background: var(--color-foreground);
    color: var(--color-background);
    border-color: var(--color-foreground);
}
.filter-count {
    display: inline-block;
    min-width: 1.25rem;
    padding: 0 0.35rem;
    border-radius: 9999px;
    background: color-mix(in oklab, currentColor 15%, transparent);
    font-size: 0.65rem;
    text-align: center;
    font-weight: 600;
    letter-spacing: 0.04em;
}
.scroll-row {
    scrollbar-width: none;
    -ms-overflow-style: none;
    scroll-snap-type: x proximity;
}
.scroll-row::-webkit-scrollbar {
    display: none;
}
.scroll-row > * {
    scroll-snap-align: start;
}

/* Timeline dot */
.timeline-dot {
    position: absolute;
    left: 5px;
    top: 1.5rem;
    width: 12px;
    height: 12px;
    align-items: center;
    justify-content: center;
    z-index: 2;
}
.timeline-dot span {
    width: 8px;
    height: 8px;
    border-radius: 9999px;
    background: var(--color-foreground);
    box-shadow: 0 0 0 3px var(--color-background);
    transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1);
}
.feed-card:hover .timeline-dot span {
    transform: scale(1.4);
}

/* Activity chip (monochrome) */
.activity-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.75rem;
    height: 1.75rem;
    border-radius: 9999px;
    border: 1px solid var(--color-border);
    background: color-mix(in oklab, var(--color-muted) 40%, transparent);
    color: var(--color-foreground);
    flex-shrink: 0;
}

/* Pinned chip */
.pinned-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.2rem 0.55rem;
    border-radius: 9999px;
    border: 1px solid color-mix(in oklab, #f59e0b 40%, var(--color-border));
    background: color-mix(in oklab, #f59e0b 10%, transparent);
    color: #b45309;
    font-family: 'JetBrains Mono', monospace;
    font-size: 9px;
    font-weight: 600;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    flex-shrink: 0;
}
:global(.dark) .pinned-chip {
    color: #fbbf24;
}

/* Tag chips */
.tag-chip {
    display: inline-flex;
    align-items: center;
    padding: 0.2rem 0.55rem;
    border-radius: 9999px;
    border: 1px solid var(--color-border);
    background: color-mix(in oklab, var(--color-muted) 30%, transparent);
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    letter-spacing: 0.04em;
    color: var(--color-foreground);
    transition: transform 0.2s ease, border-color 0.2s ease;
}
.tag-chip:hover {
    transform: translateY(-1px);
    border-color: color-mix(in oklab, var(--color-foreground) 35%, var(--color-border));
}

/* Feed card subtle hover */
.feed-card > div > div {
    transition:
        border-color 0.35s ease,
        box-shadow 0.35s ease;
}
@media (hover: hover) and (pointer: fine) {
    .feed-card:hover > div > div {
        box-shadow: 0 22px 45px -28px rgba(0, 0, 0, 0.35);
    }
}

/* Lightbox transition */
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

@media (prefers-reduced-motion: reduce) {
    .reveal,
    .is-visible .reveal {
        opacity: 1 !important;
        transform: none !important;
        animation: none !important;
    }
    .ambient-blob {
        transition: none;
    }
}
</style>
