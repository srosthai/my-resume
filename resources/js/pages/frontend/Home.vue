<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton';
import { usePageReveal } from '@/composables/usePageReveal';
import { usePhnomPenhClock } from '@/composables/usePhnomPenhClock';
import { usePointerGlow } from '@/composables/usePointerGlow';
import FrontendLayout from '@/layouts/FrontendLayout.vue';
import type { PublicOwner, TechStack } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowUpRight, Book, Github, Linkedin, Mail, Rss, Sparkles } from 'lucide-vue-next';
import type { Component } from 'vue';
import { computed, onBeforeUnmount, ref } from 'vue';

interface HomeStats {
    projects: number;
    techStacks: number;
    experience: number;
    notes: number;
}

const props = withDefaults(
    defineProps<{
        users: PublicOwner;
        techStacks?: TechStack[];
        stats?: HomeStats;
        title?: string;
        description?: string;
    }>(),
    {
        techStacks: () => [],
        stats: () => ({ projects: 0, techStacks: 0, experience: 0, notes: 0 }),
        title: 'Home',
        description: 'Welcome to my portfolio',
    },
);

const { isLoading, isVisible } = usePageReveal();
const { time: timeInPhnomPenh } = usePhnomPenhClock();
const { pointer } = usePointerGlow();

const fullName = computed(() => (props.users?.name || 'SROS THAI').trim());

/** Position split into stencil lines, e.g. "Full Stack Developer" -> ["FULL STACK", "DEVELOPER"]. */
const positionLines = computed(() => {
    const words = (props.users?.position || 'Full Stack Developer')
        .trim()
        .split(/\s+/)
        .map((word) => word.toUpperCase());

    if (words.length < 2) return [words[0] ?? 'DEVELOPER'];
    return [words.slice(0, -1).join(' '), words[words.length - 1]];
});

const baseSrc = '/me-man-cutout.png';
const spotSrc = '/man-aligned.png';
const showingAlt = ref(false);

const frameEl = ref<HTMLElement | null>(null);
let spotRaf = 0;
let lastWasTouch = false;
const spotTarget = { x: 0, y: 0 };
const spotCurrent = { x: 0, y: 0 };
let spotActive = false;

/** Spotlight reveal: the second image shows through a mask that glides toward the pointer. */
const renderSpotlight = () => {
    const el = frameEl.value;
    if (!el) {
        spotRaf = 0;
        return;
    }

    spotCurrent.x += (spotTarget.x - spotCurrent.x) * 0.22;
    spotCurrent.y += (spotTarget.y - spotCurrent.y) * 0.22;
    el.style.setProperty('--spot-x', `${spotCurrent.x}px`);
    el.style.setProperty('--spot-y', `${spotCurrent.y}px`);

    const settled = Math.abs(spotTarget.x - spotCurrent.x) < 0.4 && Math.abs(spotTarget.y - spotCurrent.y) < 0.4;
    spotRaf = spotActive || !settled ? requestAnimationFrame(renderSpotlight) : 0;
};

const moveSpotlight = (clientX: number, clientY: number) => {
    const el = frameEl.value;
    if (!el) return;
    const rect = el.getBoundingClientRect();
    spotTarget.x = clientX - rect.left;
    spotTarget.y = clientY - rect.top;
    spotActive = true;
    el.classList.add('is-spotting');
    if (!spotRaf) spotRaf = requestAnimationFrame(renderSpotlight);
};

const handleSpotPointer = (event: PointerEvent) => {
    lastWasTouch = false;
    moveSpotlight(event.clientX, event.clientY);
};

const handleSpotTouch = (event: TouchEvent) => {
    const touch = event.touches[0];
    if (!touch) return;
    lastWasTouch = true;
    moveSpotlight(touch.clientX, touch.clientY);
};

const hideSpotlight = (event: PointerEvent) => {
    const el = frameEl.value;
    if (!el) return;

    // Chromium can fire pointerleave while the pointer is still over the frame
    // (compositing/hit-test quirks); ignore those, only collapse for a real exit.
    const rect = el.getBoundingClientRect();
    const stillInside =
        event.clientX >= rect.left && event.clientX <= rect.right && event.clientY >= rect.top && event.clientY <= rect.bottom;
    if (stillInside) return;

    spotActive = false;
    el.classList.remove('is-spotting');
};

const toggleAura = () => {
    showingAlt.value = !showingAlt.value;
};

const handleFrameClick = () => {
    // Touch drags end with a synthetic click; ignore it so the spotlight stays a drag gesture.
    if (lastWasTouch) {
        lastWasTouch = false;
        return;
    }
    toggleAura();
};

onBeforeUnmount(() => {
    if (spotRaf) cancelAnimationFrame(spotRaf);
});

const stackSummary = computed(() => {
    const names = (props.techStacks || [])
        .slice(0, 3)
        .map((tech) => tech.name)
        .filter(Boolean);
    return names.length ? names.join(' · ') : 'Laravel · Vue · TypeScript';
});

const specs = computed(() => [
    { label: 'Stack', value: stackSummary.value },
    { label: 'Base', value: 'Phnom Penh, KH' },
    { label: 'Local time', value: timeInPhnomPenh.value, live: true },
    { label: 'Status', value: 'Available for work' },
]);

const deckStats = computed(() => [
    { value: props.stats.projects, label: 'Projects' },
    { value: props.stats.techStacks, label: 'Stacks' },
    { value: props.stats.experience, label: 'Work entries' },
    { value: props.stats.notes, label: 'Notes' },
]);

interface QuickLink {
    label: string;
    href: string;
    icon: Component;
}

const quickLinks: QuickLink[] = [
    { label: 'See projects', href: '/portfolio', icon: ArrowUpRight },
    { label: 'My feeds', href: '/feeds', icon: Rss },
    { label: 'Notes', href: '/note', icon: Book },
    { label: 'Contact', href: '/contact', icon: Mail },
];
</script>

<template>
    <FrontendLayout currentRoute="/">
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta name="keywords" content="software developer, portfolio, Vue.js, Laravel, full stack developer, SROS THAI, Cambodia" />
            <meta name="author" :content="users.name || 'SROS THAI'" />
            <meta property="og:title" :content="title" />
            <meta property="og:description" :content="description" />
            <meta property="og:type" content="website" />
        </Head>

        <!-- Skeleton: same silhouette as the stage so nothing shifts on load -->
        <section v-if="isLoading" aria-busy="true" aria-hidden="true" class="stage-skeleton">
            <div class="stage-skeleton-grid">
                <Skeleton class="col-span-12 h-6 w-64" />
                <Skeleton class="col-span-12 h-44 md:col-span-6 md:h-72" />
                <Skeleton class="col-span-12 h-72 md:col-span-6 md:h-96" />
                <Skeleton class="col-span-12 h-40 md:col-span-8 md:h-56" />
            </div>
        </section>

        <section v-else id="home" class="stage" :class="{ 'is-visible': isVisible }">
            <!-- Field wash + pointer bloom -->
            <div class="stage-field" aria-hidden="true"></div>
            <div class="stage-bloom" :style="{ left: pointer.x + '%', top: pointer.y + '%' }" aria-hidden="true"></div>

            <div class="stage-hero">
                <div class="stage-grid">
                    <!-- Stencil name block -->
                    <div class="stage-intro">
                        <h1 class="stage-name">
                            <span class="reveal" style="--d: 100ms">
                                {{ fullName }}<span class="stage-slash"> //</span>
                            </span>
                            <span v-for="line in positionLines" :key="line" class="reveal block">{{ line }}</span>
                        </h1>
                        <p class="stage-desc reveal" style="--d: 480ms">
                            {{ users.description }}
                        </p>
                        <ul class="stage-controls reveal" style="--d: 560ms">
                            <li v-for="item in quickLinks" :key="item.label">
                                <Link :href="item.href" class="stage-icon-btn" :aria-label="item.label" :title="item.label">
                                    <component :is="item.icon" class="h-4 w-4" aria-hidden="true" />
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Cut-out portrait: me-1 sits on top; hover or the switch button reveals me-2 -->
                    <div class="stage-figure reveal" style="--d: 180ms">
                        <div
                            ref="frameEl"
                            class="figure-frame"
                            :class="{ 'is-flipped': showingAlt }"
                            @pointermove="handleSpotPointer"
                            @pointerleave="hideSpotlight"
                            @pointercancel="hideSpotlight"
                            @touchmove.passive="handleSpotTouch"
                            @click="handleFrameClick"
                        >
                            <div class="figure-bloom" aria-hidden="true"></div>
                            <img
                                :src="baseSrc"
                                :alt="`Portrait of ${users.name}`"
                                width="1002"
                                height="996"
                                fetchpriority="high"
                                decoding="async"
                                class="figure-img"
                            />
                            <div class="figure-spot-layer" aria-hidden="true">
                                <img
                                    :src="spotSrc"
                                    alt=""
                                    width="1002"
                                    height="996"
                                    loading="lazy"
                                    decoding="async"
                                    class="figure-img-spot"
                                />
                            </div>
                        </div>
                        <div class="figure-ticker">
                            <span class="figure-ticker-name">
                                {{ fullName }}
                                <span aria-hidden="true">'26</span>
                            </span>
                            <button
                                type="button"
                                class="figure-switch"
                                :aria-pressed="showingAlt"
                                @click="toggleAura"
                            >
                                <span class="figure-switch-icon" aria-hidden="true">
                                    <Sparkles class="h-4 w-4" />
                                </span>
                                <span class="figure-switch-text">
                                    <span class="figure-switch-label">{{ showingAlt ? 'Dev on' : 'Show dev' }}</span>
                                    <span class="figure-switch-sub">Switch to dev</span>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Right rail: spec table + credential card -->
                    <div class="stage-side">
                        <aside class="stage-specs reveal" style="--d: 620ms" aria-label="Developer specs">
                        <div class="specs-head">
                            <span>Developer specs</span>
                            <span class="specs-verified">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400" aria-hidden="true"></span>
                                Verified
                            </span>
                        </div>
                        <dl class="specs-list">
                            <div v-for="row in specs" :key="row.label" class="spec-row">
                                <dt>{{ row.label }}</dt>
                                <dd :class="{ 'is-live': row.live }">
                                    <span v-if="row.live" class="spec-live-dot" aria-hidden="true"></span>
                                    {{ row.value }}
                                </dd>
                            </div>
                        </dl>
                    </aside>

                    <!-- Credential: actions row (identity lives in the headline) -->
                    <div class="stage-cred reveal" style="--d: 700ms">
                        <Link href="/portfolio" class="stage-cta">
                            <span>See projects</span>
                            <ArrowUpRight class="h-4 w-4" aria-hidden="true" />
                        </Link>
                        <Link href="/contact" class="card-link">Get in touch</Link>
                    </div>
                    </div>
                </div>
            </div>

            <!-- Data deck -->
            <div class="deck">
                <div class="deck-inner">
                    <div class="deck-stats">
                        <div v-for="stat in deckStats" :key="stat.label" class="stat-cell reveal">
                            <span class="stat-value">{{ stat.value }}</span>
                            <span class="stat-key">{{ stat.label }}</span>
                        </div>
                    </div>

                    <div class="deck-grid">
                        <section class="deck-links reveal" style="--d: 900ms" aria-labelledby="elsewhere-title">
                            <h2 id="elsewhere-title" class="deck-title">Elsewhere</h2>
                            <ul>
                                <li>
                                    <a href="https://github.com/srosthai" target="_blank" rel="noopener noreferrer" class="link-row">
                                        <span class="link-label"><Github class="h-4 w-4" aria-hidden="true" />GitHub</span>
                                        <ArrowUpRight class="link-arrow h-4 w-4" aria-hidden="true" />
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="https://www.linkedin.com/in/sros-thai-b491b42ab/"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="link-row"
                                    >
                                        <span class="link-label"><Linkedin class="h-4 w-4" aria-hidden="true" />LinkedIn</span>
                                        <ArrowUpRight class="link-arrow h-4 w-4" aria-hidden="true" />
                                    </a>
                                </li>
                                <li>
                                    <Link href="/contact" class="link-row">
                                        <span class="link-label"><Mail class="h-4 w-4" aria-hidden="true" />Contact</span>
                                        <ArrowUpRight class="link-arrow h-4 w-4" aria-hidden="true" />
                                    </Link>
                                </li>
                                <li>
                                    <Link href="/note" class="link-row">
                                        <span class="link-label"><Book class="h-4 w-4" aria-hidden="true" />Notes</span>
                                        <ArrowUpRight class="link-arrow h-4 w-4" aria-hidden="true" />
                                    </Link>
                                </li>
                            </ul>
                        </section>

                        <section v-if="techStacks.length" class="deck-drivers reveal" style="--d: 960ms" aria-labelledby="drivers-title">
                            <div class="drivers-head">
                                <h2 id="drivers-title" class="deck-title">Daily drivers</h2>
                            </div>
                            <ul class="drivers-grid">
                                <li v-for="tech in techStacks" :key="tech.id" class="driver-chip" :title="tech.name ?? undefined">
                                    <img
                                        v-if="tech.logo"
                                        :src="tech.logo"
                                        :alt="tech.name ?? undefined"
                                        width="18"
                                        height="18"
                                        class="driver-logo"
                                        loading="lazy"
                                        decoding="async"
                                    />
                                    <span v-else class="driver-badge">{{ tech.name?.charAt(0) }}</span>
                                    <span class="driver-name">{{ tech.name }}</span>
                                </li>
                            </ul>
                        </section>
                    </div>

                    <div class="deck-foot reveal" style="--d: 1020ms">
                        <span>© {{ new Date().getFullYear() }} {{ users.name }}</span>
                        <span>Crafted in Cambodia</span>
                        <span class="deck-foot-hint">Scroll to explore ↓</span>
                    </div>
                </div>
            </div>
        </section>
    </FrontendLayout>
</template>

<style scoped>
/* ============================================================
   Home stage — the reference composition in the site's own
   palette. Cut-out portrait, HUD spec table, quiet deck; every
   color resolves from the theme tokens (light and dark).
   ============================================================ */

.stage {
    /* Stage tokens map to the site theme so home wears the same colors as
       every other page, in both light and dark. */
    --rx-ink: var(--color-foreground);
    --rx-ink-dim: var(--color-muted-foreground);
    --rx-ink-soft: color-mix(in oklab, var(--color-muted-foreground) 58%, transparent);
    --rx-rule: var(--color-border);
    --rx-rule-soft: color-mix(in oklab, var(--color-border) 65%, transparent);
    --rx-rule-strong: color-mix(in oklab, var(--color-foreground) 28%, transparent);
    --rx-solid: var(--color-foreground);
    --rx-solid-hover: color-mix(in oklab, var(--color-foreground) 85%, transparent);
    --rx-on-solid: var(--color-background);
    --rx-ease: cubic-bezier(0.16, 1, 0.3, 1);

    position: relative;
    color: var(--rx-ink);
    overflow: clip;
}

.stage :where(a, button):focus-visible {
    outline: 2px solid var(--accent-ink);
    outline-offset: 3px;
}

/* ------------------------------------------------------------
   Field layers
   ------------------------------------------------------------ */

.stage-field {
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    background: radial-gradient(120% 100% at 62% 28%, color-mix(in oklab, var(--color-foreground) 5%, transparent), transparent 62%);
}

.stage-bloom {
    position: absolute;
    z-index: 0;
    width: 42rem;
    height: 42rem;
    max-width: 110vw;
    max-height: 110vw;
    border-radius: 9999px;
    transform: translate(-50%, -50%);
    background: radial-gradient(closest-side, color-mix(in oklab, var(--color-foreground) 5%, transparent), transparent 70%);
    filter: blur(48px);
    pointer-events: none;
    transition:
        left 900ms var(--rx-ease),
        top 900ms var(--rx-ease);
}

/* ------------------------------------------------------------
   Hero shell
   ------------------------------------------------------------ */

.stage-hero {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    min-height: 100svh;
    overflow: hidden;
    padding: 1.5rem 1.25rem 3rem;
}

@media (min-width: 768px) {
    .stage-hero {
        min-height: calc(100svh - 5.75rem);
        padding: 4.5rem 2.5rem 2.5rem;
    }
}

.stage-grid {
    position: relative;
    display: grid;
    grid-template-columns: repeat(12, minmax(0, 1fr));
    column-gap: 1rem;
    row-gap: 1.5rem;
    width: 100%;
    max-width: 84rem;
    margin-inline: auto;
}

/* Mobile flow: name, portrait, copy, right rail */
.stage-intro,
.stage-figure,
.stage-side,
.stage-name,
.stage-desc,
.stage-controls {
    grid-column: 1 / -1;
}

.stage-side {
    display: grid;
    gap: 1.5rem;
}

@media (max-width: 1023.98px) {
    .stage-intro {
        display: contents;
    }

    .stage-name {
        order: 2;
    }
    .stage-figure {
        order: 3;
    }
    .stage-desc {
        order: 4;
        margin-top: 0;
    }
    .stage-controls {
        order: 5;
        margin-top: 0;
    }
    .stage-side {
        order: 6;
    }
}

@media (min-width: 1024px) {
    .stage-grid {
        grid-template-rows: auto 1fr auto;
        column-gap: 2rem;
        row-gap: 2rem;
        min-height: calc(100svh - 12.75rem);
    }

    .stage-intro {
        grid-column: 1 / 7;
        grid-row: 2;
        align-self: start;
        padding-top: 1rem;
    }
    .stage-side {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        grid-column: 9 / 13;
        grid-row: 2 / 4;
        align-self: start;
    }

    .stage-intro,
    .stage-side {
        position: relative;
        z-index: 2;
    }
}

/* ------------------------------------------------------------
   Name, copy, controls
   ------------------------------------------------------------ */

.stage-name {
    font-family: 'Michroma', 'Chakra Petch', sans-serif;
    font-weight: 400;
    font-size: clamp(1.5rem, 4.2vw, 3.4rem);
    line-height: 1.04;
    letter-spacing: 0.01em;
    text-transform: uppercase;
    color: var(--rx-ink);
}

.stage-name > span {
    display: block;
    white-space: nowrap;
}

.stage-name > span:nth-child(1) {
    --d: 100ms;
}
.stage-name > span:nth-child(2) {
    --d: 240ms;
}
.stage-name > span:nth-child(3) {
    --d: 360ms;
}

.stage-slash {
    color: var(--accent-ink);
}

.stage-desc {
    margin-top: 1.25rem;
    max-width: 34ch;
    font-family: 'Chakra Petch', 'Instrument Sans', sans-serif;
    font-weight: 300;
    font-size: 1rem;
    line-height: 1.7;
    color: var(--rx-ink-dim);
}

.stage-controls {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    margin-top: 1.5rem;
    list-style: none;
}

.stage-icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.75rem;
    height: 2.75rem;
    border: 1px solid var(--rx-rule-strong);
    border-radius: 9999px;
    background: color-mix(in oklab, var(--color-card) 65%, transparent);
    color: var(--rx-ink);
    transition:
        background-color 0.2s ease,
        border-color 0.2s ease,
        color 0.2s ease;
}

@media (hover: hover) and (pointer: fine) {
    .stage-icon-btn:hover {
        background: var(--rx-solid);
        border-color: var(--rx-solid);
        color: var(--rx-on-solid);
    }
}

/* ------------------------------------------------------------
   Portrait
   ------------------------------------------------------------ */

.stage-figure {
    width: min(88vw, 26rem);
    margin-inline: auto;
}

@media (min-width: 1024px) {
    .stage-figure {
        position: absolute;
        bottom: 2rem;
        left: 50%;
        z-index: 1;
        width: min(52vw, 42.5rem, calc(100svh - 15rem));
        margin-inline: 0;
        translate: -50% 0;
    }
}

.figure-frame {
    position: relative;
    cursor: pointer;
}

.figure-bloom {
    position: absolute;
    inset: 6% -10% 4%;
    z-index: 0;
    border-radius: 9999px;
    background: radial-gradient(closest-side, color-mix(in oklab, var(--color-foreground) 6%, transparent), transparent 72%);
    filter: blur(34px);
    pointer-events: none;
}

.figure-img {
    position: relative;
    z-index: 1;
    display: block;
    width: 100%;
    height: auto;
    filter: drop-shadow(0 22px 34px rgb(0 0 0 / 0.16));
    mask-image: linear-gradient(to bottom, #000 84%, transparent 100%);
    transition: opacity 0.45s var(--rx-ease);
}

/* Spotlight: the second portrait shows through a mask that follows the pointer */
.figure-spot-layer {
    position: absolute;
    inset: 0;
    z-index: 2;
    mask-image: linear-gradient(to bottom, #000 84%, transparent 100%);
    pointer-events: none;
}

.figure-img-spot {
    width: 100%;
    height: 100%;
    object-fit: contain;
    mask-image: radial-gradient(
        circle var(--spot-r, 0px) at var(--spot-x, 50%) var(--spot-y, 50%),
        #fff 0%,
        #fff 78%,
        rgba(255, 255, 255, 0.5) 90%,
        transparent 100%
    );
}

.figure-frame.is-spotting .figure-img-spot {
    --spot-r: clamp(64px, 8vw, 120px);
}

.figure-frame.is-flipped .figure-img {
    opacity: 0;
}

.figure-frame.is-flipped .figure-img-spot {
    mask-image: none;
    filter:
        drop-shadow(0 0 12px color-mix(in oklab, var(--accent-ink) 42%, transparent))
        drop-shadow(0 0 36px color-mix(in oklab, var(--accent-ink) 22%, transparent)) drop-shadow(0 22px 34px rgb(0 0 0 / 0.16));
}

.figure-ticker {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 2rem;
    margin-top: 0.75rem;
    padding-top: 0.6rem;
    border-top: 1px solid var(--rx-rule-soft);
    font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace;
    font-size: 10px;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--rx-ink-dim);
}

.figure-ticker-name {
    font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace;
    letter-spacing: 0.22em;
}

.figure-switch {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    text-align: left;
    color: var(--rx-ink);
    cursor: pointer;
}

.figure-switch-icon {
    display: grid;
    place-items: center;
    width: 2.4rem;
    height: 2.4rem;
    border: 1px solid var(--rx-rule-strong);
    border-radius: 9999px;
    transition:
        background-color 0.25s ease,
        border-color 0.25s ease,
        color 0.25s ease,
        box-shadow 0.25s ease;
}

.figure-switch-text {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.figure-switch-label {
    font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace;
    font-size: 9px;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--rx-ink);
}

.figure-switch-sub {
    font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace;
    font-size: 9px;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--rx-ink-soft);
}

@media (hover: hover) and (pointer: fine) {
    .figure-switch:hover .figure-switch-icon {
        border-color: var(--accent-ink);
        color: var(--accent-ink);
    }
}

.figure-switch[aria-pressed='true'] .figure-switch-icon {
    background: var(--accent-ink);
    border-color: var(--accent-ink);
    color: #fff;
    box-shadow: 0 0 16px 2px color-mix(in oklab, var(--accent-ink) 45%, transparent);
}

@media (min-width: 1024px) {
    .figure-ticker {
        justify-content: center;
    }

    /* The name is already the hero headline on desktop; keep only the control, centered */
    .figure-ticker-name {
        display: none;
    }
}

/* ------------------------------------------------------------
   Spec table
   ------------------------------------------------------------ */

.stage-specs {
    border: 1px solid var(--rx-rule);
    border-radius: 14px;
    background: color-mix(in oklab, var(--color-card) 75%, transparent);
    padding: 0.9rem 1rem 0.7rem;
    backdrop-filter: blur(10px);
}

.specs-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding-bottom: 0.6rem;
    border-bottom: 1px solid var(--rx-rule);
    font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace;
    font-size: 10px;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--rx-ink-dim);
}

.specs-verified {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    color: var(--rx-ink-soft);
}

.spec-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 1rem;
    padding: 0.55rem 0;
    border-bottom: 1px solid var(--rx-rule-soft);
}

.specs-list .spec-row:last-child {
    border-bottom: 0;
    padding-bottom: 0.15rem;
}

.spec-row dt {
    font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace;
    font-size: 10px;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--rx-ink-dim);
}

.spec-row dd {
    font-family: 'Chakra Petch', 'Instrument Sans', sans-serif;
    font-size: 0.875rem;
    text-align: right;
    color: var(--rx-ink);
}

.spec-row dd.is-live {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace;
    font-variant-numeric: tabular-nums;
}

.spec-live-dot {
    width: 6px;
    height: 6px;
    border-radius: 9999px;
    background: #34d399;
    box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.12);
}

/* ------------------------------------------------------------
   Credential card
   ------------------------------------------------------------ */

.stage-cred {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.75rem 1rem;
    padding-top: 1.1rem;
    border-top: 1px solid var(--rx-rule);
}

.stage-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border-radius: 9999px;
    background: var(--rx-solid);
    padding: 0.6rem 1.05rem;
    font-family: 'Chakra Petch', 'Instrument Sans', sans-serif;
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--rx-on-solid);
    transition: background-color 0.2s ease;
}

@media (hover: hover) and (pointer: fine) {
    .stage-cta:hover {
        background: var(--rx-solid-hover);
    }
}

.card-link {
    margin-left: auto;
    font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace;
    font-size: 10px;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--rx-ink-dim);
    text-decoration: underline;
    text-underline-offset: 4px;
    transition: color 0.2s ease;
}

@media (hover: hover) and (pointer: fine) {
    .card-link:hover {
        color: var(--rx-ink);
    }
}

/* ------------------------------------------------------------
   Skeleton
   ------------------------------------------------------------ */

.stage-skeleton {
    display: flex;
    align-items: center;
    min-height: 100svh;
    padding: 5.5rem 1.25rem 2rem;
}

.stage-skeleton-grid {
    display: grid;
    grid-template-columns: repeat(12, minmax(0, 1fr));
    gap: 1rem;
    width: 100%;
    max-width: 84rem;
    margin-inline: auto;
}

/* ------------------------------------------------------------
   Data deck
   ------------------------------------------------------------ */

.deck {
    position: relative;
    border-top: 1px solid var(--rx-rule);
}

.deck-inner {
    position: relative;
    width: 100%;
    max-width: 84rem;
    margin-inline: auto;
    padding: 2.5rem 1.25rem 2.25rem;
}

@media (min-width: 768px) {
    .deck-inner {
        padding: 3rem 2.5rem 2.5rem;
    }
}

.deck-stats {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    column-gap: 1rem;
}

@media (min-width: 768px) {
    .deck-stats {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        column-gap: 2rem;
    }
}

.stat-cell {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    padding: 1rem 0.15rem;
    border-top: 1px solid var(--rx-rule);
}

.deck-stats .stat-cell:nth-child(1) {
    --d: 760ms;
}
.deck-stats .stat-cell:nth-child(2) {
    --d: 820ms;
}
.deck-stats .stat-cell:nth-child(3) {
    --d: 880ms;
}
.deck-stats .stat-cell:nth-child(4) {
    --d: 940ms;
}

.stat-value {
    font-family: 'Michroma', 'Chakra Petch', sans-serif;
    font-size: clamp(1.6rem, 4.5vw, 2.35rem);
    line-height: 1;
    color: var(--rx-ink);
    font-variant-numeric: tabular-nums;
}

.stat-key {
    font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace;
    font-size: 10px;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--rx-ink-dim);
}

.deck-grid {
    display: grid;
    gap: 2.25rem;
    margin-top: 2.5rem;
}

@media (min-width: 900px) {
    .deck-grid {
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.15fr);
        gap: 4rem;
    }
}

.deck-title {
    font-family: 'Michroma', 'Chakra Petch', sans-serif;
    font-size: 0.72rem;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--rx-ink-dim);
}

.deck-links ul {
    margin-top: 0.5rem;
    border-top: 1px solid var(--rx-rule-soft);
    list-style: none;
}

.link-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    min-height: 3.1rem;
    padding: 0.75rem 0.15rem;
    border-bottom: 1px solid var(--rx-rule-soft);
    font-family: 'Chakra Petch', 'Instrument Sans', sans-serif;
    font-size: 0.95rem;
    color: var(--rx-ink);
    transition: color 0.2s ease;
}

.link-label {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
}

.link-arrow {
    color: var(--rx-ink-soft);
    transition:
        transform 0.2s ease,
        color 0.2s ease;
}

@media (hover: hover) and (pointer: fine) {
    .link-row:hover {
        color: var(--accent-ink);
    }

    .link-row:hover .link-arrow {
        transform: translate(2px, -2px);
        color: var(--accent-ink);
    }
}

.drivers-head {
    display: flex;
    align-items: baseline;
    gap: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--rx-rule-soft);
}

.deck-foot {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem 1.5rem;
    margin-top: 2.75rem;
    padding-top: 1.25rem;
    border-top: 1px solid var(--rx-rule);
    font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace;
    font-size: 10px;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--rx-ink-dim);
}

.deck-foot-hint {
    color: var(--rx-ink-soft);
}

/* ------------------------------------------------------------
   Daily drivers
   ------------------------------------------------------------ */

.drivers-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem 0.75rem;
    margin-top: 1rem;
    padding: 0;
    list-style: none;
}

.driver-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.9rem 0.4rem 0.5rem;
    border: 1px solid var(--rx-rule-soft);
    border-radius: 9999px;
    background: color-mix(in oklab, var(--color-muted) 55%, transparent);
    transition: border-color 0.2s ease;
}

@media (hover: hover) and (pointer: fine) {
    .driver-chip:hover {
        border-color: var(--rx-rule-strong);
    }
}

.driver-logo {
    width: 1.1rem;
    height: 1.1rem;
    object-fit: contain;
}

@media (min-width: 640px) {
    .driver-logo {
        width: 1.35rem;
        height: 1.35rem;
    }
}

.driver-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.35rem;
    height: 1.35rem;
    border-radius: 6px;
    background: color-mix(in oklab, var(--color-foreground) 8%, transparent);
    font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace;
    font-size: 10px;
    font-weight: 600;
    color: var(--rx-ink-dim);
}

.driver-name {
    font-family: 'JetBrains Mono', ui-monospace, Menlo, monospace;
    font-size: 0.72rem;
    letter-spacing: 0.04em;
    color: var(--rx-ink);
}

/* ------------------------------------------------------------
   The one authored moment: a single stage power-on
   ------------------------------------------------------------ */

.reveal {
    opacity: 0;
    transform: translateY(16px);
    filter: blur(8px);
}

.is-visible .reveal {
    animation: stage-in 900ms var(--rx-ease) forwards;
    animation-delay: var(--d, 0ms);
}

@keyframes stage-in {
    to {
        opacity: 1;
        transform: translateY(0);
        filter: blur(0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .reveal,
    .is-visible .reveal {
        opacity: 1;
        transform: none;
        filter: none;
        animation: none;
    }

    .stage-bloom {
        transition: none;
    }
}
</style>
