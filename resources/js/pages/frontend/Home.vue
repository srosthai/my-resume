<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Skeleton } from '@/components/ui/skeleton'
import {
    ArrowUpRight,
    Github,
    Linkedin,
    Mail,
    Book,
    MapPin,
    Rss,
} from 'lucide-vue-next'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import LanyardCard from '@/components/LanyardCard.vue'

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    techStacks: {
        type: Array,
        default: () => [],
    },
    stats: {
        type: Object,
        default: () => ({ projects: 0, techStacks: 0, experience: 0, notes: 0 }),
    },
    title: {
        type: String,
        default: 'Home',
    },
    description: {
        type: String,
        default: 'Welcome to my portfolio',
    },
})

const isLoading = ref(true)
const isVisible = ref(false)

const now = ref(new Date())
let clockTimer = null

const timeInPhnomPenh = computed(() => {
    try {
        return new Intl.DateTimeFormat('en-GB', {
            timeZone: 'Asia/Phnom_Penh',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        }).format(now.value)
    } catch (e) {
        return '--:--:--'
    }
})

const dateInPhnomPenh = computed(() => {
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

const firstName = computed(() => {
    const parts = (props.users?.name || 'Developer').trim().split(/\s+/)
    return parts[0] || 'Developer'
})

const lastName = computed(() => {
    const parts = (props.users?.name || '').trim().split(/\s+/)
    return parts.slice(1).join(' ')
})

const doubledStacks = computed(() => {
    const arr = props.techStacks || []
    return arr.length ? [...arr, ...arr] : []
})

const pointer = ref({ x: 50, y: 50 })
const handlePointer = (e) => {
    const x = (e.clientX / window.innerWidth) * 100
    const y = (e.clientY / window.innerHeight) * 100
    pointer.value = { x, y }
}

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
        requestAnimationFrame(() => {
            isVisible.value = true
        })
    }, 450)

    clockTimer = setInterval(() => {
        now.value = new Date()
    }, 1000)

    window.addEventListener('pointermove', handlePointer, { passive: true })
})

onBeforeUnmount(() => {
    if (clockTimer) clearInterval(clockTimer)
    window.removeEventListener('pointermove', handlePointer)
})
</script>

<template>
    <FrontendLayout currentRoute="/">
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta
                name="keywords"
                content="software developer, portfolio, Vue.js, Laravel, full stack developer, SROS THAI, Cambodia"
            />
            <meta name="author" :content="users.name || 'SROS THAI'" />
            <meta property="og:title" :content="title" />
            <meta property="og:description" :content="description" />
            <meta property="og:type" content="website" />
            <link
                href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500;600&display=swap"
                rel="stylesheet"
            />
        </Head>

        <!-- Skeleton state -->
        <section
            v-if="isLoading"
            class="mx-auto flex min-h-[calc(100vh-6rem)] w-full max-w-7xl items-center px-3 py-6 sm:px-6 sm:py-8 lg:px-10"
        >
            <div class="grid w-full grid-cols-2 gap-3 sm:gap-4 md:grid-cols-12 md:gap-5">
                <Skeleton class="col-span-2 h-[22rem] rounded-3xl sm:h-[24rem] md:col-span-8 md:h-[28rem]" />
                <Skeleton class="col-span-2 hidden h-[28rem] rounded-3xl md:col-span-4 md:block" />
                <Skeleton class="col-span-1 h-28 rounded-3xl sm:h-32 md:col-span-4" />
                <Skeleton class="col-span-1 h-28 rounded-3xl sm:h-32 md:col-span-4" />
                <Skeleton class="col-span-2 h-28 rounded-3xl sm:h-32 md:col-span-4" />
                <Skeleton class="col-span-2 h-20 rounded-3xl sm:h-24 md:col-span-12" />
            </div>
        </section>

        <section
            v-else
            id="home"
            class="relative mx-auto w-full max-w-7xl px-3 py-6 sm:px-6 sm:py-8 lg:px-10"
            :class="{ 'is-visible': isVisible }"
        >
            <!-- Ambient mouse-reactive glow -->
            <div
                class="pointer-events-none fixed inset-0 -z-10 overflow-hidden"
                aria-hidden="true"
            >
                <div
                    class="ambient-blob"
                    :style="{
                        left: pointer.x + '%',
                        top: pointer.y + '%',
                    }"
                ></div>
                <div class="grain-overlay"></div>
            </div>

            <!-- Top meta strip -->
            <div
                class="reveal mb-4 flex flex-wrap items-center justify-between gap-x-4 gap-y-2 font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground/70 sm:mb-5 sm:text-[10px] md:text-xs"
                style="--d: 0ms"
            >
                <span class="flex items-center gap-2">
                    <span class="relative flex h-1.5 w-1.5">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex h-1.5 w-1.5 rounded-full bg-emerald-500"
                        ></span>
                    </span>
                    Available for work
                </span>
                <span class="hidden md:inline">Portfolio / 2026</span>
                <span class="tabular-nums">{{ dateInPhnomPenh }}</span>
            </div>

            <!-- Main bento grid -->
            <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-12 md:gap-5">
                <!-- Hero card (name + meta) -->
                <article
                    class="bento-card reveal relative col-span-2 overflow-hidden rounded-[1.5rem] border border-border/60 bg-card/60 p-5 shadow-[0_1px_0_rgba(255,255,255,0.04)_inset,0_20px_60px_-30px_rgba(0,0,0,0.35)] backdrop-blur-xl sm:rounded-3xl sm:p-8 md:col-span-8 md:p-10"
                    style="--d: 80ms"
                >
                    <!-- Diagonal stripe decoration -->
                    <div
                        class="pointer-events-none absolute -right-24 -top-24 h-64 w-64 rotate-45 bg-gradient-to-br from-foreground/[0.04] to-transparent"
                        aria-hidden="true"
                    ></div>

                    <!-- Floating mobile avatar chip (hidden on md+) -->
                    <div
                        class="absolute right-4 top-4 z-10 md:hidden"
                        aria-hidden="true"
                    >
                        <div
                            class="relative h-20 w-20 overflow-hidden rounded-[1.25rem] border border-border/60 bg-muted shadow-xl ring-1 ring-foreground/5 xs:h-24 xs:w-24 sm:h-28 sm:w-28"
                        >
                            <img
                                v-if="users.image"
                                :src="
                                    users.image.startsWith('http')
                                        ? users.image
                                        : `/${users.image}`
                                "
                                :alt="users.name"
                                class="h-full w-full object-cover object-[center_25%]"
                            />
                        </div>
                        <span
                            class="absolute -bottom-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full border-2 border-card bg-emerald-500 sm:h-4.5 sm:w-4.5"
                        >
                            <span class="h-1.5 w-1.5 animate-ping rounded-full bg-white/80"></span>
                        </span>
                    </div>

                    <div class="flex items-center justify-between pr-24 xs:pr-28 sm:pr-32 md:pr-0">
                        <span
                            class="inline-flex items-center gap-2 font-mono text-[9px] uppercase tracking-[0.25em] text-muted-foreground sm:text-[10px] md:text-xs"
                        >
                            <span class="h-px w-5 bg-foreground/40 sm:w-6"></span>
                            Hello, world
                        </span>
                    </div>

                    <h1 class="mt-5 font-serif leading-[0.9] tracking-tight sm:mt-6">
                        <span
                            class="block text-[clamp(2.75rem,11vw,7rem)] font-normal text-foreground"
                        >
                            {{ firstName }}
                        </span>
                        <span
                            v-if="lastName"
                            class="block text-[clamp(2.75rem,11vw,7rem)] font-normal italic text-foreground/80"
                        >
                            {{ lastName }}.
                        </span>
                    </h1>

                    <div class="mt-5 flex flex-wrap items-baseline gap-x-3 gap-y-1 sm:mt-6">
                        <span
                            class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted-foreground sm:text-xs"
                        >
                            — Currently
                        </span>
                        <span class="text-sm text-foreground sm:text-base md:text-lg">
                            {{ users.position }}
                        </span>
                    </div>

                    <p
                        class="mt-4 max-w-xl text-sm leading-relaxed text-muted-foreground sm:mt-5 sm:text-[15px] md:text-base"
                    >
                        {{ users.description }}
                    </p>

                    <!-- CTA row -->
                    <div class="mt-6 flex flex-wrap items-center gap-2.5 sm:mt-8 sm:gap-3">
                        <Link
                            href="/resume"
                            class="cta-primary group inline-flex items-center gap-2 rounded-full bg-foreground px-5 py-2.5 text-xs font-medium text-background transition-all duration-300 hover:-translate-y-0.5 sm:gap-3 sm:px-6 sm:py-3 sm:text-sm"
                        >
                            <span>View résumé</span>
                            <span
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-background/20 transition-transform duration-300 group-hover:rotate-45 sm:h-7 sm:w-7"
                            >
                                <ArrowUpRight class="h-3 w-3 sm:h-3.5 sm:w-3.5" />
                            </span>
                        </Link>
                        <Link
                            href="/portfolio"
                            class="group inline-flex items-center gap-2 rounded-full border border-border/70 bg-background/40 px-4 py-2.5 text-xs font-medium text-foreground backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-foreground/60 sm:px-5 sm:py-3 sm:text-sm"
                        >
                            <span>See projects</span>
                            <ArrowUpRight
                                class="h-3 w-3 opacity-60 transition-all duration-300 group-hover:rotate-45 group-hover:opacity-100 sm:h-3.5 sm:w-3.5"
                            />
                        </Link>
                        <Link
                            href="/feeds"
                            class="group inline-flex items-center gap-2 rounded-full border border-border/70 bg-background/40 px-4 py-2.5 text-xs font-medium text-foreground backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-foreground/60 sm:px-5 sm:py-3 sm:text-sm"
                        >
                            <Rss class="h-3 w-3 opacity-70 sm:h-3.5 sm:w-3.5" />
                            <span>My feeds</span>
                        </Link>
                    </div>

                    <!-- Signature line -->
                    <div
                        class="mt-8 flex items-center gap-3 font-mono text-[9px] uppercase tracking-[0.25em] text-muted-foreground/60 sm:mt-10 sm:gap-4 sm:text-[10px] md:text-xs"
                    >
                        <span class="h-px flex-1 bg-border/60"></span>
                        <span>Crafted in Cambodia</span>
                        <span class="h-px flex-1 bg-border/60"></span>
                    </div>
                </article>

                <!-- Lanyard card (hidden on mobile; chip lives inside hero card) -->
                <article
                    class="bento-card reveal relative col-span-2 hidden overflow-hidden rounded-3xl border border-border/60 bg-card/60 p-5 backdrop-blur-xl md:col-span-4 md:block"
                    style="--d: 160ms"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="font-mono text-[10px] uppercase tracking-[0.25em] text-muted-foreground"
                        >
                            / credentials
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-[0.2em] text-muted-foreground/70"
                        >
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            drag me
                        </span>
                    </div>

                    <LanyardCard
                        :name="users.name"
                        :position="users.position"
                        :image="users.image"
                        badge="DEV"
                        label="ID · 2026"
                    />
                </article>

                <!-- Clock / location card -->
                <article
                    class="bento-card reveal col-span-1 overflow-hidden rounded-2xl border border-border/60 bg-card/60 p-4 backdrop-blur-xl sm:rounded-3xl sm:p-6 md:col-span-4"
                    style="--d: 240ms"
                >
                    <div class="flex items-center justify-between gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground sm:gap-2 sm:text-[10px]"
                        >
                            <MapPin class="h-3 w-3 shrink-0" />
                            <span class="truncate">Phnom Penh</span>
                        </span>
                        <span
                            class="font-mono text-[9px] uppercase tracking-[0.2em] text-muted-foreground/60 sm:text-[10px]"
                        >
                            UTC+7
                        </span>
                    </div>
                    <div
                        class="mt-4 font-mono text-[clamp(1.75rem,8vw,3.5rem)] font-medium tabular-nums leading-none tracking-tight text-foreground sm:mt-5"
                    >
                        {{ timeInPhnomPenh }}
                    </div>
                    <div
                        class="mt-2 truncate font-mono text-[9px] uppercase tracking-[0.18em] text-muted-foreground sm:mt-3 sm:text-[11px] sm:tracking-[0.2em]"
                    >
                        Local · {{ dateInPhnomPenh }}
                    </div>
                </article>

                <!-- Stats card -->
                <article
                    class="bento-card reveal col-span-1 overflow-hidden rounded-2xl border border-border/60 bg-card/60 p-4 backdrop-blur-xl sm:rounded-3xl sm:p-6 md:col-span-4"
                    style="--d: 320ms"
                >
                    <span
                        class="font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground sm:text-[10px] sm:tracking-[0.25em]"
                    >
                        / by the numbers
                    </span>
                    <div class="mt-4 grid grid-cols-2 gap-3 sm:mt-5 sm:gap-4">
                        <div class="stat-item">
                            <div class="stat-num">{{ stats.projects }}</div>
                            <div class="stat-label">Projects</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-num">{{ stats.techStacks }}</div>
                            <div class="stat-label">Stacks</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-num">{{ stats.experience }}</div>
                            <div class="stat-label">Exps</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-num">{{ stats.notes }}</div>
                            <div class="stat-label">Notes</div>
                        </div>
                    </div>
                </article>

                <!-- Social / links card -->
                <article
                    class="bento-card reveal col-span-2 overflow-hidden rounded-2xl border border-border/60 bg-card/60 p-4 backdrop-blur-xl sm:rounded-3xl sm:p-6 md:col-span-4"
                    style="--d: 400ms"
                >
                    <span
                        class="font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground sm:text-[10px] sm:tracking-[0.25em]"
                    >
                        / elsewhere
                    </span>
                    <ul class="mt-3 divide-y divide-border/50 sm:mt-4">
                        <li>
                            <a
                                href="https://github.com/srosthai"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="link-row group"
                            >
                                <span class="flex items-center gap-3">
                                    <Github class="h-4 w-4 opacity-70" />
                                    <span>GitHub</span>
                                </span>
                                <ArrowUpRight
                                    class="h-4 w-4 opacity-40 transition-all duration-200 group-hover:-translate-y-0.5 group-hover:translate-x-0.5 group-hover:opacity-100"
                                />
                            </a>
                        </li>
                        <li>
                            <a
                                href="https://www.linkedin.com/in/sros-thai-b491b42ab/"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="link-row group"
                            >
                                <span class="flex items-center gap-3">
                                    <Linkedin class="h-4 w-4 opacity-70" />
                                    <span>LinkedIn</span>
                                </span>
                                <ArrowUpRight
                                    class="h-4 w-4 opacity-40 transition-all duration-200 group-hover:-translate-y-0.5 group-hover:translate-x-0.5 group-hover:opacity-100"
                                />
                            </a>
                        </li>
                        <li>
                            <Link href="/contact" class="link-row group">
                                <span class="flex items-center gap-3">
                                    <Mail class="h-4 w-4 opacity-70" />
                                    <span>Contact</span>
                                </span>
                                <ArrowUpRight
                                    class="h-4 w-4 opacity-40 transition-all duration-200 group-hover:-translate-y-0.5 group-hover:translate-x-0.5 group-hover:opacity-100"
                                />
                            </Link>
                        </li>
                        <li>
                            <Link href="/note" class="link-row group">
                                <span class="flex items-center gap-3">
                                    <Book class="h-4 w-4 opacity-70" />
                                    <span>Notes</span>
                                </span>
                                <ArrowUpRight
                                    class="h-4 w-4 opacity-40 transition-all duration-200 group-hover:-translate-y-0.5 group-hover:translate-x-0.5 group-hover:opacity-100"
                                />
                            </Link>
                        </li>
                    </ul>
                </article>

                <!-- Tech stack marquee -->
                <article
                    v-if="techStacks.length"
                    class="bento-card reveal col-span-2 overflow-hidden rounded-2xl border border-border/60 bg-card/60 backdrop-blur-xl sm:rounded-3xl md:col-span-12"
                    style="--d: 480ms"
                >
                    <div
                        class="flex items-center justify-between border-b border-border/50 px-4 py-2.5 font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground sm:px-6 sm:py-3 sm:text-[10px] sm:tracking-[0.25em]"
                    >
                        <span>/ daily drivers</span>
                        <span>{{ stats.techStacks }} tools</span>
                    </div>
                    <div class="marquee-viewport group relative py-4 sm:py-6">
                        <div class="marquee-track">
                            <div
                                v-for="(tech, i) in doubledStacks"
                                :key="`${tech.id}-${i}`"
                                class="marquee-item"
                                :title="tech.name"
                            >
                                <img
                                    v-if="tech.logo"
                                    :src="tech.logo"
                                    :alt="tech.name"
                                    class="marquee-logo"
                                    loading="lazy"
                                />
                                <span
                                    v-else
                                    class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-muted font-mono text-[10px] font-semibold text-muted-foreground sm:h-7 sm:w-7 sm:text-xs"
                                >
                                    {{ tech.name?.charAt(0) }}
                                </span>
                                <span class="marquee-name">{{ tech.name }}</span>
                            </div>
                        </div>
                        <div class="fade-edge fade-left"></div>
                        <div class="fade-edge fade-right"></div>
                    </div>
                </article>
            </div>

            <!-- Footer tag -->
            <div
                class="reveal mt-5 flex flex-col items-start justify-between gap-1.5 font-mono text-[9px] uppercase tracking-[0.2em] text-muted-foreground/60 sm:mt-6 sm:flex-row sm:items-center sm:gap-2 sm:text-[10px] sm:tracking-[0.22em] md:text-xs"
                style="--d: 560ms"
            >
                <span>© {{ new Date().getFullYear() }} {{ users.name }}</span>
                <span>Scroll, click, explore →</span>
            </div>
        </section>
    </FrontendLayout>
</template>

<style scoped>
:global(.font-serif) {
    font-family: 'Instrument Serif', 'Iowan Old Style', 'Apple Garamond', Georgia, serif;
}

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

/* Serif font (scoped-safe) */
h1,
.font-serif {
    font-family: 'Instrument Serif', 'Iowan Old Style', 'Apple Garamond', Georgia, serif;
    font-feature-settings: 'ss01', 'liga';
}

/* Mono font */
.font-mono {
    font-family: 'JetBrains Mono', ui-monospace, 'SFMono-Regular', Menlo, monospace;
}

/* Ambient background blob */
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

/* Bento cards — lift on hover for desktop only */
.bento-card {
    transition:
        transform 0.35s cubic-bezier(0.22, 1, 0.36, 1),
        border-color 0.35s ease,
        box-shadow 0.35s ease;
}
@media (hover: hover) and (pointer: fine) {
    .bento-card:hover {
        transform: translateY(-3px);
        border-color: color-mix(in oklab, var(--color-foreground) 20%, var(--color-border));
    }
}

/* Stat grid */
.stat-item {
    padding: 0.4rem 0.2rem;
    border-top: 1px solid var(--color-border);
}
@media (min-width: 640px) {
    .stat-item {
        padding: 0.5rem 0.25rem;
    }
}
.stat-num {
    font-family: 'Instrument Serif', Georgia, serif;
    font-size: clamp(1.6rem, 6vw, 2.75rem);
    line-height: 1;
    letter-spacing: -0.02em;
    color: var(--color-foreground);
    font-feature-settings: 'tnum';
}
.stat-label {
    margin-top: 0.35rem;
    font-family: 'JetBrains Mono', monospace;
    font-size: 9px;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--color-muted-foreground);
}
@media (min-width: 640px) {
    .stat-label {
        margin-top: 0.5rem;
        font-size: 10px;
        letter-spacing: 0.22em;
    }
}

/* Link rows */
.link-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 0.25rem;
    font-size: 0.9rem;
    color: var(--color-foreground);
    transition: color 0.2s ease;
}
.link-row:hover {
    color: var(--color-foreground);
}

/* Primary CTA shadow pulse */
.cta-primary {
    box-shadow: 0 10px 30px -10px color-mix(in oklab, var(--color-foreground) 40%, transparent);
}
.cta-primary:hover {
    box-shadow: 0 14px 40px -12px color-mix(in oklab, var(--color-foreground) 55%, transparent);
}

/* Marquee */
.marquee-viewport {
    overflow: hidden;
    position: relative;
    padding-left: 1rem;
    padding-right: 1rem;
}
@media (min-width: 640px) {
    .marquee-viewport {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
}
.marquee-track {
    display: flex;
    gap: 1rem;
    width: max-content;
    animation: marquee 38s linear infinite;
}
@media (min-width: 640px) {
    .marquee-track {
        gap: 2rem;
    }
}
.marquee-viewport.group:hover .marquee-track,
.marquee-viewport:hover .marquee-track {
    animation-play-state: paused;
}
.marquee-item {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.3rem 0.75rem 0.3rem 0.4rem;
    border-radius: 9999px;
    border: 1px solid var(--color-border);
    background: color-mix(in oklab, var(--color-card) 60%, transparent);
    backdrop-filter: blur(8px);
    white-space: nowrap;
    transition:
        transform 0.25s ease,
        border-color 0.25s ease;
}
@media (min-width: 640px) {
    .marquee-item {
        gap: 0.6rem;
        padding: 0.35rem 0.9rem 0.35rem 0.5rem;
    }
}
.marquee-item:hover {
    transform: translateY(-2px);
    border-color: color-mix(in oklab, var(--color-foreground) 40%, var(--color-border));
}
.marquee-logo {
    width: 1.1rem;
    height: 1.1rem;
    object-fit: contain;
}
@media (min-width: 640px) {
    .marquee-logo {
        width: 1.4rem;
        height: 1.4rem;
    }
}
.marquee-name {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.7rem;
    letter-spacing: 0.04em;
    color: var(--color-foreground);
}
@media (min-width: 640px) {
    .marquee-name {
        font-size: 0.75rem;
    }
}
@keyframes marquee {
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(-50%);
    }
}
.fade-edge {
    pointer-events: none;
    position: absolute;
    top: 0;
    bottom: 0;
    width: 4rem;
    z-index: 2;
}
.fade-left {
    left: 0;
    background: linear-gradient(
        to right,
        var(--color-card),
        transparent
    );
}
.fade-right {
    right: 0;
    background: linear-gradient(
        to left,
        var(--color-card),
        transparent
    );
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .reveal,
    .is-visible .reveal {
        opacity: 1 !important;
        transform: none !important;
        animation: none !important;
    }
    .marquee-track {
        animation: none;
    }
    .ambient-blob {
        transition: none;
    }
}
</style>
