<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { Laptop, Github, Folder, ExternalLink, Search, X } from 'lucide-vue-next'
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
import BentoGrid from '@/components/ui/bento-grid.vue'
import BentoGridItem from '@/components/ui/bento-grid-item.vue'

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

const staggerDelay = {
    badge: 0,
    title: 100,
    description: 200,
    filters: 300,
    grid: 400,
}

// Computed filtered projects
const filteredProjects = computed(() => {
    let result = props.projects

    // Filter by category
    if (selectedCategory.value !== 'all') {
        const categoryId = parseInt(selectedCategory.value)
        result = result.filter((project) => project.project_type?.id === categoryId)
    }

    // Filter by search query
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

// Clear search
const clearSearch = () => {
    searchQuery.value = ''
}

const openProjectDetails = (project: Project) => {
    router.visit(route('portfolio.show', project.id))
}

const getProjectIcon = (project: Project) => {
    if (!project.project_type) return Laptop
    if (project.project_type?.name?.toLowerCase().includes('web')) return Github
    return Folder
}

const getBentoItemClass = (index: number) => {
    return index === 3 || index === 6 ? 'md:col-span-2' : ''
}

// Helper to get GitHub URL from project
const getGithubUrl = (project: Project): string | undefined => {
    if (project.github_url) return project.github_url
    if (!project.links) return undefined
    const githubLink = project.links.find((link) =>
        Object.keys(link)[0]?.toLowerCase().includes('github'),
    )
    return githubLink ? Object.values(githubLink)[0] : undefined
}

// Helper to get live/demo URL from project
const getLiveUrl = (project: Project): string | undefined => {
    if (project.demo_url) return project.demo_url
    if (project.live_url) return project.live_url
    if (!project.links) return undefined
    const liveLink = project.links.find(
        (link) => !Object.keys(link)[0]?.toLowerCase().includes('github'),
    )
    return liveLink ? Object.values(liveLink)[0] : undefined
}

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
        setTimeout(() => {
            isVisible.value = true
        }, 50)
    }, 800)
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
        
        <!-- Open Graph Meta Tags -->
        <meta property="og:title" :content="title" />
        <meta property="og:description" :content="description" />
        <meta property="og:image" content="/portfolio-og-image.jpg" />
        <meta property="og:url" :content="$page.url" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="Developer Portfolio" />
        
        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="title" />
        <meta name="twitter:description" :content="description" />
        <meta name="twitter:image" content="/portfolio-og-image.jpg" />
        
        <!-- Additional SEO Meta Tags -->
        <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="format-detection" content="telephone=no" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <link rel="canonical" :href="$page.url" />
        </Head>

        <div class="min-h-screen bg-gradient-to-br from-background via-background/95 to-background text-foreground font-sans overflow-x-hidden transition-all duration-300 pt-16">

        <template v-if="isLoading">
            <!-- Hero Skeleton -->
            <section class="pt-6 sm:pt-8 pb-8 px-4 max-w-6xl mx-auto text-center mt-5">
                <div class="space-y-4 flex flex-col items-center">
                    <Skeleton class="h-9 w-28 rounded-full" />
                    <Skeleton class="h-10 sm:h-12 w-56 sm:w-72 rounded-lg" />
                    <div class="space-y-2 max-w-3xl w-full">
                        <Skeleton class="h-4 w-full rounded-lg mx-auto" />
                        <Skeleton class="h-4 w-3/4 rounded-lg mx-auto" />
                    </div>
                </div>
            </section>
            <!-- Filter Skeleton -->
            <section class="py-6 px-4 max-w-6xl mx-auto">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-center sm:gap-4 mb-8">
                    <Skeleton class="h-10 w-full sm:w-72 lg:w-80 rounded-lg" />
                    <Skeleton class="h-10 w-full sm:w-48 rounded-lg" />
                    <Skeleton class="h-5 w-20 rounded-lg" />
                </div>
            </section>
            <!-- Grid Skeleton -->
            <section class="pb-12 px-4 max-w-6xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-4xl mx-auto">
                    <Skeleton v-for="i in 6" :key="i" class="h-56 rounded-lg" :class="i === 4 || i === 7 ? 'md:col-span-2' : ''" />
                </div>
            </section>
        </template>

        <template v-else>
            <!-- Portfolio Hero Section -->
            <section class="pt-6 sm:pt-8 pb-8 px-4 max-w-6xl mx-auto text-center hero-section mt-5">
                <div class="space-y-4">
                    <div
                        class="flex items-center justify-center gap-3 mb-4 stagger-fade-in"
                        :class="{ 'animate': isVisible }"
                        :style="{ animationDelay: staggerDelay.badge + 'ms' }"
                    >
                        <Badge variant="secondary" class="text-sm px-4 py-2">
                            <Folder class="w-4 h-4 mr-2" />
                            Portfolio
                        </Badge>
                    </div>
                    <h1
                        class="text-3xl sm:text-4xl lg:text-5xl font-black bg-gradient-to-br from-primary to-primary/70 bg-clip-text text-transparent leading-tight stagger-fade-in"
                        :class="{ 'animate': isVisible }"
                        :style="{ animationDelay: staggerDelay.title + 'ms' }"
                    >
                        My Portfolio
                    </h1>
                    <p
                        class="text-base lg:text-lg text-muted-foreground leading-relaxed max-w-3xl mx-auto stagger-fade-in"
                        :class="{ 'animate': isVisible }"
                        :style="{ animationDelay: staggerDelay.description + 'ms' }"
                    >
                        Explore my collection of projects showcasing modern web development, mobile applications, and innovative solutions.
                    </p>
                </div>
            </section>

            <!-- Filter Section -->
            <section class="py-6 px-4 max-w-6xl mx-auto">
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-center sm:gap-4 mb-8 stagger-fade-in"
                    :class="{ 'animate': isVisible }"
                    :style="{ animationDelay: staggerDelay.filters + 'ms' }"
                >
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-72 lg:w-80">
                        <Search
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground pointer-events-none"
                        />
                        <Input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search projects..."
                            class="pl-9 pr-9 h-10 bg-background border-border/60 focus:border-primary/50"
                        />
                        <button
                            v-if="searchQuery"
                            @click="clearSearch"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors"
                        >
                            <X class="size-4" />
                        </button>
                    </div>

                    <!-- Category Dropdown -->
                    <Select v-model="selectedCategory">
                        <SelectTrigger class="w-full sm:w-48 h-10 bg-background border-border/60">
                            <Folder class="size-4 mr-2 text-muted-foreground" />
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

                    <!-- Results count -->
                    <div class="text-sm text-muted-foreground text-center sm:text-left">
                        <span class="font-medium text-foreground">{{ filteredProjects.length }}</span>
                        {{ filteredProjects.length === 1 ? 'project' : 'projects' }}
                    </div>
                </div>
            </section>

            <!-- Projects Grid Section -->
            <section
                class="pb-12 px-4 max-w-6xl mx-auto stagger-fade-in"
                :class="{ 'animate': isVisible }"
                :style="{ animationDelay: staggerDelay.grid + 'ms' }"
            >
                <!-- Debug Info -->
                <div v-if="$page.props.debug" class="mb-4 p-4 bg-gray-100 rounded">
                    <p>Projects count: {{ projects.length }}</p>
                    <p>Filtered projects count: {{ filteredProjects.length }}</p>
                    <p>Project types count: {{ projectTypes.length }}</p>
                </div>

                <!-- No Projects Message -->
                <div v-if="filteredProjects.length === 0" class="text-center py-12">
                    <div class="max-w-md mx-auto">
                        <Search class="size-16 mx-auto text-muted-foreground mb-4" />
                        <h3 class="text-lg font-semibold text-foreground mb-2">No Projects Found</h3>
                        <p class="text-muted-foreground mb-4">
                            <template v-if="searchQuery && selectedCategory !== 'all'">
                                No projects match "{{ searchQuery }}" in the selected category.
                            </template>
                            <template v-else-if="searchQuery">
                                No projects match "{{ searchQuery }}".
                            </template>
                            <template v-else-if="selectedCategory !== 'all'">
                                No projects in this category.
                            </template>
                            <template v-else> No projects are available at the moment. </template>
                        </p>
                        <div class="flex flex-wrap justify-center gap-2">
                            <button
                                v-if="searchQuery"
                                @click="clearSearch"
                                class="px-4 py-2 bg-accent text-accent-foreground rounded-md hover:bg-accent/80 transition-colors"
                            >
                                Clear Search
                            </button>
                            <button
                                v-if="selectedCategory !== 'all'"
                                @click="selectedCategory = 'all'"
                                class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors"
                            >
                                Show All Categories
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Projects Bento Grid -->
                <BentoGrid v-else class="max-w-4xl mx-auto">
                    <BentoGridItem
                        v-for="(project, index) in filteredProjects"
                        :key="project.id"
                        :title="project.title"
                        :description="project.description"
                        :icon="getProjectIcon(project)"
                        :class="getBentoItemClass(index)"
                        @click="openProjectDetails(project)"
                    >
                        <template #header>
                            <div class="w-full h-full rounded-lg relative overflow-hidden bg-gradient-to-br from-muted/80 to-muted/40">
                                <!-- Project Image -->
                                <img
                                    v-if="project.image"
                                    :src="project.image"
                                    :alt="project.title"
                                    class="w-full h-full object-cover"
                                />
                                <div
                                    v-else
                                    class="w-full h-full flex items-center justify-center"
                                >
                                    <Laptop class="size-10 sm:size-12 text-muted-foreground/50" />
                                </div>

                                <!-- Category Badge (top right) -->
                                <div class="absolute top-2 right-2">
                                    <Badge
                                        variant="secondary"
                                        class="bg-background/95 text-foreground shadow-sm text-xs px-2 py-1"
                                    >
                                        {{ project.project_type?.name || 'General' }}
                                    </Badge>
                                </div>

                                <!-- Bottom overlay with status and links -->
                                <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/60 to-transparent">
                                    <div class="flex items-center justify-between">
                                        <!-- Status Badge -->
                                        <Badge
                                            v-if="project.status"
                                            :class="{
                                                'bg-emerald-500/90 text-white border-emerald-400':
                                                    project.status === 'completed',
                                                'bg-blue-500/90 text-white border-blue-400':
                                                    project.status === 'in-progress' || project.status === 'processing',
                                                'bg-amber-500/90 text-white border-amber-400':
                                                    project.status === 'pending'
                                            }"
                                            class="text-xs px-2 py-0.5 font-medium capitalize shadow-sm"
                                        >
                                            {{ project.status }}
                                        </Badge>
                                        <span v-else></span>

                                        <!-- Links -->
                                        <div class="flex gap-1.5">
                                            <a
                                                v-if="getGithubUrl(project)"
                                                :href="getGithubUrl(project)"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="size-7 flex items-center justify-center rounded-md bg-white/90 hover:bg-white text-gray-800 transition-colors shadow-sm"
                                                @click.stop
                                            >
                                                <Github class="size-4" />
                                            </a>
                                            <a
                                                v-if="getLiveUrl(project)"
                                                :href="getLiveUrl(project)"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="size-7 flex items-center justify-center rounded-md bg-white/90 hover:bg-white text-gray-800 transition-colors shadow-sm"
                                                @click.stop
                                            >
                                                <ExternalLink class="size-4" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </BentoGridItem>
                </BentoGrid>
            </section>
        </template>
        </div>
    </FrontendLayout>
</template>

<style scoped>
.stagger-fade-in {
    opacity: 0;
    transform: translateY(20px);
}

.stagger-fade-in.animate {
    animation: staggerFadeInUp 0.6s ease-out forwards;
}

@keyframes staggerFadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .stagger-fade-in {
        opacity: 1;
        transform: none;
    }
    .stagger-fade-in.animate {
        animation: none;
    }
}
</style>