<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Laptop, Eye, Github, Folder, Star, ArrowLeft, ChevronLeft, ChevronRight, Calendar, User, ExternalLink } from 'lucide-vue-next'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import DockNavigation from '@/components/DockNavigation.vue'
import MusicPlayer from '@/components/MusicPlayer.vue'

const props = defineProps({
    title: String,
    description: String,
    projects: {
        type: Array,
        required: true
    },
    projectTypes: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({ type: '', search: '' })
    }
})

const isVisible       = ref(false)
const selectedFilter = ref('all')
const selectedProject = ref(null)
const showDetails = ref(false)

const filteredProjects = ref([])

const filterProjects = (category) => {
    selectedFilter.value = category
    if (category === 'all') {
        filteredProjects.value = props.projects
    } else {
        filteredProjects.value = props.projects.filter(project => project.project_type?.id === category)
    }
    console.log('Filtered projects:', filteredProjects.value)
}

const openProjectDetails = (project) => {
    selectedProject.value = project
    showDetails.value = true
    // Reset animation trigger
    isVisible.value = false
    setTimeout(() => {
        isVisible.value = true
    }, 50)
}

const closeProjectDetails = () => {
    showDetails.value = false
    selectedProject.value = null
    // Reset animation trigger
    isVisible.value = false
    setTimeout(() => {
        isVisible.value = true
    }, 50)
}

const navigateToProject = (direction) => {
    const currentIndex = filteredProjects.value.findIndex(p => p.id === selectedProject.value.id)
    let nextIndex
    
    if (direction === 'next') {
        nextIndex = (currentIndex + 1) % filteredProjects.value.length
    } else {
        nextIndex = currentIndex === 0 ? filteredProjects.value.length - 1 : currentIndex - 1
    }
    
    // Smooth transition between projects
    isVisible.value = false
    setTimeout(() => {
        selectedProject.value = filteredProjects.value[nextIndex]
        isVisible.value = true
    }, 150)
}

onMounted(() => {
    console.log('Props received:', props)
    console.log('Projects:', props.projects)
    console.log('Project Types:', props.projectTypes)
    
    // Initialize filtered projects
    filteredProjects.value = props.projects || []
    
    setTimeout(() => {
        isVisible.value = true
    }, 100)
})
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="description" />
        <meta name="keywords" content="portfolio, projects, web development, mobile apps, Vue.js, Laravel, programming projects" />
        <meta name="author" content="Software Developer" />
        
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
        <meta name="robots" content="index, follow" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="canonical" :href="$page.url" />
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-background via-background/95 to-background text-foreground font-sans overflow-x-hidden transition-all duration-300 pt-16">
        <DockNavigation currentRoute="/portfolio" />
        
        <!-- Music Player -->
        <MusicPlayer />

        <!-- Portfolio Hero Section -->
        <section v-if="!showDetails" class="pt-6 sm:pt-8 pb-8 px-4 max-w-6xl mx-auto text-center hero-section mt-5">
            <div class="space-y-4" :class="{ 'fade-in-up': isVisible }">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <Badge variant="secondary" class="text-sm px-4 py-2">
                        <Folder class="w-4 h-4 mr-2" />
                        Portfolio
                    </Badge>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black bg-gradient-to-br from-primary to-primary/70 bg-clip-text text-transparent leading-tight">
                    My Portfolio
                </h1>
                <p class="text-base lg:text-lg text-muted-foreground leading-relaxed max-w-3xl mx-auto">
                    Explore my collection of projects showcasing modern web development, mobile applications, and innovative solutions.
                </p>
            </div>
        </section>
        
        <!-- Project Detail Hero Section -->
        <section v-if="showDetails && selectedProject" class="pt-6 sm:pt-8 pb-8 px-4 max-w-6xl mx-auto mt-5">
            <div class="space-y-4" :class="{ 'fade-in-up': isVisible }">
                <!-- Back Button -->
                <div class="flex items-center gap-4 mb-6">
                    <button 
                        @click="closeProjectDetails"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-muted-foreground hover:text-foreground bg-accent/50 hover:bg-accent rounded-lg transition-colors"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        Back to Portfolio
                    </button>
                    <Badge variant="secondary" class="text-sm px-3 py-1">
                        {{ selectedProject.project_type?.name || 'General' }}
                    </Badge>
                </div>
                
                <div class="text-left">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black bg-gradient-to-br from-primary to-primary/70 bg-clip-text text-transparent leading-tight mb-4">
                        {{ selectedProject.title }}
                    </h1>
                    <p class="text-base lg:text-lg text-muted-foreground leading-relaxed max-w-4xl">
                        {{ selectedProject.description }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Filter Section -->
        <section v-if="!showDetails" class="py-6 px-4 max-w-6xl mx-auto">
            <div class="flex justify-center flex-wrap gap-3 mb-8" :class="{ 'fade-in-up': isVisible }">
            <button 
                @click="filterProjects('all')"
                :class="selectedFilter === 'all' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-accent active:bg-accent active:text-foreground bg-background'"
                class="px-3 py-1.5 text-sm font-medium border border-border rounded-md transition-colors"
            >
                All Projects
            </button>
            <button 
                v-for="projectType in projectTypes"
                :key="projectType.id"
                @click="filterProjects(projectType.id)"
                :class="selectedFilter === projectType.id ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-accent active:bg-accent active:text-foreground bg-background'"
                class="px-3 py-1.5 text-sm font-medium border border-border rounded-md transition-colors"
            >
                {{ projectType.name }}
            </button>
            </div>
        </section>

        <!-- Projects Grid Section -->
        <section v-if="!showDetails" class="pb-12 px-4 max-w-6xl mx-auto">
            <!-- Debug Info -->
            <div v-if="$page.props.debug" class="mb-4 p-4 bg-gray-100 rounded">
                <p>Projects count: {{ projects.length }}</p>
                <p>Filtered projects count: {{ filteredProjects.length }}</p>
                <p>Project types count: {{ projectTypes.length }}</p>
            </div>
            
            <!-- No Projects Message -->
            <div v-if="filteredProjects.length === 0" class="text-center py-12">
                <div class="max-w-md mx-auto">
                    <Folder class="w-16 h-16 mx-auto text-muted-foreground mb-4" />
                    <h3 class="text-lg font-semibold text-foreground mb-2">No Projects Found</h3>
                    <p class="text-muted-foreground">
                        {{ selectedFilter === 'all' ? 'No projects are available at the moment.' : 'No projects match the selected filter.' }}
                    </p>
                    <button 
                        v-if="selectedFilter !== 'all'"
                        @click="filterProjects('all')"
                        class="mt-4 px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 active:bg-primary/80 transition-colors"
                    >
                        Show All Projects
                    </button>
                </div>
            </div>
            
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 card-grid">
                <Card
                    v-for="(project, index) in filteredProjects"
                    :key="project.id"
                    class="group overflow-hidden bg-card/80 backdrop-blur-sm border-border/50 hover:border-primary/20 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-primary/5 cursor-pointer"
                    :class="{ 'fade-in-up': isVisible }"
                    :style="{ animationDelay: `${index * 0.05}s` }"
                    @click="openProjectDetails(project)"
                >
                    <!-- Project Image -->
                    <div class="relative overflow-hidden h-48 md:h-52">
                        <img 
                            v-if="project.image" 
                            :src="project.image" 
                            :alt="project.title"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                        />
                        <div 
                            v-else 
                            class="w-full h-full bg-gradient-to-br from-primary/20 via-primary/10 to-primary/5 flex items-center justify-center"
                        >
                            <Laptop class="w-12 h-12 text-primary/60" />
                        </div>
                        
                        <!-- Project Type Badge -->
                        <div class="absolute top-3 right-3">
                            <Badge variant="secondary" class="bg-background/90 text-foreground border-border/50 text-xs backdrop-blur-sm">
                                {{ project.project_type?.name || 'General' }}
                            </Badge>
                        </div>
                        
                        <!-- Click to view details overlay -->
                        <div class="absolute inset-0 bg-background/60 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <div class="text-center">
                                <Eye class="w-8 h-8 mx-auto mb-2 text-primary" />
                                <p class="text-sm font-medium text-foreground">Click to view details</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Project Content -->
                    <CardContent class="p-4 space-y-3">
                        <div class="space-y-2">
                            <CardTitle class="text-lg font-semibold text-foreground leading-tight line-clamp-2">
                                {{ project.title }}
                            </CardTitle>
                            
                            <CardDescription class="text-muted-foreground text-sm leading-relaxed line-clamp-2">
                                {{ project.description }}
                            </CardDescription>
                        </div>
                        
                        <!-- Technologies -->
                        <div class="flex flex-wrap gap-1.5" v-if="project.technologies && project.technologies.length > 0">
                            <Badge
                                v-for="tech in project.technologies.slice(0, 4)"
                                :key="tech"
                                variant="outline"
                                class="bg-accent/30 border-accent text-accent-foreground text-xs px-2 py-1 font-medium"
                            >
                                {{ tech }}
                            </Badge>
                            <Badge
                                v-if="project.technologies.length > 4"
                                variant="outline"
                                class="bg-muted/50 border-muted text-muted-foreground text-xs px-2 py-1"
                            >
                                +{{ project.technologies.length - 4 }}
                            </Badge>
                        </div>
                        
                        <!-- Tech Stack from database -->
                        <div class="flex flex-wrap gap-1.5" v-else-if="project.tech_stack">
                            <Badge
                                v-for="tech in project.tech_stack.split(',').slice(0, 4)"
                                :key="tech"
                                variant="outline"
                                class="bg-accent/30 border-accent text-accent-foreground text-xs px-2 py-1 font-medium"
                            >
                                {{ tech.trim() }}
                            </Badge>
                            <Badge
                                v-if="project.tech_stack.split(',').length > 4"
                                variant="outline"
                                class="bg-muted/50 border-muted text-muted-foreground text-xs px-2 py-1"
                            >
                                +{{ project.tech_stack.split(',').length - 4 }}
                            </Badge>
                        </div>
                    </CardContent>
                    
                    <!-- Project Footer -->
                    <CardFooter class="px-4 pb-4 pt-0">
                        <div class="flex justify-between items-center w-full">
                            <div class="flex items-center gap-1 text-muted-foreground">
                                <Star class="w-3.5 h-3.5" />
                                <span class="text-xs font-medium">Featured</span>
                            </div>
                            <Badge 
                                v-if="project.status"
                                :class="{
                                    'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border-emerald-500/30': project.status === 'completed',
                                    'bg-blue-500/20 text-blue-600 dark:text-blue-400 border-blue-500/30': project.status === 'in-progress',
                                    'bg-amber-500/20 text-amber-600 dark:text-amber-400 border-amber-500/30': project.status === 'pending'
                                }"
                                class="text-xs font-medium capitalize"
                            >
                                {{ project.status }}
                            </Badge>
                        </div>
                    </CardFooter>
                </Card>
            </div>
        </section>
        
        <!-- Project Details Page -->
        <section v-if="showDetails && selectedProject" class="pb-12 px-4 max-w-7xl mx-auto">
            <div class="space-y-8" :class="{ 'fade-in-up': isVisible }">
                
                <!-- Project Image -->
                <div class="relative overflow-hidden rounded-xl h-64 sm:h-80 md:h-96 lg:h-[28rem] shadow-2xl">
                    <img 
                        v-if="selectedProject.image" 
                        :src="selectedProject.image" 
                        :alt="selectedProject.title"
                        class="w-full h-full object-cover"
                    />
                    <div 
                        v-else 
                        class="w-full h-full bg-gradient-to-br from-primary/20 via-primary/10 to-primary/5 flex items-center justify-center"
                    >
                        <Laptop class="w-16 h-16 md:w-24 md:h-24 text-primary/60" />
                    </div>
                    
                    <!-- Project Links Overlay -->
                    <div class="absolute top-4 right-4 flex flex-col sm:flex-row gap-2" v-if="selectedProject.links && Array.isArray(selectedProject.links) && selectedProject.links.length > 0">
                        <a 
                            v-for="(linkObj, linkIndex) in selectedProject.links" 
                            :key="linkIndex"
                            :href="Object.values(linkObj)[0]" 
                            target="_blank" 
                            rel="noopener noreferrer" 
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-foreground bg-background/90 hover:bg-background border border-border rounded-lg transition-colors shadow-sm backdrop-blur-sm"
                        >
                            <component 
                                :is="Object.keys(linkObj)[0].toLowerCase().includes('github') ? Github : ExternalLink" 
                                class="w-4 h-4 mr-2" 
                            />
                            <span class="hidden sm:inline">{{ Object.keys(linkObj)[0] }}</span>
                        </a>
                    </div>
                </div>
                
                <!-- Project Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- About Section -->
                        <Card class="p-6">
                            <h3 class="text-xl font-semibold text-foreground mb-4">About This Project</h3>
                            <p class="text-muted-foreground leading-relaxed text-base">
                                {{ selectedProject.description }}
                            </p>
                        </Card>
                        
                        <!-- Technologies -->
                        <Card class="p-6" v-if="(selectedProject.technologies && selectedProject.technologies.length > 0) || selectedProject.tech_stack">
                            <h3 class="text-xl font-semibold text-foreground mb-4">Technologies Used</h3>
                            <div class="flex flex-wrap gap-2">
                                <!-- From technologies array -->
                                <template v-if="selectedProject.technologies && selectedProject.technologies.length > 0">
                                    <Badge
                                        v-for="tech in selectedProject.technologies"
                                        :key="tech"
                                        variant="outline"
                                        class="bg-accent/30 border-accent text-accent-foreground text-sm px-3 py-2 font-medium"
                                    >
                                        {{ tech }}
                                    </Badge>
                                </template>
                                <!-- From tech_stack string -->
                                <template v-else-if="selectedProject.tech_stack">
                                    <Badge
                                        v-for="tech in selectedProject.tech_stack.split(',')"
                                        :key="tech"
                                        variant="outline"
                                        class="bg-accent/30 border-accent text-accent-foreground text-sm px-3 py-2 font-medium"
                                    >
                                        {{ tech.trim() }}
                                    </Badge>
                                </template>
                            </div>
                        </Card>
                        
                    </div>
                    
                    <!-- Sidebar -->
                    <div class="space-y-6">
                        
                        <!-- Project Info -->
                        <Card class="p-6">
                            <h3 class="text-lg font-semibold text-foreground mb-4">Project Info</h3>
                            <div class="space-y-4">
                                
                                <!-- Status -->
                                <div v-if="selectedProject.status">
                                    <h4 class="text-sm font-medium text-foreground mb-2">Status</h4>
                                    <Badge 
                                        :class="{
                                            'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border-emerald-500/30': selectedProject.status === 'completed',
                                            'bg-blue-500/20 text-blue-600 dark:text-blue-400 border-blue-500/30': selectedProject.status === 'in-progress',
                                            'bg-amber-500/20 text-amber-600 dark:text-amber-400 border-amber-500/30': selectedProject.status === 'pending'
                                        }"
                                        class="text-sm font-medium capitalize"
                                    >
                                        {{ selectedProject.status }}
                                    </Badge>
                                </div>
                                
                                <!-- Date -->
                                <div v-if="selectedProject.created_at">
                                    <h4 class="text-sm font-medium text-foreground mb-2">Created</h4>
                                    <div class="flex items-center gap-2 text-muted-foreground">
                                        <Calendar class="w-4 h-4" />
                                        <span class="text-sm">{{ new Date(selectedProject.created_at).toLocaleDateString() }}</span>
                                    </div>
                                </div>
                                
                                <!-- Project Type -->
                                <div>
                                    <h4 class="text-sm font-medium text-foreground mb-2">Category</h4>
                                    <Badge variant="secondary" class="text-sm">
                                        {{ selectedProject.project_type?.name || 'General' }}
                                    </Badge>
                                </div>
                                
                            </div>
                        </Card>
                        
                        <!-- Project Links -->
                        <Card class="p-6" v-if="selectedProject.links && Array.isArray(selectedProject.links) && selectedProject.links.length > 0">
                            <h3 class="text-lg font-semibold text-foreground mb-4">Project Links</h3>
                            <div class="space-y-3">
                                <a 
                                    v-for="(linkObj, linkIndex) in selectedProject.links" 
                                    :key="linkIndex"
                                    :href="Object.values(linkObj)[0]" 
                                    target="_blank" 
                                    rel="noopener noreferrer" 
                                    class="flex items-center gap-3 p-3 rounded-lg border border-border hover:bg-accent transition-colors w-full"
                                >
                                    <component 
                                        :is="Object.keys(linkObj)[0].toLowerCase().includes('github') ? Github : ExternalLink" 
                                        class="w-5 h-5 text-primary" 
                                    />
                                    <span class="font-medium">{{ Object.keys(linkObj)[0] }}</span>
                                </a>
                            </div>
                        </Card>
                        
                    </div>
                    
                </div>
                
                <!-- Navigation to Other Projects -->
                <Card class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <button 
                            @click="navigateToProject('prev')"
                            class="flex items-center gap-2 px-6 py-3 text-sm font-medium text-muted-foreground hover:text-foreground bg-accent/50 hover:bg-accent rounded-lg transition-colors w-full sm:w-auto"
                            :disabled="filteredProjects.length <= 1"
                        >
                            <ChevronLeft class="w-4 h-4" />
                            Previous Project
                        </button>
                        
                        <span class="text-sm text-muted-foreground font-medium">
                            {{ filteredProjects.findIndex(p => p.id === selectedProject.id) + 1 }} of {{ filteredProjects.length }} projects
                        </span>
                        
                        <button 
                            @click="navigateToProject('next')"
                            class="flex items-center gap-2 px-6 py-3 text-sm font-medium text-muted-foreground hover:text-foreground bg-accent/50 hover:bg-accent rounded-lg transition-colors w-full sm:w-auto"
                            :disabled="filteredProjects.length <= 1"
                        >
                            Next Project
                            <ChevronRight class="w-4 h-4" />
                        </button>
                    </div>
                </Card>
                
            </div>
        </section>
    </div>
</template>

<style scoped>
/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
}

/* Smooth transitions for view switching */
.min-h-screen {
    transition: all 0.3s ease-in-out;
}

/* Enhanced responsive design */
@media (max-width: 768px) {
    .grid-cols-1.lg\:grid-cols-3 {
        gap: 1rem;
    }
    
    .space-y-6 > * + * {
        margin-top: 1rem;
    }
    
    .space-y-8 > * + * {
        margin-top: 1.5rem;
    }
}

@media (max-width: 640px) {
    .px-4 {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .gap-8 {
        gap: 1rem;
    }
    
    .p-6 {
        padding: 1rem;
    }
    
    .px-6 {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .py-3 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
}
</style>