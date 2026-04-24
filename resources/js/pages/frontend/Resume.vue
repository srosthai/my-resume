<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Skeleton } from '@/components/ui/skeleton'
import { Phone, Mail, MapPin, Printer } from 'lucide-vue-next'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

const props = defineProps({
    users: { type: Object, default: () => ({}) },
    aboutMe: { type: Object, default: () => ({}) },
    workExperience: { type: Array, default: () => [] },
    education: { type: Array, default: () => [] },
    techStacks: { type: Array, default: () => [] },
    projects: { type: Array, default: () => [] },
    title: { type: String, default: 'Resume' },
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
    const parts = (props.users?.name || 'Name').trim().split(/\s+/)
    return parts[0] || 'Name'
})
const lastName = computed(() => {
    const parts = (props.users?.name || '').trim().split(/\s+/)
    return parts.slice(1).join(' ')
})

const imageSrc = computed(() => {
    const img = props.users?.image
    if (!img) return ''
    return img.startsWith('http') ? img : `/${img}`
})

const groupedSkills = computed(() => {
    const groups = {}
    const order = []
    ;(props.techStacks || []).forEach((t) => {
        const type = t.type || 'Other'
        if (!groups[type]) {
            groups[type] = []
            order.push(type)
        }
        groups[type].push(t)
    })
    return order.map((type) => ({ type, items: groups[type] }))
})

const hasContact = computed(
    () => props.users?.phone || props.users?.email || props.users?.address,
)

const bio = computed(
    () =>
        props.users?.description ||
        props.aboutMe?.description ||
        'A passionate developer crafting modern, reliable web applications.',
)

const printResume = () => {
    window.print()
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
    <FrontendLayout current-route="/resume">
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta
                name="keywords"
                content="resume, CV, curriculum vitae, software developer, experience, skills"
            />
            <meta name="author" :content="users?.name" />
            <meta property="og:title" :content="title" />
            <meta property="og:description" :content="description" />
            <meta property="og:type" content="profile" />
            <link
                href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500;600&display=swap"
                rel="stylesheet"
            />
        </Head>

        <!-- Floating print button (hidden on print) -->
        <div class="fixed right-4 top-24 z-40 print:hidden sm:right-6 sm:top-28">
            <button
                @click="printResume"
                class="group inline-flex items-center gap-2 rounded-full border border-border/70 bg-background/70 px-4 py-2 font-mono text-[10px] uppercase tracking-[0.22em] text-foreground backdrop-blur-xl transition-all duration-300 hover:-translate-y-0.5 hover:border-foreground/60 sm:text-[11px]"
                aria-label="Print résumé"
            >
                <Printer class="h-3.5 w-3.5 opacity-70 transition-transform duration-200 group-hover:scale-110" />
                <span>Print</span>
            </button>
        </div>

        <!-- Skeleton -->
        <section
            v-if="isLoading"
            class="mx-auto w-full max-w-5xl px-3 py-6 sm:px-6 sm:py-8 lg:px-10"
        >
            <div class="grid w-full grid-cols-2 gap-3 sm:gap-4 md:grid-cols-12 md:gap-5">
                <Skeleton class="col-span-2 h-72 rounded-3xl md:col-span-12" />
                <Skeleton class="col-span-2 h-96 rounded-3xl md:col-span-4" />
                <Skeleton class="col-span-2 h-96 rounded-3xl md:col-span-8" />
            </div>
        </section>

        <section
            v-else
            class="resume-root relative mx-auto w-full max-w-5xl px-3 py-6 print:max-w-none print:p-0 sm:px-6 sm:py-8 lg:px-10"
            :class="{ 'is-visible': isVisible }"
        >
            <!-- Ambient + grain (screen only) -->
            <div
                class="pointer-events-none fixed inset-0 -z-10 overflow-hidden print:hidden"
                aria-hidden="true"
            >
                <div
                    class="ambient-blob"
                    :style="{ left: pointer.x + '%', top: pointer.y + '%' }"
                ></div>
                <div class="grain-overlay"></div>
            </div>

            <!-- Top meta strip (screen only) -->
            <div
                class="reveal mb-4 flex flex-wrap items-center justify-between gap-x-4 gap-y-2 font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground/70 print:hidden sm:mb-5 sm:text-[10px] md:text-xs"
                style="--d: 0ms"
            >
                <span class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-foreground/40"></span>
                    Chapter 05
                </span>
                <span class="hidden md:inline">Résumé / 2026</span>
                <span class="tabular-nums">{{ dateString }}</span>
            </div>

            <!-- MASTHEAD -->
            <article
                class="masthead reveal relative overflow-hidden rounded-[1.5rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl print:rounded-none print:border-0 print:border-b print:border-gray-300 print:bg-white print:p-0 print:pb-5 sm:rounded-3xl sm:p-8 md:p-10"
                style="--d: 80ms"
            >
                <div
                    class="pointer-events-none absolute -right-24 -top-24 h-64 w-64 rotate-45 bg-gradient-to-br from-foreground/[0.04] to-transparent print:hidden"
                    aria-hidden="true"
                ></div>

                <!-- Eyebrow -->
                <div
                    class="flex items-center justify-between font-mono text-[9px] uppercase tracking-[0.25em] text-muted-foreground print:text-gray-500 sm:text-[10px] md:text-xs"
                >
                    <span class="inline-flex items-center gap-2">
                        <span class="h-px w-5 bg-foreground/40 print:bg-gray-400 sm:w-6"></span>
                        Curriculum Vitæ · 2026
                    </span>
                    <span v-if="users?.position" class="hidden md:inline">
                        {{ users.position }}
                    </span>
                </div>

                <!-- Name + portrait -->
                <div class="mt-5 flex flex-col gap-6 sm:mt-6 md:flex-row md:items-end md:justify-between md:gap-10">
                    <div class="min-w-0">
                        <h1
                            class="font-serif leading-[0.9] tracking-tight"
                        >
                            <span
                                class="block text-[clamp(2.5rem,9vw,6.5rem)] font-normal text-foreground print:text-[3.5rem] print:text-black"
                            >
                                {{ firstName }}
                            </span>
                            <span
                                v-if="lastName"
                                class="block text-[clamp(2.5rem,9vw,6.5rem)] font-normal italic text-foreground/80 print:text-[3.5rem] print:text-black"
                            >
                                {{ lastName }}.
                            </span>
                        </h1>
                        <p
                            class="mt-3 md:hidden font-mono text-xs uppercase tracking-[0.22em] text-muted-foreground print:hidden"
                        >
                            {{ users?.position }}
                        </p>
                    </div>

                    <div
                        class="flex items-center gap-4 print:hidden md:flex-col md:items-end md:gap-3"
                    >
                        <div
                            class="relative h-20 w-20 overflow-hidden rounded-2xl border border-border/60 bg-muted shadow-lg ring-1 ring-foreground/5 xs:h-24 xs:w-24 sm:h-28 sm:w-28 md:h-32 md:w-32"
                        >
                            <img
                                v-if="imageSrc"
                                :src="imageSrc"
                                :alt="users?.name"
                                class="h-full w-full object-cover object-[center_25%]"
                            />
                            <div
                                v-else
                                class="flex h-full w-full items-center justify-center font-serif text-3xl italic text-muted-foreground"
                            >
                                {{ firstName.charAt(0) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact row -->
                <div
                    v-if="hasContact"
                    class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-border/50 pt-5 font-mono text-xs text-muted-foreground print:text-[11px] print:text-gray-700 sm:mt-7"
                >
                    <span
                        v-if="users?.phone"
                        class="inline-flex items-center gap-2"
                    >
                        <Phone class="h-3.5 w-3.5 opacity-70" />
                        {{ users.phone }}
                    </span>
                    <span
                        v-if="users?.email"
                        class="inline-flex items-center gap-2"
                    >
                        <Mail class="h-3.5 w-3.5 opacity-70" />
                        {{ users.email }}
                    </span>
                    <span
                        v-if="users?.address"
                        class="inline-flex items-center gap-2"
                    >
                        <MapPin class="h-3.5 w-3.5 opacity-70" />
                        {{ users.address }}
                    </span>
                </div>
            </article>

            <!-- BODY: 2-column grid -->
            <div
                class="mt-4 grid grid-cols-1 gap-4 print:mt-5 print:gap-5 sm:mt-5 sm:gap-5 md:grid-cols-[280px_1fr]"
            >
                <!-- SIDEBAR -->
                <aside class="space-y-4 print:space-y-5 sm:space-y-5">
                    <!-- Profile -->
                    <section
                        class="reveal resume-card overflow-hidden rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl print:rounded-none print:border-0 print:bg-transparent print:p-0 sm:rounded-3xl sm:p-6"
                        style="--d: 160ms"
                    >
                        <span class="section-eyebrow">/ Profile</span>
                        <p
                            class="mt-3 text-sm leading-[1.75] text-muted-foreground print:mt-2 print:text-[11px] print:leading-[1.6] print:text-gray-800"
                        >
                            {{ bio }}
                        </p>
                    </section>

                    <!-- Skills -->
                    <section
                        v-if="groupedSkills.length"
                        class="reveal resume-card overflow-hidden rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl print:rounded-none print:border-0 print:bg-transparent print:p-0 sm:rounded-3xl sm:p-6"
                        style="--d: 240ms"
                    >
                        <span class="section-eyebrow">/ Skills</span>
                        <div class="mt-4 space-y-4 print:mt-2">
                            <div
                                v-for="group in groupedSkills"
                                :key="group.type"
                            >
                                <h3
                                    class="font-serif text-lg text-foreground print:text-[13px] print:font-semibold print:text-black"
                                >
                                    {{ group.type }}
                                </h3>
                                <ul class="mt-2 flex flex-wrap gap-1.5">
                                    <li
                                        v-for="tech in group.items"
                                        :key="tech.id"
                                        class="skill-chip"
                                    >
                                        {{ tech.name }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </section>
                </aside>

                <!-- MAIN -->
                <div class="space-y-4 print:space-y-5 sm:space-y-5">
                    <!-- Experience -->
                    <section
                        v-if="workExperience?.length"
                        class="reveal resume-card overflow-hidden rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl print:rounded-none print:border-0 print:bg-transparent print:p-0 sm:rounded-3xl sm:p-7"
                        style="--d: 320ms"
                    >
                        <span class="section-eyebrow">/ Experience</span>

                        <div class="mt-4 print:mt-2">
                            <article
                                v-for="(work, i) in workExperience"
                                :key="work.id"
                                class="resume-row"
                                :class="{ 'border-t': i > 0 }"
                            >
                                <div class="resume-row-head">
                                    <div class="min-w-0">
                                        <h3 class="resume-row-title">
                                            {{ work.title || work.position }}
                                        </h3>
                                        <div class="resume-row-subtitle">
                                            <span>{{ work.company }}</span>
                                            <span
                                                v-if="work.position && work.title && work.title !== work.position"
                                                class="resume-row-pill"
                                            >
                                                {{ work.position }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="resume-row-meta">
                                        {{ work.from }} — {{ work.to || 'Present' }}
                                    </div>
                                </div>

                                <ul
                                    v-if="work.responsibilities"
                                    class="resume-row-list"
                                >
                                    <li
                                        v-for="r in work.responsibilities.split('\n').filter(Boolean)"
                                        :key="r"
                                    >
                                        <span class="resume-row-bullet"></span>
                                        {{ r }}
                                    </li>
                                </ul>
                                <p
                                    v-else-if="work.description"
                                    class="resume-row-body"
                                >
                                    {{ work.description }}
                                </p>
                            </article>
                        </div>
                    </section>

                    <!-- Education -->
                    <section
                        v-if="education?.length"
                        class="reveal resume-card overflow-hidden rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl print:rounded-none print:border-0 print:bg-transparent print:p-0 sm:rounded-3xl sm:p-7"
                        style="--d: 400ms"
                    >
                        <span class="section-eyebrow">/ Education</span>

                        <div class="mt-4 print:mt-2">
                            <article
                                v-for="(edu, i) in education"
                                :key="edu.id"
                                class="resume-row"
                                :class="{ 'border-t': i > 0 }"
                            >
                                <div class="resume-row-head">
                                    <div class="min-w-0">
                                        <h3 class="resume-row-title">
                                            {{ edu.title || edu.degree }}
                                        </h3>
                                        <div class="resume-row-subtitle">
                                            <span>{{ edu.institution }}</span>
                                            <span
                                                v-if="edu.major"
                                                class="resume-row-pill"
                                            >
                                                {{ edu.major }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="resume-row-meta">
                                        {{ edu.from }} — {{ edu.to }}
                                    </div>
                                </div>
                                <p
                                    v-if="edu.description"
                                    class="resume-row-body"
                                >
                                    {{ edu.description }}
                                </p>
                                <p
                                    v-if="edu.gpa"
                                    class="mt-1 font-mono text-[10px] uppercase tracking-[0.2em] text-muted-foreground print:text-gray-600"
                                >
                                    GPA · {{ edu.gpa }}
                                </p>
                            </article>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Footer (screen) -->
            <div
                class="reveal mt-8 flex flex-col items-start justify-between gap-1.5 font-mono text-[9px] uppercase tracking-[0.2em] text-muted-foreground/60 print:hidden sm:mt-10 sm:flex-row sm:items-center sm:gap-2 sm:text-[10px] sm:tracking-[0.22em] md:text-xs"
                style="--d: 520ms"
            >
                <span>
                    Last updated —
                    {{ new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' }) }}
                </span>
                <span>End of document →</span>
            </div>

            <!-- Print-only footer -->
            <div
                class="hidden print:mt-6 print:block print:border-t print:border-gray-200 print:pt-3 print:text-center print:font-mono print:text-[9px] print:uppercase print:tracking-[0.2em] print:text-gray-500"
            >
                {{ users?.name }} · {{ users?.email }} · Generated
                {{ new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}
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

/* Section eyebrow (mono label above each section) */
.section-eyebrow {
    font-family: 'JetBrains Mono', ui-monospace, monospace;
    font-size: 10px;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    color: var(--color-muted-foreground);
}

/* Resume cards on screen */
.resume-card {
    transition:
        transform 0.35s cubic-bezier(0.22, 1, 0.36, 1),
        border-color 0.35s ease;
}
@media (hover: hover) and (pointer: fine) {
    .resume-card:hover {
        transform: translateY(-2px);
        border-color: color-mix(in oklab, var(--color-foreground) 18%, var(--color-border));
    }
}

/* Resume row (shared by experience + education) */
.resume-row {
    padding: 1rem 0;
    border-color: var(--color-border);
}
.resume-row:first-child {
    padding-top: 0;
}
.resume-row-head {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}
@media (min-width: 640px) {
    .resume-row-head {
        flex-direction: row;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
    }
}
.resume-row-title {
    font-family: 'Instrument Serif', Georgia, serif;
    font-size: 1.35rem;
    line-height: 1.15;
    letter-spacing: -0.01em;
    color: var(--color-foreground);
}
.resume-row-subtitle {
    margin-top: 0.25rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--color-muted-foreground);
}
.resume-row-pill {
    padding: 0.15rem 0.5rem;
    border-radius: 9999px;
    border: 1px solid var(--color-border);
    background: color-mix(in oklab, var(--color-muted) 40%, transparent);
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    letter-spacing: 0.12em;
    text-transform: none;
    color: var(--color-foreground);
}
.resume-row-meta {
    flex-shrink: 0;
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    letter-spacing: 0.1em;
    color: var(--color-muted-foreground);
}
.resume-row-body {
    margin-top: 0.6rem;
    font-size: 0.875rem;
    line-height: 1.7;
    color: var(--color-muted-foreground);
}
.resume-row-list {
    margin-top: 0.6rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--color-muted-foreground);
}
.resume-row-list li {
    display: flex;
    gap: 0.6rem;
    line-height: 1.65;
}
.resume-row-bullet {
    display: inline-block;
    margin-top: 0.55rem;
    width: 0.3rem;
    height: 0.3rem;
    border-radius: 9999px;
    background: var(--color-foreground);
    opacity: 0.4;
    flex-shrink: 0;
}

/* Skill chips */
.skill-chip {
    display: inline-flex;
    align-items: center;
    padding: 0.3rem 0.7rem;
    border-radius: 9999px;
    border: 1px solid var(--color-border);
    background: color-mix(in oklab, var(--color-muted) 40%, transparent);
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.68rem;
    letter-spacing: 0.04em;
    color: var(--color-foreground);
    transition: transform 0.2s ease, border-color 0.2s ease;
}
.skill-chip:hover {
    transform: translateY(-1.5px);
    border-color: color-mix(in oklab, var(--color-foreground) 35%, var(--color-border));
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

/* ========================================
   PRINT STYLES — clean A4 document
   ======================================== */
@media print {
    @page {
        size: A4;
        margin: 0.6in 0.55in;
    }

    :global(body) {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        background: #fff !important;
    }

    .reveal,
    .is-visible .reveal {
        opacity: 1 !important;
        transform: none !important;
        animation: none !important;
    }

    /* Flatten everything to plain document */
    .resume-card,
    .masthead {
        box-shadow: none !important;
        backdrop-filter: none !important;
    }

    .section-eyebrow {
        color: #4b5563 !important;
        font-size: 9px;
        letter-spacing: 0.22em;
        border-bottom: 1px solid #d1d5db;
        padding-bottom: 4px;
        display: block;
        margin-bottom: 8px;
    }

    .resume-row {
        padding: 10px 0;
        page-break-inside: avoid;
        border-color: #e5e7eb !important;
    }

    .resume-row-title {
        font-size: 14px;
        color: #111 !important;
    }

    .resume-row-subtitle {
        font-size: 10px;
        color: #374151 !important;
    }

    .resume-row-pill {
        background: #f3f4f6 !important;
        color: #111 !important;
        border-color: #d1d5db !important;
    }

    .resume-row-meta {
        font-size: 10px;
        color: #6b7280 !important;
    }

    .resume-row-body {
        font-size: 11px;
        color: #374151 !important;
        line-height: 1.55;
    }

    .resume-row-list {
        font-size: 11px;
        color: #374151 !important;
        gap: 3px;
    }

    .resume-row-bullet {
        background: #111 !important;
        opacity: 0.7;
    }

    .skill-chip {
        background: #f3f4f6 !important;
        color: #111 !important;
        border-color: #d1d5db !important;
        font-size: 9.5px;
        padding: 2px 7px;
    }
}
</style>
