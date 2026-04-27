<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Skeleton } from '@/components/ui/skeleton'
import {
    Search,
    Copy,
    Check,
    ArrowLeft,
    Book,
    Code2,
    Terminal,
    Lightbulb,
    X,
    ArrowUpRight,
} from 'lucide-vue-next'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

const props = defineProps({
    title: { type: String, default: 'My Notes' },
    description: {
        type: String,
        default: 'My collection of programming notes and tutorials',
    },
    notes: { type: Array, default: () => [] },
})

const isLoading = ref(true)
const isVisible = ref(false)
const searchQuery = ref('')
const selectedFilter = ref('All')
const selectedNote = ref(null)
const copiedCommands = ref(new Set())

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

const categories = computed(() => {
    return ['All', ...new Set(props.notes.map((n) => n.category))]
})

const countByCategory = computed(() => {
    const m = { All: props.notes.length }
    for (const cat of categories.value) {
        if (cat === 'All') continue
        m[cat] = props.notes.filter((n) => n.category === cat).length
    }
    return m
})

const filteredNotes = computed(() => {
    let filtered = props.notes
    if (selectedFilter.value !== 'All') {
        filtered = filtered.filter((n) => n.category === selectedFilter.value)
    }
    const q = searchQuery.value.trim().toLowerCase()
    if (q) {
        filtered = filtered.filter(
            (n) =>
                (n.title || '').toLowerCase().includes(q) ||
                (n.description || '').toLowerCase().includes(q) ||
                (Array.isArray(n.tags) && n.tags.some((t) => t.toLowerCase().includes(q))),
        )
    }
    return filtered
})

const copyCommand = async (command, stepIndex, commandIndex) => {
    try {
        await navigator.clipboard.writeText(command)
        const key = `${stepIndex}-${commandIndex}`
        copiedCommands.value.add(key)
        setTimeout(() => {
            copiedCommands.value.delete(key)
        }, 2000)
    } catch (err) {
        console.error('Failed to copy command:', err)
    }
}

const isCopied = (stepIndex, commandIndex) => {
    return copiedCommands.value.has(`${stepIndex}-${commandIndex}`)
}

const getCategoryIcon = (category) => {
    const icons = {
        Laravel: Code2,
        'Vue.js': Code2,
        'Next.js': Code2,
        Bootstrap: Lightbulb,
        React: Code2,
        JavaScript: Terminal,
        PHP: Code2,
    }
    return icons[category] || Book
}

const formatDate = (date) => {
    if (!date) return ''
    try {
        return new Date(date).toLocaleDateString('en-US', {
            month: 'short',
            day: '2-digit',
            year: 'numeric',
        })
    } catch (e) {
        return ''
    }
}

const openNote = (note) => {
    selectedNote.value = note
    if (typeof window !== 'undefined') {
        window.scrollTo({ top: 0, behavior: 'smooth' })
    }
}

const closeNote = () => {
    selectedNote.value = null
    if (typeof window !== 'undefined') {
        window.scrollTo({ top: 0, behavior: 'smooth' })
    }
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
    <FrontendLayout current-route="/note">
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta
                name="keywords"
                content="programming notes, tutorials, coding guides, developer notebook"
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

        <!-- LIST VIEW -->
        <section
            v-else-if="!selectedNote"
            class="relative mx-auto w-full max-w-7xl px-3 py-6 sm:px-6 sm:py-8 lg:px-10"
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
                    Chapter 09
                </span>
                <span class="hidden md:inline">Notebook / 2026</span>
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
                        Field notebook
                    </span>
                    <span>{{ String(notes.length).padStart(2, '0') }} entries</span>
                </div>

                <h1 class="mt-5 font-serif leading-[0.9] tracking-tight sm:mt-6">
                    <span
                        class="block text-[clamp(2.5rem,9vw,6.5rem)] font-normal text-foreground"
                    >
                        Note
                    </span>
                    <span
                        class="block text-[clamp(2.5rem,9vw,6.5rem)] font-normal italic text-foreground/80"
                    >
                        -book.
                    </span>
                </h1>

                <p
                    class="mt-5 max-w-2xl text-sm leading-relaxed text-muted-foreground sm:mt-6 sm:text-[15px] md:text-base"
                >
                    A working notebook of installation guides, configuration recipes, and quick
                    references — the things I keep wanting to look up.
                </p>
            </article>

            <!-- FILTER BAR -->
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
                            type="text"
                            placeholder="Search notes, tags, or content…"
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
                                v-for="category in categories"
                                :key="category"
                                class="filter-pill shrink-0"
                                :class="{ 'filter-pill-active': selectedFilter === category }"
                                @click="selectedFilter = category"
                            >
                                <span>{{ category }}</span>
                                <span class="filter-count">{{ countByCategory[category] || 0 }}</span>
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
                            {{ String(filteredNotes.length).padStart(2, '0') }} /
                            {{ String(notes.length).padStart(2, '0') }} entries
                        </span>
                        <span v-if="selectedFilter !== 'All'" class="hidden sm:inline">
                            filed under {{ selectedFilter }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- NOTES GRID -->
            <div class="mt-4 sm:mt-5">
                <div
                    v-if="filteredNotes.length === 0"
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
                        <template v-if="searchQuery || selectedFilter !== 'All'">
                            Try a different search term or filter.
                        </template>
                        <template v-else>The notebook is empty for now.</template>
                    </p>
                </div>

                <div
                    v-else
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3"
                >
                    <article
                        v-for="(note, i) in filteredNotes"
                        :key="note.id"
                        class="note-card reveal group relative cursor-pointer overflow-hidden rounded-2xl border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:p-6"
                        :style="{ '--d': 240 + i * 60 + 'ms' }"
                        tabindex="0"
                        role="button"
                        @click="openNote(note)"
                        @keydown.enter="openNote(note)"
                        @keydown.space.prevent="openNote(note)"
                    >
                        <!-- Index watermark -->
                        <span class="index-watermark" aria-hidden="true">
                            {{ String(i + 1).padStart(2, '0') }}
                        </span>

                        <div class="relative z-[1] flex h-full flex-col">
                            <div class="flex items-start justify-between gap-3">
                                <span
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-border/60 bg-muted/40 text-foreground transition-all duration-300 group-hover:border-foreground/40 group-hover:bg-foreground group-hover:text-background"
                                >
                                    <component
                                        :is="getCategoryIcon(note.category)"
                                        class="h-4 w-4"
                                    />
                                </span>
                                <span class="category-chip">{{ note.category }}</span>
                            </div>

                            <h3
                                class="mt-5 font-serif text-2xl leading-tight tracking-tight text-foreground sm:text-[26px]"
                            >
                                {{ note.title }}
                            </h3>

                            <p
                                class="mt-3 line-clamp-2 flex-1 text-sm leading-relaxed text-muted-foreground"
                            >
                                {{ note.description }}
                            </p>

                            <div
                                v-if="note.tags && note.tags.length"
                                class="mt-4 flex flex-wrap gap-1.5"
                            >
                                <span
                                    v-for="tag in note.tags.slice(0, 4)"
                                    :key="tag"
                                    class="tag-chip"
                                >
                                    {{ tag }}
                                </span>
                                <span
                                    v-if="note.tags.length > 4"
                                    class="tag-chip tag-chip-more"
                                >
                                    +{{ note.tags.length - 4 }}
                                </span>
                            </div>

                            <div
                                class="mt-5 flex items-center justify-between border-t border-border/50 pt-3 font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground/70"
                            >
                                <span>{{ formatDate(note.created_at) }}</span>
                                <span class="inline-flex items-center gap-1.5">
                                    Read entry
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
                style="--d: 800ms"
            >
                <span>© {{ new Date().getFullYear() }} · Notebook</span>
                <span>End of index →</span>
            </div>
        </section>

        <!-- DETAIL VIEW -->
        <section
            v-else
            class="relative mx-auto w-full max-w-4xl px-3 py-6 sm:px-6 sm:py-8 lg:px-10"
            :class="{ 'is-visible': isVisible }"
            :key="selectedNote.id"
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
                    Notebook entry
                </span>
                <span class="hidden md:inline">Notebook / 2026</span>
                <span class="tabular-nums">{{ dateString }}</span>
            </div>

            <!-- Breadcrumb -->
            <nav
                class="reveal mb-5 flex items-center gap-2 font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground sm:mb-6 sm:text-[11px]"
                style="--d: 60ms"
            >
                <button
                    @click="closeNote"
                    class="inline-flex items-center gap-1.5 transition-colors hover:text-foreground"
                >
                    <ArrowLeft class="h-3 w-3" />
                    <span>Notebook</span>
                </button>
                <span class="text-border">/</span>
                <span class="truncate text-foreground/80">{{ selectedNote.title }}</span>
            </nav>

            <!-- HERO -->
            <article
                class="reveal relative overflow-hidden rounded-[1.5rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-3xl sm:p-8 md:p-10"
                style="--d: 140ms"
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
                        {{ selectedNote.category }}
                    </span>
                    <span class="tabular-nums">{{ formatDate(selectedNote.created_at) }}</span>
                </div>

                <h1
                    class="mt-5 font-serif text-[clamp(2rem,6vw,4.25rem)] font-normal leading-[0.95] tracking-tight text-foreground sm:mt-6"
                >
                    {{ selectedNote.title }}
                </h1>

                <p
                    class="mt-5 max-w-3xl text-sm leading-relaxed text-muted-foreground sm:text-[15px] md:text-base"
                >
                    {{ selectedNote.description }}
                </p>

                <div
                    v-if="selectedNote.tags && selectedNote.tags.length"
                    class="mt-6 flex flex-wrap gap-1.5"
                >
                    <span v-for="tag in selectedNote.tags" :key="tag" class="tag-chip">
                        {{ tag }}
                    </span>
                </div>
            </article>

            <!-- CONTENT -->
            <div v-if="selectedNote.content" class="mt-4 space-y-4 sm:mt-5 sm:space-y-5">
                <!-- Overview -->
                <article
                    v-if="selectedNote.content.overview"
                    class="reveal overflow-hidden rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-3xl sm:p-8"
                    style="--d: 220ms"
                >
                    <span class="section-eyebrow">— Overview</span>
                    <p
                        class="mt-4 text-sm leading-[1.8] text-muted-foreground sm:text-[15px] md:text-base"
                    >
                        {{ selectedNote.content.overview }}
                    </p>
                </article>

                <!-- Requirements -->
                <article
                    v-if="selectedNote.content.requirements && selectedNote.content.requirements.length"
                    class="reveal overflow-hidden rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-3xl sm:p-8"
                    style="--d: 280ms"
                >
                    <span class="section-eyebrow">— Requirements</span>
                    <ul class="mt-4 space-y-2.5">
                        <li
                            v-for="req in selectedNote.content.requirements"
                            :key="req"
                            class="flex items-start gap-3 text-sm leading-relaxed text-muted-foreground sm:text-[15px]"
                        >
                            <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-foreground"></span>
                            {{ req }}
                        </li>
                    </ul>
                </article>

                <!-- Steps -->
                <article
                    v-if="selectedNote.content.steps && selectedNote.content.steps.length"
                    class="reveal overflow-hidden rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-3xl sm:p-8"
                    style="--d: 340ms"
                >
                    <span class="section-eyebrow">— Steps</span>

                    <div class="mt-4">
                        <article
                            v-for="(step, stepIndex) in selectedNote.content.steps"
                            :key="stepIndex"
                            class="step-row"
                            :class="{ 'border-t': stepIndex > 0 }"
                        >
                            <div class="step-row-head">
                                <div class="step-row-number">
                                    {{ String(stepIndex + 1).padStart(2, '0') }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="step-row-title">{{ step.title }}</h3>
                                    <p
                                        v-if="step.description"
                                        class="mt-1.5 text-sm leading-relaxed text-muted-foreground sm:text-[15px]"
                                    >
                                        {{ step.description }}
                                    </p>
                                </div>
                            </div>

                            <div
                                v-if="step.commands && step.commands.length"
                                class="mt-4 space-y-2 sm:ml-12"
                            >
                                <div
                                    v-for="(command, commandIndex) in step.commands"
                                    :key="commandIndex"
                                    class="code-block group/code"
                                >
                                    <div class="code-prompt" aria-hidden="true">$</div>
                                    <code class="code-content">{{ command }}</code>
                                    <button
                                        @click.stop="copyCommand(command, stepIndex, commandIndex)"
                                        class="code-copy"
                                        :aria-label="
                                            isCopied(stepIndex, commandIndex)
                                                ? 'Copied'
                                                : 'Copy command'
                                        "
                                    >
                                        <Check
                                            v-if="isCopied(stepIndex, commandIndex)"
                                            class="h-3.5 w-3.5 text-emerald-500"
                                        />
                                        <Copy v-else class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>
                </article>
            </div>

            <!-- Bottom nav -->
            <nav
                class="reveal mt-8 border-t border-border/50 pt-6 sm:mt-10 sm:pt-8"
                style="--d: 500ms"
            >
                <button
                    @click="closeNote"
                    class="inline-flex items-center gap-2 rounded-full border border-border/60 bg-background/40 px-5 py-3 font-mono text-[10px] uppercase tracking-[0.22em] text-foreground backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-foreground/60"
                >
                    <ArrowLeft class="h-3.5 w-3.5 opacity-60" />
                    All entries
                </button>
            </nav>

            <!-- Footer -->
            <div
                class="reveal mt-10 flex flex-col items-start justify-between gap-1.5 font-mono text-[9px] uppercase tracking-[0.2em] text-muted-foreground/60 sm:mt-14 sm:flex-row sm:items-center sm:gap-2 sm:text-[10px] sm:tracking-[0.22em] md:text-xs"
                style="--d: 600ms"
            >
                <span>© {{ new Date().getFullYear() }} · Entry · {{ selectedNote.category }}</span>
                <span>End of entry →</span>
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

/* Section eyebrow */
.section-eyebrow {
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    color: var(--color-muted-foreground);
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

/* Category chip */
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

/* Tag chips */
.tag-chip {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.6rem;
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
.tag-chip-more {
    color: color-mix(in oklab, var(--color-muted-foreground) 70%, transparent);
}

/* Note cards */
.note-card {
    transition:
        transform 0.4s cubic-bezier(0.22, 1, 0.36, 1),
        border-color 0.4s ease,
        box-shadow 0.4s ease;
    min-height: 16rem;
    outline: none;
}
@media (hover: hover) and (pointer: fine) {
    .note-card:hover {
        transform: translateY(-4px);
        border-color: color-mix(in oklab, var(--color-foreground) 25%, var(--color-border));
        box-shadow: 0 22px 45px -28px rgba(0, 0, 0, 0.4);
    }
}
.note-card:focus-visible {
    border-color: color-mix(in oklab, var(--color-foreground) 40%, var(--color-border));
    box-shadow: 0 0 0 2px color-mix(in oklab, var(--color-foreground) 20%, transparent);
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
    .note-card:hover .index-watermark {
        color: color-mix(in oklab, var(--color-foreground) 10%, transparent);
        transform: translateX(-0.25rem);
    }
}

/* Step rows in detail view */
.step-row {
    padding: 1.25rem 0;
    border-color: var(--color-border);
}
.step-row:first-child {
    padding-top: 0;
}
.step-row-head {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}
.step-row-number {
    flex-shrink: 0;
    width: 2.5rem;
    height: 2.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    background: var(--color-foreground);
    color: var(--color-background);
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.05em;
}
.step-row-title {
    font-family: 'Instrument Serif', Georgia, serif;
    font-size: 1.5rem;
    line-height: 1.15;
    letter-spacing: -0.01em;
    color: var(--color-foreground);
}
@media (min-width: 640px) {
    .step-row-title {
        font-size: 1.75rem;
    }
}

/* Code blocks */
.code-block {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.85rem 3rem 0.85rem 1rem;
    background: color-mix(in oklab, var(--color-muted) 40%, transparent);
    border: 1px solid var(--color-border);
    border-radius: 0.75rem;
    overflow-x: auto;
    transition: border-color 0.2s ease;
}
.code-block:hover {
    border-color: color-mix(in oklab, var(--color-foreground) 30%, var(--color-border));
}
.code-prompt {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.8rem;
    color: color-mix(in oklab, var(--color-muted-foreground) 80%, transparent);
    user-select: none;
    flex-shrink: 0;
}
.code-content {
    font-family: 'JetBrains Mono', ui-monospace, 'SFMono-Regular', Menlo, monospace;
    font-size: 0.78rem;
    color: var(--color-foreground);
    white-space: pre;
    line-height: 1.6;
}
.code-copy {
    position: absolute;
    right: 0.5rem;
    top: 0.5rem;
    width: 2rem;
    height: 2rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
    border: 1px solid transparent;
    background: transparent;
    color: var(--color-muted-foreground);
    cursor: pointer;
    opacity: 0.6;
    transition:
        opacity 0.2s ease,
        background-color 0.2s ease,
        border-color 0.2s ease,
        color 0.2s ease;
}
.code-block:hover .code-copy {
    opacity: 1;
}
.code-copy:hover {
    background: color-mix(in oklab, var(--color-foreground) 8%, transparent);
    border-color: var(--color-border);
    color: var(--color-foreground);
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
