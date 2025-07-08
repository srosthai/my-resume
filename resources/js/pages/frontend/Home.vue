<script setup>
import { ref, onMounted, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Sparkles, Download, ArrowRight, Github, Linkedin, Mail } from 'lucide-vue-next'
import DockNavigation from '@/components/DockNavigation.vue'

const props = defineProps({
    users: {
        type: Object,
        required: true
    },
    title: {
        type: String,
        default: 'Home'
    },
    description: {
        type: String,
        default: 'Welcome to my portfolio'
    }
})

const isVisible = ref(false)
const animatedText = ref('')
const textIndex = ref(0)

onMounted(() => {
    setTimeout(() => {
        isVisible.value = true
        animateText()
    }, 100)
})

const animateText = () => {
    const fullText = props.users.name
    const typeSpeed = 100
    const pauseBeforeLoop = 2000
    
    const typeText = () => {
        if (textIndex.value < fullText.length) {
            animatedText.value += fullText.charAt(textIndex.value)
            textIndex.value++
            setTimeout(typeText, typeSpeed)
        } else {
            setTimeout(() => {
                animatedText.value = ''
                textIndex.value = 0
                setTimeout(typeText, 500)
            }, pauseBeforeLoop)
        }
    }
    
    setTimeout(typeText, 500)
}
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="description" />
    </Head>

    <div class="min-h-screen lg:h-screen bg-gradient-to-br from-background via-background/95 to-background text-foreground font-sans lg:overflow-hidden transition-all duration-300">
        <!-- Dock Navigation -->
        <DockNavigation currentRoute="/" />

        <!-- Hero Section -->
        <section id="home" class="min-h-screen lg:h-full flex items-center px-4 py-8 lg:py-0 max-w-6xl mx-auto relative hero-section">
            <!-- Background decoration -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
            </div>
            
            <div class="flex flex-col lg:grid lg:grid-cols-[3fr_2fr] gap-6 sm:gap-8 lg:gap-12 items-center w-full relative z-10">
                <!-- Avatar Section - Shows first on mobile -->
                <div class="flex justify-center items-center relative order-1 lg:order-2 mt-12 lg:mt-0" :class="{ 'fade-in-up': isVisible }">
                    <div class="relative group avatar-container">
                        <!-- Decorative rings -->
                        <div class="absolute inset-0 rounded-full border-2 border-primary/20 scale-110 group-hover:scale-125 group-active:scale-115 transition-transform duration-700"></div>
                        <div class="absolute inset-0 rounded-full border border-primary/10 scale-125 group-hover:scale-140 group-active:scale-130 transition-transform duration-1000"></div>
                        
                        <Avatar class="w-40 h-40 xs:w-48 xs:h-48 sm:w-56 sm:h-56 md:w-64 md:h-64 lg:w-72 lg:h-72 floating border-4 border-background shadow-2xl relative z-10">
                            <AvatarImage 
                                v-if="users.image" 
                                :src="users.image.startsWith('http') ? users.image : `/${users.image}`" 
                                :alt="users.name" 
                                class="object-cover"
                            />
                            <AvatarFallback class="bg-gradient-to-br from-primary via-primary/90 to-primary/70 text-primary-foreground text-2xl xs:text-3xl sm:text-4xl md:text-5xl font-semibold">
                                {{ users.name?.charAt(0) }}
                            </AvatarFallback>
                        </Avatar>
                        
                        <!-- Glowing effect -->
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-primary/5 rounded-full blur-2xl -z-10 group-hover:from-primary/20 group-hover:to-primary/10 group-active:from-primary/15 group-active:to-primary/8 transition-all duration-700"></div>
                    </div>
                </div>

                <!-- Content Section - Shows second on mobile -->
                <div class="space-y-4 sm:space-y-6 lg:space-y-8 text-center lg:text-left order-2 lg:order-1" :class="{ 'fade-in-up': isVisible }">
                    <div class="space-y-3 sm:space-y-4">
                        <Badge variant="outline" class="w-fit px-3 py-1.5 bg-primary/10 border-primary/20 text-primary mx-auto lg:mx-0">
                            <Sparkles class="w-3 h-3 mr-2" />
                            Software Developer
                        </Badge>
                        
                        <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black leading-tight">
                            <span class="bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                                {{ animatedText }}<span class="animate-pulse">|</span>
                            </span>
                        </h1>
                        
                        <h2 class="text-sm xs:text-base sm:text-lg md:text-xl font-medium text-muted-foreground tracking-wide">
                            {{ users.position }}
                        </h2>
                    </div>
                    
                    <p class="text-sm xs:text-base sm:text-lg text-muted-foreground leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        {{ users.description }}
                    </p>
                    
                    <!-- Social Links -->
                    <div class="flex items-center justify-center lg:justify-start gap-3">
                        <Button asChild size="icon" variant="ghost" class="h-9 w-9 bg-background/50 backdrop-blur-sm border border-border/50 hover:bg-accent active:bg-accent focus:bg-accent transition-colors">
                            <a href="https://github.com/Sovannthai" target="_blank" rel="noopener noreferrer">
                                <Github class="h-4 w-4" />
                            </a>
                        </Button>
                        <Button asChild size="icon" variant="ghost" class="h-9 w-9 bg-background/50 backdrop-blur-sm border border-border/50 hover:bg-accent active:bg-accent focus:bg-accent transition-colors">
                            <a href="https://www.linkedin.com/in/sros-thai-b491b42ab/" target="_blank" rel="noopener noreferrer">
                                <Linkedin class="h-4 w-4" />
                            </a>
                        </Button>
                        <Button asChild size="icon" variant="ghost" class="h-9 w-9 bg-background/50 backdrop-blur-sm border border-border/50 hover:bg-accent active:bg-accent focus:bg-accent transition-colors">
                            <a href="mailto:srosthai00@gmail.com" rel="noopener noreferrer">
                                <Mail class="h-4 w-4" />
                            </a>
                        </Button>
                    </div>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col xs:flex-row gap-3 justify-center lg:justify-start">
                        <Button asChild class="bg-primary hover:bg-primary/90 active:bg-primary/80 text-primary-foreground font-medium px-6 py-3 text-sm transition-all duration-300 hover:-translate-y-1 active:translate-y-0 hover:shadow-lg group">
                            <Link href="/about" class="flex items-center gap-2">
                                <span>Details About Me</span>
                                <ArrowRight class="h-4 w-4 transition-transform group-hover:translate-x-1" />
                            </Link>
                        </Button>
                        <Button asChild variant="outline" class="bg-background/50 backdrop-blur-sm border-border/50 hover:bg-accent active:bg-accent px-6 py-3 text-sm transition-all duration-300 hover:-translate-y-1 active:translate-y-0 group">
                            <Link href="/portfolio" class="flex items-center gap-2">
                                <Download class="h-4 w-4 transition-transform group-hover:translate-y-1" />
                                View Portfolio
                            </Link>
                        </Button>
                    </div>
                </div>
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

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.fade-in-up {
    animation: fadeInUp 1s ease-out forwards;
}

.floating {
    animation: float 6s ease-in-out infinite;
}
</style>