<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Separator } from '@/components/ui/separator'
import { Calendar, MapPin, GraduationCap, Briefcase, Code, Award } from 'lucide-vue-next'
import DockNavigation from '@/components/DockNavigation.vue'
import MusicPlayer from '@/components/MusicPlayer.vue'

const props = defineProps({
    title: String,
    description: String,
    aboutMe: {
        type: Object,
        required: true
    },
    workExperience: {
        type: Array,
        required: true
    },
    education: {
        type: Array,
        required: true
    },
    techStacks: {
        type: Array,
        required: true
    }
})

const isVisible = ref(false)

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
        <meta name="keywords" content="about me, software developer, experience, education, skills, professional background" />
        <meta name="author" :content="aboutMe.title || 'Software Developer'" />
        
        <!-- Open Graph Meta Tags -->
        <meta property="og:title" :content="title" />
        <meta property="og:description" :content="description" />
        <meta property="og:image" content="/about-og-image.jpg" />
        <meta property="og:url" :content="$page.url" />
        <meta property="og:type" content="profile" />
        <meta property="og:site_name" content="Professional Portfolio" />
        
        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="title" />
        <meta name="twitter:description" :content="description" />
        <meta name="twitter:image" content="/about-og-image.jpg" />
        
        <!-- Additional SEO Meta Tags -->
        <meta name="robots" content="index, follow" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="canonical" :href="$page.url" />
    </Head>

    <div
        class="min-h-screen bg-gradient-to-br from-background via-background/95 to-background text-foreground font-sans overflow-x-hidden transition-all duration-300 pt-16">
        <!-- Dock Navigation -->
        <DockNavigation currentRoute="/about" />
        
        <!-- Music Player -->
        <MusicPlayer />

        <!-- About Hero Section -->
        <section class="pt-6 sm:pt-8 pb-12 px-4 max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-8 lg:gap-12 items-start"
                :class="{ 'fade-in-up': isVisible }">
                <div class="space-y-6">
                    <div class="space-y-4">
                        <Badge variant="outline" class="w-fit px-4 py-2 bg-primary/10 border-primary/20 text-primary">
                            <Code class="w-3 h-3 mr-2" />
                            Software Developer
                        </Badge>

                        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black leading-tight">
                            <span
                                class="bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                                {{ aboutMe.title }}
                            </span>
                        </h1>
                    </div>

                    <div
                        class="prose prose-base lg:prose-lg max-w-none text-muted-foreground leading-relaxed space-y-4">
                        <p class="text-base lg:text-lg">
                            {{ aboutMe.description || 'No description available.' }}
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
                                    <p class="font-medium">{{ aboutMe.location || 'Not yet set' }}</p>
                                </div>
                            </div>

                            <Separator class="bg-border/50" />

                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <Briefcase class="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Experience</p>
                                    <p class="font-medium">{{ aboutMe.year_experience || 'Not yet set' }}</p>
                                </div>
                            </div>

                            <Separator class="bg-border/50" />

                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-primary/10 rounded-lg">
                                    <Award class="h-4 w-4 text-primary" />
                                </div>
                                <div>
                                    <p class="text-sm text-muted-foreground">Focus</p>
                                    <p class="font-medium">{{ aboutMe.fucus_on || 'Not yet set' }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Resume Section -->
        <section class="py-12 lg:py-16 px-4 max-w-6xl mx-auto">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-center mb-8 lg:mb-12 bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent"
                :class="{ 'fade-in-up': isVisible }">
                Professional Journey
            </h2>

            <!-- Experience Timeline -->
            <div class="mb-12 lg:mb-16">
                <div class="flex items-center gap-4 mb-6 lg:mb-8">
                    <div class="p-3 bg-primary/10 rounded-xl">
                        <Briefcase class="h-6 w-6 text-primary" />
                    </div>
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-foreground"
                        :class="{ 'fade-in-up': isVisible }">
                        Work Experience
                    </h3>
                </div>

                <div class="space-y-4 lg:space-y-6 relative">
                    <!-- Timeline line -->
                    <div
                        class="absolute left-6 top-0 bottom-0 w-px bg-gradient-to-b from-primary via-primary/50 to-transparent hidden md:block">
                    </div>

                    <Card v-for="(item, index) in workExperience" :key="item.id"
                        class="bg-card/50 backdrop-blur-sm border border-border/50 hover:bg-card/80 active:bg-card/80 focus:bg-card/80 transition-all duration-300 hover:-translate-y-1 active:-translate-y-1 focus:-translate-y-1 hover:shadow-lg active:shadow-lg focus:shadow-lg group relative md:ml-16 cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary/50"
                        :class="{ 'fade-in-up': isVisible }" :style="{ animationDelay: `${index * 0.2}s` }" tabindex="0">
                        <!-- Timeline dot -->
                        <div
                            class="absolute -left-20 top-8 w-4 h-4 bg-primary rounded-full border-4 border-background hidden md:block group-hover:scale-125 group-active:scale-125 group-focus:scale-125 transition-transform">
                        </div>

                        <CardContent class="p-4 lg:p-6">
                            <div class="flex flex-col lg:flex-row lg:items-start gap-4 lg:gap-6">
                                <Badge variant="outline"
                                    class="bg-primary/10 border-primary/20 text-primary w-fit px-4 py-2">
                                    <Calendar class="w-3 h-3 mr-2" />
                                    {{ item.from }} - {{ item.to }}
                                </Badge>
                                <div class="flex-1 space-y-3">
                                    <div>
                                        <h4 class="text-lg lg:text-xl font-bold text-foreground mb-2">{{ item.title }}
                                        </h4>
                                        <h5 class="text-base lg:text-lg font-semibold text-primary mb-2">{{ item.company
                                            }}</h5>
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
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-foreground"
                        :class="{ 'fade-in-up': isVisible }">
                        Education
                    </h3>
                </div>

                <div class="space-y-4 lg:space-y-6 relative">
                    <!-- Timeline line -->
                    <div
                        class="absolute left-6 top-0 bottom-0 w-px bg-gradient-to-b from-primary via-primary/50 to-transparent hidden md:block">
                    </div>

                    <Card v-for="(item, index) in education" :key="item.id"
                        class="bg-card/50 backdrop-blur-sm border border-border/50 hover:bg-card/80 active:bg-card/80 focus:bg-card/80 transition-all duration-300 hover:-translate-y-1 active:-translate-y-1 focus:-translate-y-1 hover:shadow-lg active:shadow-lg focus:shadow-lg group relative md:ml-16 cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary/50"
                        :class="{ 'fade-in-up': isVisible }"
                        :style="{ animationDelay: `${(index + workExperience.length) * 0.2}s` }" tabindex="0">
                        <!-- Timeline dot -->
                        <div
                            class="absolute -left-20 top-8 w-4 h-4 bg-primary rounded-full border-4 border-background hidden md:block group-hover:scale-125 group-active:scale-125 group-focus:scale-125 transition-transform">
                        </div>

                        <CardContent class="p-4 lg:p-6">
                            <div class="flex flex-col lg:flex-row lg:items-start gap-4 lg:gap-6">
                                <Badge variant="outline"
                                    class="bg-primary/10 border-primary/20 text-primary w-fit px-4 py-2">
                                    <Calendar class="w-3 h-3 mr-2" />
                                    {{ item.from }} - {{ item.to }}
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
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-4 lg:mb-6 bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent"
                    :class="{ 'fade-in-up': isVisible }">
                    Technology Stack
                </h2>
                <p class="text-base lg:text-lg text-muted-foreground max-w-2xl mx-auto">
                    The tools and technologies I work with to bring ideas to life
                </p>
            </div>

            <!-- Tech categories grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                <Card v-for="(tech, index) in techStacks" :key="tech.id" 
                    class="bg-card/50 backdrop-blur-sm border border-border/50 hover:bg-card/80 active:bg-card/80 focus:bg-card/80 transition-all duration-300 hover:-translate-y-2 active:-translate-y-2 focus:-translate-y-2 hover:shadow-xl active:shadow-xl focus:shadow-xl group overflow-hidden relative cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary/50"
                    :class="{ 'fade-in-up': isVisible }" :style="{ animationDelay: `${index * 0.05}s` }" tabindex="0">
                    <!-- Hover gradient border -->
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-primary/20 via-primary/5 to-primary/20 opacity-0 group-hover:opacity-100 group-active:opacity-100 group-focus:opacity-100 transition-opacity duration-300 rounded-lg">
                    </div>

                    <CardContent class="p-3 lg:p-4 relative z-10">
                        <div class="flex items-start gap-2 lg:gap-3 mb-3">
                            <div
                                class="p-2 lg:p-3 bg-primary/10 rounded-lg group-hover:bg-primary/20 group-active:bg-primary/20 group-focus:bg-primary/20 transition-colors duration-300 flex-shrink-0">
                                <Avatar class="h-6 w-6 lg:h-8 lg:w-8">
                                    <AvatarImage :src="tech.logo" :alt="tech.name" class="object-contain p-1" />
                                    <AvatarFallback class="bg-primary/20 text-primary text-xs font-bold">
                                        {{ tech.name.slice(0, 2) }}
                                    </AvatarFallback>
                                </Avatar>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3
                                    class="text-sm lg:text-base font-bold text-foreground mb-1 group-hover:text-primary group-active:text-primary group-focus:text-primary transition-colors duration-300 truncate">
                                    {{ tech.name }}
                                </h3>
                                <Badge variant="outline"
                                    class="bg-primary/10 border-primary/20 text-primary text-xs px-1.5 py-0.5">
                                    {{ tech.type }}
                                </Badge>
                            </div>
                        </div>

                        <p
                            class="text-xs lg:text-sm text-muted-foreground leading-relaxed group-hover:text-foreground/80 group-active:text-foreground/80 group-focus:text-foreground/80 transition-colors duration-300 line-clamp-2 lg:line-clamp-3">
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