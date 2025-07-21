<script setup>
import { ref, onMounted, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Sparkles, Download, ArrowRight, Github, Linkedin, Mail, Book } from 'lucide-vue-next'
import DockNavigation from '@/components/DockNavigation.vue'
import MusicPlayer from '@/components/MusicPlayer.vue'

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
        <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="format-detection" content="telephone=no" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <link rel="canonical" :href="$page.url" />
        
        <!-- JSON-LD Structured Data -->
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "WebSite",
          "name": "{{ users.name || 'SROS THAI' }} - Portfolio",
          "description": "{{ description }}",
          "url": "{{ $page.url }}",
          "author": {
            "@type": "Person",
            "name": "{{ users.name || 'SROS THAI' }}",
            "jobTitle": "Software Developer",
            "url": "{{ $page.url }}",
            "sameAs": [
              "https://github.com/Sovannthai",
              "https://www.linkedin.com/in/sros-thai-b491b42ab/"
            ]
          },
          "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ $page.url }}/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
          }
        }
        </script>
    </Head>

    <div
        class="min-h-screen lg:h-screen bg-gradient-to-br from-background via-background/95 to-background text-foreground font-sans lg:overflow-hidden transition-all duration-300">
        <!-- Dock Navigation -->
        <DockNavigation currentRoute="/" />

        <!-- Music Player -->
        <MusicPlayer />

        <!-- Hero Section -->
        <section id="home"
            class="min-h-screen lg:h-full flex items-center px-4 py-8 lg:py-0 max-w-6xl mx-auto relative hero-section">
            <!-- Background decoration -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
            </div>

            <div
                class="flex flex-col lg:grid lg:grid-cols-[3fr_2fr] gap-6 sm:gap-8 lg:gap-12 items-center w-full relative z-10">
                <!-- Avatar Section - Shows first on mobile -->
                <div class="flex justify-center items-center relative order-1 lg:order-2 mt-12 lg:mt-0"
                    :class="{ 'fade-in-up': isVisible }">
                    <div class="relative group avatar-container">
                        <!-- Decorative rings -->
                        <div
                            class="absolute inset-0 rounded-full border-2 border-primary/20 scale-110 group-hover:scale-125 group-active:scale-115 transition-transform duration-700">
                        </div>
                        <div
                            class="absolute inset-0 rounded-full border border-primary/10 scale-125 group-hover:scale-140 group-active:scale-130 transition-transform duration-1000">
                        </div>

                        <Avatar
                            class="w-40 h-40 xs:w-48 xs:h-48 sm:w-56 sm:h-56 md:w-64 md:h-64 lg:w-72 lg:h-72 floating border-4 border-background shadow-2xl relative z-10">
                            <AvatarImage v-if="users.image"
                                :src="users.image.startsWith('http') ? users.image : `/${users.image}`"
                                :alt="users.name" class="object-cover" />
                            <AvatarFallback
                                class="bg-gradient-to-br from-primary via-primary/90 to-primary/70 text-primary-foreground text-2xl xs:text-3xl sm:text-4xl md:text-5xl font-semibold">
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
                <div class="space-y-4 sm:space-y-6 lg:space-y-8 text-center lg:text-left order-2 lg:order-1"
                    :class="{ 'fade-in-up': isVisible }">
                    <div class="space-y-3 sm:space-y-4">
                        <Badge variant="outline"
                            class="w-fit px-3 py-1.5 bg-primary/10 border-primary/20 text-primary mx-auto lg:mx-0">
                            <Sparkles class="w-3 h-3 mr-2" />
                            Software Developer
                        </Badge>

                        <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black leading-tight">
                            <span
                                class="bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                                {{ animatedText }}<span class="animate-pulse">|</span>
                            </span>
                        </h1>

                        <h2
                            class="text-sm xs:text-base sm:text-lg md:text-xl font-medium text-muted-foreground tracking-wide">
                            {{ users.position }}
                        </h2>
                    </div>

                    <p
                        class="text-sm xs:text-base sm:text-lg text-muted-foreground leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        {{ users.description }}
                    </p>

                    <!-- Social Links -->
                    <div class="flex items-center justify-center lg:justify-start gap-4 sm:gap-6">
                        <Button asChild variant="ghost" size="sm"
                            class="h-auto p-2 text-muted-foreground hover:text-foreground transition-all duration-300 hover:scale-105 active:scale-95 group">
                            <a href="https://github.com/Sovannthai" target="_blank" rel="noopener noreferrer"
                                class="flex items-center gap-2 text-sm sm:text-base">
                                <Github class="h-4 w-4 sm:h-5 sm:w-5 transition-transform group-hover:rotate-12" />
                                <span class="font-medium tracking-wide">GitHub</span>
                            </a>
                        </Button>
                        <Button asChild variant="ghost" size="sm"
                            class="h-auto p-2 text-muted-foreground hover:text-foreground transition-all duration-300 hover:scale-105 active:scale-95 group">
                            <a href="https://www.linkedin.com/in/sros-thai-b491b42ab/" target="_blank"
                                rel="noopener noreferrer" class="flex items-center gap-2 text-sm sm:text-base">
                                <Linkedin class="h-4 w-4 sm:h-5 sm:w-5 transition-transform group-hover:rotate-12" />
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

                    <!-- CTA Buttons -->
                    <div class="flex flex-col xs:flex-row gap-3 justify-center lg:justify-start">
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
        </section>
    </div>
</template>