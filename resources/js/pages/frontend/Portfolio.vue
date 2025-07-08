<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Laptop, Eye, Github, Folder, Star } from 'lucide-vue-next'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import DockNavigation from '@/components/DockNavigation.vue'

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
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-background via-background/95 to-background text-foreground font-sans overflow-x-hidden transition-all duration-300 pt-16">
        <DockNavigation currentRoute="/portfolio" />

        <!-- Portfolio Hero Section -->
        <section class="pt-6 sm:pt-8 pb-8 px-4 max-w-6xl mx-auto text-center hero-section">
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

        <!-- Filter Section -->
        <section class="py-6 px-4 max-w-6xl mx-auto">
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

        <!-- Projects Section -->
        <section class="pb-12 px-4 max-w-6xl mx-auto">
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
            
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 card-grid">
                <Card
                    v-for="(project, index) in filteredProjects"
                    :key="project.id"
                    class="bg-card/50 backdrop-blur-md border-border/50 hover:bg-card/70 active:bg-card/60 transition-all duration-300 hover:-translate-y-2 active:translate-y-0 hover:shadow-xl hover:shadow-primary/10 group overflow-hidden"
                    :class="{ 'fade-in-up': isVisible }"
                    :style="{ animationDelay: `${index * 0.05}s` }"
                >
                    <CardHeader class="p-0 relative overflow-hidden">
                        <div class="h-36 lg:h-48 relative group-hover:scale-105 group-active:scale-102 transition-transform duration-300 overflow-hidden">
                            <img 
                                v-if="project.image" 
                                :src="project.image" 
                                :alt="project.title"
                                class="w-full h-full object-cover"
                            />
                            <div 
                                v-else 
                                class="w-full h-full bg-gradient-to-br from-primary to-primary/70 flex items-center justify-center"
                            >
                                <Laptop class="w-12 h-12 text-primary-foreground/80" />
                            </div>
                            <div class="absolute inset-0 bg-background/60 opacity-0 group-hover:opacity-100 group-active:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                <div class="flex gap-4" v-if="project.links && Array.isArray(project.links) && project.links.length > 0">
                                    <a 
                                        v-for="(linkObj, index) in project.links.slice(0, 2)" 
                                        :key="index"
                                        :href="Object.values(linkObj)[0]" 
                                        target="_blank" 
                                        rel="noopener noreferrer" 
                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-foreground bg-card/80 hover:bg-card active:bg-card/60 border border-border rounded-md transition-colors"
                                    >
                                        <component 
                                            :is="Object.keys(linkObj)[0].toLowerCase().includes('github') ? Github : Eye" 
                                            class="w-4 h-4 mr-2" 
                                        />
                                        {{ Object.keys(linkObj)[0] }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </CardHeader>
                    
                    <CardContent class="p-4 lg:p-6">
                        <div class="flex justify-between items-start mb-3">
                            <CardTitle class="text-base lg:text-lg font-semibold text-foreground line-clamp-2">
                                {{ project.title }}
                            </CardTitle>
                            <Badge variant="secondary" class="bg-primary/20 text-primary border-primary/30 text-xs shrink-0 ml-2">
                                {{ project.project_type?.name || 'General' }}
                            </Badge>
                        </div>
                        
                        <CardDescription class="text-muted-foreground text-sm leading-relaxed mb-3 line-clamp-2 lg:line-clamp-3">
                            {{ project.description }}
                        </CardDescription>
                        
                        <div class="flex flex-wrap gap-1 mb-3" v-if="project.technologies && project.technologies.length > 0">
                            <Badge
                                v-for="tech in project.technologies"
                                :key="tech"
                                variant="outline"
                                class="bg-accent/50 border-border text-foreground text-xs hover:bg-accent active:bg-accent/70 transition-all"
                            >
                                {{ tech }}
                            </Badge>
                        </div>
                    </CardContent>
                    
                    <CardFooter class="px-4 lg:px-6 pb-4 lg:pb-6 pt-0">
                        <div class="flex justify-between items-center w-full">
                            <div class="flex items-center gap-1 text-muted-foreground">
                                <Star class="w-4 h-4" />
                                <span class="text-sm">Featured</span>
                            </div>
                            <Badge class="bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border-emerald-500/30 capitalize">
                                {{ project.status }}
                            </Badge>
                        </div>
                    </CardFooter>
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
    animation: fadeInUp 1s ease-out forwards;
}
</style>