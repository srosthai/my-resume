<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Skeleton } from '@/components/ui/skeleton'
import { ArrowRight, Github, Linkedin, Mail, Book, MapPin, Briefcase } from 'lucide-vue-next'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        default: 'Home',
    },
    description: {
        type: String,
        default: 'Welcome to my portfolio',
    },
})

const isLoading = ref(true)
const isVisible = ref(false)
const animatedText = ref('')
const textIndex = ref(0)

const stagger = {
    greeting: 0,
    name: 120,
    position: 240,
    divider: 340,
    description: 420,
    meta: 520,
    social: 620,
    cta: 740,
    avatar: 180,
}

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
        setTimeout(() => {
            isVisible.value = true
            // Start typing after name fade-in completes (120ms delay + 700ms animation)
            setTimeout(animateText, stagger.name + 700)
        }, 50)
    }, 600)
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
            <meta
                name="keywords"
                content="software developer, portfolio, Vue.js, Laravel, web development, full stack developer, SROS THAI, professional developer, Cambodia developer"
            />
            <meta name="author" :content="users.name || 'SROS THAI'" />
            <meta name="language" content="en" />
            <meta name="geo.region" content="KH" />
            <meta name="geo.country" content="Cambodia" />
            <meta name="theme-color" content="#2563eb" />

            <!-- Open Graph Meta Tags -->
            <meta property="og:title" :content="title" />
            <meta property="og:description" :content="description" />
            <meta
                property="og:image"
                :content="
                    users.image
                        ? users.image.startsWith('http')
                            ? users.image
                            : `${$page.url}/${users.image}`
                        : `${$page.url}/default-og-image.jpg`
                "
            />
            <meta property="og:url" :content="$page.url" />
            <meta property="og:type" content="website" />
            <meta property="og:site_name" :content="users.name + ' - Portfolio'" />

            <!-- Twitter Card Meta Tags -->
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" :content="title" />
            <meta name="twitter:description" :content="description" />
            <meta
                name="twitter:image"
                :content="
                    users.image
                        ? users.image.startsWith('http')
                            ? users.image
                            : `${$page.url}/${users.image}`
                        : `${$page.url}/default-og-image.jpg`
                "
            />

            <!-- Additional SEO Meta Tags -->
            <meta
                name="robots"
                content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1"
            />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <meta name="format-detection" content="telephone=no" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <link rel="canonical" :href="$page.url" />
        </Head>

        <div class="flex items-center overflow-hidden transition-all duration-300">
            <!-- Skeleton Loading State -->
            <section
                v-if="isLoading"
                class="hero-section mx-auto flex h-full w-full max-w-5xl items-center px-6 py-4"
            >
                <div
                    class="flex w-full flex-col items-center gap-10 lg:grid lg:grid-cols-[1.1fr_0.9fr] lg:gap-16"
                >
                    <div
                        class="order-2 flex w-full flex-col items-center space-y-5 lg:order-1 lg:items-start"
                    >
                        <Skeleton class="h-5 w-28 rounded-full" />
                        <Skeleton class="h-10 w-64 rounded-lg sm:h-14 sm:w-80" />
                        <Skeleton class="h-5 w-40 rounded-lg sm:w-48" />
                        <Skeleton class="h-px w-16 rounded-full" />
                        <div class="w-full max-w-md space-y-2.5">
                            <Skeleton class="h-3.5 w-full rounded" />
                            <Skeleton class="h-3.5 w-5/6 rounded" />
                            <Skeleton class="h-3.5 w-3/4 rounded" />
                        </div>
                        <div class="flex gap-3 pt-2">
                            <Skeleton class="h-9 w-9 rounded-full" />
                            <Skeleton class="h-9 w-9 rounded-full" />
                            <Skeleton class="h-9 w-9 rounded-full" />
                            <Skeleton class="h-9 w-9 rounded-full" />
                        </div>
                        <div class="flex gap-3 pt-1">
                            <Skeleton class="h-11 w-32 rounded-full" />
                            <Skeleton class="h-11 w-32 rounded-full" />
                        </div>
                    </div>
                    <div class="order-1 flex items-center justify-center lg:order-2">
                        <Skeleton
                            class="w-44 h-44 xs:w-52 xs:h-52 sm:w-64 sm:h-64 md:w-80 md:h-80 lg:w-96 lg:h-96 xl:w-[28rem] xl:h-[28rem] 2xl:w-[32rem] 2xl:h-[32rem] rounded-full"
                        />
                    </div>
                </div>
            </section>

            <!-- Hero Section -->
            <section
                v-else
                id="home"
                class="hero-section relative mx-auto flex h-full w-full max-w-5xl items-center px-6 py-4"
            >
                <!-- Ambient background -->
                <div class="pointer-events-none absolute inset-0 overflow-hidden">
                    <div
                        class="absolute -top-32 right-1/4 h-[28rem] w-[28rem] rounded-full bg-primary/[0.03] blur-[100px]"
                    ></div>
                    <div
                        class="absolute -bottom-32 left-1/4 h-[24rem] w-[24rem] rounded-full bg-primary/[0.04] blur-[100px]"
                    ></div>
                </div>

                <div
                    class="relative z-10 flex w-full flex-col items-center gap-10 lg:grid lg:grid-cols-[1.1fr_0.9fr] lg:gap-16"
                >
                    <!-- Content -->
                    <div
                        class="order-2 space-y-4 text-center lg:order-1 lg:space-y-5 lg:text-left"
                    >
                        <!-- Greeting -->
                        <div
                            class="reveal"
                            :style="{ animationDelay: `${stagger.greeting}ms` }"
                            :class="{ 'is-visible': isVisible }"
                        >
                            <span
                                class="inline-block text-xs font-medium tracking-[0.2em] uppercase text-muted-foreground/70 sm:text-sm"
                            >
                                Hello, I'm
                            </span>
                        </div>

                        <!-- Name -->
                        <div
                            class="reveal"
                            :style="{ animationDelay: `${stagger.name}ms` }"
                            :class="{ 'is-visible': isVisible }"
                        >
                            <h1
                                class="text-3xl font-extrabold tracking-tight text-foreground xs:text-4xl sm:text-5xl lg:text-6xl"
                            >
                                {{ animatedText }}<span class="animate-pulse">|</span>
                            </h1>
                        </div>

                        <!-- Position -->
                        <div
                            class="reveal"
                            :style="{ animationDelay: `${stagger.position}ms` }"
                            :class="{ 'is-visible': isVisible }"
                        >
                            <h2
                                class="text-sm font-medium tracking-wide text-primary/80 sm:text-base lg:text-lg"
                            >
                                {{ users.position }}
                            </h2>
                        </div>

                        <!-- Divider -->
                        <div
                            class="reveal"
                            :style="{ animationDelay: `${stagger.divider}ms` }"
                            :class="{ 'is-visible': isVisible }"
                        >
                            <div
                                class="mx-auto h-px w-12 bg-border/60 lg:mx-0"
                            ></div>
                        </div>

                        <!-- Description -->
                        <div
                            class="reveal"
                            :style="{ animationDelay: `${stagger.description}ms` }"
                            :class="{ 'is-visible': isVisible }"
                        >
                            <p
                                class="mx-auto max-w-md text-sm leading-relaxed text-muted-foreground sm:text-base lg:mx-0 lg:max-w-lg"
                            >
                                {{ users.description }}
                            </p>
                        </div>

                        <!-- Meta badges -->
                        <div
                            class="reveal"
                            :style="{ animationDelay: `${stagger.meta}ms` }"
                            :class="{ 'is-visible': isVisible }"
                        >
                            <div
                                class="flex flex-wrap items-center justify-center gap-2 lg:justify-start"
                            >
                                <Badge
                                    variant="outline"
                                    class="gap-1.5 border-border/40 bg-muted/40 px-3 py-1 text-xs font-normal text-muted-foreground backdrop-blur-sm transition-colors duration-200 hover:border-primary/30 hover:bg-primary/10 hover:text-primary"
                                >
                                    <MapPin class="h-3 w-3 opacity-60" />
                                    Cambodia
                                </Badge>
                                <Badge
                                    variant="outline"
                                    class="gap-1.5 border-border/40 bg-muted/40 px-3 py-1 text-xs font-normal text-muted-foreground backdrop-blur-sm transition-colors duration-200 hover:border-primary/30 hover:bg-primary/10 hover:text-primary"
                                >
                                    <Briefcase class="h-3 w-3 opacity-60" />
                                    Software Developer
                                </Badge>
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div
                            class="reveal"
                            :style="{ animationDelay: `${stagger.social}ms` }"
                            :class="{ 'is-visible': isVisible }"
                        >
                            <div
                                class="flex items-center justify-center gap-1.5 lg:justify-start"
                            >
                                <a
                                    href="https://github.com/srosthai"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="social-icon"
                                    aria-label="GitHub"
                                >
                                    <Github class="h-4 w-4" />
                                </a>
                                <a
                                    href="https://www.linkedin.com/in/sros-thai-b491b42ab/"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="social-icon"
                                    aria-label="LinkedIn"
                                >
                                    <Linkedin class="h-4 w-4" />
                                </a>
                                <Link href="/contact" class="social-icon" aria-label="Contact">
                                    <Mail class="h-4 w-4" />
                                </Link>
                                <Link href="/note" class="social-icon" aria-label="Notes">
                                    <Book class="h-4 w-4" />
                                </Link>
                            </div>
                        </div>

                        <!-- CTA Buttons -->
                        <div
                            class="reveal"
                            :style="{ animationDelay: `${stagger.cta}ms` }"
                            :class="{ 'is-visible': isVisible }"
                        >
                            <div
                                class="flex flex-col items-center gap-3 pt-1 xs:flex-row lg:items-start"
                            >
                                <Button
                                    asChild
                                    class="group rounded-full bg-foreground px-7 py-2.5 text-sm font-medium text-background transition-all duration-300 hover:-translate-y-0.5 hover:bg-foreground/90 hover:shadow-lg active:translate-y-0 active:bg-foreground/80"
                                >
                                    <Link href="/resume" class="flex items-center gap-2.5">
                                        <span>View Resume</span>
                                        <ArrowRight
                                            class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-0.5"
                                        />
                                    </Link>
                                </Button>
                                <Button
                                    asChild
                                    variant="outline"
                                    class="group rounded-full border-border/50 bg-background/60 px-7 py-2.5 text-sm font-medium backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-border hover:bg-accent active:translate-y-0"
                                >
                                    <Link href="/portfolio" class="flex items-center gap-2.5">
                                        <span>Projects</span>
                                        <ArrowRight
                                            class="h-3.5 w-3.5 opacity-0 transition-all duration-300 group-hover:translate-x-0.5 group-hover:opacity-100"
                                        />
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Avatar Section -->
                    <div
                        class="reveal order-1 flex items-center justify-center relative lg:order-2"
                        :style="{ animationDelay: `${stagger.avatar}ms` }"
                        :class="{ 'is-visible': isVisible }"
                    >
                        <div class="relative group avatar-container">
                            <!-- Decorative rings -->
                            <div
                                class="absolute inset-0 rounded-full border-2 border-primary/20 scale-110 group-hover:scale-125 group-active:scale-115 transition-transform duration-700"
                            ></div>
                            <div
                                class="absolute inset-0 rounded-full border border-primary/10 scale-125 group-hover:scale-140 group-active:scale-130 transition-transform duration-1000"
                            ></div>

                            <Avatar
                                class="w-44 h-44 xs:w-52 xs:h-52 sm:w-64 sm:h-64 md:w-80 md:h-80 lg:w-96 lg:h-96 xl:w-[28rem] xl:h-[28rem] 2xl:w-[32rem] 2xl:h-[32rem] floating border-4 border-background shadow-2xl relative z-10"
                            >
                                <AvatarImage
                                    v-if="users.image"
                                    :src="users.image.startsWith('http') ? users.image : `/${users.image}`"
                                    :alt="users.name"
                                    class="object-cover object-[center_30%]"
                                />
                                <AvatarFallback
                                    class="bg-gradient-to-br from-primary via-primary/90 to-primary/70 text-primary-foreground text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-semibold"
                                >
                                    {{ users.name?.charAt(0) }}
                                </AvatarFallback>
                            </Avatar>

                            <!-- Glowing effect -->
                            <div
                                class="absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-primary/5 rounded-full blur-2xl -z-10 group-hover:from-primary/20 group-hover:to-primary/10 group-active:from-primary/15 group-active:to-primary/8 transition-all duration-700"
                            ></div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </FrontendLayout>
</template>

<style scoped>
/* --- Reveal animation --- */
.reveal {
    opacity: 0;
    transform: translateY(14px);
}

.reveal.is-visible {
    animation: revealUp 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

@keyframes revealUp {
    from {
        opacity: 0;
        transform: translateY(14px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* --- Social icon buttons --- */
.social-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 9999px;
    color: var(--color-muted-foreground);
    transition:
        color 0.25s ease,
        background-color 0.25s ease,
        transform 0.25s ease;
}

.social-icon:hover {
    color: var(--color-foreground);
    background-color: var(--color-muted);
    transform: translateY(-2px);
}

.social-icon:active {
    transform: translateY(0);
}

/* --- Reduced motion --- */
@media (prefers-reduced-motion: reduce) {
    .reveal {
        opacity: 1;
        transform: none;
    }
    .reveal.is-visible {
        animation: none;
    }
    .social-icon {
        transition: none;
    }
}
</style>
