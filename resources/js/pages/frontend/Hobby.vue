<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Skeleton } from '@/components/ui/skeleton'
import {
    Heart,
    Gamepad2,
    Palette,
    Music,
    Book,
    Code2,
    Coffee,
    Dumbbell,
    Camera,
    Plane,
    Terminal,
    ArrowUpRight,
} from 'lucide-vue-next'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

const props = defineProps({
    title: { type: String, default: 'Hobbies' },
    description: { type: String, default: '' },
})

const hobbies = [
    {
        icon: Code2,
        title: 'Open Source',
        description:
            'Contributing to public repositories, opening pull requests, and building small tools that scratch my own itch — and occasionally someone else\'s too.',
        category: 'Development',
    },
    {
        icon: Terminal,
        title: 'New Technologies',
        description:
            'Constantly experimenting with emerging frameworks, languages, and patterns. If it\'s new and interesting, it gets a weekend.',
        category: 'Learning',
    },
    {
        icon: Book,
        title: 'Technical Reading',
        description:
            'Working through programming books, long-form blog posts, and RFCs. Nothing beats reading the manual carefully.',
        category: 'Learning',
    },
    {
        icon: Palette,
        title: 'Interface Design',
        description:
            'Sketching layouts in Figma, studying typography, and obsessing over spacing. Building better eyes for better builds.',
        category: 'Creative',
    },
    {
        icon: Coffee,
        title: 'Coffee Brewing',
        description:
            'Pour-over, AeroPress, espresso. The ritual matters as much as the result — and the result pairs well with long sessions.',
        category: 'Lifestyle',
    },
    {
        icon: Gamepad2,
        title: 'Strategy Games',
        description:
            'Turn-based strategy and puzzles. The skills translate more directly to programming than I care to admit.',
        category: 'Entertainment',
    },
    {
        icon: Dumbbell,
        title: 'Fitness',
        description:
            'Regular lifting and conditioning. A reliable body makes everything else — including shipping — feel lighter.',
        category: 'Health',
    },
    {
        icon: Camera,
        title: 'Photography',
        description:
            'Composition, light, and patience. A quiet practice that sharpens attention to detail in every other discipline.',
        category: 'Creative',
    },
    {
        icon: Music,
        title: 'Music Production',
        description:
            'DAWs, sound design, and the occasional half-finished track. Signal processing and software have more in common than not.',
        category: 'Creative',
    },
    {
        icon: Plane,
        title: 'Travel & Culture',
        description:
            'Exploring new places, languages, and food. Distance from the screen reliably returns better ideas to it.',
        category: 'Lifestyle',
    },
]

const categories = [
    'All',
    'Development',
    'Learning',
    'Creative',
    'Lifestyle',
    'Entertainment',
    'Health',
]

const isLoading = ref(true)
const isVisible = ref(false)
const selectedCategory = ref('All')

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

const pointer = ref({ x: 50, y: 50 })
const handlePointer = (e) => {
    pointer.value = {
        x: (e.clientX / window.innerWidth) * 100,
        y: (e.clientY / window.innerHeight) * 100,
    }
}

const countByCategory = computed(() => {
    const map = { All: hobbies.length }
    for (const cat of categories) {
        if (cat === 'All') continue
        map[cat] = hobbies.filter((h) => h.category === cat).length
    }
    return map
})

const filteredHobbies = computed(() => {
    if (selectedCategory.value === 'All') return hobbies
    return hobbies.filter((h) => h.category === selectedCategory.value)
})

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
        requestAnimationFrame(() => {
            isVisible.value = true
        })
    }, 400)

    clockTimer = setInterval(() => {
        now.value = new Date()
    }, 60000)

    window.addEventListener('pointermove', handlePointer, { passive: true })
})

onBeforeUnmount(() => {
    if (clockTimer) clearInterval(clockTimer)
    window.removeEventListener('pointermove', handlePointer)
})
</script>

<template>
    <FrontendLayout current-route="/hobby">
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta
                name="keywords"
                content="hobbies, interests, personal, developer lifestyle, creative pursuits"
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
            class="mx-auto w-full max-w-7xl px-3 py-6 sm:px-6 sm:py-8 lg:px-10"
        >
            <div class="grid w-full grid-cols-2 gap-3 sm:gap-4 md:grid-cols-12 md:gap-5">
                <Skeleton class="col-span-2 h-56 rounded-3xl md:col-span-12" />
                <Skeleton class="col-span-2 h-16 rounded-2xl md:col-span-12" />
                <Skeleton
                    v-for="i in 6"
                    :key="i"
                    class="col-span-2 h-56 rounded-2xl sm:col-span-1 md:col-span-4"
                />
            </div>
        </section>

        <section
            v-else
            class="relative mx-auto w-full max-w-7xl px-3 py-6 sm:px-6 sm:py-8 lg:px-10"
            :class="{ 'is-visible': isVisible }"
        >
            <!-- Ambient + grain -->
            <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
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
                    Chapter 07
                </span>
                <span class="hidden md:inline">Off-screen / 2026</span>
                <span class="tabular-nums">{{ dateString }}</span>
            </div>

            <!-- HERO -->
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
                        Interludes
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <Heart class="h-3 w-3" />
                        {{ hobbies.length }} chapters
                    </span>
                </div>

                <h1 class="mt-5 font-serif leading-[0.9] tracking-tight sm:mt-6">
                    <span
                        class="block text-[clamp(2.5rem,9vw,6.5rem)] font-normal text-foreground"
                    >
                        Off
                    </span>
                    <span
                        class="block text-[clamp(2.5rem,9vw,6.5rem)] font-normal italic text-foreground/80"
                    >
                        -screen.
                    </span>
                </h1>

                <p
                    class="mt-5 max-w-2xl text-sm leading-relaxed text-muted-foreground sm:mt-6 sm:text-[15px] md:text-base"
                >
                    Beyond the editor — the rituals, obsessions, and practices that refill the
                    well. Each one quietly shapes the way I think, design, and ship code.
                </p>
            </article>

            <!-- FILTER BAR -->
            <div class="reveal mt-4 sm:mt-5" style="--d: 160ms">
                <div
                    class="rounded-[1.25rem] border border-border/60 bg-card/60 p-3 backdrop-blur-xl sm:rounded-2xl sm:p-4"
                >
                    <div class="relative">
                        <div
                            class="scroll-row flex gap-2 overflow-x-auto pb-1 sm:flex-wrap sm:overflow-visible sm:pb-0"
                        >
                            <button
                                v-for="category in categories"
                                :key="category"
                                class="filter-pill shrink-0"
                                :class="{
                                    'filter-pill-active': selectedCategory === category,
                                }"
                                @click="selectedCategory = category"
                            >
                                <span>{{ category }}</span>
                                <span class="filter-count">{{ countByCategory[category] }}</span>
                            </button>
                        </div>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 w-8 bg-gradient-to-l from-card/80 to-transparent sm:hidden"
                            aria-hidden="true"
                        ></div>
                    </div>
                    <div
                        class="mt-3 flex items-center justify-between font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground sm:mt-3"
                    >
                        <span>{{ filteredHobbies.length }} / {{ hobbies.length }} showing</span>
                        <span
                            v-if="selectedCategory !== 'All'"
                            class="hidden sm:inline"
                        >
                            filed under {{ selectedCategory }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- HOBBY GRID -->
            <div class="mt-4 sm:mt-5">
                <!-- Empty state -->
                <div
                    v-if="filteredHobbies.length === 0"
                    class="reveal rounded-[1.25rem] border border-dashed border-border/60 bg-card/30 px-6 py-16 text-center backdrop-blur-xl sm:rounded-3xl sm:py-24"
                    style="--d: 240ms"
                >
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-border/60 bg-muted/40"
                    >
                        <Heart class="h-5 w-5 text-muted-foreground/60" />
                    </div>
                    <h3 class="mt-5 font-serif text-2xl text-foreground sm:text-3xl">
                        Nothing here yet.
                    </h3>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-muted-foreground">
                        No entries filed under {{ selectedCategory }}.
                    </p>
                    <button
                        class="mt-6 inline-flex items-center gap-2 rounded-full border border-border/70 bg-background/60 px-5 py-2.5 font-mono text-[10px] uppercase tracking-[0.25em] text-foreground transition-all duration-300 hover:-translate-y-0.5 hover:border-foreground/60"
                        @click="selectedCategory = 'All'"
                    >
                        Show all
                        <ArrowUpRight class="h-3 w-3 opacity-60" />
                    </button>
                </div>

                <!-- Grid -->
                <div
                    v-else
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3"
                >
                    <article
                        v-for="(hobby, i) in filteredHobbies"
                        :key="hobby.title"
                        class="hobby-card reveal group relative overflow-hidden rounded-2xl border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:p-6"
                        :style="{ '--d': 240 + i * 60 + 'ms' }"
                    >
                        <!-- Serif index watermark -->
                        <span class="index-watermark" aria-hidden="true">
                            {{ String(i + 1).padStart(2, '0') }}
                        </span>

                        <div class="relative z-[1] flex h-full flex-col">
                            <!-- Top row: icon + category -->
                            <div class="flex items-start justify-between gap-3">
                                <span
                                    class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-border/60 bg-muted/40 text-foreground transition-all duration-300 group-hover:border-foreground/40 group-hover:bg-foreground group-hover:text-background sm:h-12 sm:w-12"
                                >
                                    <component
                                        :is="hobby.icon"
                                        class="h-5 w-5 sm:h-5 sm:w-5"
                                    />
                                </span>
                                <span class="category-chip">{{ hobby.category }}</span>
                            </div>

                            <!-- Title -->
                            <h3
                                class="mt-5 font-serif text-2xl leading-tight tracking-tight text-foreground sm:text-[26px]"
                            >
                                {{ hobby.title }}
                            </h3>

                            <!-- Description -->
                            <p
                                class="mt-3 flex-1 text-sm leading-relaxed text-muted-foreground"
                            >
                                {{ hobby.description }}
                            </p>

                            <!-- Footer -->
                            <div
                                class="mt-5 flex items-center justify-between border-t border-border/50 pt-3 font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground/70"
                            >
                                <span>/ {{ String(i + 1).padStart(2, '0') }}</span>
                                <span class="inline-flex items-center gap-1.5">
                                    Interlude
                                    <ArrowUpRight
                                        class="h-3 w-3 opacity-60 transition-all duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5 group-hover:opacity-100"
                                    />
                                </span>
                            </div>
                        </div>
                    </article>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="reveal mt-10 flex flex-col items-start justify-between gap-1.5 font-mono text-[9px] uppercase tracking-[0.2em] text-muted-foreground/60 sm:mt-14 sm:flex-row sm:items-center sm:gap-2 sm:text-[10px] sm:tracking-[0.22em] md:text-xs"
                style="--d: 700ms"
            >
                <span>© {{ new Date().getFullYear() }} · Interludes</span>
                <span>Back to the screen →</span>
            </div>
        </section>
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

/* Filter pills (shared vocabulary with Portfolio) */
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

/* Category chip in card */
.category-chip {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.65rem;
    border-radius: 9999px;
    border: 1px solid var(--color-border);
    background: color-mix(in oklab, var(--color-muted) 40%, transparent);
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--color-muted-foreground);
}

/* Hobby cards */
.hobby-card {
    transition:
        transform 0.4s cubic-bezier(0.22, 1, 0.36, 1),
        border-color 0.4s ease,
        box-shadow 0.4s ease;
    min-height: 16rem;
}
@media (hover: hover) and (pointer: fine) {
    .hobby-card:hover {
        transform: translateY(-4px);
        border-color: color-mix(in oklab, var(--color-foreground) 25%, var(--color-border));
        box-shadow: 0 22px 45px -28px rgba(0, 0, 0, 0.4);
    }
}

/* Italic serif index watermark */
.index-watermark {
    position: absolute;
    right: -0.3rem;
    bottom: -1.4rem;
    font-family: 'Instrument Serif', 'Iowan Old Style', Georgia, serif;
    font-style: italic;
    font-size: clamp(6rem, 13vw, 10rem);
    line-height: 0.8;
    letter-spacing: -0.05em;
    color: color-mix(in oklab, var(--color-foreground) 6%, transparent);
    pointer-events: none;
    user-select: none;
    z-index: 0;
    transition: color 0.5s ease, transform 0.5s ease;
}
@media (hover: hover) and (pointer: fine) {
    .hobby-card:hover .index-watermark {
        color: color-mix(in oklab, var(--color-foreground) 10%, transparent);
        transform: translateX(-0.25rem);
    }
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
