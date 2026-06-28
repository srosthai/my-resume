<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardAction, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { BriefcaseBusiness, CalendarDays, Code2, ExternalLink, GraduationCap, Layers3, Music4, Sparkles } from 'lucide-vue-next';
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
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const formatDate = (date?: string | null) => {
    if (!date) return 'Present';

    const parsedDate = new Date(date);

    if (Number.isNaN(parsedDate.getTime())) return 'Present';

    return parsedDate.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
    });
};

const formatDuration = (song: any) => {
    if (song.formatted_duration) return song.formatted_duration;

    const duration = Number(song.duration || 0);
    const minutes = Math.floor(duration / 60);
    const seconds = String(duration % 60).padStart(2, '0');

    return `${minutes}:${seconds}`;
};

const initials = computed(() => props.summary.user?.name?.charAt(0)?.toUpperCase() || 'U');
const experienceLabel = computed(() => `${props.summary.aboutMe?.year_experience || 0}+ years`);
const focusLabel = computed(() => props.summary.aboutMe?.fucus_on || 'development');

const statCards = computed(() => [
    {
        title: 'Projects',
        value: props.summary.projects.total,
        caption: `${props.summary.projects.byType.length} categories tracked`,
        icon: BriefcaseBusiness,
    },
    {
        title: 'Tech Stack',
        value: props.summary.techStacks.total,
        caption: 'Grouped by discipline',
        icon: Code2,
    },
    {
        title: 'Experience',
        value: props.summary.workExperience.total,
        caption: `${props.summary.education.total} education records`,
        icon: CalendarDays,
    },
    {
        title: 'Popular Songs',
        value: props.summary.songs.total,
        caption: 'Music collection',
        icon: Music4,
    },
]);

const techStackRows = computed(() =>
    Object.entries(props.summary.techStacks.byType || {}).map(([type, count]) => ({
        type,
        count,
        width: props.summary.techStacks.total ? Math.max(8, Math.round((Number(count) / props.summary.techStacks.total) * 100)) : 0,
    })),
);

const projectTypePreview = computed(() => props.summary.projects.byType.slice(0, 5));
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="dashboard-surface min-h-full">
            <div class="mx-auto flex w-full max-w-[1720px] flex-1 flex-col gap-5 px-4 py-5 sm:px-6 lg:px-8">
                <section class="dashboard-hero reveal-panel overflow-hidden rounded-xl border border-border/70 bg-card shadow-sm">
                    <div class="hero-grid px-5 py-5 sm:px-6 lg:px-7">
                        <div class="flex min-w-0 flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                            <div class="flex min-w-0 items-start gap-4">
                                <Avatar class="size-16 border border-white/15 shadow-sm ring-4 ring-white/10">
                                    <AvatarFallback class="bg-foreground text-xl font-semibold text-background">
                                        {{ initials }}
                                    </AvatarFallback>
                                </Avatar>

                                <div class="min-w-0 space-y-3">
                                    <div class="flex flex-wrap items-center gap-2 text-white/80">
                                        <Badge variant="outline" class="border-white/15 bg-white/10 text-white shadow-none backdrop-blur">
                                            Portfolio command
                                        </Badge>
                                        <span class="text-xs font-medium tracking-[0.18em] text-white/45 uppercase">Dashboard</span>
                                    </div>
                                    <div class="space-y-1">
                                        <h1 class="text-3xl font-semibold tracking-tight text-balance text-white sm:text-4xl">
                                            Welcome back, {{ summary.user?.name || 'User' }}
                                        </h1>
                                        <p class="text-sm font-medium text-white/68 sm:text-base">
                                            {{ summary.aboutMe?.title || summary.user?.position || 'Developer' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="hero-metrics grid gap-2 rounded-lg border border-white/10 bg-white/[0.07] p-2 text-white shadow-sm backdrop-blur md:min-w-[420px] md:grid-cols-3"
                            >
                                <div class="rounded-md bg-white/[0.08] px-4 py-3">
                                    <p class="text-2xl font-semibold">{{ summary.projects.total }}</p>
                                    <p class="mt-1 text-[11px] font-medium tracking-[0.16em] text-white/45 uppercase">Projects</p>
                                </div>
                                <div class="rounded-md bg-white/[0.08] px-4 py-3">
                                    <p class="text-2xl font-semibold">{{ summary.techStacks.total }}</p>
                                    <p class="mt-1 text-[11px] font-medium tracking-[0.16em] text-white/45 uppercase">Stacks</p>
                                </div>
                                <div class="rounded-md bg-white/[0.08] px-4 py-3">
                                    <p class="text-2xl font-semibold">{{ summary.workExperience.total }}</p>
                                    <p class="mt-1 text-[11px] font-medium tracking-[0.16em] text-white/45 uppercase">Roles</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="reveal-panel grid gap-4 lg:grid-cols-[1fr_360px]">
                    <Card class="premium-card overflow-hidden">
                        <CardContent class="grid gap-5 p-5 lg:grid-cols-[1fr_280px] lg:p-6">
                            <div class="space-y-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex size-9 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                                        <Sparkles class="size-4" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-foreground">Current focus</p>
                                        <p class="text-sm text-muted-foreground">Clean delivery snapshot</p>
                                    </div>
                                </div>
                                <p class="max-w-2xl text-2xl leading-tight font-semibold tracking-tight text-foreground sm:text-3xl">
                                    {{ experienceLabel }} building polished {{ focusLabel }} products.
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <Badge v-for="type in projectTypePreview" :key="type.id" variant="secondary" class="rounded-md px-3 py-1">
                                        {{ type.name }}: {{ type.projects_count }}
                                    </Badge>
                                </div>
                            </div>

                            <div class="rounded-lg border bg-muted/30 p-4">
                                <p class="text-xs font-semibold tracking-[0.18em] text-muted-foreground uppercase">Profile health</p>
                                <div class="mt-4 space-y-3">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-muted-foreground">Portfolio content</span>
                                        <span class="font-semibold">{{ summary.projects.total }}</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-background">
                                        <div class="h-full w-4/5 rounded-full bg-foreground" />
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 pt-2">
                                        <div class="rounded-md bg-background p-3">
                                            <p class="text-lg font-semibold">{{ summary.songs.total }}</p>
                                            <p class="text-xs text-muted-foreground">Songs</p>
                                        </div>
                                        <div class="rounded-md bg-background p-3">
                                            <p class="text-lg font-semibold">{{ summary.education.total }}</p>
                                            <p class="text-xs text-muted-foreground">Education</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="premium-card overflow-hidden">
                        <CardHeader class="pb-2">
                            <CardTitle class="text-base">Stack signal</CardTitle>
                            <CardDescription>Skill weight by category</CardDescription>
                            <CardAction>
                                <Layers3 class="size-5 text-muted-foreground" />
                            </CardAction>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div v-for="row in techStackRows" :key="row.type" class="space-y-2">
                                <div class="flex items-center justify-between gap-3 text-sm">
                                    <span class="font-medium text-foreground capitalize">{{ row.type }}</span>
                                    <span class="text-muted-foreground">{{ row.count }}</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-muted">
                                    <div class="stack-bar h-full rounded-full bg-foreground" :style="{ width: `${row.width}%` }" />
                                </div>
                            </div>
                            <div
                                v-if="techStackRows.length === 0"
                                class="rounded-lg border border-dashed py-10 text-center text-sm text-muted-foreground"
                            >
                                No tech stacks yet
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <Card v-for="card in statCards" :key="card.title" class="stat-card group reveal-panel">
                        <CardHeader class="gap-3">
                            <CardAction>
                                <div class="stat-icon flex size-11 items-center justify-center rounded-lg border bg-muted/50 text-muted-foreground">
                                    <component :is="card.icon" class="size-5" />
                                </div>
                            </CardAction>
                            <CardDescription class="font-semibold tracking-[0.14em] uppercase">{{ card.title }}</CardDescription>
                            <CardTitle class="text-4xl font-semibold tracking-tight">{{ card.value }}</CardTitle>
                        </CardHeader>
                        <CardContent class="pt-0">
                            <p class="text-sm text-muted-foreground">{{ card.caption }}</p>
                        </CardContent>
                    </Card>
                </div>

                <div class="grid gap-5 xl:grid-cols-[1.08fr_0.92fr]">
                    <Card class="premium-card reveal-panel">
                        <CardHeader class="border-b">
                            <div>
                                <CardTitle class="text-xl">Recent Projects</CardTitle>
                                <CardDescription>Latest portfolio work</CardDescription>
                            </div>
                            <CardAction>
                                <Button as-child variant="outline" size="sm" class="action-button">
                                    <Link href="/projects">
                                        View all
                                        <ExternalLink class="size-4" />
                                    </Link>
                                </Button>
                            </CardAction>
                        </CardHeader>
                        <CardContent class="space-y-3 pt-6">
                            <div v-for="project in summary.projects.recent" :key="project.id" class="list-row group">
                                <div
                                    class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-foreground text-sm font-semibold text-background shadow-xs"
                                >
                                    {{ project.title.charAt(0) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-semibold text-foreground">{{ project.title }}</p>
                                    <div class="mt-1 flex flex-wrap items-center gap-2">
                                        <Badge variant="secondary" class="font-medium">
                                            {{ project.project_type?.name || 'Project' }}
                                        </Badge>
                                        <span class="text-xs text-muted-foreground">{{ formatDate(project.created_date) }}</span>
                                    </div>
                                </div>
                                <ExternalLink
                                    class="size-4 text-muted-foreground opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                                />
                            </div>
                            <div
                                v-if="summary.projects.recent.length === 0"
                                class="rounded-lg border border-dashed py-10 text-center text-sm text-muted-foreground"
                            >
                                No projects yet
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="premium-card reveal-panel">
                        <CardHeader class="border-b">
                            <div>
                                <CardTitle class="text-xl">Work Experience</CardTitle>
                                <CardDescription>Career timeline and roles</CardDescription>
                            </div>
                            <CardAction>
                                <Button as-child variant="outline" size="sm" class="action-button">
                                    <Link href="/work-experience">
                                        View all
                                        <ExternalLink class="size-4" />
                                    </Link>
                                </Button>
                            </CardAction>
                        </CardHeader>
                        <CardContent class="pt-6">
                            <div class="space-y-1">
                                <div
                                    v-for="(experience, index) in summary.workExperience.recent"
                                    :key="experience.id"
                                    class="relative grid grid-cols-[22px_1fr] gap-3 pb-5 last:pb-0"
                                >
                                    <div class="relative flex justify-center">
                                        <span class="mt-2 size-2.5 rounded-full bg-foreground ring-4 ring-muted" />
                                        <span
                                            v-if="index < summary.workExperience.recent.length - 1"
                                            class="absolute top-6 bottom-0 w-px bg-border"
                                        />
                                    </div>
                                    <div class="timeline-card rounded-lg border bg-muted/25 p-4">
                                        <h4 class="font-semibold text-foreground">{{ experience.position }}</h4>
                                        <p class="mt-1 text-sm text-muted-foreground">{{ experience.company }}</p>
                                        <p class="mt-3 text-xs font-medium text-muted-foreground">
                                            {{ formatDate(experience.from) }} - {{ formatDate(experience.to) }}
                                        </p>
                                    </div>
                                </div>
                                <div
                                    v-if="summary.workExperience.recent.length === 0"
                                    class="rounded-lg border border-dashed py-10 text-center text-sm text-muted-foreground"
                                >
                                    No work experience recorded
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="grid gap-5 xl:grid-cols-2">
                    <Card class="premium-card reveal-panel">
                        <CardHeader>
                            <div>
                                <CardTitle class="text-xl">Education</CardTitle>
                                <CardDescription>Latest academic record</CardDescription>
                            </div>
                            <CardAction>
                                <Button as-child variant="outline" size="sm" class="action-button">
                                    <Link href="/eductions">
                                        View all
                                        <ExternalLink class="size-4" />
                                    </Link>
                                </Button>
                            </CardAction>
                        </CardHeader>
                        <CardContent>
                            <div v-if="summary.education.latest" class="list-row">
                                <div class="flex size-12 shrink-0 items-center justify-center rounded-lg border bg-background">
                                    <GraduationCap class="size-5 text-muted-foreground" />
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-semibold text-foreground">{{ summary.education.latest.title }}</h4>
                                    <p class="mt-1 text-sm text-muted-foreground">{{ summary.education.latest.major }}</p>
                                    <p class="text-sm text-muted-foreground">{{ summary.education.latest.institution }}</p>
                                    <p class="mt-3 text-xs font-medium text-muted-foreground">
                                        {{ formatDate(summary.education.latest.from) }} - {{ formatDate(summary.education.latest.to) }}
                                    </p>
                                </div>
                            </div>
                            <div v-else class="rounded-lg border border-dashed py-10 text-center text-sm text-muted-foreground">
                                No education records
                            </div>
                        </CardContent>
                    </Card>

                    <Card class="premium-card reveal-panel">
                        <CardHeader>
                            <div>
                                <CardTitle class="text-xl">Recent Songs</CardTitle>
                                <CardDescription>Latest additions to collection</CardDescription>
                            </div>
                            <CardAction>
                                <Button as-child variant="outline" size="sm" class="action-button">
                                    <Link href="/popular-songs">
                                        View all
                                        <ExternalLink class="size-4" />
                                    </Link>
                                </Button>
                            </CardAction>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div v-for="song in summary.songs.recent" :key="song.id" class="list-row">
                                <div class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-foreground text-background">
                                    <Music4 class="size-4" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-semibold text-foreground">{{ song.title }}</p>
                                    <p class="truncate text-sm text-muted-foreground">{{ song.artist }}</p>
                                </div>
                                <Badge variant="outline" class="font-mono">{{ formatDuration(song) }}</Badge>
                            </div>
                            <div
                                v-if="summary.songs.recent.length === 0"
                                class="rounded-lg border border-dashed py-10 text-center text-sm text-muted-foreground"
                            >
                                No songs in collection
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.dashboard-surface {
    background:
        linear-gradient(to right, color-mix(in oklab, var(--border) 24%, transparent) 1px, transparent 1px),
        linear-gradient(to bottom, color-mix(in oklab, var(--border) 20%, transparent) 1px, transparent 1px),
        linear-gradient(180deg, color-mix(in oklab, var(--muted) 38%, transparent), var(--background) 42%);
    background-size:
        44px 44px,
        44px 44px,
        100% 100%;
}

.dashboard-hero {
    background: linear-gradient(135deg, hsl(0 0% 5%) 0%, hsl(0 0% 11%) 48%, hsl(0 0% 17%) 100%), var(--foreground);
}

.hero-grid {
    position: relative;
    isolation: isolate;
}

.hero-grid::before {
    position: absolute;
    inset: 0;
    z-index: -1;
    content: '';
    background:
        linear-gradient(to right, hsl(0 0% 100% / 0.06) 1px, transparent 1px), linear-gradient(to bottom, hsl(0 0% 100% / 0.05) 1px, transparent 1px);
    background-size: 32px 32px;
    mask-image: linear-gradient(90deg, black, transparent 78%);
}

.premium-card,
.stat-card {
    border-color: color-mix(in oklab, var(--border) 72%, transparent);
    background: color-mix(in oklab, var(--card) 92%, transparent);
    box-shadow:
        0 1px 1px hsl(0 0% 0% / 0.03),
        0 18px 50px hsl(0 0% 0% / 0.045);
    backdrop-filter: blur(14px);
}

.stat-card,
.list-row,
.timeline-card,
.action-button,
.stat-icon {
    transition:
        transform 240ms cubic-bezier(0.22, 1, 0.36, 1),
        border-color 200ms ease,
        background-color 200ms ease,
        box-shadow 240ms cubic-bezier(0.22, 1, 0.36, 1),
        color 200ms ease;
}

.stat-card:hover {
    transform: translate3d(0, -4px, 0);
    border-color: color-mix(in oklab, var(--foreground) 22%, transparent);
    box-shadow:
        0 1px 1px hsl(0 0% 0% / 0.04),
        0 24px 70px hsl(0 0% 0% / 0.1);
}

.stat-card:hover .stat-icon {
    transform: translate3d(0, -2px, 0);
    color: var(--foreground);
}

.list-row {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    border-radius: var(--radius);
    border: 1px solid color-mix(in oklab, var(--border) 74%, transparent);
    background: color-mix(in oklab, var(--muted) 22%, transparent);
    padding: 0.875rem;
}

.list-row:hover,
.timeline-card:hover {
    transform: translate3d(0, -2px, 0);
    border-color: color-mix(in oklab, var(--foreground) 18%, transparent);
    background: color-mix(in oklab, var(--muted) 34%, transparent);
    box-shadow: 0 14px 34px hsl(0 0% 0% / 0.055);
}

.action-button:active {
    transform: scale(0.97);
}

.stack-bar {
    transform-origin: left center;
    animation: grow-x 620ms cubic-bezier(0.22, 1, 0.36, 1) both;
}

.reveal-panel {
    animation: panel-in 520ms cubic-bezier(0.22, 1, 0.36, 1) both;
}

.reveal-panel:nth-child(2) {
    animation-delay: 40ms;
}

.reveal-panel:nth-child(3) {
    animation-delay: 85ms;
}

.reveal-panel:nth-child(4) {
    animation-delay: 130ms;
}

.reveal-panel:nth-child(5) {
    animation-delay: 175ms;
}

@keyframes panel-in {
    from {
        opacity: 0;
        transform: translate3d(0, 10px, 0) scale(0.985);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0) scale(1);
    }
}

@keyframes grow-x {
    from {
        transform: scaleX(0);
    }
    to {
        transform: scaleX(1);
    }
}

@media (prefers-reduced-motion: reduce) {
    .reveal-panel,
    .stack-bar {
        animation: none;
    }

    .stat-card,
    .list-row,
    .timeline-card,
    .action-button,
    .stat-icon {
        transition: none;
    }
}
</style>
