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
    projects: Array
})

const isVisible       = ref(false)
const selectedFilter = ref('all')

const projectsData = [
    {
        id: 1,
        title: 'Online Cinema Platform',
        description: 'A comprehensive streaming platform featuring user authentication, personalized recommendations, watchlists, and responsive design for seamless viewing across all devices.',
        image: '/api/placeholder/600/400',
        technologies: ['React', 'Node.js', 'MongoDB', 'Express.js', 'Socket.io'],
        category: 'fullstack',
        date: 'September 2023',
        status: 'completed',
        links: {
            demo: '#',
            github: '#'
        }
    },
    {
        id: 2,
        title: 'E-Commerce Dashboard',
        description: 'Modern admin dashboard for e-commerce management with real-time analytics, inventory tracking, and order management system.',
        image: '/api/placeholder/600/400',
        technologies: ['Vue.js', 'TypeScript', 'PostgreSQL', 'Express.js', 'Chart.js'],
        category: 'frontend',
        date: 'June 2023',
        status: 'completed',
        links: {
            demo: '#',
            github: '#'
        }
    },
    {
        id: 3,
        title: 'Task Management API',
        description: 'RESTful API for task management with authentication, role-based permissions, file uploads, and real-time notifications.',
        image: '/api/placeholder/600/400',
        technologies: ['Go', 'PostgreSQL', 'Redis', 'Docker', 'JWT'],
        category: 'backend',
        date: 'March 2023',
        status: 'completed',
        links: {
            demo: '#',
            github: '#'
        }
    },
    {
        id: 4,
        title: 'Social Media Analytics',
        description: 'Data visualization platform for social media metrics with interactive charts, real-time updates, and export functionality.',
        image: '/api/placeholder/600/400',
        technologies: ['Next.js', 'D3.js', 'Python', 'FastAPI', 'PostgreSQL'],
        category: 'fullstack',
        date: 'December 2022',
        status: 'completed',
        links: {
            demo: '#',
            github: '#'
        }
    },
    {
        id: 5,
        title: 'Mobile Banking App',
        description: 'Cross-platform mobile application for banking services with biometric authentication and real-time transaction tracking.',
        image: '/api/placeholder/600/400',
        technologies: ['React Native', 'TypeScript', 'Node.js', 'MongoDB'],
        category: 'mobile',
        date: 'October 2022',
        status: 'completed',
        links: {
            demo: '#',
            github: '#'
        }
    },
    {
        id: 6,
        title: 'AI Content Generator',
        description: 'AI-powered platform for generating marketing content with natural language processing and customizable templates.',
        image: '/api/placeholder/600/400',
        technologies: ['Python', 'Flask', 'OpenAI API', 'React', 'PostgreSQL'],
        category: 'ai',
        date: 'August 2022',
        status: 'completed',
        links: {
            demo: '#',
            github: '#'
        }
    }
]

const filteredProjects = ref(projectsData)

const filterProjects = (category) => {
    selectedFilter.value = category
    if (category === 'all') {
        filteredProjects.value = projectsData
    } else {
        filteredProjects.value = projectsData.filter(project => project.category === category)
    }
}

onMounted(() => {
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
        <section class="pt-6 sm:pt-8 pb-8 px-4 max-w-6xl mx-auto text-center">
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
                    :class="selectedFilter === 'all' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-accent bg-background'"
                    class="px-3 py-1.5 text-sm font-medium border border-border rounded-md transition-colors"
                >
                    All Projects
                </button>
                <button 
                    @click="filterProjects('fullstack')"
                    :class="selectedFilter === 'fullstack' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-accent bg-background'"
                    class="px-3 py-1.5 text-sm font-medium border border-border rounded-md transition-colors"
                >
                    Full Stack
                </button>
                <button 
                    @click="filterProjects('frontend')"
                    :class="selectedFilter === 'frontend' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-accent bg-background'"
                    class="px-3 py-1.5 text-sm font-medium border border-border rounded-md transition-colors"
                >
                    Frontend
                </button>
                <button 
                    @click="filterProjects('backend')"
                    :class="selectedFilter === 'backend' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-accent bg-background'"
                    class="px-3 py-1.5 text-sm font-medium border border-border rounded-md transition-colors"
                >
                    Backend
                </button>
                <button 
                    @click="filterProjects('mobile')"
                    :class="selectedFilter === 'mobile' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-accent bg-background'"
                    class="px-3 py-1.5 text-sm font-medium border border-border rounded-md transition-colors"
                >
                    Mobile
                </button>
                <button 
                    @click="filterProjects('ai')"
                    :class="selectedFilter === 'ai' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground hover:bg-accent bg-background'"
                    class="px-3 py-1.5 text-sm font-medium border border-border rounded-md transition-colors"
                >
                    AI/ML
                </button>
            </div>
        </section>

        <!-- Projects Section -->
        <section class="pb-12 px-4 max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                <Card
                    v-for="(project, index) in filteredProjects"
                    :key="project.id"
                    class="bg-card/50 backdrop-blur-md border-border/50 hover:bg-card/70 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl hover:shadow-primary/10 group overflow-hidden"
                    :class="{ 'fade-in-up': isVisible }"
                    :style="{ animationDelay: `${index * 0.1}s` }"
                >
                    <CardHeader class="p-0 relative overflow-hidden">
                        <div class="h-36 lg:h-48 bg-gradient-to-br from-primary to-primary/70 flex items-center justify-center relative group-hover:scale-105 transition-transform duration-300">
                            <Laptop class="w-12 h-12 text-primary-foreground/80" />
                            <div class="absolute inset-0 bg-background/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                <div class="flex gap-4">
                                    <a :href="project.links.demo" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-foreground bg-card/80 hover:bg-card border border-border rounded-md transition-colors">
                                        <Eye class="w-4 h-4 mr-2" />
                                        Demo
                                    </a>
                                    <a :href="project.links.github" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-foreground bg-card/80 hover:bg-card border border-border rounded-md transition-colors">
                                        <Github class="w-4 h-4 mr-2" />
                                        Code
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
                                {{ project.date }}
                            </Badge>
                        </div>
                        
                        <CardDescription class="text-muted-foreground text-sm leading-relaxed mb-3 line-clamp-2 lg:line-clamp-3">
                            {{ project.description }}
                        </CardDescription>
                        
                        <div class="flex flex-wrap gap-1 mb-3">
                            <Badge
                                v-for="tech in project.technologies"
                                :key="tech"
                                variant="outline"
                                class="bg-accent/50 border-border text-foreground text-xs hover:bg-accent transition-all"
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