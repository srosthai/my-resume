<script setup lang="ts">
import { usePhnomPenhClock } from '@/composables/usePhnomPenhClock';
import { usePointerGlow } from '@/composables/usePointerGlow';
import FrontendLayout from '@/layouts/FrontendLayout.vue';
import { formatDate } from '@/lib/date';
import type { Feed } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
import { ArrowLeft, Eye, Heart, MapPin, Pin } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    title: string;
    description: string;
    feed: Feed;
}>();

const { date: dateString } = usePhnomPenhClock(60000);
const { pointer } = usePointerGlow();
const currentYear = new Date().getFullYear();

const likes = ref(props.feed.likes_count);
const liked = ref(props.feed.liked === true);
const busy = ref(false);

const toggleLike = async () => {
    if (busy.value) return;
    busy.value = true;
    try {
        const { data } = await axios.post<{ likes_count: number; liked: boolean }>(`/api/feeds/${props.feed.id}/like`);
        likes.value = data.likes_count;
        liked.value = data.liked;
    } catch {
        // Leave the counter as it was.
    } finally {
        busy.value = false;
    }
};

const publishedOn = formatDate(props.feed.published_at ?? props.feed.created_at, {
    weekday: 'short',
    month: 'long',
    day: 'numeric',
    year: 'numeric',
});
</script>

<template>
    <FrontendLayout current-route="/feeds">
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta property="og:title" :content="title" />
            <meta property="og:description" :content="description" />
            <meta property="og:type" content="article" />
            <meta v-if="feed.images && feed.images.length" property="og:image" :content="feed.images[0]" />
        </Head>

        <section class="is-visible relative mx-auto w-full max-w-3xl px-3 py-6 sm:px-6 sm:py-8 lg:px-10">
            <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
                <div class="ambient-blob" :style="{ left: pointer.x + '%', top: pointer.y + '%' }"></div>
            </div>

            <div
                class="reveal mb-4 flex flex-wrap items-center justify-between gap-x-4 gap-y-2 font-mono text-[9px] tracking-[0.22em] text-muted-foreground/70 uppercase sm:mb-5 sm:text-[10px] md:text-xs"
                style="--d: 0ms"
            >
                <span class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-foreground/40"></span>
                    Feed entry
                </span>
                <span class="tabular-nums">{{ dateString }}</span>
            </div>

            <nav
                class="reveal mb-5 flex items-center gap-2 font-mono text-[10px] tracking-[0.22em] text-muted-foreground uppercase sm:mb-6 sm:text-[11px]"
                style="--d: 60ms"
                aria-label="Breadcrumb"
            >
                <Link :href="route('feeds')" class="inline-flex items-center gap-1.5 transition-colors hover:text-foreground">
                    <ArrowLeft class="h-3 w-3" />
                    <span>Feeds</span>
                </Link>
                <span class="text-border">/</span>
                <span class="truncate text-foreground/80">{{ feed.title || publishedOn }}</span>
            </nav>

            <article class="card-3d reveal overflow-hidden rounded-[1.5rem] border border-border/60 bg-card/60 backdrop-blur-xl" style="--d: 140ms">
                <header class="px-5 pt-5 sm:px-8 sm:pt-8">
                    <div
                        class="flex flex-wrap items-center justify-between gap-3 font-mono text-[9px] tracking-[0.25em] text-muted-foreground uppercase sm:text-[10px] md:text-xs"
                    >
                        <span class="inline-flex items-center gap-2">
                            <span class="h-px w-5 bg-foreground/40 sm:w-6"></span>
                            {{ feed.activity_type || 'Moment' }}
                            <span v-if="feed.mood" class="tracking-normal normal-case">· {{ feed.mood }}</span>
                        </span>
                        <span class="inline-flex items-center gap-3">
                            <span v-if="feed.is_pinned" class="inline-flex items-center gap-1"><Pin class="h-3 w-3" /> Pinned</span>
                            <time :datetime="feed.published_at ?? undefined" class="tabular-nums">{{ publishedOn }}</time>
                        </span>
                    </div>

                    <h1
                        v-if="feed.title"
                        class="mt-5 font-serif text-[clamp(1.9rem,5.5vw,3.5rem)] leading-[0.98] font-normal tracking-tight text-foreground sm:mt-6"
                    >
                        {{ feed.title }}
                    </h1>

                    <p class="mt-5 text-sm leading-[1.8] whitespace-pre-line text-muted-foreground sm:text-[15px] md:text-base">
                        {{ feed.body }}
                    </p>

                    <p
                        v-if="feed.location"
                        class="mt-4 inline-flex items-center gap-1.5 font-mono text-[10px] tracking-[0.2em] text-muted-foreground uppercase"
                    >
                        <MapPin class="h-3 w-3" /> {{ feed.location }}
                    </p>
                </header>

                <div
                    v-if="feed.images && feed.images.length"
                    class="mt-5 grid gap-1 px-5 sm:px-8"
                    :class="feed.images.length > 1 ? 'sm:grid-cols-2' : ''"
                >
                    <a
                        v-for="(img, idx) in feed.images"
                        :key="img"
                        :href="img"
                        target="_blank"
                        rel="noopener"
                        class="block overflow-hidden rounded-2xl bg-muted/30"
                        :class="idx === 0 && feed.images.length > 1 ? 'sm:col-span-2' : ''"
                    >
                        <img
                            :src="img"
                            :alt="feed.title ? `${feed.title} — photo ${idx + 1}` : `Photo ${idx + 1}`"
                            width="1200"
                            height="800"
                            class="h-auto w-full object-cover"
                            :loading="idx === 0 ? 'eager' : 'lazy'"
                            decoding="async"
                        />
                    </a>
                </div>

                <footer class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-border/60 px-5 py-4 sm:px-8">
                    <div v-if="feed.tags && feed.tags.length" class="flex flex-wrap gap-1.5">
                        <span v-for="tag in feed.tags" :key="tag" class="tag-chip">#{{ tag }}</span>
                    </div>
                    <div class="ml-auto flex items-center gap-4 font-mono text-[10px] tracking-[0.2em] text-muted-foreground uppercase">
                        <span class="inline-flex items-center gap-1.5"><Eye class="h-3.5 w-3.5" /> {{ feed.views }}</span>
                        <button
                            type="button"
                            class="btn-3d inline-flex items-center gap-1.5 rounded-full bg-background px-3 py-1.5 text-foreground"
                            :aria-pressed="liked"
                            :aria-label="liked ? 'Unlike this entry' : 'Like this entry'"
                            :disabled="busy"
                            @click="toggleLike"
                        >
                            <Heart class="h-3.5 w-3.5" :class="liked ? 'fill-current' : ''" />
                            {{ likes }}
                        </button>
                    </div>
                </footer>
            </article>

            <div
                class="reveal mt-10 flex flex-col items-start justify-between gap-1.5 font-mono text-[9px] tracking-[0.2em] text-muted-foreground/60 uppercase sm:mt-14 sm:flex-row sm:items-center sm:text-[10px] md:text-xs"
                style="--d: 260ms"
            >
                <span>© {{ currentYear }} · Feed</span>
                <Link :href="route('feeds')" class="transition-colors hover:text-foreground">All entries →</Link>
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
.font-serif {
    font-family: 'Instrument Serif', 'Iowan Old Style', 'Apple Garamond', Georgia, serif;
    font-feature-settings: 'ss01', 'liga';
}
.font-mono,
.tag-chip {
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
.tag-chip {
    display: inline-flex;
    align-items: center;
    padding: 0.2rem 0.55rem;
    border-radius: 9999px;
    border: 1px solid var(--color-border);
    background: color-mix(in oklab, var(--color-muted) 30%, transparent);
    font-size: 10px;
    letter-spacing: 0.04em;
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
