<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { Skeleton } from '@/components/ui/skeleton'
import {
    ArrowUpRight,
    Github,
    ExternalLink,
    Search,
    X,
    Laptop,
} from 'lucide-vue-next'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

const props = defineProps({
    title: { type: String, default: 'Portfolio' },
    description: { type: String, default: '' },
    projects: { type: Array, default: () => [] },
    projectTypes: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({ type: '', search: '' }) },
})

const isLoading = ref(true)
const isVisible = ref(false)

const selectedCategory = ref(props.filters?.type ? String(props.filters.type) : 'all')
const searchQuery = ref(props.filters?.search || '')

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

const countByType = computed(() => {
    const m = {}
    for (const p of props.projects) {
        const id = p.project_type?.id
        if (!id) continue
        m[id] = (m[id] || 0) + 1
    }
    return m
})

const filteredProjects = computed(() => {
    let result = props.projects || []

    if (selectedCategory.value !== 'all') {
        const categoryId = parseInt(selectedCategory.value, 10)
        result = result.filter((p) => p.project_type?.id === categoryId)
    }

    const q = searchQuery.value.trim().toLowerCase()
    if (q) {
        result = result.filter((p) => {
            const title = (p.title || '').toLowerCase()
            const desc = (p.description || '').toLowerCase()
            const tech = Array.isArray(p.technologies)
                ? p.technologies.join(' ').toLowerCase()
                : (p.tech_stack || '').toLowerCase()
            return title.includes(q) || desc.includes(q) || tech.includes(q)
        })
    }

    return result
})

const clearSearch = () => {
    searchQuery.value = ''
}

const resetFilters = () => {
    searchQuery.value = ''
    selectedCategory.value = 'all'
}

const openProject = (project) => {
    router.visit(route('portfolio.show', project.id))
}

const getGithubUrl = (project) => {
    if (project.github_url) return project.github_url
    if (!Array.isArray(project.links)) return null
    const link = project.links.find((l) =>
        Object.keys(l)[0]?.toLowerCase().includes('github'),
    )
    return link ? Object.values(link)[0] : null
}

const getLiveUrl = (project) => {
    if (project.demo_url) return project.demo_url
    if (project.live_url) return project.live_url
    if (!Array.isArray(project.links)) return null
    const link = project.links.find(
        (l) => !Object.keys(l)[0]?.toLowerCase().includes('github'),
    )
    return link ? Object.values(link)[0] : null
}

const getYear = (project) => {
    const src = project.created_date || project.created_at
    if (!src) return null
    try {
        return new Date(src).getFullYear()
    } catch (e) {
        return null
    }
}

const techsOf = (project) => {
    if (Array.isArray(project.technologies) && project.technologies.length) {
        return project.technologies
    }
    if (project.tech_stack) {
        return project.tech_stack.split(',').map((t) => t.trim()).filter(Boolean)
    }
    return []
}

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
    <FrontendLayout current-route="/portfolio">
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta
                name="keywords"
                content="portfolio, projects, web development, Vue.js, Laravel, SROS THAI, Cambodia"
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
                    class="col-span-2 h-80 rounded-2xl sm:col-span-1 md:col-span-4"
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
                    Chapter 02
                </span>
                <span class="hidden md:inline">Archive / 2026</span>
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
                        Selected works
                    </span>
                    <span>{{ String(projects.length).padStart(2, '0') }} entries</span>
                </div>

                <h1 class="mt-5 font-serif leading-[0.9] tracking-tight sm:mt-6">
                    <span
                        class="block text-[clamp(2.75rem,10vw,7rem)] font-normal text-foreground"
                    >
                        Archive
                    </span>
                    <span
                        class="block text-[clamp(2.75rem,10vw,7rem)] font-normal italic text-foreground/80"
                    >
                        of work.
                    </span>
                </h1>

                <p
                    class="mt-5 max-w-2xl text-sm leading-relaxed text-muted-foreground sm:mt-6 sm:text-[15px] md:text-base"
                >
                    A curated catalog of things I've designed, built, and shipped — from full-stack
                    web applications to APIs and internal tools. Each entry is a chapter in an
                    ongoing practice.
                </p>
            </article>

            <!-- FILTER BAR -->
            <div
                class="reveal mt-4 sm:mt-5"
                style="--d: 160ms"
            >
                <div
                    class="rounded-[1.25rem] border border-border/60 bg-card/60 p-3 backdrop-blur-xl sm:rounded-2xl sm:p-4"
                >
                    <!-- Search + count -->
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="relative flex-1">
                            <Search
                                class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground/50"
                            />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search projects, tech, descriptions…"
                                class="search-input"
                            />
                            <button
                                v-if="searchQuery"
                                @click="clearSearch"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground/60 transition-colors hover:text-foreground"
                                aria-label="Clear search"
                            >
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                        <div
                            class="flex items-center justify-between gap-3 font-mono text-[10px] uppercase tracking-[0.25em] text-muted-foreground sm:justify-end"
                        >
                            <span class="tabular-nums">
                                {{ String(filteredProjects.length).padStart(2, '0') }} /
                                {{ String(projects.length).padStart(2, '0') }} showing
                            </span>
                        </div>
                    </div>

                    <!-- Category pills -->
                    <div class="relative mt-3">
                        <div
                            class="scroll-row flex gap-2 overflow-x-auto pb-1 sm:flex-wrap sm:overflow-visible sm:pb-0"
                        >
                            <button
                                class="filter-pill shrink-0"
                                :class="{ 'filter-pill-active': selectedCategory === 'all' }"
                                @click="selectedCategory = 'all'"
                            >
                                <span>All</span>
                                <span class="filter-count">{{ projects.length }}</span>
                            </button>
                            <button
                                v-for="type in projectTypes"
                                :key="type.id"
                                class="filter-pill shrink-0"
                                :class="{ 'filter-pill-active': selectedCategory === String(type.id) }"
                                @click="selectedCategory = String(type.id)"
                            >
                                <span>{{ type.name }}</span>
                                <span class="filter-count">{{ countByType[type.id] || 0 }}</span>
                            </button>
                        </div>
                        <!-- Fade edge to hint at more content on mobile -->
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 w-8 bg-gradient-to-l from-card/80 to-transparent sm:hidden"
                            aria-hidden="true"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- PROJECTS GRID -->
            <div class="mt-4 sm:mt-5">
                <!-- Empty state -->
                <div
                    v-if="filteredProjects.length === 0"
                    class="reveal rounded-[1.25rem] border border-dashed border-border/60 bg-card/30 px-6 py-16 text-center backdrop-blur-xl sm:rounded-3xl sm:py-24"
                    style="--d: 240ms"
                >
                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-border/60 bg-muted/40"
                    >
                        <Search class="h-5 w-5 text-muted-foreground/60" />
                    </div>
                    <h3 class="mt-5 font-serif text-2xl text-foreground sm:text-3xl">
                        Nothing on file.
                    </h3>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-muted-foreground">
                        <template v-if="searchQuery && selectedCategory !== 'all'">
                            No entries match "<span class="font-medium text-foreground">{{ searchQuery }}</span>" in this category.
                        </template>
                        <template v-else-if="searchQuery">
                            No entries match "<span class="font-medium text-foreground">{{ searchQuery }}</span>".
                        </template>
                        <template v-else-if="selectedCategory !== 'all'">
                            No entries filed under this category yet.
                        </template>
                        <template v-else>No entries yet — check back soon.</template>
                    </p>
                    <button
                        v-if="searchQuery || selectedCategory !== 'all'"
                        class="mt-6 inline-flex items-center gap-2 rounded-full border border-border/70 bg-background/60 px-5 py-2.5 font-mono text-[10px] uppercase tracking-[0.25em] text-foreground transition-all duration-300 hover:-translate-y-0.5 hover:border-foreground/60"
                        @click="resetFilters"
                    >
                        Reset filters
                        <ArrowUpRight class="h-3 w-3 opacity-60" />
                    </button>
                </div>

                <!-- Grid -->
                <div
                    v-else
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3"
                >
                    <article
                        v-for="(project, i) in filteredProjects"
                        :key="project.id"
                        class="project-card reveal group relative cursor-pointer overflow-hidden rounded-2xl border border-border/60 bg-card/60 backdrop-blur-xl"
                        :class="{
                            'project-card-featured sm:col-span-2 lg:col-span-2':
                                i === 0 && filteredProjects.length > 1,
                            'lg:col-span-3':
                                i === 0 && filteredProjects.length === 1,
                        }"
                        :style="{ '--d': 240 + i * 70 + 'ms' }"
                        @click="openProject(project)"
                        @keydown.enter="openProject(project)"
                        tabindex="0"
                        role="link"
                    >
                        <!-- Image -->
                        <div
                            class="relative overflow-hidden bg-muted/30"
                            :class="
                                i === 0 && filteredProjects.length > 1
                                    ? 'aspect-[21/10] sm:aspect-[21/9]'
                                    : 'aspect-[16/10]'
                            "
                        >
                            <img
                                v-if="project.image"
                                :src="project.image"
                                :alt="project.title"
                                class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.06]"
                                loading="lazy"
                            />
                            <div
                                v-else
                                class="flex h-full w-full items-center justify-center bg-gradient-to-br from-muted/60 to-muted/20"
                            >
                                <Laptop class="h-10 w-10 text-muted-foreground/40" />
                            </div>

                            <!-- Gradient wash on hover -->
                            <div
                                class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/50 via-black/10 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"
                            ></div>

                            <!-- Featured flag (only on first card) -->
                            <div
                                v-if="i === 0 && filteredProjects.length > 1"
                                class="absolute left-3 top-3 inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-black/55 px-2.5 py-1 font-mono text-[9px] uppercase tracking-[0.22em] text-white backdrop-blur-md"
                            >
                                <span class="relative flex h-1.5 w-1.5">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-70"></span>
                                    <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                                </span>
                                Featured
                            </div>

                            <!-- Top-left: category (moved down when featured badge is shown) -->
                            <div
                                class="absolute flex items-center gap-1.5"
                                :class="
                                    i === 0 && filteredProjects.length > 1
                                        ? 'left-3 top-12'
                                        : 'left-3 top-3'
                                "
                            >
                                <span class="badge-light">
                                    {{ project.project_type?.name || 'General' }}
                                </span>
                            </div>

                            <!-- Top-right: status -->
                            <div
                                v-if="project.status"
                                class="absolute right-3 top-3"
                            >
                                <span
                                    class="badge-status"
                                    :data-status="project.status"
                                >
                                    <span class="status-dot"></span>
                                    {{ project.status }}
                                </span>
                            </div>

                            <!-- Bottom-right: quick links (hover) -->
                            <div
                                class="absolute bottom-3 right-3 flex gap-1.5 opacity-0 transition-all duration-500 group-hover:opacity-100"
                            >
                                <a
                                    v-if="getGithubUrl(project)"
                                    :href="getGithubUrl(project)"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="quick-link"
                                    @click.stop
                                    aria-label="View on GitHub"
                                >
                                    <Github class="h-3.5 w-3.5" />
                                </a>
                                <a
                                    v-if="getLiveUrl(project)"
                                    :href="getLiveUrl(project)"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="quick-link"
                                    @click.stop
                                    aria-label="Open live demo"
                                >
                                    <ExternalLink class="h-3.5 w-3.5" />
                                </a>
                            </div>
                        </div>

                        <!-- Content -->
                        <div
                            class="relative"
                            :class="
                                i === 0 && filteredProjects.length > 1
                                    ? 'p-5 sm:p-7 md:p-8'
                                    : 'p-4 sm:p-5'
                            "
                        >
                            <!-- Serif index watermark -->
                            <span
                                class="index-watermark"
                                :class="{ 'index-watermark-featured': i === 0 && filteredProjects.length > 1 }"
                                aria-hidden="true"
                            >
                                {{ String(i + 1).padStart(2, '0') }}
                            </span>

                            <div class="relative z-[1]">
                                <div class="flex items-start justify-between gap-3">
                                    <h3
                                        class="line-clamp-2 font-serif leading-tight tracking-tight text-foreground"
                                        :class="
                                            i === 0 && filteredProjects.length > 1
                                                ? 'text-2xl sm:text-3xl md:text-4xl'
                                                : 'text-xl sm:text-[22px]'
                                        "
                                    >
                                        {{ project.title }}
                                    </h3>
                                    <span
                                        class="mt-0.5 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-border/60 text-muted-foreground transition-all duration-300 group-hover:-rotate-45 group-hover:border-foreground/60 group-hover:text-foreground"
                                    >
                                        <ArrowUpRight class="h-3.5 w-3.5" />
                                    </span>
                                </div>

                                <div
                                    v-if="getYear(project)"
                                    class="mt-1 font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground"
                                >
                                    {{ getYear(project) }}
                                </div>

                                <p
                                    class="mt-3 text-sm leading-relaxed text-muted-foreground"
                                    :class="
                                        i === 0 && filteredProjects.length > 1
                                            ? 'line-clamp-3 sm:line-clamp-4 md:max-w-2xl md:text-[15px]'
                                            : 'line-clamp-2'
                                    "
                                >
                                    {{ project.description }}
                                </p>

                                <!-- Tech chips -->
                                <div
                                    v-if="techsOf(project).length"
                                    class="mt-4 flex flex-wrap gap-1.5"
                                >
                                    <template v-if="i === 0 && filteredProjects.length > 1">
                                        <span
                                            v-for="(tech, ti) in techsOf(project).slice(0, 6)"
                                            :key="tech + ti"
                                            class="tech-chip"
                                        >
                                            {{ tech }}
                                        </span>
                                        <span
                                            v-if="techsOf(project).length > 6"
                                            class="tech-chip tech-chip-more"
                                        >
                                            +{{ techsOf(project).length - 6 }}
                                        </span>
                                    </template>
                                    <template v-else>
                                        <span
                                            v-for="(tech, ti) in techsOf(project).slice(0, 4)"
                                            :key="tech + ti"
                                            class="tech-chip"
                                        >
                                            {{ tech }}
                                        </span>
                                        <span
                                            v-if="techsOf(project).length > 4"
                                            class="tech-chip tech-chip-more"
                                        >
                                            +{{ techsOf(project).length - 4 }}
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>

            <!-- Footer tag -->
            <div
                class="reveal mt-10 flex flex-col items-start justify-between gap-1.5 font-mono text-[9px] uppercase tracking-[0.2em] text-muted-foreground/60 sm:mt-14 sm:flex-row sm:items-center sm:gap-2 sm:text-[10px] sm:tracking-[0.22em] md:text-xs"
                style="--d: 700ms"
            >
                <span>© {{ new Date().getFullYear() }} · Archive</span>
                <span>End of index →</span>
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
.filter-pill-active:hover {
    transform: translateY(-1.5px);
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

/* Project cards */
.project-card {
    transition:
        transform 0.4s cubic-bezier(0.22, 1, 0.36, 1),
        border-color 0.4s ease,
        box-shadow 0.4s ease;
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.02) inset;
    outline: none;
}
@media (hover: hover) and (pointer: fine) {
    .project-card:hover {
        transform: translateY(-4px);
        border-color: color-mix(in oklab, var(--color-foreground) 25%, var(--color-border));
        box-shadow: 0 24px 50px -30px rgba(0, 0, 0, 0.45);
    }
    .project-card-featured:hover {
        transform: translateY(-5px);
        box-shadow: 0 36px 70px -35px rgba(0, 0, 0, 0.55);
    }
}
.project-card:focus-visible {
    border-color: color-mix(in oklab, var(--color-foreground) 40%, var(--color-border));
    box-shadow: 0 0 0 2px color-mix(in oklab, var(--color-foreground) 20%, transparent);
}

/* Featured card — deeper base shadow to give it gravity */
.project-card-featured {
    box-shadow:
        0 1px 0 rgba(255, 255, 255, 0.04) inset,
        0 22px 50px -30px rgba(0, 0, 0, 0.35);
}

/* Italic serif index watermark (B direction) */
.index-watermark {
    position: absolute;
    right: -0.35rem;
    bottom: -1.25rem;
    font-family: 'Instrument Serif', 'Iowan Old Style', Georgia, serif;
    font-style: italic;
    font-weight: 400;
    font-size: clamp(5rem, 11vw, 8rem);
    line-height: 0.8;
    letter-spacing: -0.04em;
    color: color-mix(in oklab, var(--color-foreground) 7%, transparent);
    pointer-events: none;
    user-select: none;
    z-index: 0;
    transition:
        color 0.5s ease,
        transform 0.5s ease;
    will-change: color, transform;
}
.index-watermark-featured {
    right: -0.5rem;
    bottom: -2rem;
    font-size: clamp(8rem, 18vw, 14rem);
    letter-spacing: -0.06em;
    color: color-mix(in oklab, var(--color-foreground) 6%, transparent);
}
@media (hover: hover) and (pointer: fine) {
    .project-card:hover .index-watermark {
        color: color-mix(in oklab, var(--color-foreground) 11%, transparent);
        transform: translateX(-0.25rem);
    }
    .project-card:hover .index-watermark-featured {
        color: color-mix(in oklab, var(--color-foreground) 10%, transparent);
    }
}

/* Badges on image */
.badge-dark {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    background: rgba(10, 10, 10, 0.85);
    backdrop-filter: blur(8px);
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.625rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: #fafafa;
}
.badge-light {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.55rem;
    border-radius: 0.375rem;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(8px);
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.625rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    color: #0a0a0a;
    text-transform: uppercase;
}

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.55rem;
    border-radius: 9999px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    background: rgba(10, 10, 10, 0.7);
    backdrop-filter: blur(8px);
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.6rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    color: #fafafa;
    text-transform: uppercase;
}
.status-dot {
    width: 0.375rem;
    height: 0.375rem;
    border-radius: 9999px;
    background: #a3a3a3;
    box-shadow: 0 0 6px currentColor;
}
.badge-status[data-status='completed'] .status-dot {
    background: #10b981;
    color: #10b981;
}
.badge-status[data-status='in-progress'] .status-dot,
.badge-status[data-status='processing'] .status-dot {
    background: #3b82f6;
    color: #3b82f6;
}
.badge-status[data-status='pending'] .status-dot {
    background: #f59e0b;
    color: #f59e0b;
}

/* Quick link buttons */
.quick-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.95);
    color: #0a0a0a;
    transition:
        transform 0.2s ease,
        background-color 0.2s ease;
}
.quick-link:hover {
    transform: translateY(-1.5px);
    background: #fff;
}

/* Tech chips in card body */
.tech-chip {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.55rem;
    border-radius: 9999px;
    border: 1px solid var(--color-border);
    background: color-mix(in oklab, var(--color-muted) 40%, transparent);
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.65rem;
    letter-spacing: 0.04em;
    color: var(--color-muted-foreground);
}
.tech-chip-more {
    color: color-mix(in oklab, var(--color-muted-foreground) 60%, transparent);
}

/* Horizontal scroll styling for mobile pill row */
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
