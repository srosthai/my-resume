<script setup>
import FrontendLayout from '@/layouts/FrontendLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, ArrowUpRight, ChevronLeft, ChevronRight, ExternalLink, Github, Laptop } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    title: { type: String, default: 'Project' },
    description: { type: String, default: '' },
    project: { type: Object, required: true },
    previousProject: { type: Object, default: null },
    nextProject: { type: Object, default: null },
});

const isLoading = ref(true);
const isVisible = ref(false);

const now = ref(new Date());
let clockTimer = null;

const dateString = computed(() => {
    try {
        return new Intl.DateTimeFormat('en-US', {
            timeZone: 'Asia/Phnom_Penh',
            weekday: 'short',
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        }).format(now.value);
    } catch (e) {
        return '';
    }
});

const pointer = ref({ x: 50, y: 50 });
const handlePointer = (e) => {
    pointer.value = {
        x: (e.clientX / window.innerWidth) * 100,
        y: (e.clientY / window.innerHeight) * 100,
    };
};

const statusMeta = computed(() => {
    const s = props.project?.status;
    switch (s) {
        case 'completed':
            return { label: 'Completed', color: '#10b981' };
        case 'in-progress':
        case 'processing':
            return { label: s === 'processing' ? 'Processing' : 'In Progress', color: '#3b82f6' };
        case 'pending':
            return { label: 'Pending', color: '#f59e0b' };
        default:
            return { label: s || 'Unknown', color: '#a3a3a3' };
    }
});

const formattedDate = computed(() => {
    const raw = props.project?.created_date || props.project?.created_at;
    if (!raw) return null;
    try {
        return new Date(raw).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    } catch (e) {
        return null;
    }
});

const year = computed(() => {
    const raw = props.project?.created_date || props.project?.created_at;
    if (!raw) return null;
    try {
        return new Date(raw).getFullYear();
    } catch (e) {
        return null;
    }
});

const technologies = computed(() => {
    if (Array.isArray(props.project?.technologies) && props.project.technologies.length) {
        return props.project.technologies;
    }
    if (props.project?.tech_stack) {
        return props.project.tech_stack
            .split(',')
            .map((t) => t.trim())
            .filter(Boolean);
    }
    return [];
});

const projectLinks = computed(() => {
    const out = [];
    if (Array.isArray(props.project?.links)) {
        for (const l of props.project.links) {
            const name = Object.keys(l)[0];
            const url = Object.values(l)[0];
            if (!name || !url) continue;
            out.push({ name, url, isGithub: name.toLowerCase().includes('github') });
        }
    }
    return out;
});

const entryNumber = computed(() => {
    if (!props.project?.id) return '00';
    return String(props.project.id).padStart(2, '0');
});

const goBack = () => {
    router.visit(route('portfolio'));
};
const goToProject = (id) => {
    router.visit(route('portfolio.show', id));
};

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false;
        requestAnimationFrame(() => {
            isVisible.value = true;
        });
    }, 300);

    clockTimer = setInterval(() => {
        now.value = new Date();
    }, 60000);

    window.addEventListener('pointermove', handlePointer, { passive: true });
});

onBeforeUnmount(() => {
    if (clockTimer) clearInterval(clockTimer);
    window.removeEventListener('pointermove', handlePointer);
});
</script>

<template>
    <FrontendLayout current-route="/portfolio">
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta property="og:title" :content="title" />
            <meta property="og:description" :content="description" />
            <meta property="og:type" content="article" />
            <meta name="robots" content="index, follow" />
            <link
                href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500;600&display=swap"
                rel="stylesheet"
            />
        </Head>

        <section
            class="project-detail-screen relative mx-auto w-full max-w-5xl px-3 py-6 sm:px-6 sm:py-8 lg:px-10"
            :class="{ 'is-visible': isVisible }"
        >
            <!-- Ambient + grain -->
            <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
                <div class="ambient-blob" :style="{ left: pointer.x + '%', top: pointer.y + '%' }"></div>
                <div class="grain-overlay"></div>
            </div>

            <!-- Top meta strip -->
            <div
                class="mobile-status reveal mb-4 flex flex-wrap items-center justify-between gap-x-4 gap-y-2 font-mono text-[9px] tracking-[0.22em] text-muted-foreground/70 uppercase sm:mb-5 sm:text-[10px] md:text-xs"
                style="--d: 0ms"
            >
                <span class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-foreground/40"></span>
                    Entry #{{ entryNumber }}
                </span>
                <span class="hidden md:inline">Archive / 2026</span>
                <span class="tabular-nums">{{ dateString }}</span>
            </div>

            <!-- Breadcrumb -->
            <nav
                class="mobile-breadcrumb reveal mb-5 flex items-center gap-2 font-mono text-[10px] tracking-[0.22em] text-muted-foreground uppercase sm:mb-6 sm:text-[11px]"
                style="--d: 60ms"
            >
                <button @click="goBack" class="inline-flex items-center gap-1.5 transition-colors hover:text-foreground">
                    <ArrowLeft class="h-3 w-3" />
                    <span>Archive</span>
                </button>
                <span class="text-border">/</span>
                <span class="truncate text-foreground/80">{{ project.title }}</span>
            </nav>

            <!-- HERO -->
            <article
                class="mobile-detail-hero reveal relative overflow-hidden rounded-[1.5rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-3xl sm:p-8 md:p-10"
                style="--d: 140ms"
            >
                <div
                    class="pointer-events-none absolute -top-24 -right-24 h-64 w-64 rotate-45 bg-gradient-to-br from-foreground/[0.04] to-transparent"
                    aria-hidden="true"
                ></div>

                <div
                    class="flex items-center justify-between font-mono text-[9px] tracking-[0.25em] text-muted-foreground uppercase sm:text-[10px] md:text-xs"
                >
                    <span class="inline-flex items-center gap-2">
                        <span class="h-px w-5 bg-foreground/40 sm:w-6"></span>
                        {{ project.project_type?.name || 'General' }}
                    </span>
                    <span v-if="year" class="tabular-nums">
                        {{ year }}
                    </span>
                </div>

                <h1 class="mt-5 font-serif text-[clamp(2rem,6vw,4.25rem)] leading-[0.95] font-normal tracking-tight text-foreground sm:mt-6">
                    {{ project.title }}
                </h1>

                <div
                    v-if="project.status"
                    class="mobile-status-pill mt-5 inline-flex items-center gap-2 rounded-full border border-border/60 bg-background/40 px-3 py-1.5 font-mono text-[10px] tracking-[0.22em] text-foreground uppercase backdrop-blur-sm"
                >
                    <span
                        class="inline-flex h-1.5 w-1.5 rounded-full"
                        :style="{ backgroundColor: statusMeta.color, boxShadow: `0 0 8px ${statusMeta.color}` }"
                    ></span>
                    {{ statusMeta.label }}
                </div>
            </article>

            <!-- IMAGE -->
            <article
                class="mobile-image-card reveal mt-4 overflow-hidden rounded-[1.5rem] border border-border/60 bg-card/60 backdrop-blur-xl sm:mt-5 sm:rounded-3xl"
                style="--d: 220ms"
            >
                <div class="relative aspect-[16/9] overflow-hidden bg-muted/30">
                    <img v-if="project.image" :src="project.image" :alt="project.title" class="h-full w-full object-cover" />
                    <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-muted/60 to-muted/20">
                        <Laptop class="h-14 w-14 text-muted-foreground/40" />
                    </div>
                    <div class="corner corner-tl" aria-hidden="true"></div>
                    <div class="corner corner-tr" aria-hidden="true"></div>
                    <div class="corner corner-bl" aria-hidden="true"></div>
                    <div class="corner corner-br" aria-hidden="true"></div>
                </div>
            </article>

            <!-- CONTENT GRID -->
            <div class="mobile-content-grid mt-4 grid grid-cols-1 gap-4 sm:mt-5 sm:gap-5 lg:grid-cols-[1fr_300px]">
                <!-- Main column -->
                <article
                    class="mobile-content-card reveal overflow-hidden rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-3xl sm:p-8"
                    style="--d: 300ms"
                >
                    <span class="font-mono text-[10px] tracking-[0.25em] text-muted-foreground uppercase"> — About this entry </span>
                    <p class="mt-4 text-sm leading-[1.8] whitespace-pre-line text-muted-foreground sm:mt-5 sm:text-[15px] md:text-base">
                        {{ project.description }}
                    </p>

                    <div v-if="technologies.length" class="mt-8 border-t border-border/50 pt-6">
                        <span class="font-mono text-[10px] tracking-[0.25em] text-muted-foreground uppercase"> — Technologies </span>
                        <ul class="mt-4 flex flex-wrap gap-2">
                            <li v-for="tech in technologies" :key="tech" class="tech-chip">
                                {{ tech }}
                            </li>
                        </ul>
                    </div>
                </article>

                <!-- Sidebar -->
                <aside class="space-y-4 sm:space-y-5">
                    <!-- Details card -->
                    <article
                        class="mobile-side-card reveal overflow-hidden rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-3xl"
                        style="--d: 380ms"
                    >
                        <span class="font-mono text-[9px] tracking-[0.22em] text-muted-foreground uppercase sm:text-[10px] sm:tracking-[0.25em]">
                            / details
                        </span>

                        <dl class="mt-4 space-y-3">
                            <div v-if="project.status" class="detail-row">
                                <dt>Status</dt>
                                <dd class="inline-flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full" :style="{ backgroundColor: statusMeta.color }"></span>
                                    {{ statusMeta.label }}
                                </dd>
                            </div>
                            <div v-if="formattedDate" class="detail-row">
                                <dt>Date</dt>
                                <dd>{{ formattedDate }}</dd>
                            </div>
                            <div class="detail-row">
                                <dt>Category</dt>
                                <dd>{{ project.project_type?.name || 'General' }}</dd>
                            </div>
                            <div v-if="technologies.length" class="detail-row">
                                <dt>Stack</dt>
                                <dd>{{ technologies.length }} tools</dd>
                            </div>
                        </dl>
                    </article>

                    <!-- Links card -->
                    <article
                        v-if="projectLinks.length"
                        class="mobile-side-card reveal overflow-hidden rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-3xl"
                        style="--d: 440ms"
                    >
                        <span class="font-mono text-[9px] tracking-[0.22em] text-muted-foreground uppercase sm:text-[10px] sm:tracking-[0.25em]">
                            / links
                        </span>
                        <ul class="mt-3 divide-y divide-border/50">
                            <li v-for="link in projectLinks" :key="link.url">
                                <a :href="link.url" target="_blank" rel="noopener noreferrer" class="link-row group">
                                    <span class="flex min-w-0 items-center gap-2.5">
                                        <Github v-if="link.isGithub" class="h-4 w-4 shrink-0 opacity-70" />
                                        <ExternalLink v-else class="h-4 w-4 shrink-0 opacity-70" />
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate font-medium text-foreground">
                                                {{ link.name }}
                                            </span>
                                            <span class="block truncate font-mono text-[10px] tracking-[0.15em] text-muted-foreground/70 uppercase">
                                                {{ link.url.replace(/^https?:\/\//, '').split('/')[0] }}
                                            </span>
                                        </span>
                                    </span>
                                    <ArrowUpRight
                                        class="h-4 w-4 shrink-0 opacity-40 transition-all duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:opacity-100"
                                    />
                                </a>
                            </li>
                        </ul>
                    </article>
                </aside>
            </div>

            <!-- PAGINATION -->
            <nav class="mobile-pager reveal mt-8 border-t border-border/50 pt-6 sm:mt-10 sm:pt-8" style="--d: 500ms">
                <div class="flex flex-col items-stretch gap-3 sm:flex-row sm:justify-between sm:gap-4">
                    <button v-if="previousProject" @click="goToProject(previousProject.id)" class="pager-btn group pager-prev">
                        <ChevronLeft class="h-4 w-4 shrink-0 opacity-60 transition-transform duration-300 group-hover:-translate-x-0.5" />
                        <div class="min-w-0 text-left">
                            <p class="font-mono text-[9px] tracking-[0.22em] text-muted-foreground uppercase sm:text-[10px]">← Previous</p>
                            <p class="mt-1 truncate font-serif text-base text-foreground sm:text-lg">
                                {{ previousProject.title }}
                            </p>
                        </div>
                    </button>
                    <div v-else class="hidden sm:block"></div>

                    <button
                        @click="goBack"
                        class="inline-flex items-center justify-center gap-2 rounded-full border border-border/60 bg-background/40 px-5 py-3 font-mono text-[10px] tracking-[0.22em] text-foreground uppercase backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-foreground/60 sm:self-center"
                    >
                        All entries
                    </button>

                    <button v-if="nextProject" @click="goToProject(nextProject.id)" class="pager-btn group pager-next">
                        <div class="min-w-0 text-right">
                            <p class="font-mono text-[9px] tracking-[0.22em] text-muted-foreground uppercase sm:text-[10px]">Next →</p>
                            <p class="mt-1 truncate font-serif text-base text-foreground sm:text-lg">
                                {{ nextProject.title }}
                            </p>
                        </div>
                        <ChevronRight class="h-4 w-4 shrink-0 opacity-60 transition-transform duration-300 group-hover:translate-x-0.5" />
                    </button>
                    <div v-else class="hidden sm:block"></div>
                </div>
            </nav>

            <!-- Footer -->
            <div
                class="reveal mt-10 flex flex-col items-start justify-between gap-1.5 font-mono text-[9px] tracking-[0.2em] text-muted-foreground/60 uppercase sm:mt-14 sm:flex-row sm:items-center sm:gap-2 sm:text-[10px] sm:tracking-[0.22em] md:text-xs"
                style="--d: 600ms"
            >
                <span>© {{ new Date().getFullYear() }} · Entry #{{ entryNumber }}</span>
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
    background: radial-gradient(closest-side, color-mix(in oklab, var(--color-foreground) 7%, transparent), transparent 70%);
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

/* Image corner ticks */
.corner {
    position: absolute;
    width: 18px;
    height: 18px;
    border-color: rgba(255, 255, 255, 0.6);
    pointer-events: none;
}
@media (min-width: 640px) {
    .corner {
        width: 24px;
        height: 24px;
    }
}
.corner-tl {
    top: 14px;
    left: 14px;
    border-top: 1.5px solid;
    border-left: 1.5px solid;
}
.corner-tr {
    top: 14px;
    right: 14px;
    border-top: 1.5px solid;
    border-right: 1.5px solid;
}
.corner-bl {
    bottom: 14px;
    left: 14px;
    border-bottom: 1.5px solid;
    border-left: 1.5px solid;
}
.corner-br {
    bottom: 14px;
    right: 14px;
    border-bottom: 1.5px solid;
    border-right: 1.5px solid;
}

/* Detail rows in sidebar */
.detail-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.5rem 0;
    border-top: 1px solid var(--color-border);
    font-size: 0.8rem;
}
.detail-row:first-child {
    border-top: 0;
    padding-top: 0;
}
.detail-row dt {
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--color-muted-foreground);
}
.detail-row dd {
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    letter-spacing: 0.08em;
    color: var(--color-foreground);
    text-align: right;
    min-width: 0;
}

/* Tech chips */
.tech-chip {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.75rem;
    border-radius: 9999px;
    border: 1px solid var(--color-border);
    background: color-mix(in oklab, var(--color-muted) 40%, transparent);
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.7rem;
    letter-spacing: 0.04em;
    color: var(--color-foreground);
    transition:
        transform 0.2s ease,
        border-color 0.2s ease;
}
.tech-chip:hover {
    transform: translateY(-1.5px);
    border-color: color-mix(in oklab, var(--color-foreground) 35%, var(--color-border));
}

/* Sidebar link rows */
.link-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.75rem 0.25rem;
    color: var(--color-foreground);
    transition: color 0.2s ease;
}

/* Pager buttons */
.pager-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    min-width: 0;
    max-width: 100%;
    border-radius: 1rem;
    border: 1px solid var(--color-border);
    background: color-mix(in oklab, var(--color-card) 60%, transparent);
    backdrop-filter: blur(8px);
    text-align: left;
    cursor: pointer;
    transition:
        transform 0.3s ease,
        border-color 0.3s ease,
        background-color 0.3s ease;
}
@media (min-width: 640px) {
    .pager-btn {
        flex: 1 1 0;
        max-width: 44%;
    }
}
.pager-btn:hover {
    transform: translateY(-2px);
    border-color: color-mix(in oklab, var(--color-foreground) 30%, var(--color-border));
}
.pager-next {
    justify-content: flex-end;
}

@media (max-width: 767px) {
    .project-detail-screen {
        min-height: calc(100dvh - 6rem);
        padding: 0.875rem 0.875rem 1.25rem;
    }

    .mobile-status {
        position: sticky;
        top: 0.625rem;
        z-index: 20;
        margin: 0 auto 0.875rem;
        width: min(100%, 26rem);
        flex-wrap: nowrap;
        border: 1px solid color-mix(in oklab, var(--color-border) 70%, transparent);
        border-radius: 9999px;
        background: color-mix(in oklab, var(--color-background) 86%, transparent);
        padding: 0.7rem 0.85rem;
        box-shadow: 0 18px 45px -28px color-mix(in oklab, var(--color-foreground) 35%, transparent);
        backdrop-filter: blur(18px);
        letter-spacing: 0.14em;
    }

    .mobile-status span {
        min-width: 0;
    }

    .mobile-status > span:first-child {
        max-width: 9rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .mobile-status > span:last-child {
        text-align: right;
        white-space: nowrap;
    }

    .mobile-breadcrumb {
        margin-bottom: 0.875rem;
        border: 1px solid color-mix(in oklab, var(--color-border) 70%, transparent);
        border-radius: 1.1rem;
        background: color-mix(in oklab, var(--color-card) 76%, transparent);
        padding: 0.75rem 0.9rem;
        letter-spacing: 0.14em;
    }

    .mobile-breadcrumb span.truncate {
        max-width: 13rem;
    }

    .mobile-detail-hero {
        border-radius: 2rem;
        padding: 1.25rem;
        background:
            linear-gradient(
                145deg,
                color-mix(in oklab, var(--color-card) 92%, transparent),
                color-mix(in oklab, var(--color-muted) 62%, transparent)
            ),
            radial-gradient(circle at 88% 4%, color-mix(in oklab, var(--color-foreground) 7%, transparent), transparent 34%);
        box-shadow:
            0 1px 0 color-mix(in oklab, white 16%, transparent) inset,
            0 24px 70px -42px color-mix(in oklab, var(--color-foreground) 70%, transparent);
    }

    .mobile-detail-hero h1 {
        margin-top: 1rem;
        font-size: clamp(2.25rem, 12vw, 3.35rem);
        line-height: 0.95;
    }

    .mobile-status-pill {
        margin-top: 1rem;
        border-radius: 9999px;
        padding: 0.55rem 0.7rem;
        letter-spacing: 0.12em;
    }

    .mobile-image-card {
        border-radius: 1.45rem;
        background: color-mix(in oklab, var(--color-card) 78%, transparent);
    }

    .mobile-image-card > div {
        aspect-ratio: 16 / 10;
    }

    .corner {
        display: none;
    }

    .mobile-content-grid {
        gap: 0.75rem;
    }

    .mobile-content-card,
    .mobile-side-card {
        border-radius: 1.35rem;
        background: color-mix(in oklab, var(--color-card) 78%, transparent);
        padding: 1rem;
    }

    .mobile-content-card > span,
    .mobile-side-card > span {
        letter-spacing: 0.16em;
    }

    .mobile-content-card p {
        margin-top: 0.85rem;
        font-size: 0.88rem;
        line-height: 1.7;
    }

    .tech-chip {
        border-radius: 0.85rem;
        padding: 0.32rem 0.6rem;
        font-size: 0.66rem;
    }

    .detail-row {
        padding: 0.75rem 0;
    }

    .detail-row dt {
        letter-spacing: 0.14em;
    }

    .detail-row dd {
        max-width: 55%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .link-row {
        min-height: 3.35rem;
        padding: 0.85rem 0.15rem;
    }

    .mobile-pager {
        margin-top: 1.25rem;
        padding-top: 1rem;
    }

    .pager-btn {
        border-radius: 1.1rem;
        padding: 0.9rem 1rem;
        background: color-mix(in oklab, var(--color-card) 78%, transparent);
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
