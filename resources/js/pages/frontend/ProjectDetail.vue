<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import {
    ArrowLeft,
    ChevronLeft,
    ChevronRight,
    Calendar,
    ExternalLink,
    Github,
    Laptop,
    Folder,
    Tag,
    CircleCheckBig,
    Clock,
    Loader,
    ArrowUpRight,
} from 'lucide-vue-next'
import { Badge } from '@/components/ui/badge'
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

interface NavProject {
    id: number
    title: string
}

interface Props {
    title?: string
    description?: string
    project: Project
    previousProject?: NavProject | null
    nextProject?: NavProject | null
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Project',
    description: '',
})

const isVisible = ref(false)

const stagger = {
    breadcrumb: 0,
    header: 80,
    image: 160,
    content: 240,
    sidebar: 300,
    nav: 380,
}

onMounted(() => {
    setTimeout(() => {
        isVisible.value = true
    }, 100)
})

// Status config
const statusConfig = computed(() => {
    const status = props.project.status
    switch (status) {
        case 'completed':
            return {
                icon: CircleCheckBig,
                label: 'Completed',
                dot: 'bg-emerald-400',
                classes: 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
            }
        case 'in-progress':
        case 'processing':
            return {
                icon: Loader,
                label: status === 'processing' ? 'Processing' : 'In Progress',
                dot: 'bg-blue-400',
                classes: 'bg-blue-500/10 text-blue-500 border-blue-500/20',
            }
        case 'pending':
            return {
                icon: Clock,
                label: 'Pending',
                dot: 'bg-amber-400',
                classes: 'bg-amber-500/10 text-amber-500 border-amber-500/20',
            }
        default:
            return {
                icon: Clock,
                label: status || 'Unknown',
                dot: 'bg-muted-foreground',
                classes: 'bg-muted text-muted-foreground border-border/40',
            }
    }
})

// Formatted date
const formattedDate = computed(() => {
    const dateStr = props.project.created_date || props.project.created_at
    if (!dateStr) return null
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
})

// Technologies list
const technologies = computed(() => {
    if (props.project.technologies && props.project.technologies.length > 0) {
        return props.project.technologies
    }
    if (props.project.tech_stack) {
        return props.project.tech_stack.split(',').map((t) => t.trim())
    }
    return []
})

// Extract links
const projectLinks = computed(() => {
    if (!props.project.links || !Array.isArray(props.project.links)) return []
    return props.project.links.map((linkObj) => {
        const name = Object.keys(linkObj)[0]
        const url = Object.values(linkObj)[0]
        const isGithub = name.toLowerCase().includes('github')
        return { name, url, isGithub }
    })
})

// Navigation
const goBack = () => {
    router.visit(route('portfolio'))
}

const goToProject = (id: number) => {
    router.visit(route('portfolio.show', id))
}
</script>

<template>
    <FrontendLayout currentRoute="/portfolio">
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta property="og:title" :content="title" />
            <meta property="og:description" :content="description" />
            <meta property="og:type" content="article" />
            <meta name="robots" content="index, follow" />
        </Head>

        <div class="min-h-screen text-foreground pt-20 sm:pt-24">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 pb-16">

                <!-- Breadcrumb -->
                <nav
                    class="reveal mb-8"
                    :style="{ animationDelay: `${stagger.breadcrumb}ms` }"
                    :class="{ 'is-visible': isVisible }"
                >
                    <div class="flex items-center gap-2 text-sm">
                        <button
                            @click="goBack"
                            class="flex items-center gap-1.5 text-muted-foreground/60 transition-colors hover:text-foreground"
                        >
                            <ArrowLeft class="size-3.5" />
                            <span>Portfolio</span>
                        </button>
                        <span class="text-border/60">/</span>
                        <span class="truncate font-medium text-foreground/80">{{ project.title }}</span>
                    </div>
                </nav>

                <!-- Header -->
                <div
                    class="reveal mb-8"
                    :style="{ animationDelay: `${stagger.header}ms` }"
                    :class="{ 'is-visible': isVisible }"
                >
                    <!-- Meta row -->
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <span class="rounded-md bg-muted/50 px-2 py-0.5 text-[11px] font-medium text-muted-foreground/70">
                            {{ project.project_type?.name || 'General' }}
                        </span>
                        <span
                            v-if="project.status"
                            class="inline-flex items-center gap-1.5 rounded-md border px-2 py-0.5 text-[11px] font-medium capitalize"
                            :class="statusConfig.classes"
                        >
                            <span class="size-1.5 rounded-full" :class="statusConfig.dot" />
                            {{ statusConfig.label }}
                        </span>
                        <span v-if="formattedDate" class="ml-auto flex items-center gap-1.5 text-xs text-muted-foreground/50">
                            <Calendar class="size-3" />
                            {{ formattedDate }}
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl lg:text-4xl">
                        {{ project.title }}
                    </h1>
                </div>

                <!-- Image -->
                <div
                    class="reveal mb-10"
                    :style="{ animationDelay: `${stagger.image}ms` }"
                    :class="{ 'is-visible': isVisible }"
                >
                    <div
                        v-if="project.image"
                        class="overflow-hidden rounded-xl border border-border/30"
                    >
                        <img
                            :src="project.image"
                            :alt="project.title"
                            class="w-full max-h-[480px] object-cover"
                        />
                    </div>
                    <div
                        v-else
                        class="flex h-48 items-center justify-center overflow-hidden rounded-xl border border-border/30 bg-muted/20 sm:h-64"
                    >
                        <Laptop class="size-14 text-muted-foreground/20" />
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1fr_280px]">

                    <!-- Main Content -->
                    <div
                        class="reveal space-y-8"
                        :style="{ animationDelay: `${stagger.content}ms` }"
                        :class="{ 'is-visible': isVisible }"
                    >
                        <!-- Description -->
                        <div>
                            <h2 class="mb-3 flex items-center gap-2 text-xs font-medium uppercase tracking-[0.15em] text-muted-foreground/50">
                                <Folder class="size-3.5" />
                                About This Project
                            </h2>
                            <p class="whitespace-pre-line text-sm leading-[1.8] text-muted-foreground/80">
                                {{ project.description }}
                            </p>
                        </div>

                        <!-- Technologies -->
                        <div v-if="technologies.length > 0">
                            <h2 class="mb-3 flex items-center gap-2 text-xs font-medium uppercase tracking-[0.15em] text-muted-foreground/50">
                                <Tag class="size-3.5" />
                                Technologies
                            </h2>
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="tech in technologies"
                                    :key="tech"
                                    class="rounded-lg border border-border/30 bg-muted/30 px-2.5 py-1 text-xs font-medium text-foreground/70 transition-colors hover:border-primary/20 hover:bg-primary/5 hover:text-primary"
                                >
                                    {{ tech }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div
                        class="reveal space-y-6"
                        :style="{ animationDelay: `${stagger.sidebar}ms` }"
                        :class="{ 'is-visible': isVisible }"
                    >
                        <!-- Details -->
                        <div class="rounded-xl border border-border/30 bg-card/30 p-5">
                            <h3 class="mb-4 text-xs font-medium uppercase tracking-[0.15em] text-muted-foreground/50">
                                Details
                            </h3>
                            <div class="space-y-3.5">
                                <div v-if="project.status" class="flex items-center justify-between">
                                    <span class="text-xs text-muted-foreground/60">Status</span>
                                    <span
                                        class="inline-flex items-center gap-1.5 text-xs font-medium"
                                        :class="statusConfig.classes.replace(/bg-\S+/, '').replace(/border-\S+/, '')"
                                    >
                                        <span class="size-1.5 rounded-full" :class="statusConfig.dot" />
                                        {{ statusConfig.label }}
                                    </span>
                                </div>
                                <div v-if="formattedDate" class="flex items-center justify-between">
                                    <span class="text-xs text-muted-foreground/60">Date</span>
                                    <span class="text-xs font-medium text-foreground/70">{{ formattedDate }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-muted-foreground/60">Category</span>
                                    <span class="text-xs font-medium text-foreground/70">{{ project.project_type?.name || 'General' }}</span>
                                </div>
                                <div v-if="technologies.length > 0" class="flex items-center justify-between">
                                    <span class="text-xs text-muted-foreground/60">Tech stack</span>
                                    <span class="text-xs font-medium text-foreground/70">{{ technologies.length }} tools</span>
                                </div>
                            </div>
                        </div>

                        <!-- Links -->
                        <div v-if="projectLinks.length > 0" class="rounded-xl border border-border/30 bg-card/30 p-5">
                            <h3 class="mb-4 text-xs font-medium uppercase tracking-[0.15em] text-muted-foreground/50">
                                Links
                            </h3>
                            <div class="space-y-2">
                                <a
                                    v-for="link in projectLinks"
                                    :key="link.url"
                                    :href="link.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group flex items-center gap-3 rounded-lg border border-border/20 bg-background/50 px-3.5 py-2.5 transition-all duration-200 hover:border-primary/20 hover:bg-primary/5"
                                >
                                    <div
                                        class="flex size-8 shrink-0 items-center justify-center rounded-lg transition-colors duration-200"
                                        :class="link.isGithub ? 'bg-muted/60 group-hover:bg-muted' : 'bg-primary/8 group-hover:bg-primary/15'"
                                    >
                                        <Github v-if="link.isGithub" class="size-3.5 text-foreground/70" />
                                        <ExternalLink v-else class="size-3.5 text-primary/70" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-xs font-medium text-foreground/80 transition-colors group-hover:text-primary">
                                            {{ link.name }}
                                        </p>
                                        <p class="truncate text-[10px] text-muted-foreground/40">
                                            {{ link.url.replace(/^https?:\/\//, '').split('/')[0] }}
                                        </p>
                                    </div>
                                    <ArrowUpRight class="size-3 shrink-0 text-muted-foreground/30 transition-all duration-200 group-hover:text-primary group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div
                    class="reveal mt-12 border-t border-border/20 pt-8"
                    :style="{ animationDelay: `${stagger.nav}ms` }"
                    :class="{ 'is-visible': isVisible }"
                >
                    <div class="flex items-stretch justify-between gap-4">
                        <!-- Previous -->
                        <button
                            v-if="previousProject"
                            @click="goToProject(previousProject.id)"
                            class="group flex max-w-[40%] items-center gap-3 rounded-xl border border-border/20 bg-card/20 px-4 py-3.5 text-left transition-all duration-200 hover:border-border/40 hover:bg-card/50"
                        >
                            <ChevronLeft class="size-4 shrink-0 text-muted-foreground/40 transition-colors group-hover:text-primary" />
                            <div class="min-w-0">
                                <p class="text-[10px] font-medium uppercase tracking-[0.1em] text-muted-foreground/40">Previous</p>
                                <p class="mt-0.5 truncate text-sm font-medium text-foreground/70 transition-colors group-hover:text-primary">
                                    {{ previousProject.title }}
                                </p>
                            </div>
                        </button>
                        <div v-else />

                        <!-- Back -->
                        <button
                            @click="goBack"
                            class="hidden items-center gap-1.5 self-center rounded-lg border border-border/20 px-4 py-2 text-xs font-medium text-muted-foreground/50 transition-colors hover:border-border/40 hover:text-foreground sm:flex"
                        >
                            All Projects
                        </button>

                        <!-- Next -->
                        <button
                            v-if="nextProject"
                            @click="goToProject(nextProject.id)"
                            class="group flex max-w-[40%] items-center gap-3 rounded-xl border border-border/20 bg-card/20 px-4 py-3.5 text-right transition-all duration-200 hover:border-border/40 hover:bg-card/50"
                        >
                            <div class="min-w-0">
                                <p class="text-[10px] font-medium uppercase tracking-[0.1em] text-muted-foreground/40">Next</p>
                                <p class="mt-0.5 truncate text-sm font-medium text-foreground/70 transition-colors group-hover:text-primary">
                                    {{ nextProject.title }}
                                </p>
                            </div>
                            <ChevronRight class="size-4 shrink-0 text-muted-foreground/40 transition-colors group-hover:text-primary" />
                        </button>
                        <div v-else />
                    </div>
                </div>
            </div>
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

@media (prefers-reduced-motion: reduce) {
    .reveal {
        opacity: 1;
        transform: none;
        transition: none;
    }
}
</style>
