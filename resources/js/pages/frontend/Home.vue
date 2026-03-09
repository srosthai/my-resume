<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Skeleton } from '@/components/ui/skeleton'
import { Sparkles, Download, ArrowRight, Github, Linkedin, Mail, Book } from 'lucide-vue-next'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

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

const isLoading = ref(true)
const isVisible = ref(false)
const animatedText = ref('')
const textIndex = ref(0)

// Staggered fade-in delays (ms)
const staggerDelay = {
    badge: 0,
    title: 150,
    position: 300,
    description: 450,
    socialLinks: 600,
    ctaButtons: 750,
    avatar: 200,
}

onMounted(() => {
    // Simulate brief loading then trigger animations
    setTimeout(() => {
        isLoading.value = false
        setTimeout(() => {
            isVisible.value = true
            // Start typing after title fade-in completes (150ms delay + 600ms animation)
            setTimeout(animateText, staggerDelay.title + 600)
        }, 50)
    }, 800)
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

    typeText()
}
</script>

<template>
    <FrontendLayout currentRoute="/">

        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta name="keywords"
                content="software developer, portfolio, Vue.js, Laravel, web development, full stack developer, SROS THAI, professional developer, Cambodia developer" />
            <meta name="author" :content="users.name || 'SROS THAI'" />
            <meta name="language" content="en" />
            <meta name="geo.region" content="KH" />
            <meta name="geo.country" content="Cambodia" />
            <meta name="theme-color" content="#2563eb" />

            <!-- Open Graph Meta Tags -->
            <meta property="og:title" :content="title" />
            <meta property="og:description" :content="description" />
            <meta property="og:image"
                :content="users.image ? (users.image.startsWith('http') ? users.image : `${$page.url}/${users.image}`) : `${$page.url}/default-og-image.jpg`" />
            <meta property="og:url" :content="$page.url" />
            <meta property="og:type" content="website" />
            <meta property="og:site_name" :content="users.name + ' - Portfolio'" />

            <!-- Twitter Card Meta Tags -->
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" :content="title" />
            <meta name="twitter:description" :content="description" />
            <meta name="twitter:image"
                :content="users.image ? (users.image.startsWith('http') ? users.image : `${$page.url}/${users.image}`) : `${$page.url}/default-og-image.jpg`" />

            <!-- Additional SEO Meta Tags -->
            <meta name="robots"
                content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <meta name="format-detection" content="telephone=no" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <link rel="canonical" :href="$page.url" />
        </Head>

        <div class="overflow-hidden flex items-center transition-all duration-300">

            <!-- Skeleton Loading State -->
            <section v-if="isLoading"
                class="w-full h-full flex items-center px-4 py-4 max-w-6xl mx-auto hero-section">
                <div
                    class="flex flex-col lg:grid lg:grid-cols-[1fr_1fr] gap-6 sm:gap-8 lg:gap-12 items-center w-full">
                    <!-- Avatar Skeleton -->
                    <div class="flex justify-center items-center order-1 lg:order-2">
                        <Skeleton
                            class="w-44 h-44 xs:w-52 xs:h-52 sm:w-64 sm:h-64 md:w-80 md:h-80 lg:w-96 lg:h-96 xl:w-[28rem] xl:h-[28rem] 2xl:w-[32rem] 2xl:h-[32rem] rounded-full" />
                    </div>
                    <!-- Content Skeleton -->
                    <div class="space-y-3 sm:space-y-4 lg:space-y-5 order-2 lg:order-1 w-full flex flex-col items-center lg:items-start">
                        <Skeleton class="h-8 w-44 rounded-full" />
                        <Skeleton class="h-8 sm:h-12 lg:h-14 w-48 sm:w-80 rounded-lg" />
                        <Skeleton class="h-4 sm:h-5 w-32 sm:w-36 rounded-lg" />
                        <div class="space-y-2 w-full max-w-xs sm:max-w-md">
                            <Skeleton class="h-3 sm:h-4 w-full rounded-lg" />
                            <Skeleton class="h-3 sm:h-4 w-4/5 rounded-lg" />
                        </div>
                        <div class="flex flex-wrap justify-center lg:justify-start gap-2 sm:gap-3 pt-2">
                            <Skeleton class="h-8 sm:h-9 w-18 sm:w-20 rounded-lg" />
                            <Skeleton class="h-8 sm:h-9 w-20 sm:w-22 rounded-lg" />
                            <Skeleton class="h-8 sm:h-9 w-18 sm:w-20 rounded-lg" />
                            <Skeleton class="h-8 sm:h-9 w-14 sm:w-16 rounded-lg" />
                        </div>
                        <div class="flex gap-2 pt-1">
                            <Skeleton class="h-9 sm:h-10 w-24 sm:w-28 rounded-lg" />
                            <Skeleton class="h-9 sm:h-10 w-24 sm:w-28 rounded-lg" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Hero Section -->
            <section v-else id="home"
                class="w-full h-full flex items-center px-4 py-4 max-w-6xl mx-auto relative hero-section">
                <!-- Background decoration -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
                </div>

                <div
                    class="flex flex-col lg:grid lg:grid-cols-[1fr_1fr] gap-6 sm:gap-8 lg:gap-12 items-center w-full relative z-10">
                    <!-- Avatar Section - Shows first on mobile -->
                    <div class="flex justify-center items-center relative order-1 lg:order-2 stagger-fade-in"
                        :style="{ animationDelay: `${staggerDelay.avatar}ms` }"
                        :class="{ 'animate': isVisible }">
                        <div class="relative group avatar-container">
                            <!-- Decorative rings -->
                            <div
                                class="absolute inset-0 rounded-full border-2 border-primary/20 scale-110 group-hover:scale-125 group-active:scale-115 transition-transform duration-700">
                            </div>
                            <div
                                class="absolute inset-0 rounded-full border border-primary/10 scale-125 group-hover:scale-140 group-active:scale-130 transition-transform duration-1000">
                            </div>

                            <Avatar
                                class="w-44 h-44 xs:w-52 xs:h-52 sm:w-64 sm:h-64 md:w-80 md:h-80 lg:w-96 lg:h-96 xl:w-[28rem] xl:h-[28rem] 2xl:w-[32rem] 2xl:h-[32rem] floating border-4 border-background shadow-2xl relative z-10">
                                <AvatarImage v-if="users.image"
                                    :src="users.image.startsWith('http') ? users.image : `/${users.image}`"
                                    :alt="users.name" class="object-cover object-[center_30%]" />
                                <AvatarFallback
                                    class="bg-gradient-to-br from-primary via-primary/90 to-primary/70 text-primary-foreground text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-semibold">
                                    {{ users.name?.charAt(0) }}
                                </AvatarFallback>
                            </Avatar>

                            <!-- Glowing effect -->
                            <div
                                class="absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-primary/5 rounded-full blur-2xl -z-10 group-hover:from-primary/20 group-hover:to-primary/10 group-active:from-primary/15 group-active:to-primary/8 transition-all duration-700">
                            </div>
                        </div>
                    </div>

                    <!-- Content Section - Shows second on mobile -->
                    <div class="space-y-3 sm:space-y-4 lg:space-y-5 text-center lg:text-left order-2 lg:order-1">
                        <div class="space-y-2 sm:space-y-3">
                            <div class="stagger-fade-in"
                                :style="{ animationDelay: `${staggerDelay.badge}ms` }"
                                :class="{ 'animate': isVisible }">
                                <Badge variant="outline"
                                    class="w-fit px-3 py-1.5 bg-primary/10 border-primary/20 text-primary mx-auto lg:mx-0">
                                    <Sparkles class="w-3 h-3 mr-2" />
                                    Software Developer
                                </Badge>
                            </div>

                            <div class="stagger-fade-in"
                                :style="{ animationDelay: `${staggerDelay.title}ms` }"
                                :class="{ 'animate': isVisible }">
                                <h1
                                    class="text-xl xs:text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black leading-tight">
                                    <span
                                        class="bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                                        {{ animatedText }}<span class="animate-pulse">|</span>
                                    </span>
                                </h1>
                            </div>

                            <div class="stagger-fade-in"
                                :style="{ animationDelay: `${staggerDelay.position}ms` }"
                                :class="{ 'animate': isVisible }">
                                <h2
                                    class="text-xs xs:text-sm sm:text-base md:text-lg font-medium text-muted-foreground tracking-wide">
                                    {{ users.position }}
                                </h2>
                            </div>
                        </div>

                        <div class="stagger-fade-in"
                            :style="{ animationDelay: `${staggerDelay.description}ms` }"
                            :class="{ 'animate': isVisible }">
                            <p
                                class="text-xs xs:text-sm sm:text-base text-muted-foreground leading-relaxed max-w-2xl mx-auto lg:mx-0">
                                {{ users.description }}
                            </p>
                        </div>

                        <!-- Social Links -->
                        <div class="stagger-fade-in"
                            :style="{ animationDelay: `${staggerDelay.socialLinks}ms` }"
                            :class="{ 'animate': isVisible }">
                            <div class="flex items-center justify-center lg:justify-start gap-2 sm:gap-4">
                                <Button asChild variant="ghost" size="sm"
                                    class="h-auto p-2 text-muted-foreground hover:text-foreground transition-all duration-300 hover:scale-105 active:scale-95 group">
                                    <a href="https://github.com/srosthai" target="_blank" rel="noopener noreferrer"
                                        class="flex items-center gap-2 text-sm sm:text-base">
                                        <Github class="h-4 w-4 sm:h-5 sm:w-5 transition-transform group-hover:rotate-12" />
                                        <span class="font-medium tracking-wide">GitHub</span>
                                    </a>
                                </Button>
                                <Button asChild variant="ghost" size="sm"
                                    class="h-auto p-2 text-muted-foreground hover:text-foreground transition-all duration-300 hover:scale-105 active:scale-95 group">
                                    <a href="https://www.linkedin.com/in/sros-thai-b491b42ab/" target="_blank"
                                        rel="noopener noreferrer" class="flex items-center gap-2 text-sm sm:text-base">
                                        <Linkedin
                                            class="h-4 w-4 sm:h-5 sm:w-5 transition-transform group-hover:rotate-12" />
                                        <span class="font-medium tracking-wide">LinkedIn</span>
                                    </a>
                                </Button>
                                <Button asChild variant="ghost" size="sm"
                                    class="h-auto p-2 text-muted-foreground hover:text-foreground transition-all duration-300 hover:scale-105 active:scale-95 group">
                                    <Link href="/contact" class="flex items-center gap-2 text-sm sm:text-base">
                                    <Mail class="h-4 w-4 sm:h-5 sm:w-5 transition-transform group-hover:rotate-12" />
                                    <span class="font-medium tracking-wide">Contact</span>
                                    </Link>
                                </Button>
                                <Button asChild variant="ghost" size="sm"
                                    class="h-auto p-2 text-muted-foreground hover:text-foreground transition-all duration-300 hover:scale-105 active:scale-95 group">
                                    <Link href="/note" class="flex items-center gap-2 text-sm sm:text-base">
                                    <Book class="h-4 w-4 sm:h-5 sm:w-5 transition-transform group-hover:rotate-12" />
                                    <span class="font-medium tracking-wide">Note</span>
                                    </Link>
                                </Button>
                            </div>
                        </div>

                        <!-- CTA Buttons -->
                        <div class="stagger-fade-in"
                            :style="{ animationDelay: `${staggerDelay.ctaButtons}ms` }"
                            :class="{ 'animate': isVisible }">
                            <div class="flex flex-col xs:flex-row gap-2 justify-center lg:justify-start">
                                <Button asChild
                                    class="bg-primary hover:bg-primary/90 active:bg-primary/80 text-primary-foreground font-medium px-6 py-3 text-sm transition-all duration-300 hover:-translate-y-1 active:translate-y-0 hover:shadow-lg group">
                                    <Link href="/resume" class="flex items-center gap-2">
                                    <span>Resume</span>
                                    <ArrowRight class="h-4 w-4 transition-transform group-hover:translate-x-1" />
                                    </Link>
                                </Button>
                                <Button asChild variant="outline"
                                    class="bg-background/50 backdrop-blur-sm border-border/50 hover:bg-accent active:bg-accent px-6 py-3 text-sm transition-all duration-300 hover:-translate-y-1 active:translate-y-0 group">
                                    <Link href="/portfolio" class="flex items-center gap-2">
                                    <Download class="h-4 w-4 transition-transform group-hover:translate-y-1" />
                                    Projects
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
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