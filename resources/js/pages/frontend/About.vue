<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Skeleton } from '@/components/ui/skeleton'
import {
    MapPin,
    Briefcase,
    Target,
    GraduationCap,
    ArrowUpRight,
    Code2,
} from 'lucide-vue-next'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

const props = defineProps({
    user: { type: Object, default: () => ({}) },
    aboutMe: { type: Object, default: () => ({}) },
    workExperience: { type: Array, default: () => [] },
    education: { type: Array, default: () => [] },
    techStacks: { type: Array, default: () => [] },
    title: { type: String, default: 'About' },
    description: { type: String, default: '' },
})

const isLoading = ref(true)
const isVisible = ref(false)

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

const firstName = computed(() => {
    const parts = (props.user?.name || props.aboutMe?.title || 'About').trim().split(/\s+/)
    return parts[0] || 'About'
})
const lastName = computed(() => {
    const parts = (props.user?.name || props.aboutMe?.title || '').trim().split(/\s+/)
    return parts.slice(1).join(' ')
})

const imageSrc = computed(() => {
    const img = props.user?.image
    if (!img) return ''
    return img.startsWith('http') ? img : `/${img}`
})

// Group tech stacks by type
const groupedTechStacks = computed(() => {
    const groups = {}
    const order = []
    ;(props.techStacks || []).forEach((tech) => {
        const type = tech.type || 'Other'
        if (!groups[type]) {
            groups[type] = []
            order.push(type)
        }
        groups[type].push(tech)
    })
    return order.map((type) => ({ type, items: groups[type] }))
})

const stats = computed(() => [
    {
        icon: MapPin,
        label: 'Location',
        value: props.aboutMe?.location || 'Phnom Penh, Cambodia',
    },
    {
        icon: Briefcase,
        label: 'Experience',
        value: props.aboutMe?.year_experience || '5+ Years',
    },
    {
        icon: Target,
        label: 'Focus',
        value: props.aboutMe?.fucus_on || 'Web Development',
    },
])

const formatRange = (from, to) => {
    const f = from || ''
    const t = to || 'Present'
    if (!f && !t) return ''
    return `${f}${t ? ' — ' + t : ''}`
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
    <FrontendLayout current-route="/about">
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta
                name="keywords"
                content="about, software developer, SROS THAI, full stack developer, Cambodia, experience, education, tech stack"
            />
            <meta property="og:title" :content="title" />
            <meta property="og:description" :content="description" />
            <meta property="og:type" content="profile" />
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
                <Skeleton class="col-span-2 h-80 rounded-3xl md:col-span-8" />
                <Skeleton class="col-span-2 h-80 rounded-3xl md:col-span-4" />
                <Skeleton class="col-span-1 h-28 rounded-3xl md:col-span-4" />
                <Skeleton class="col-span-1 h-28 rounded-3xl md:col-span-4" />
                <Skeleton class="col-span-2 h-28 rounded-3xl md:col-span-4" />
                <Skeleton class="col-span-2 h-[24rem] rounded-3xl md:col-span-12" />
                <Skeleton class="col-span-2 h-72 rounded-3xl md:col-span-12" />
            </div>
        </section>

        <section
            v-else
            class="relative mx-auto w-full max-w-7xl px-3 py-6 sm:px-6 sm:py-8 lg:px-10"
            :class="{ 'is-visible': isVisible }"
        >
            <!-- Ambient glow + grain -->
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
                    Chapter 01
                </span>
                <span class="hidden md:inline">About / 2026</span>
                <span class="tabular-nums">{{ dateString }}</span>
            </div>

            <!-- HERO BENTO -->
            <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-12 md:gap-5">
                <!-- Big intro card -->
                <article
                    class="bento-card reveal relative col-span-2 overflow-hidden rounded-[1.5rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-3xl sm:p-8 md:col-span-8 md:p-10"
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
                            Profile
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <Code2 class="h-3 w-3" />
                            Software Dev
                        </span>
                    </div>

                    <h1 class="mt-5 font-serif leading-[0.9] tracking-tight sm:mt-6">
                        <span
                            class="block text-[clamp(2.5rem,10vw,6.5rem)] font-normal text-foreground"
                        >
                            About.
                        </span>
                    </h1>

                    <div class="mt-6 grid gap-4 sm:mt-8 md:grid-cols-[auto_1fr] md:gap-8">
                        <div
                            class="font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground sm:text-xs"
                        >
                            — Currently<br />
                            <span class="mt-1 block font-sans text-sm normal-case tracking-normal text-foreground sm:text-base">
                                {{ user?.position || 'Full Stack Developer' }}
                            </span>
                        </div>
                        <p
                            class="max-w-2xl text-sm leading-relaxed text-muted-foreground sm:text-[15px] md:text-base"
                        >
                            {{ aboutMe?.description || 'Passionate about building crafted, maintainable, and meaningful software.' }}
                        </p>
                    </div>

                    <div class="mt-8 flex flex-wrap items-center gap-2.5 sm:gap-3">
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
                            href="/contact"
                            class="group inline-flex items-center gap-2 rounded-full border border-border/70 bg-background/40 px-4 py-2.5 text-xs font-medium text-foreground backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-foreground/60 sm:px-5 sm:py-3 sm:text-sm"
                        >
                            <span>Get in touch</span>
                            <ArrowUpRight
                                class="h-3 w-3 opacity-60 transition-all duration-300 group-hover:rotate-45 group-hover:opacity-100 sm:h-3.5 sm:w-3.5"
                            />
                        </Link>
                    </div>
                </article>

                <!-- Portrait card -->
                <article
                    class="bento-card reveal relative col-span-2 hidden overflow-hidden rounded-3xl border border-border/60 bg-card/60 p-5 backdrop-blur-xl md:col-span-4 md:block"
                    style="--d: 160ms"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="font-mono text-[10px] uppercase tracking-[0.25em] text-muted-foreground"
                        >
                            / id card
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-[0.2em] text-muted-foreground/70"
                        >
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            verified
                        </span>
                    </div>

                    <div class="relative mx-auto mt-5 overflow-hidden rounded-2xl border border-border/50 aspect-[3/4]">
                        <img
                            v-if="imageSrc"
                            :src="imageSrc"
                            :alt="user?.name"
                            class="h-full w-full object-cover object-[center_25%]"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center bg-muted font-serif text-6xl italic text-muted-foreground"
                        >
                            {{ firstName.charAt(0) }}
                        </div>

                        <!-- corners -->
                        <div class="corner corner-tl"></div>
                        <div class="corner corner-tr"></div>
                        <div class="corner corner-bl"></div>
                        <div class="corner corner-br"></div>

                        <div
                            class="absolute inset-x-3 bottom-3 flex items-center justify-between rounded-xl border border-white/10 bg-black/40 px-3 py-2 font-mono text-[10px] uppercase tracking-[0.2em] text-white/90 backdrop-blur-md"
                        >
                            <span>{{ firstName }} {{ lastName }}</span>
                            <span>'26</span>
                        </div>
                    </div>
                </article>

                <!-- Stats row -->
                <article
                    v-for="(stat, i) in stats"
                    :key="stat.label"
                    class="bento-card reveal col-span-1 overflow-hidden rounded-2xl border border-border/60 bg-card/60 p-4 backdrop-blur-xl sm:rounded-3xl sm:p-6 md:col-span-4"
                    :class="{ 'col-span-2': i === 2 }"
                    :style="{ '--d': 240 + i * 80 + 'ms' }"
                >
                    <div class="flex items-center gap-2">
                        <component
                            :is="stat.icon"
                            class="h-3 w-3 text-muted-foreground/80"
                        />
                        <span
                            class="font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground sm:text-[10px] sm:tracking-[0.25em]"
                        >
                            {{ stat.label }}
                        </span>
                    </div>
                    <div
                        class="mt-3 font-serif text-[clamp(1.25rem,4vw,1.75rem)] leading-tight tracking-tight text-foreground sm:mt-4"
                    >
                        {{ stat.value }}
                    </div>
                </article>
            </div>

            <!-- SECTION 02 — EXPERIENCE -->
            <section class="mt-10 sm:mt-14">
                <div class="reveal mb-6 flex items-end justify-between gap-4 border-b border-border/50 pb-4 sm:mb-8" style="--d: 0ms">
                    <div>
                        <span
                            class="font-mono text-[9px] uppercase tracking-[0.25em] text-muted-foreground sm:text-[10px]"
                        >
                            Chapter 02
                        </span>
                        <h2
                            class="mt-1.5 font-serif text-3xl leading-[0.95] tracking-tight text-foreground sm:text-4xl md:text-5xl"
                        >
                            Experience.
                        </h2>
                    </div>
                    <span
                        class="hidden font-mono text-[10px] uppercase tracking-[0.25em] text-muted-foreground/60 sm:inline-flex"
                    >
                        {{ workExperience.length }} role<span v-if="workExperience.length !== 1">s</span>
                    </span>
                </div>

                <div v-if="workExperience.length">
                    <article
                        v-for="(job, i) in workExperience"
                        :key="job.id"
                        class="timeline-row reveal group relative grid grid-cols-1 gap-3 border-t border-border/40 py-6 md:grid-cols-[10rem_1fr_auto] md:gap-8 md:py-8 first:border-t-0"
                        :style="{ '--d': 120 + i * 90 + 'ms' }"
                    >
                        <div class="flex flex-col gap-1">
                            <span
                                class="font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground/80"
                            >
                                {{ String(i + 1).padStart(2, '0') }}
                            </span>
                            <span
                                class="font-serif text-2xl leading-none tracking-tight text-foreground tabular-nums sm:text-3xl"
                            >
                                {{ job.from }}
                            </span>
                            <span
                                class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted-foreground"
                            >
                                → {{ job.to || 'Present' }}
                            </span>
                        </div>

                        <div class="min-w-0">
                            <h3 class="font-serif text-xl text-foreground sm:text-2xl">
                                {{ job.title }}
                            </h3>
                            <div
                                class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground sm:text-xs"
                            >
                                <span>{{ job.company }}</span>
                                <span
                                    v-if="job.position"
                                    class="rounded-full border border-border/60 bg-muted/40 px-2 py-0.5 normal-case tracking-wide"
                                >
                                    {{ job.position }}
                                </span>
                            </div>
                            <p class="mt-3 text-sm leading-relaxed text-muted-foreground sm:text-[15px]">
                                {{ job.description }}
                            </p>
                        </div>

                        <div
                            class="hidden items-start md:flex"
                        >
                            <span
                                class="relative inline-flex h-8 w-8 items-center justify-center rounded-full border border-border/60 bg-card/60 text-muted-foreground transition-all duration-300 group-hover:-rotate-45 group-hover:border-foreground/60 group-hover:text-foreground"
                            >
                                <ArrowUpRight class="h-3.5 w-3.5" />
                            </span>
                        </div>
                    </article>
                </div>
                <p v-else class="text-sm text-muted-foreground">No experience yet.</p>
            </section>

            <!-- SECTION 03 — EDUCATION -->
            <section class="mt-10 sm:mt-14">
                <div class="reveal mb-6 flex items-end justify-between gap-4 border-b border-border/50 pb-4 sm:mb-8" style="--d: 0ms">
                    <div>
                        <span
                            class="font-mono text-[9px] uppercase tracking-[0.25em] text-muted-foreground sm:text-[10px]"
                        >
                            Chapter 03
                        </span>
                        <h2
                            class="mt-1.5 font-serif text-3xl leading-[0.95] tracking-tight text-foreground sm:text-4xl md:text-5xl"
                        >
                            Education.
                        </h2>
                    </div>
                    <span
                        class="hidden font-mono text-[10px] uppercase tracking-[0.25em] text-muted-foreground/60 sm:inline-flex items-center gap-1.5"
                    >
                        <GraduationCap class="h-3 w-3" />
                        {{ education.length }} record<span v-if="education.length !== 1">s</span>
                    </span>
                </div>

                <div v-if="education.length">
                    <article
                        v-for="(edu, i) in education"
                        :key="edu.id"
                        class="timeline-row reveal group relative grid grid-cols-1 gap-3 border-t border-border/40 py-6 md:grid-cols-[10rem_1fr_auto] md:gap-8 md:py-8 first:border-t-0"
                        :style="{ '--d': 120 + i * 90 + 'ms' }"
                    >
                        <div class="flex flex-col gap-1">
                            <span
                                class="font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground/80"
                            >
                                {{ String(i + 1).padStart(2, '0') }}
                            </span>
                            <span
                                class="font-serif text-2xl leading-none tracking-tight text-foreground tabular-nums sm:text-3xl"
                            >
                                {{ edu.from }}
                            </span>
                            <span
                                class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted-foreground"
                            >
                                → {{ edu.to || 'Present' }}
                            </span>
                        </div>

                        <div class="min-w-0">
                            <h3 class="font-serif text-xl text-foreground sm:text-2xl">
                                {{ edu.title }}
                            </h3>
                            <div
                                class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground sm:text-xs"
                            >
                                <span>{{ edu.institution }}</span>
                                <span
                                    v-if="edu.major"
                                    class="rounded-full border border-border/60 bg-muted/40 px-2 py-0.5 normal-case tracking-wide"
                                >
                                    {{ edu.major }}
                                </span>
                            </div>
                            <p class="mt-3 text-sm leading-relaxed text-muted-foreground sm:text-[15px]">
                                {{ edu.description }}
                            </p>
                        </div>

                        <div class="hidden items-start md:flex">
                            <span
                                class="relative inline-flex h-8 w-8 items-center justify-center rounded-full border border-border/60 bg-card/60 text-muted-foreground transition-all duration-300 group-hover:-rotate-45 group-hover:border-foreground/60 group-hover:text-foreground"
                            >
                                <GraduationCap class="h-3.5 w-3.5" />
                            </span>
                        </div>
                    </article>
                </div>
                <p v-else class="text-sm text-muted-foreground">No education records yet.</p>
            </section>

            <!-- SECTION 04 — STACK -->
            <section class="mt-10 sm:mt-14">
                <div class="reveal mb-6 flex items-end justify-between gap-4 border-b border-border/50 pb-4 sm:mb-8" style="--d: 0ms">
                    <div>
                        <span
                            class="font-mono text-[9px] uppercase tracking-[0.25em] text-muted-foreground sm:text-[10px]"
                        >
                            Chapter 04
                        </span>
                        <h2
                            class="mt-1.5 font-serif text-3xl leading-[0.95] tracking-tight text-foreground sm:text-4xl md:text-5xl"
                        >
                            Stack.
                        </h2>
                    </div>
                    <span
                        class="hidden font-mono text-[10px] uppercase tracking-[0.25em] text-muted-foreground/60 sm:inline-flex"
                    >
                        {{ techStacks.length }} tools
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-12 md:gap-5">
                    <article
                        v-for="(group, gi) in groupedTechStacks"
                        :key="group.type"
                        class="bento-card reveal col-span-2 overflow-hidden rounded-2xl border border-border/60 bg-card/60 p-4 backdrop-blur-xl sm:rounded-3xl sm:p-6 md:col-span-6"
                        :style="{ '--d': 120 + gi * 100 + 'ms' }"
                    >
                        <div class="flex items-center justify-between">
                            <span
                                class="font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground sm:text-[10px] sm:tracking-[0.25em]"
                            >
                                / {{ group.type }}
                            </span>
                            <span
                                class="font-mono text-[9px] uppercase tracking-[0.2em] text-muted-foreground/60 sm:text-[10px]"
                            >
                                {{ group.items.length }}
                            </span>
                        </div>

                        <ul class="mt-4 flex flex-wrap gap-2">
                            <li
                                v-for="tech in group.items"
                                :key="tech.id"
                                class="tech-chip"
                                :title="tech.description"
                            >
                                <img
                                    v-if="tech.logo"
                                    :src="tech.logo"
                                    :alt="tech.name"
                                    class="tech-chip-logo"
                                    loading="lazy"
                                />
                                <span
                                    v-else
                                    class="inline-flex h-4 w-4 items-center justify-center rounded-md bg-muted font-mono text-[9px] font-semibold text-muted-foreground"
                                >
                                    {{ tech.name.charAt(0) }}
                                </span>
                                <span class="tech-chip-name">{{ tech.name }}</span>
                            </li>
                        </ul>
                    </article>
                </div>
            </section>

            <!-- Footer tag -->
            <div
                class="reveal mt-10 flex flex-col items-start justify-between gap-1.5 font-mono text-[9px] uppercase tracking-[0.2em] text-muted-foreground/60 sm:mt-14 sm:flex-row sm:items-center sm:gap-2 sm:text-[10px] sm:tracking-[0.22em] md:text-xs"
                style="--d: 700ms"
            >
                <span>© {{ new Date().getFullYear() }} {{ user?.name || 'Portfolio' }}</span>
                <span>End of file →</span>
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

/* Fonts */
h1,
h2,
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

/* Bento cards */
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

/* Timeline row hover accent */
.timeline-row {
    transition: background-color 0.3s ease;
}
@media (hover: hover) and (pointer: fine) {
    .timeline-row:hover {
        background-color: color-mix(in oklab, var(--color-foreground) 2%, transparent);
    }
}

/* Portrait corner ticks */
.corner {
    position: absolute;
    width: 22px;
    height: 22px;
    border-color: rgba(255, 255, 255, 0.75);
    pointer-events: none;
}
.corner-tl {
    top: 10px;
    left: 10px;
    border-top: 1.5px solid;
    border-left: 1.5px solid;
}
.corner-tr {
    top: 10px;
    right: 10px;
    border-top: 1.5px solid;
    border-right: 1.5px solid;
}
.corner-bl {
    bottom: 10px;
    left: 10px;
    border-bottom: 1.5px solid;
    border-left: 1.5px solid;
}
.corner-br {
    bottom: 10px;
    right: 10px;
    border-bottom: 1.5px solid;
    border-right: 1.5px solid;
}

/* CTA */
.cta-primary {
    box-shadow: 0 10px 30px -10px color-mix(in oklab, var(--color-foreground) 40%, transparent);
}
.cta-primary:hover {
    box-shadow: 0 14px 40px -12px color-mix(in oklab, var(--color-foreground) 55%, transparent);
}

/* Tech chips */
.tech-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 0.8rem 0.35rem 0.45rem;
    border-radius: 9999px;
    border: 1px solid var(--color-border);
    background: color-mix(in oklab, var(--color-card) 70%, transparent);
    backdrop-filter: blur(8px);
    transition:
        transform 0.2s ease,
        border-color 0.2s ease;
}
.tech-chip:hover {
    transform: translateY(-1.5px);
    border-color: color-mix(in oklab, var(--color-foreground) 35%, var(--color-border));
}
.tech-chip-logo {
    width: 1.125rem;
    height: 1.125rem;
    object-fit: contain;
}
.tech-chip-name {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.72rem;
    letter-spacing: 0.04em;
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
