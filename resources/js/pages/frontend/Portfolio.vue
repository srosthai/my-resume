<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { Laptop, Github, Folder, ExternalLink, Search, X, ArrowUpRight } from 'lucide-vue-next'
import { Badge } from '@/components/ui/badge'
import { Skeleton } from '@/components/ui/skeleton'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { Input } from '@/components/ui/input'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

// Types
interface ProjectType {
    id: number
    name: string
}

interface ProjectLink {
    [key: string]: string
}

interface Project {
    id: number
    title: string
    description: string
    image?: string
    project_type?: ProjectType
    technologies?: string[]
    tech_stack?: string
    status?: 'completed' | 'in-progress' | 'pending' | 'processing'
    links?: ProjectLink[]
    github_url?: string
    demo_url?: string
    live_url?: string
    created_at?: string
    created_date?: string
}

interface Props {
    title?: string
    description?: string
    projects: Project[]
    projectTypes?: ProjectType[]
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Portfolio',
    description: '',
    projectTypes: () => [],
})

// State
const isVisible = ref(false)
const isLoading = ref(true)
const selectedCategory = ref<string>('all')
const searchQuery = ref('')

const stagger = {
    badge: 0,
    title: 100,
    subtitle: 200,
    filters: 300,
    grid: 400,
}

// Computed filtered projects
const filteredProjects = computed(() => {
    let result = props.projects

    if (selectedCategory.value !== 'all') {
        const categoryId = parseInt(selectedCategory.value)
        result = result.filter((project) => project.project_type?.id === categoryId)
    }

    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase().trim()
        result = result.filter(
            (project) =>
                project.title.toLowerCase().includes(query) ||
                project.description.toLowerCase().includes(query) ||
                project.technologies?.some((tech) => tech.toLowerCase().includes(query)) ||
                project.tech_stack?.toLowerCase().includes(query),
        )
    }

    return result
})

const clearSearch = () => {
    searchQuery.value = ''
}

const openProjectDetails = (project: Project) => {
    router.visit(route('portfolio.show', project.id))
}

const getGithubUrl = (project: Project): string | undefined => {
    if (project.github_url) return project.github_url
    if (!project.links) return undefined
    const githubLink = project.links.find((link) =>
        Object.keys(link)[0]?.toLowerCase().includes('github'),
    )
    return githubLink ? Object.values(githubLink)[0] : undefined
}

const getLiveUrl = (project: Project): string | undefined => {
    if (project.demo_url) return project.demo_url
    if (project.live_url) return project.live_url
    if (!project.links) return undefined
    const liveLink = project.links.find(
        (link) => !Object.keys(link)[0]?.toLowerCase().includes('github'),
    )
    return liveLink ? Object.values(liveLink)[0] : undefined
}

const getStatusColor = (status: string) => {
    switch (status) {
        case 'completed':
            return 'bg-emerald-500/15 text-emerald-400 border-emerald-500/20'
        case 'in-progress':
        case 'processing':
            return 'bg-blue-500/15 text-blue-400 border-blue-500/20'
        case 'pending':
            return 'bg-amber-500/15 text-amber-400 border-amber-500/20'
        default:
            return 'bg-muted text-muted-foreground border-border/40'
    }
}

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
        setTimeout(() => {
            isVisible.value = true
        }, 50)
    }, 600)
})
</script>

<template>
    <FrontendLayout currentRoute="/portfolio">
        <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="description" />
        <meta name="keywords" content="portfolio, projects, web development, mobile apps, Vue.js, Laravel, programming projects, SROS THAI, Cambodia developer" />
        <meta name="author" content="SROS THAI - Software Developer" />
        <meta name="language" content="en" />
        <meta name="geo.region" content="KH" />
        <meta name="geo.country" content="Cambodia" />
        <meta name="theme-color" content="#2563eb" />
        <meta property="og:title" :content="title" />
        <meta property="og:description" :content="description" />
        <meta property="og:image" content="/portfolio-og-image.jpg" />
        <meta property="og:url" :content="$page.url" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="Developer Portfolio" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="title" />
        <meta name="twitter:description" :content="description" />
        <meta name="twitter:image" content="/portfolio-og-image.jpg" />
        <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="format-detection" content="telephone=no" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <link rel="canonical" :href="$page.url" />
        </Head>

        <div class="min-h-screen text-foreground pt-20 sm:pt-24">

            <!-- Loading Skeleton -->
            <template v-if="isLoading">
                <section class="mx-auto max-w-5xl px-4 sm:px-6 pt-4 sm:pt-8">
                    <div class="space-y-4">
                        <Skeleton class="h-7 w-24 rounded-full" />
                        <Skeleton class="h-10 w-64 rounded-lg" />
                        <Skeleton class="h-5 w-96 max-w-full rounded-lg" />
                    </div>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
                        <Skeleton class="h-10 w-full sm:w-72 rounded-lg" />
                        <Skeleton class="h-10 w-full sm:w-44 rounded-lg" />
                    </div>

                    <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <Skeleton v-for="i in 6" :key="i" class="h-72 rounded-xl" />
                    </div>
                </section>
            </template>

            <!-- Content -->
            <template v-else>
                <!-- Hero -->
                <section class="mx-auto max-w-5xl px-4 sm:px-6 pt-4 sm:pt-8">
                    <div>
                        <div
                            class="reveal"
                            :style="{ animationDelay: `${stagger.badge}ms` }"
                            :class="{ 'is-visible': isVisible }"
                        >
                            <Badge
                                variant="outline"
                                class="gap-1.5 border-border/40 bg-muted/40 px-3 py-1.5 text-xs font-normal text-muted-foreground backdrop-blur-sm transition-colors duration-200 hover:border-primary/30 hover:bg-primary/10 hover:text-primary"
                            >
                                <Folder class="size-3 opacity-60" />
                                Portfolio
                            </Badge>
                        </div>

                        <div
                            class="reveal mt-4"
                            :style="{ animationDelay: `${stagger.title}ms` }"
                            :class="{ 'is-visible': isVisible }"
                        >
                            <h1 class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
                                My Projects
                            </h1>
                        </div>

                        <div
                            class="reveal mt-3"
                            :style="{ animationDelay: `${stagger.subtitle}ms` }"
                            :class="{ 'is-visible': isVisible }"
                        >
                            <p class="max-w-2xl text-base leading-relaxed text-muted-foreground/80">
                                A curated collection of projects showcasing modern web development, mobile applications, and creative solutions.
                            </p>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div
                        class="reveal mt-8"
                        :style="{ animationDelay: `${stagger.filters}ms` }"
                        :class="{ 'is-visible': isVisible }"
                    >
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-3">
                            <!-- Search -->
                            <div class="relative w-full sm:w-72 lg:w-80">
                                <Search
                                    class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground/50"
                                />
                                <Input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search projects..."
                                    class="h-10 border-border/40 bg-muted/30 pl-9 pr-9 text-sm placeholder:text-muted-foreground/40 focus:border-primary/40 focus:bg-background"
                                />
                                <button
                                    v-if="searchQuery"
                                    @click="clearSearch"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground/60 transition-colors hover:text-foreground"
                                >
                                    <X class="size-4" />
                                </button>
                            </div>

                            <!-- Category -->
                            <Select v-model="selectedCategory">
                                <SelectTrigger class="h-10 w-full border-border/40 bg-muted/30 sm:w-44">
                                    <SelectValue placeholder="All Categories" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Categories</SelectItem>
                                    <SelectItem
                                        v-for="projectType in projectTypes"
                                        :key="projectType.id"
                                        :value="String(projectType.id)"
                                    >
                                        {{ projectType.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <!-- Count -->
                            <span class="text-xs text-muted-foreground/60">
                                {{ filteredProjects.length }} {{ filteredProjects.length === 1 ? 'project' : 'projects' }}
                            </span>
                        </div>
                    </div>
                </section>

                <!-- Projects Grid -->
                <section
                    class="reveal mx-auto max-w-5xl px-4 sm:px-6 pb-16 mt-10"
                    :style="{ animationDelay: `${stagger.grid}ms` }"
                    :class="{ 'is-visible': isVisible }"
                >
                    <!-- Empty State -->
                    <div v-if="filteredProjects.length === 0" class="py-20 text-center">
                        <div class="mx-auto max-w-sm">
                            <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-2xl bg-muted/50">
                                <Search class="size-7 text-muted-foreground/40" />
                            </div>
                            <h3 class="text-base font-semibold text-foreground">No projects found</h3>
                            <p class="mt-1.5 text-sm text-muted-foreground/60">
                                <template v-if="searchQuery && selectedCategory !== 'all'">
                                    No projects match "{{ searchQuery }}" in the selected category.
                                </template>
                                <template v-else-if="searchQuery">
                                    No projects match "{{ searchQuery }}".
                                </template>
                                <template v-else-if="selectedCategory !== 'all'">
                                    No projects in this category yet.
                                </template>
                                <template v-else>No projects available yet.</template>
                            </p>
                            <div class="mt-5 flex flex-wrap justify-center gap-2">
                                <button
                                    v-if="searchQuery"
                                    @click="clearSearch"
                                    class="rounded-lg bg-muted/60 px-4 py-2 text-xs font-medium text-foreground transition-colors hover:bg-muted"
                                >
                                    Clear Search
                                </button>
                                <button
                                    v-if="selectedCategory !== 'all'"
                                    @click="selectedCategory = 'all'"
                                    class="rounded-lg bg-primary/10 px-4 py-2 text-xs font-medium text-primary transition-colors hover:bg-primary/20"
                                >
                                    Show All
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Grid -->
                    <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <article
                            v-for="(project, index) in filteredProjects"
                            :key="project.id"
                            class="project-card group cursor-pointer overflow-hidden rounded-xl border border-border/30 bg-card/50 transition-all duration-300 hover:border-border/60 hover:bg-card/80 hover:shadow-lg hover:shadow-black/5"
                            :style="{ animationDelay: `${index * 60}ms` }"
                            @click="openProjectDetails(project)"
                        >
                            <!-- Image -->
                            <div class="relative aspect-[16/10] overflow-hidden bg-muted/30">
                                <img
                                    v-if="project.image"
                                    :src="project.image"
                                    :alt="project.title"
                                    class="size-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                                <div
                                    v-else
                                    class="flex size-full items-center justify-center bg-gradient-to-br from-muted/40 to-muted/20"
                                >
                                    <Laptop class="size-10 text-muted-foreground/30" />
                                </div>

                                <!-- Overlay on hover -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100" />

                                <!-- Category badge -->
                                <div class="absolute left-3 top-3">
                                    <span class="rounded-md bg-background/90 px-2 py-1 text-[10px] font-medium text-foreground/80 shadow-sm backdrop-blur-sm">
                                        {{ project.project_type?.name || 'General' }}
                                    </span>
                                </div>

                                <!-- Status badge -->
                                <div v-if="project.status" class="absolute right-3 top-3">
                                    <span
                                        class="rounded-md border px-2 py-0.5 text-[10px] font-medium capitalize backdrop-blur-sm"
                                        :class="getStatusColor(project.status)"
                                    >
                                        {{ project.status }}
                                    </span>
                                </div>

                                <!-- Quick links on hover -->
                                <div class="absolute bottom-3 right-3 flex gap-1.5 opacity-0 transition-all duration-300 group-hover:opacity-100">
                                    <a
                                        v-if="getGithubUrl(project)"
                                        :href="getGithubUrl(project)"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="flex size-8 items-center justify-center rounded-lg bg-background/90 text-foreground/70 shadow-sm backdrop-blur-sm transition-colors hover:bg-background hover:text-foreground"
                                        @click.stop
                                    >
                                        <Github class="size-3.5" />
                                    </a>
                                    <a
                                        v-if="getLiveUrl(project)"
                                        :href="getLiveUrl(project)"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="flex size-8 items-center justify-center rounded-lg bg-background/90 text-foreground/70 shadow-sm backdrop-blur-sm transition-colors hover:bg-background hover:text-foreground"
                                        @click.stop
                                    >
                                        <ExternalLink class="size-3.5" />
                                    </a>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-4">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="line-clamp-1 text-sm font-semibold text-foreground transition-colors group-hover:text-primary">
                                        {{ project.title }}
                                    </h3>
                                    <ArrowUpRight class="size-4 shrink-0 text-muted-foreground/30 transition-all duration-300 group-hover:text-primary group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                                </div>
                                <p class="mt-1.5 line-clamp-2 text-xs leading-relaxed text-muted-foreground/60">
                                    {{ project.description }}
                                </p>

                                <!-- Tech tags -->
                                <div v-if="project.technologies?.length || project.tech_stack" class="mt-3 flex flex-wrap gap-1">
                                    <template v-if="project.technologies?.length">
                                        <span
                                            v-for="tech in project.technologies.slice(0, 3)"
                                            :key="tech"
                                            class="rounded-md bg-muted/50 px-1.5 py-0.5 text-[10px] font-medium text-muted-foreground/70"
                                        >
                                            {{ tech }}
                                        </span>
                                        <span
                                            v-if="project.technologies.length > 3"
                                            class="rounded-md bg-muted/50 px-1.5 py-0.5 text-[10px] font-medium text-muted-foreground/50"
                                        >
                                            +{{ project.technologies.length - 3 }}
                                        </span>
                                    </template>
                                    <template v-else-if="project.tech_stack">
                                        <span
                                            v-for="tech in project.tech_stack.split(',').slice(0, 3)"
                                            :key="tech"
                                            class="rounded-md bg-muted/50 px-1.5 py-0.5 text-[10px] font-medium text-muted-foreground/70"
                                        >
                                            {{ tech.trim() }}
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>
            </template>
        </div>
    </FrontendLayout>
</template>

<style scoped>
.reveal {
    opacity: 0;
    transform: translateY(16px);
    transition:
        opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1),
        transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.reveal.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.project-card {
    opacity: 0;
    transform: translateY(12px);
    animation: cardReveal 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes cardReveal {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .reveal,
    .project-card {
        opacity: 1;
        transform: none;
        transition: none;
        animation: none;
    }
}
</style>
