<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Separator } from '@/components/ui/separator'
import { Calendar, MapPin, GraduationCap, Briefcase, Code, Award } from 'lucide-vue-next'
import DockNavigation from '@/components/DockNavigation.vue'

const props = defineProps({
    title: String,
    description: String
})

const isVisible = ref(false)

const education = [
    {
        period: '2021-2025',
        title: 'Bachelor of Information Technology',
        institution: 'BBU (Build Bright University)',
        description: 'Studying advanced concepts in Information Technology and software development. Focused on fullstack development, data structures, algorithms, and software engineering principles.',
        type: 'education'
    },
    {
        period: '2019-2021',
        title: 'High School Diploma',
        institution: 'KDHS (Kouk Doung High School)',
        description: 'Studied at High School for Specialized Studies, Siem Reap, Cambodia.',
        type: 'education'
    }
]

const experience = [
    {
        period: '2023-Present',
        title: 'Backend Laravel Dev',
        company: 'EOT (eOcambo Technology)',
        description: 'Developing and maintaining backend systems using Laravel and PHP. Focused on building scalable APIs, optimizing database queries, and ensuring application security. Collaborating with frontend developers to integrate user-facing elements.',
        type: 'experience'
    },
    {
        period: '2023-2024',
        title: 'Junior Backend Laravel Dev',
        company: 'EOT (eOcambo Technology)',
        description: 'Started career in software development. Worked on backend systems using Laravel and PHP. Gained experience in RESTful APIs, database design, and application security.',
        type: 'experience'
    }
]

const techStack = [
    // Frontend Technologies
    {
        name: 'HTML',
        category: 'Frontend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg',
        description: 'Standard markup language for creating web pages and applications.'
    },
    {
        name: 'CSS',
        category: 'Frontend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg',
        description: 'Style sheet language used for describing the presentation of a document written in HTML.'
    },
    {
        name: 'JavaScript',
        category: 'Frontend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg',
        description: 'High-level programming language for web development and interactive applications.'
    },
    {
        name: 'Tailwind CSS',
        category: 'Frontend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/tailwindcss/tailwindcss-original.svg',
        description: 'Utility-first CSS framework for rapid UI development.'
    },
    {
        name: 'Bootstrap',
        category: 'Frontend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bootstrap/bootstrap-original.svg',
        description: 'Popular CSS framework for building responsive and mobile-first websites.'
    },
    {
        name: 'PHP',
        category: 'Backend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg',
        description: 'Popular server-side scripting language for web development.'
    },
    {
        name: 'Laravel',
        category: 'Backend',
        image: 'https://cdn.worldvectorlogo.com/logos/laravel-2.svg',
        description: 'PHP framework for building web applications with elegant syntax and powerful features.'
    },
    {
        name: 'Vue.js',
        category: 'Frontend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg',
        description: 'Progressive JavaScript framework for building modern web applications.'
    },
    {
        name: 'Next.js',
        category: 'Frontend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nextjs/nextjs-original.svg',
        description: 'React framework for production with server-side rendering and static site generation.'
    },
    // Database Technologies
    {
        name: 'PostgreSQL',
        category: 'Database',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/postgresql/postgresql-original.svg',
        description: 'Advanced open-source relational database with powerful features.'
    },
    {
        name: 'MySQL',
        category: 'Database',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg',
        description: 'Popular open-source relational database management system.'
    },
    // DevOps & Tools
    {
        name: 'Git',
        category: 'DevOps & Tools',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/git/git-original.svg',
        description: 'Distributed version control system for tracking changes in source code.'
    },
    {
        name: 'Linux (UBT)',
        category: 'DevOps & Tools',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/linux/linux-original.svg',
        description: 'Open-source operating system kernel widely used in server environments.'
    },
    // Design & Media
    {
        name: 'Figma',
        category: 'Design & Media',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/figma/figma-original.svg',
        description: 'Collaborative design tool for creating user interfaces and prototypes.'
    },
    {
        name: 'Canva',
        category: 'Design & Media',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/canva/canva-original.svg',
        description: 'User-friendly graphic design tool for creating visual content.'
    },
    //Server & Hostings
    { 
        name: 'Vercel',
        category: 'Server & Hosting',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vercel/vercel-original.svg',
        description: 'Platform for deploying and hosting web applications with serverless functions.'
    },
    { 
        name: 'DigitalOcean',
        category: 'Server & Hosting',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/digitalocean/digitalocean-original.svg',
        description: 'Cloud infrastructure provider offering scalable virtual servers and managed databases.'
    },
    { 
        name: 'cPanel',
        category: 'Server & Hosting',
        image: 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/CPanel_logo.svg/2560px-CPanel_logo.svg.png',
        description: 'Cloud infrastructure provider offering scalable virtual servers and managed databases.'
    },

]

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
        <!-- Dock Navigation -->
        <DockNavigation currentRoute="/about" />

        <!-- About Hero Section -->
        <section class="pt-6 sm:pt-8 pb-12 px-4 max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-8 lg:gap-12 items-start" :class="{ 'fade-in-up': isVisible }">
                <div class="space-y-6">
                    <div class="space-y-4">
                        <Badge variant="outline" class="w-fit px-4 py-2 bg-primary/10 border-primary/20 text-primary">
                            <Code class="w-3 h-3 mr-2" />
                            Software Developer
                        </Badge>
                        
                        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black leading-tight">
                            <span class="bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                                About Me
                            </span>
                        </h1>
                    </div>
                    
                    <div class="prose prose-base lg:prose-lg max-w-none text-muted-foreground leading-relaxed space-y-4">
                        <p class="text-base lg:text-lg">
                            Hi, I'm <span class="font-semibold text-foreground">SROS THAI</span>, a passionate software developer with a focus on backend development using Laravel and PHP. I have a strong foundation in fullstack development, data structures, algorithms, and software engineering principles.
                        </p>
                        
                        <p>
                            My goal is to create efficient and scalable applications that solve real-world problems. I am currently pursuing a Bachelor of Information Technology at BBU (Build Bright University) and have gained valuable experience working as a Backend Laravel Developer at EOT (eOcambo Technology).
                        </p>
                        
                        <p>
                            In my free time, I enjoy exploring new technologies, contributing to open-source projects, and enhancing my skills in web development. I am always eager to learn and grow as a developer, and I believe in the power of collaboration and sharing knowledge with others.
                        </p>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="space-y-4">
                    <Card class="bg-card/50 backdrop-blur-sm border border-border/50">
                        <CardContent class="p-4 lg:p-6 space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <MapPin class="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Location</p>
                                    <p class="font-medium">Cambodia</p>
                                </div>
                            </div>
                            
                            <Separator class="bg-border/50" />
                            
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <Briefcase class="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Experience</p>
                                    <p class="font-medium">2+ Years</p>
                                </div>
                            </div>
                            
                            <Separator class="bg-border/50" />
                            
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <Award class="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Focus</p>
                                    <p class="font-medium">Backend Development</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Resume Section -->
        <section class="py-12 lg:py-16 px-4 max-w-6xl mx-auto">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-center mb-8 lg:mb-12 bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent" :class="{ 'fade-in-up': isVisible }">
                Professional Journey
            </h2>
            
            <!-- Experience Timeline -->
            <div class="mb-12 lg:mb-16">
                <div class="flex items-center gap-4 mb-6 lg:mb-8">
                    <div class="p-3 bg-primary/10 rounded-xl">
                        <Briefcase class="h-6 w-6 text-primary" />
                    </div>
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-foreground" :class="{ 'fade-in-up': isVisible }">
                        Work Experience
                    </h3>
                </div>
                
                <div class="space-y-4 lg:space-y-6 relative">
                    <!-- Timeline line -->
                    <div class="absolute left-6 top-0 bottom-0 w-px bg-gradient-to-b from-primary via-primary/50 to-transparent hidden md:block"></div>
                    
                    <Card
                        v-for="(item, index) in experience"
                        :key="index"
                        class="bg-card/50 backdrop-blur-sm border border-border/50 hover:bg-card/80 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group relative md:ml-16"
                        :class="{ 'fade-in-up': isVisible }"
                        :style="{ animationDelay: `${index * 0.2}s` }"
                    >
                        <!-- Timeline dot -->
                        <div class="absolute -left-20 top-8 w-4 h-4 bg-primary rounded-full border-4 border-background hidden md:block group-hover:scale-125 transition-transform"></div>
                        
                        <CardContent class="p-4 lg:p-6">
                            <div class="flex flex-col lg:flex-row lg:items-start gap-4 lg:gap-6">
                                <Badge variant="outline" class="bg-primary/10 border-primary/20 text-primary w-fit px-4 py-2">
                                    <Calendar class="w-3 h-3 mr-2" />
                                    {{ item.period }}
                                </Badge>
                                <div class="flex-1 space-y-3">
                                    <div>
                                        <h4 class="text-lg lg:text-xl font-bold text-foreground mb-2">{{ item.title }}</h4>
                                        <h5 class="text-base lg:text-lg font-semibold text-primary mb-2">{{ item.company }}</h5>
                                    </div>
                                    <p class="text-muted-foreground leading-relaxed">{{ item.description }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Education Timeline -->
            <div>
                <div class="flex items-center gap-4 mb-10">
                    <div class="p-3 bg-primary/10 rounded-xl">
                        <GraduationCap class="h-6 w-6 text-primary" />
                    </div>
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-foreground" :class="{ 'fade-in-up': isVisible }">
                        Education
                    </h3>
                </div>
                
                <div class="space-y-4 lg:space-y-6 relative">
                    <!-- Timeline line -->
                    <div class="absolute left-6 top-0 bottom-0 w-px bg-gradient-to-b from-primary via-primary/50 to-transparent hidden md:block"></div>
                    
                    <Card
                        v-for="(item, index) in education"
                        :key="index"
                        class="bg-card/50 backdrop-blur-sm border border-border/50 hover:bg-card/80 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group relative md:ml-16"
                        :class="{ 'fade-in-up': isVisible }"
                        :style="{ animationDelay: `${(index + experience.length) * 0.2}s` }"
                    >
                        <!-- Timeline dot -->
                        <div class="absolute -left-20 top-8 w-4 h-4 bg-primary rounded-full border-4 border-background hidden md:block group-hover:scale-125 transition-transform"></div>
                        
                        <CardContent class="p-4 lg:p-6">
                            <div class="flex flex-col lg:flex-row lg:items-start gap-4 lg:gap-6">
                                <Badge variant="outline" class="bg-primary/10 border-primary/20 text-primary w-fit px-4 py-2">
                                    <Calendar class="w-3 h-3 mr-2" />
                                    {{ item.period }}
                                </Badge>
                                <div class="flex-1 space-y-3">
                                    <div>
                                        <h4 class="text-2xl font-bold text-foreground mb-2">{{ item.title }}</h4>
                                        <h5 class="text-lg font-semibold text-primary mb-3">{{ item.institution }}</h5>
                                    </div>
                                    <p class="text-muted-foreground leading-relaxed">{{ item.description }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Tech Stack Section -->
        <section class="py-12 lg:py-16 px-4 max-w-6xl mx-auto">
            <div class="text-center mb-8 lg:mb-12">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-4 lg:mb-6 bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent" :class="{ 'fade-in-up': isVisible }">
                    Technology Stack
                </h2>
                <p class="text-base lg:text-lg text-muted-foreground max-w-2xl mx-auto">
                    The tools and technologies I work with to bring ideas to life
                </p>
            </div>
            
            <!-- Tech categories grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 lg:gap-4">
                <Card
                    v-for="(tech, index) in techStack"
                    :key="index"
                    class="bg-card/50 backdrop-blur-sm border border-border/50 hover:bg-card/80 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl group overflow-hidden relative"
                    :class="{ 'fade-in-up': isVisible }"
                    :style="{ animationDelay: `${index * 0.05}s` }"
                >
                    <!-- Hover gradient border -->
                    <div class="absolute inset-0 bg-gradient-to-r from-primary/20 via-primary/5 to-primary/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-lg"></div>
                    
                    <CardContent class="p-3 lg:p-4 relative z-10">
                        <div class="flex items-start gap-2 lg:gap-3 mb-3">
                            <div class="p-2 lg:p-3 bg-primary/10 rounded-lg group-hover:bg-primary/20 transition-colors duration-300 flex-shrink-0">
                                <Avatar class="h-6 w-6 lg:h-8 lg:w-8">
                                    <AvatarImage :src="tech.image" :alt="tech.name" class="object-contain p-1" />
                                    <AvatarFallback class="bg-primary/20 text-primary text-xs font-bold">
                                        {{ tech.name.slice(0, 2) }}
                                    </AvatarFallback>
                                </Avatar>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm lg:text-base font-bold text-foreground mb-1 group-hover:text-primary transition-colors duration-300 truncate">
                                    {{ tech.name }}
                                </h3>
                                <Badge 
                                    variant="outline" 
                                    class="bg-primary/10 border-primary/20 text-primary text-xs px-1.5 py-0.5"
                                >
                                    {{ tech.category }}
                                </Badge>
                            </div>
                        </div>
                        
                        <p class="text-xs lg:text-sm text-muted-foreground leading-relaxed group-hover:text-foreground/80 transition-colors duration-300 line-clamp-2 lg:line-clamp-3">
                            {{ tech.description }}
                        </p>
                    </CardContent>
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