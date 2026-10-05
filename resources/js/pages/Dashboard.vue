<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDate as formatSharedDate } from '@/lib/date';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowUpRight,
    CalendarDays,
    Code2,
    ExternalLink,
    BookOpen,
    FolderOpenDot,
    GraduationCap,
    Layers3,
    Music4,
    Rss,
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    summary: {
        aboutMe: any;
        user: any;
        projects: {
            total: number;
            byType: any[];
            recent: any[];
        };
        techStacks: {
            byType: Record<string, number>;
            total: number;
        };
        workExperience: {
            total: number;
            recent: any[];
        };
        education: {
            total: number;
            latest: any;
        };
        songs: {
            total: number;
            recent: any[];
        };
        notes: {
            total: number;
            published: number;
            recent: any[];
        };
        feeds: {
            total: number;
            published: number;
            recent: any[];
        };
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
];

const formatDate = (date?: string | null) => formatSharedDate(date, { month: 'short', day: undefined });

/** Career periods are years. An empty end means the period is still current. */
const periodLabel = (from?: string | number | null, to?: string | number | null) => {
    const start = String(from ?? '').trim();
    const end = String(to ?? '').trim();

    if (!start && !end) return 'Present';

    return `${start || '—'} — ${end || 'Present'}`;
};

const formatDuration = (song: any) => {
    if (song.formatted_duration) return song.formatted_duration;

    const duration = Number(song.duration || 0);
    const minutes = Math.floor(duration / 60);
    const seconds = String(duration % 60).padStart(2, '0');

    return `${minutes}:${seconds}`;
};

const experienceLabel = computed(() => {
    // year_experience is free text ("5+ Years"); only bare numbers get a suffix.
    const raw = String(props.summary.aboutMe?.year_experience ?? '').trim();
    if (!raw) return '0+ years';
    return /[a-z]/i.test(raw) ? raw : `${raw}+ years`;
});
const focusLabel = computed(() => props.summary.aboutMe?.focus_on || 'development');

// One denominator for every ring so the shares stay comparable.
const contentTotal = computed(
    () => props.summary.projects.total + props.summary.techStacks.total + props.summary.workExperience.total + props.summary.education.total + props.summary.songs.total,
);

const shareOf = (value: number) => (contentTotal.value ? Math.round((value / contentTotal.value) * 100) : 0);

const statCards = computed(() => [
    {
        title: 'Projects',
        value: props.summary.projects.total,
        caption: `${props.summary.projects.byType.length} categories tracked`,
        tag: 'Portfolio',
        icon: FolderOpenDot,
    },
    {
        title: 'Tech stack',
        value: props.summary.techStacks.total,
        caption: 'Grouped by discipline',
        tag: 'Skills',
        icon: Code2,
    },
    {
        title: 'Experience',
        value: props.summary.workExperience.total,
        caption: `${props.summary.education.total} education records`,
        tag: 'Career',
        icon: CalendarDays,
    },
    {
        title: 'Popular songs',
        value: props.summary.songs.total,
        caption: 'Music collection',
        tag: 'Archive',
        icon: Music4,
    },
]);

const ringCards = computed(() => [
    {
        label: 'Projects',
        share: shareOf(props.summary.projects.total),
        caption: `${props.summary.projects.total} of ${contentTotal.value} records`,
    },
    {
        label: 'Tech stack',
        share: shareOf(props.summary.techStacks.total),
        caption: `${props.summary.techStacks.total} of ${contentTotal.value} records`,
    },
    {
        label: 'Music',
        share: shareOf(props.summary.songs.total),
        caption: `${props.summary.songs.total} of ${contentTotal.value} records`,
    },
]);

const techStackRows = computed(() =>
    Object.entries(props.summary.techStacks.byType || {}).map(([type, count]) => ({
        type,
        count,
        width: props.summary.techStacks.total ? Math.max(6, Math.round((Number(count) / props.summary.techStacks.total) * 100)) : 0,
    })),
);

// Fixed slice palette: ordered so the largest category always reads greenest.
const mixPalette = ['hsl(158 58% 34%)', 'hsl(250 44% 58%)', 'hsl(42 72% 52%)', 'hsl(8 62% 60%)', 'hsl(196 56% 46%)'];

const projectMix = computed(() => {
    const total = props.summary.projects.total || 1;

    const rows = props.summary.projects.byType.slice(0, 5).map((type, index) => ({
        id: type.id,
        name: type.name,
        count: Number(type.projects_count),
        share: Math.round((Number(type.projects_count) / total) * 100),
        color: mixPalette[index % mixPalette.length],
    }));

    const gradient =
        rows.length === 0
            ? 'conic-gradient(var(--ring-track) 0 100%)'
            : `conic-gradient(${rows
                  .map((row, index) => {
                      const from = rows.slice(0, index).reduce((sum, item) => sum + item.share, 0);
                      return `${row.color} ${from}% ${from + row.share}%`;
                  })
                  .join(', ')})`;

    return { rows, gradient };
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-[1600px] flex-1 flex-col gap-5 px-4 py-6 sm:px-6 lg:px-8">
            <!-- Page header: eyebrow, greeting, the one-line thesis about the person. -->
            <header class="reveal flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="min-w-0">
                    <p class="b-eyebrow">Dashboard</p>
                    <h1 class="mt-2 text-3xl leading-tight font-semibold tracking-tight text-balance sm:text-4xl">
                        Welcome back, {{ summary.user?.name || 'User' }}
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm text-muted-foreground">
                        {{ experienceLabel }} building {{ focusLabel }} products &middot; {{ contentTotal }} records across the portfolio.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <Button as-child variant="outline" class="rounded-xl">
                        <Link :href="route('portfolio')">
                            View portfolio
                            <ArrowUpRight class="size-4" />
                        </Link>
                    </Button>
                    <Button as-child class="rounded-xl">
                        <Link :href="route('backend.projects.create')">
                            New project
                            <FolderOpenDot class="size-4" />
                        </Link>
                    </Button>
                </div>
            </header>

            <!-- Summary slabs: four counts on one material, so no colour has to be decoded. -->
            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article v-for="card in statCards" :key="card.title" class="b-kpi reveal">
                    <div class="flex items-start justify-between gap-3">
                        <div class="b-kpi-tile">
                            <component :is="card.icon" class="size-4" />
                        </div>
                        <span class="b-kpi-caption b-eyebrow">{{ card.tag }}</span>
                    </div>
                    <p class="b-kpi-label b-eyebrow mt-4">{{ card.title }}</p>
                    <p class="b-metric mt-1 text-4xl font-semibold">{{ card.value }}</p>
                    <p class="b-kpi-caption mt-1.5 text-xs">{{ card.caption }}</p>
                </article>
            </section>

            <!-- Share rings: how the portfolio weight is distributed. -->
            <section class="grid gap-4 sm:grid-cols-3">
                <article v-for="ring in ringCards" :key="ring.label" class="b-panel b-panel-lift reveal flex items-center gap-4 p-5">
                    <div class="b-ring size-16 shrink-0" :style="{ '--value': ring.share }" />
                    <div class="min-w-0">
                        <p class="b-metric text-2xl font-semibold">{{ ring.share }}%</p>
                        <p class="b-eyebrow mt-0.5">{{ ring.label }}</p>
                        <p class="mt-1 truncate text-xs text-muted-foreground">{{ ring.caption }}</p>
                    </div>
                </article>
            </section>

            <section class="grid gap-5 xl:grid-cols-[1.15fr_0.85fr]">
                <!-- Stack signal -->
                <article class="b-panel reveal p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold tracking-tight">Stack signal</h2>
                            <p class="mt-1 text-sm text-muted-foreground">Skill weight by discipline</p>
                        </div>
                        <span class="b-tile size-10">
                            <Layers3 class="size-4" />
                        </span>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div v-for="row in techStackRows" :key="row.type" class="space-y-2">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="font-medium capitalize">{{ row.type }}</span>
                                <span class="b-metric text-muted-foreground">{{ row.count }}</span>
                            </div>
                            <div class="b-bar">
                                <div class="b-bar-fill" :style="{ width: `${row.width}%` }" />
                            </div>
                        </div>
                        <div v-if="techStackRows.length === 0" class="rounded-xl border border-dashed py-10 text-center text-sm text-muted-foreground">
                            No tech stacks yet
                        </div>
                    </div>
                </article>

                <!-- Project mix -->
                <article class="b-panel reveal p-5 sm:p-6">
                    <div>
                        <h2 class="text-lg font-semibold tracking-tight">Project mix</h2>
                        <p class="mt-1 text-sm text-muted-foreground">Share of work by category</p>
                    </div>

                    <div class="mt-5 grid place-items-center">
                        <div class="relative grid size-40 place-items-center">
                            <div class="b-ring absolute inset-0" :style="{ background: projectMix.gradient }" />
                            <div class="relative text-center">
                                <p class="b-metric text-3xl font-semibold">{{ summary.projects.total }}</p>
                                <p class="b-eyebrow mt-0.5">Projects</p>
                            </div>
                        </div>
                    </div>

                    <ul class="mt-6 space-y-2.5">
                        <li v-for="row in projectMix.rows" :key="row.id" class="flex items-center gap-2.5 text-sm">
                            <span class="b-dot shrink-0" :style="{ '--dot-color': row.color }" />
                            <span class="min-w-0 flex-1 truncate">{{ row.name }}</span>
                            <span class="b-metric text-muted-foreground">{{ row.count }}</span>
                            <span class="b-metric w-10 text-right text-xs text-muted-foreground">{{ row.share }}%</span>
                        </li>
                        <li v-if="projectMix.rows.length === 0" class="rounded-xl border border-dashed py-8 text-center text-sm text-muted-foreground">
                            No projects yet
                        </li>
                    </ul>
                </article>
            </section>

            <section class="grid gap-5 xl:grid-cols-[1.05fr_0.95fr]">
                <!-- Recent projects -->
                <article class="b-panel reveal p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold tracking-tight">Recent projects</h2>
                            <p class="mt-1 text-sm text-muted-foreground">Latest portfolio work</p>
                        </div>
                        <Button as-child variant="ghost" size="sm" class="rounded-xl text-muted-foreground hover:text-foreground">
                            <Link :href="route('backend.projects.index')">
                                View all
                                <ExternalLink class="size-4" />
                            </Link>
                        </Button>
                    </div>

                    <div class="mt-5 space-y-2.5">
                        <Link
                            v-for="project in summary.projects.recent"
                            :key="project.id"
                            :href="route('backend.projects.edit', project.id)"
                            class="b-row group"
                        >
                            <div class="b-tile size-11 shrink-0 text-sm">
                                {{ project.title.charAt(0) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium">{{ project.title }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                    <Badge variant="secondary" class="rounded-lg font-medium">
                                        {{ project.project_type?.name || 'Project' }}
                                    </Badge>
                                    <span class="text-xs text-muted-foreground">{{ formatDate(project.created_date) }}</span>
                                </div>
                            </div>
                            <ExternalLink class="size-4 text-muted-foreground opacity-0 transition-opacity duration-200 group-hover:opacity-100" />
                        </Link>
                        <div v-if="summary.projects.recent.length === 0" class="rounded-xl border border-dashed py-10 text-center text-sm text-muted-foreground">
                            No projects yet
                        </div>
                    </div>
                </article>

                <!-- Career timeline -->
                <article class="b-panel reveal p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold tracking-tight">Work experience</h2>
                            <p class="mt-1 text-sm text-muted-foreground">Career timeline and roles</p>
                        </div>
                        <Button as-child variant="ghost" size="sm" class="rounded-xl text-muted-foreground hover:text-foreground">
                            <Link :href="route('backend.work-experience.index')">
                                View all
                                <ExternalLink class="size-4" />
                            </Link>
                        </Button>
                    </div>

                    <div class="mt-5">
                        <div
                            v-for="(experience, index) in summary.workExperience.recent"
                            :key="experience.id"
                            class="relative grid grid-cols-[18px_1fr] gap-3 pb-4 last:pb-0"
                        >
                            <div class="relative flex justify-center">
                                <span class="mt-4 size-2.5 rounded-full bg-primary ring-4 ring-primary/12" />
                                <span v-if="index < summary.workExperience.recent.length - 1" class="absolute top-7 bottom-0 w-px bg-border" />
                            </div>
                            <Link
                                :href="route('backend.work-experience.edit', experience.id)"
                                class="block rounded-xl border border-border/80 bg-muted/45 p-4 transition-colors hover:bg-muted"
                            >
                                <h3 class="font-medium">{{ experience.position }}</h3>
                                <p class="mt-0.5 text-sm text-muted-foreground">{{ experience.company }}</p>
                                <p class="b-eyebrow mt-3">{{ periodLabel(experience.from, experience.to) }}</p>
                            </Link>
                        </div>
                        <div
                            v-if="summary.workExperience.recent.length === 0"
                            class="rounded-xl border border-dashed py-10 text-center text-sm text-muted-foreground"
                        >
                            No work experience recorded
                        </div>
                    </div>
                </article>
            </section>

            <section class="grid gap-5 xl:grid-cols-2">
                <!-- Education -->
                <article class="b-panel reveal p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold tracking-tight">Education</h2>
                            <p class="mt-1 text-sm text-muted-foreground">Latest academic record</p>
                        </div>
                        <Button as-child variant="ghost" size="sm" class="rounded-xl text-muted-foreground hover:text-foreground">
                            <Link :href="route('backend.education.index')">
                                View all
                                <ExternalLink class="size-4" />
                            </Link>
                        </Button>
                    </div>

                    <Link
                        v-if="summary.education.latest"
                        :href="route('backend.education.edit', summary.education.latest.id)"
                        class="b-row mt-5"
                    >
                        <div class="b-tile size-11 shrink-0">
                            <GraduationCap class="size-4" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-medium">{{ summary.education.latest.title }}</h3>
                            <p class="mt-0.5 text-sm text-muted-foreground">{{ summary.education.latest.major }}</p>
                            <p class="text-sm text-muted-foreground">{{ summary.education.latest.institution }}</p>
                            <p class="b-eyebrow mt-3">{{ periodLabel(summary.education.latest.from, summary.education.latest.to) }}</p>
                        </div>
                    </Link>
                    <div v-else class="mt-5 rounded-xl border border-dashed py-10 text-center text-sm text-muted-foreground">
                        No education records
                    </div>
                </article>

                <!-- Recent songs -->
                <article class="b-panel reveal p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold tracking-tight">Recent songs</h2>
                            <p class="mt-1 text-sm text-muted-foreground">Latest additions to the collection</p>
                        </div>
                        <Button as-child variant="ghost" size="sm" class="rounded-xl text-muted-foreground hover:text-foreground">
                            <Link :href="route('backend.popular-songs.index')">
                                View all
                                <ExternalLink class="size-4" />
                            </Link>
                        </Button>
                    </div>

                    <div class="mt-5 space-y-2.5">
                        <Link v-for="song in summary.songs.recent" :key="song.id" :href="route('backend.popular-songs.edit', song.id)" class="b-row">
                            <div class="b-tile size-11 shrink-0">
                                <Music4 class="size-4" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium">{{ song.title }}</p>
                                <p class="truncate text-sm text-muted-foreground">{{ song.artist }}</p>
                            </div>
                            <Badge variant="outline" class="rounded-lg font-mono">{{ formatDuration(song) }}</Badge>
                        </Link>
                        <div v-if="summary.songs.recent.length === 0" class="rounded-xl border border-dashed py-10 text-center text-sm text-muted-foreground">
                            No songs in collection
                        </div>
                    </div>
                </article>
            </section>

            <section class="grid gap-5 xl:grid-cols-2">
                <article class="b-panel reveal p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold tracking-tight">Notes</h2>
                            <p class="mt-1 text-sm text-muted-foreground">
                                {{ summary.notes.published }} published · {{ summary.notes.total - summary.notes.published }} not published
                            </p>
                        </div>
                        <Button as-child variant="ghost" size="sm" class="rounded-xl text-muted-foreground hover:text-foreground">
                            <Link :href="route('backend.notes.index')">
                                View all
                                <ExternalLink class="size-4" />
                            </Link>
                        </Button>
                    </div>
                    <div class="mt-5 space-y-2.5">
                        <Link
                            v-for="note in summary.notes.recent"
                            :key="note.id"
                            :href="route('backend.notes.edit', note.id)"
                            class="b-row"
                        >
                            <div class="b-tile size-11 shrink-0">
                                <BookOpen class="size-4" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium">{{ note.title }}</p>
                                <p class="truncate text-sm text-muted-foreground capitalize">{{ note.status }}</p>
                            </div>
                        </Link>
                        <div v-if="summary.notes.recent.length === 0" class="rounded-xl border border-dashed py-10 text-center text-sm text-muted-foreground">
                            No notes yet
                        </div>
                    </div>
                </article>

                <article class="b-panel reveal p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold tracking-tight">Feeds</h2>
                            <p class="mt-1 text-sm text-muted-foreground">
                                {{ summary.feeds.published }} public · {{ summary.feeds.total - summary.feeds.published }} not public
                            </p>
                        </div>
                        <Button as-child variant="ghost" size="sm" class="rounded-xl text-muted-foreground hover:text-foreground">
                            <Link :href="route('backend.feeds.index')">
                                View all
                                <ExternalLink class="size-4" />
                            </Link>
                        </Button>
                    </div>
                    <div class="mt-5 space-y-2.5">
                        <Link
                            v-for="feed in summary.feeds.recent"
                            :key="feed.id"
                            :href="route('backend.feeds.edit', feed.id)"
                            class="b-row"
                        >
                            <div class="b-tile size-11 shrink-0">
                                <Rss class="size-4" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium">{{ feed.title || feed.body }}</p>
                                <p class="truncate text-sm text-muted-foreground capitalize">{{ feed.status }}</p>
                            </div>
                        </Link>
                        <div v-if="summary.feeds.recent.length === 0" class="rounded-xl border border-dashed py-10 text-center text-sm text-muted-foreground">
                            No feeds yet
                        </div>
                    </div>
                </article>
            </section>
        </div>
    </AppLayout>
</template>

<style scoped>
.reveal {
    animation: panel-in 480ms cubic-bezier(0.22, 1, 0.36, 1) both;
}

.reveal:nth-child(2) {
    animation-delay: 40ms;
}

.reveal:nth-child(3) {
    animation-delay: 80ms;
}

.reveal:nth-child(4) {
    animation-delay: 120ms;
}

@keyframes panel-in {
    from {
        opacity: 0;
        transform: translate3d(0, 8px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .reveal {
        animation: none;
    }
}
</style>
