<script setup lang="ts">
import { usePhnomPenhClock } from '@/composables/usePhnomPenhClock';
import { usePointerGlow } from '@/composables/usePointerGlow';
import FrontendLayout from '@/layouts/FrontendLayout.vue';
import { formatDate } from '@/lib/date';
import type { Note } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, ArrowUpRight, Check, Copy, Eye } from 'lucide-vue-next';
import { ref } from 'vue';

type RelatedNote = Pick<Note, 'id' | 'title' | 'slug' | 'category' | 'description' | 'published_at'>;

const props = defineProps<{
    title: string;
    description: string;
    note: Note;
    related: RelatedNote[];
}>();

const { date: dateString } = usePhnomPenhClock(60000);
const { pointer } = usePointerGlow();
const currentYear = new Date().getFullYear();

const copied = ref<string | null>(null);
let copiedTimer: ReturnType<typeof setTimeout> | null = null;

const copyCommand = async (command: string, key: string) => {
    try {
        await navigator.clipboard.writeText(command);
        copied.value = key;
        if (copiedTimer) clearTimeout(copiedTimer);
        copiedTimer = setTimeout(() => (copied.value = null), 1800);
    } catch {
        // Clipboard is unavailable (insecure context or denied); nothing to do.
    }
};

const publishedOn = formatDate(props.note.published_at ?? props.note.created_at);
</script>

<template>
    <FrontendLayout current-route="/note">
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta property="og:title" :content="title" />
            <meta property="og:description" :content="description" />
            <meta property="og:type" content="article" />
        </Head>

        <section class="detail-screen is-visible relative mx-auto w-full max-w-4xl px-3 py-6 sm:px-6 sm:py-8 lg:px-10">
            <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
                <div class="ambient-blob" :style="{ left: pointer.x + '%', top: pointer.y + '%' }"></div>
            </div>

            <!-- Meta strip -->
            <div
                class="reveal mb-4 flex flex-wrap items-center justify-between gap-x-4 gap-y-2 font-mono text-[9px] tracking-[0.22em] text-muted-foreground/70 uppercase sm:mb-5 sm:text-[10px] md:text-xs"
                style="--d: 0ms"
            >
                <span class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-foreground/40"></span>
                    Notebook entry
                </span>
                <span class="tabular-nums">{{ dateString }}</span>
            </div>

            <!-- Breadcrumb -->
            <nav
                class="reveal mb-5 flex items-center gap-2 font-mono text-[10px] tracking-[0.22em] text-muted-foreground uppercase sm:mb-6 sm:text-[11px]"
                style="--d: 60ms"
                aria-label="Breadcrumb"
            >
                <Link :href="route('note')" class="inline-flex items-center gap-1.5 transition-colors hover:text-foreground">
                    <ArrowLeft class="h-3 w-3" />
                    <span>Notebook</span>
                </Link>
                <span class="text-border">/</span>
                <span class="truncate text-foreground/80">{{ note.title }}</span>
            </nav>

            <!-- Hero -->
            <article
                class="card-3d reveal relative overflow-hidden rounded-[1.5rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:p-8 md:p-10"
                style="--d: 140ms"
            >
                <div
                    class="flex flex-wrap items-center justify-between gap-3 font-mono text-[9px] tracking-[0.25em] text-muted-foreground uppercase sm:text-[10px] md:text-xs"
                >
                    <span class="inline-flex items-center gap-2">
                        <span class="h-px w-5 bg-foreground/40 sm:w-6"></span>
                        {{ note.category }}
                    </span>
                    <span class="inline-flex items-center gap-3 tabular-nums">
                        <span>{{ publishedOn }}</span>
                        <span class="inline-flex items-center gap-1"><Eye class="h-3 w-3" /> {{ note.views }}</span>
                    </span>
                </div>

                <h1 class="mt-5 font-serif text-[clamp(2rem,6vw,4.25rem)] leading-[0.95] font-normal tracking-tight text-foreground sm:mt-6">
                    {{ note.title }}
                </h1>

                <p class="mt-5 max-w-2xl text-sm leading-relaxed text-muted-foreground sm:text-[15px] md:text-base">
                    {{ note.description }}
                </p>

                <div v-if="note.tags && note.tags.length" class="mt-6 flex flex-wrap gap-1.5">
                    <span v-for="tag in note.tags" :key="tag" class="tag-chip">{{ tag }}</span>
                </div>
            </article>

            <!-- Content -->
            <div v-if="note.content" class="mt-4 space-y-4 sm:mt-5 sm:space-y-5">
                <article
                    v-if="note.content.overview"
                    class="card-3d reveal rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-[1.5rem] sm:p-8"
                    style="--d: 220ms"
                >
                    <span class="section-eyebrow">— Overview</span>
                    <p class="mt-4 text-sm leading-[1.8] whitespace-pre-line text-muted-foreground sm:text-[15px] md:text-base">
                        {{ note.content.overview }}
                    </p>
                </article>

                <article
                    v-if="note.content.requirements && note.content.requirements.length"
                    class="card-3d reveal rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-[1.5rem] sm:p-8"
                    style="--d: 300ms"
                >
                    <span class="section-eyebrow">— Requirements</span>
                    <ul class="mt-4 space-y-2">
                        <li
                            v-for="req in note.content.requirements"
                            :key="req"
                            class="flex items-start gap-3 text-sm leading-relaxed text-muted-foreground sm:text-[15px]"
                        >
                            <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-foreground/50" aria-hidden="true"></span>
                            <span>{{ req }}</span>
                        </li>
                    </ul>
                </article>

                <article
                    v-if="note.content.steps && note.content.steps.length"
                    class="card-3d reveal rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-[1.5rem] sm:p-8"
                    style="--d: 380ms"
                >
                    <span class="section-eyebrow">— Steps</span>
                    <ol class="mt-4 divide-y divide-border/60">
                        <li v-for="(step, stepIndex) in note.content.steps" :key="stepIndex" class="step-row">
                            <div class="step-row-head">
                                <span class="step-row-number" aria-hidden="true">{{ String(stepIndex + 1).padStart(2, '0') }}</span>
                                <div class="min-w-0 flex-1">
                                    <h2 class="step-row-title">{{ step.title }}</h2>
                                    <p class="mt-1.5 text-sm leading-relaxed text-muted-foreground">{{ step.description }}</p>
                                </div>
                            </div>
                            <div v-if="step.commands && step.commands.length" class="mt-4 space-y-2 sm:ml-14">
                                <div v-for="(command, commandIndex) in step.commands" :key="commandIndex" class="code-block">
                                    <span class="code-prompt" aria-hidden="true">$</span>
                                    <code class="code-content">{{ command }}</code>
                                    <button
                                        type="button"
                                        class="code-copy"
                                        :aria-label="copied === `${stepIndex}-${commandIndex}` ? 'Copied' : 'Copy command'"
                                        @click="copyCommand(command, `${stepIndex}-${commandIndex}`)"
                                    >
                                        <Check v-if="copied === `${stepIndex}-${commandIndex}`" class="h-3.5 w-3.5 text-emerald-500" />
                                        <Copy v-else class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ol>
                </article>
            </div>

            <!-- Related -->
            <section v-if="related.length" class="reveal mt-8 sm:mt-10" style="--d: 460ms" aria-labelledby="related-heading">
                <h2 id="related-heading" class="section-eyebrow">— More in {{ note.category }}</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    <Link
                        v-for="item in related"
                        :key="item.id"
                        :href="route('note.show', item.slug)"
                        class="card-3d group flex flex-col rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl"
                    >
                        <span class="font-serif text-xl leading-tight text-foreground">{{ item.title }}</span>
                        <span class="mt-2 line-clamp-2 flex-1 text-sm text-muted-foreground">{{ item.description }}</span>
                        <span
                            class="mt-4 inline-flex items-center gap-1.5 font-mono text-[10px] tracking-[0.22em] text-muted-foreground/70 uppercase"
                        >
                            Read entry
                            <ArrowUpRight class="h-3 w-3 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                        </span>
                    </Link>
                </div>
            </section>

            <div
                class="reveal mt-10 flex flex-col items-start justify-between gap-1.5 font-mono text-[9px] tracking-[0.2em] text-muted-foreground/60 uppercase sm:mt-14 sm:flex-row sm:items-center sm:text-[10px] md:text-xs"
                style="--d: 540ms"
            >
                <span>© {{ currentYear }} · Entry · {{ note.category }}</span>
                <Link :href="route('note')" class="transition-colors hover:text-foreground">Back to the notebook →</Link>
            </div>
        </section>
    </FrontendLayout>
</template>

<style scoped>
.reveal {
    opacity: 0;
    translate: 0 18px;
}
.is-visible .reveal {
    animation: revealUp 0.9s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    animation-delay: var(--d, 0ms);
}
@keyframes revealUp {
    to {
        opacity: 1;
        translate: 0 0;
    }
}
h1,
h2,
.font-serif {
    font-family: 'Instrument Serif', 'Iowan Old Style', 'Apple Garamond', Georgia, serif;
    font-feature-settings: 'ss01', 'liga';
}
.font-mono,
.section-eyebrow,
.tag-chip,
.code-prompt,
.step-row-number {
    font-family: 'JetBrains Mono', ui-monospace, 'SFMono-Regular', Menlo, monospace;
}
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
}
.section-eyebrow {
    display: block;
    font-size: 10px;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    color: var(--color-muted-foreground);
}
.tag-chip {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.6rem;
    border-radius: 9999px;
    border: 1px solid var(--color-border);
    background: color-mix(in oklab, var(--color-muted) 30%, transparent);
    font-size: 10px;
    letter-spacing: 0.04em;
    color: var(--color-foreground);
}
.step-row {
    padding: 1.25rem 0;
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
    font-size: 11px;
    font-weight: 600;
}
.step-row-title {
    font-size: 1.5rem;
    line-height: 1.15;
    letter-spacing: -0.01em;
    color: var(--color-foreground);
}
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
}
.code-prompt {
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
    color: var(--color-muted-foreground);
    cursor: pointer;
    transition:
        background-color 0.2s ease,
        border-color 0.2s ease,
        color 0.2s ease;
}
.code-copy:hover,
.code-copy:focus-visible {
    background: color-mix(in oklab, var(--color-foreground) 8%, transparent);
    border-color: var(--color-border);
    color: var(--color-foreground);
}
@media (prefers-reduced-motion: reduce) {
    .reveal,
    .is-visible .reveal {
        opacity: 1;
        translate: none;
        animation: none;
    }
    .ambient-blob {
        transition: none;
    }
}
</style>
