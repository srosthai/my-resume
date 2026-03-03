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
} from 'lucide-vue-next'
import { Badge } from '@/components/ui/badge'
import { Card } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
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
                classes: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-800',
            }
        case 'in-progress':
        case 'processing':
            return {
                icon: Loader,
                label: status === 'processing' ? 'Processing' : 'In Progress',
                classes: 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/50 dark:text-blue-400 dark:border-blue-800',
            }
        case 'pending':
            return {
                icon: Clock,
                label: 'Pending',
                classes: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-400 dark:border-amber-800',
            }
        default:
            return {
                icon: Clock,
                label: status || 'Unknown',
                classes: 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-950/50 dark:text-gray-400 dark:border-gray-800',
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

        <div
            class="min-h-screen bg-gradient-to-br from-background via-background/95 to-background text-foreground font-sans overflow-x-hidden transition-all duration-300 pt-16"
        >
            <!-- Main Content -->
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
                <div :class="{ 'fade-in-up': isVisible }">
                    <!-- Breadcrumb -->
                    <nav class="flex items-center gap-2 text-sm text-muted-foreground mb-6 sm:mb-8">
                        <button
                            @click="goBack"
                            class="flex items-center gap-1.5 hover:text-foreground transition-colors"
                        >
                            <ArrowLeft class="w-4 h-4" />
                            Portfolio
                        </button>
                        <span class="text-border">/</span>
                        <span class="text-foreground font-medium truncate">{{ project.title }}</span>
                    </nav>

                    <!-- Project Header -->
                    <div class="mb-8 sm:mb-10">
                        <!-- Category & Status -->
                        <div class="flex flex-wrap items-center gap-2 mb-4">
                            <Badge
                                variant="secondary"
                                class="text-xs px-2.5 py-1"
                            >
                                <Folder class="w-3 h-3 mr-1.5" />
                                {{ project.project_type?.name || 'General' }}
                            </Badge>
                            <Badge
                                v-if="project.status"
                                variant="outline"
                                :class="statusConfig.classes"
                                class="text-xs px-2.5 py-1 font-medium"
                            >
                                <component :is="statusConfig.icon" class="w-3 h-3 mr-1.5" />
                                {{ statusConfig.label }}
                            </Badge>
                            <span v-if="formattedDate" class="flex items-center gap-1.5 text-xs text-muted-foreground ml-auto">
                                <Calendar class="w-3.5 h-3.5" />
                                {{ formattedDate }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h1
                            class="text-2xl sm:text-3xl lg:text-4xl font-bold text-foreground leading-tight mb-2"
                        >
                            {{ project.title }}
                        </h1>
                    </div>

                    <!-- Project Image -->
                    <div
                        v-if="project.image"
                        class="relative overflow-hidden rounded-xl mb-8 sm:mb-10 border border-border/50 shadow-sm"
                    >
                        <img
                            :src="project.image"
                            :alt="project.title"
                            class="w-full h-auto max-h-[500px] object-cover"
                        />
                    </div>
                    <div
                        v-else
                        class="relative overflow-hidden rounded-xl mb-8 sm:mb-10 border border-border/50 bg-muted/30 h-48 sm:h-64 flex items-center justify-center"
                    >
                        <Laptop class="w-16 h-16 text-muted-foreground/40" />
                    </div>

                    <!-- Content Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                        <!-- Main Content -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Description -->
                            <Card class="p-5 sm:p-6">
                                <h2 class="text-lg font-semibold text-foreground mb-3 flex items-center gap-2">
                                    <Folder class="w-5 h-5 text-primary" />
                                    About This Project
                                </h2>
                                <Separator class="mb-4" />
                                <p
                                    class="text-muted-foreground leading-relaxed text-sm sm:text-base whitespace-pre-line"
                                >
                                    {{ project.description }}
                                </p>
                            </Card>

                            <!-- Technologies -->
                            <Card v-if="technologies.length > 0" class="p-5 sm:p-6">
                                <h2 class="text-lg font-semibold text-foreground mb-3 flex items-center gap-2">
                                    <Tag class="w-5 h-5 text-primary" />
                                    Technologies Used
                                </h2>
                                <Separator class="mb-4" />
                                <div class="flex flex-wrap gap-2">
                                    <Badge
                                        v-for="tech in technologies"
                                        :key="tech"
                                        variant="outline"
                                        class="bg-primary/5 border-primary/20 text-foreground text-xs sm:text-sm px-3 py-1.5 font-medium hover:bg-primary/10 transition-colors"
                                    >
                                        {{ tech }}
                                    </Badge>
                                </div>
                            </Card>
                        </div>

                        <!-- Sidebar -->
                        <div class="space-y-6">
                            <!-- Quick Info -->
                            <Card class="p-5 sm:p-6">
                                <h2 class="text-lg font-semibold text-foreground mb-3">
                                    Project Details
                                </h2>
                                <Separator class="mb-4" />
                                <div class="space-y-4">
                                    <!-- Status -->
                                    <div v-if="project.status" class="flex items-center justify-between">
                                        <span class="text-sm text-muted-foreground">Status</span>
                                        <Badge
                                            variant="outline"
                                            :class="statusConfig.classes"
                                            class="text-xs font-medium"
                                        >
                                            {{ statusConfig.label }}
                                        </Badge>
                                    </div>

                                    <!-- Date -->
                                    <div v-if="formattedDate" class="flex items-center justify-between">
                                        <span class="text-sm text-muted-foreground">Date</span>
                                        <span class="text-sm text-foreground">{{ formattedDate }}</span>
                                    </div>

                                    <!-- Category -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-muted-foreground">Category</span>
                                        <Badge variant="secondary" class="text-xs">
                                            {{ project.project_type?.name || 'General' }}
                                        </Badge>
                                    </div>

                                    <!-- Tech Count -->
                                    <div v-if="technologies.length > 0" class="flex items-center justify-between">
                                        <span class="text-sm text-muted-foreground">Technologies</span>
                                        <span class="text-sm text-foreground font-medium">{{ technologies.length }}</span>
                                    </div>
                                </div>
                            </Card>

                            <!-- Project Links -->
                            <Card v-if="projectLinks.length > 0" class="p-5 sm:p-6">
                                <h2 class="text-lg font-semibold text-foreground mb-3">
                                    Links
                                </h2>
                                <Separator class="mb-4" />
                                <div class="space-y-2.5">
                                    <a
                                        v-for="link in projectLinks"
                                        :key="link.url"
                                        :href="link.url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="flex items-center gap-3 px-4 py-3 rounded-lg border border-border bg-background hover:bg-accent hover:border-primary/30 transition-all group w-full"
                                    >
                                        <div
                                            class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                                            :class="
                                                link.isGithub
                                                    ? 'bg-gray-100 dark:bg-gray-800'
                                                    : 'bg-primary/10'
                                            "
                                        >
                                            <Github
                                                v-if="link.isGithub"
                                                class="w-4.5 h-4.5 text-foreground"
                                            />
                                            <ExternalLink
                                                v-else
                                                class="w-4.5 h-4.5 text-primary"
                                            />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-foreground group-hover:text-primary transition-colors truncate">
                                                {{ link.name }}
                                            </p>
                                            <p class="text-xs text-muted-foreground truncate">
                                                {{ link.url.replace(/^https?:\/\//, '').split('/')[0] }}
                                            </p>
                                        </div>
                                        <ExternalLink class="w-4 h-4 text-muted-foreground group-hover:text-primary transition-colors shrink-0" />
                                    </a>
                                </div>
                            </Card>
                        </div>
                    </div>

                    <!-- Previous / Next Navigation -->
                    <div class="mt-10 sm:mt-12 pt-6 border-t border-border">
                        <div class="flex items-center justify-between gap-4">
                            <!-- Previous -->
                            <button
                                v-if="previousProject"
                                @click="goToProject(previousProject.id)"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-accent transition-colors text-left group max-w-[45%]"
                            >
                                <ChevronLeft class="w-5 h-5 text-muted-foreground group-hover:text-primary transition-colors shrink-0" />
                                <div class="min-w-0">
                                    <p class="text-xs text-muted-foreground mb-0.5">Previous</p>
                                    <p class="text-sm font-medium text-foreground group-hover:text-primary transition-colors truncate">
                                        {{ previousProject.title }}
                                    </p>
                                </div>
                            </button>
                            <div v-else></div>

                            <!-- Back to Portfolio -->
                            <button
                                @click="goBack"
                                class="px-4 py-2 text-sm font-medium text-muted-foreground hover:text-foreground border border-border hover:border-primary/30 rounded-lg transition-colors hidden sm:block"
                            >
                                All Projects
                            </button>

                            <!-- Next -->
                            <button
                                v-if="nextProject"
                                @click="goToProject(nextProject.id)"
                                class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-accent transition-colors text-right group max-w-[45%]"
                            >
                                <div class="min-w-0">
                                    <p class="text-xs text-muted-foreground mb-0.5">Next</p>
                                    <p class="text-sm font-medium text-foreground group-hover:text-primary transition-colors truncate">
                                        {{ nextProject.title }}
                                    </p>
                                </div>
                                <ChevronRight class="w-5 h-5 text-muted-foreground group-hover:text-primary transition-colors shrink-0" />
                            </button>
                            <div v-else></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </FrontendLayout>
</template>

<style scoped>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}
</style>
