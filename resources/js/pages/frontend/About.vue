<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Badge } from '@/components/ui/badge'
import { Separator } from '@/components/ui/separator'
import { Skeleton } from '@/components/ui/skeleton'
import { MapPin, GraduationCap, Briefcase, Code2, Target } from 'lucide-vue-next'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import StatItem from '@/components/about/StatItem.vue'
import Timeline from '@/components/about/Timeline.vue'
import TechCard from '@/components/about/TechCard.vue'


// Types
interface AboutMe {
    title?: string
    description?: string
    location?: string
    year_experience?: string
    fucus_on?: string
}

interface WorkExperience {
    id: number
    title: string
    company: string
    description: string
    from: string
    to: string
}

interface Education {
    id: number
    title: string
    institution: string
    description: string
    from: string
    to: string
}

interface TechStack {
    id: number
    name: string
    logo?: string
    type: string
    description: string
}

interface Props {
    title?: string
    description?: string
    aboutMe: AboutMe
    workExperience: WorkExperience[]
    education: Education[]
    techStacks: TechStack[]
}

const props = defineProps<Props>()

const isLoading = ref(true)
const isVisible = ref(false)

const stagger = {
    badge: 0,
    title: 100,
    description: 200,
    stats: 320,
    journeyHeader: 0,
    experience: 120,
    education: 240,
    techHeader: 0,
    techGroups: 120,
}

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
        setTimeout(() => {
            isVisible.value = true
        }, 50)
    }, 600)
})

// Group tech stacks by type
const groupedTechStacks = computed(() => {
    const groups: Record<string, TechStack[]> = {}
    props.techStacks.forEach((tech) => {
        const type = tech.type || 'Other'
        if (!groups[type]) {
            groups[type] = []
        }
        groups[type].push(tech)
    })
    return groups
})

// Stats for the quick info
const stats = computed(() => [
    { icon: MapPin, label: 'Location', value: props.aboutMe.location },
    { icon: Briefcase, label: 'Experience', value: props.aboutMe.year_experience },
    { icon: Target, label: 'Focus', value: props.aboutMe.fucus_on },
])
</script>

<template>
    <FrontendLayout current-route="/about">
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta
                name="keywords"
                content="about me, software developer, experience, education, skills, professional background"
            />
            <meta property="og:title" :content="title" />
            <meta property="og:description" :content="description" />
            <meta property="og:type" content="profile" />
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" :content="title" />
            <meta name="twitter:description" :content="description" />
        </Head>

        <div class="min-h-screen pt-20">
            <!-- Skeleton Loading State -->
            <template v-if="isLoading">
                <!-- Hero Skeleton -->
                <section class="px-6 pb-16 pt-10">
                    <div class="mx-auto max-w-4xl">
                        <div class="space-y-6">
                            <Skeleton class="h-5 w-36 rounded-full" />
                            <Skeleton class="h-11 w-72 rounded-lg sm:w-96" />
                            <div class="h-px w-12"></div>
                            <div class="space-y-2.5 max-w-2xl">
                                <Skeleton class="h-4 w-full rounded" />
                                <Skeleton class="h-4 w-full rounded" />
                                <Skeleton class="h-4 w-4/5 rounded" />
                            </div>
                            <div class="flex flex-wrap gap-6 pt-2">
                                <div v-for="i in 3" :key="i" class="flex items-center gap-2.5">
                                    <Skeleton class="size-9 rounded-lg" />
                                    <div class="space-y-1">
                                        <Skeleton class="h-2.5 w-12 rounded" />
                                        <Skeleton class="h-3.5 w-24 rounded" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Journey Skeleton -->
                <section class="px-6 py-16">
                    <div class="mx-auto max-w-4xl">
                        <Skeleton class="mb-2 h-8 w-52 rounded-lg" />
                        <Skeleton class="mb-10 h-4 w-72 rounded" />
                        <div class="grid gap-10 lg:grid-cols-2">
                            <div v-for="col in 2" :key="col" class="space-y-4">
                                <Skeleton class="h-5 w-28 rounded" />
                                <div
                                    v-for="i in 2"
                                    :key="i"
                                    class="space-y-3 rounded-xl border border-border/30 p-5"
                                >
                                    <div class="flex items-center justify-between">
                                        <Skeleton class="h-5 w-44 rounded" />
                                        <Skeleton class="h-6 w-28 rounded-full" />
                                    </div>
                                    <Skeleton class="h-3.5 w-32 rounded" />
                                    <div class="space-y-1.5">
                                        <Skeleton class="h-3 w-full rounded" />
                                        <Skeleton class="h-3 w-3/4 rounded" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Tech Stack Skeleton -->
                <section class="px-6 py-16">
                    <div class="mx-auto max-w-4xl">
                        <Skeleton class="mb-2 h-8 w-44 rounded-lg" />
                        <Skeleton class="mb-10 h-4 w-80 rounded" />
                        <div class="space-y-8">
                            <div v-for="g in 2" :key="g">
                                <Skeleton class="mb-4 h-3.5 w-20 rounded" />
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                    <Skeleton v-for="i in 4" :key="i" class="h-20 rounded-xl" />
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </template>

            <!-- Actual Content -->
            <template v-else>
                <!-- Hero Section -->
                <section class="relative px-6 pb-16 pt-10">
                    <!-- Ambient background -->
                    <div class="pointer-events-none absolute inset-0 overflow-hidden">
                        <div
                            class="absolute -top-40 right-1/3 h-[30rem] w-[30rem] rounded-full bg-primary/[0.03] blur-[120px]"
                        ></div>
                    </div>

                    <div class="relative mx-auto max-w-4xl">
                        <div class="space-y-5">
                            <!-- Badge -->
                            <div
                                class="reveal"
                                :style="{ animationDelay: `${stagger.badge}ms` }"
                                :class="{ 'is-visible': isVisible }"
                            >
                                <Badge
                                    variant="outline"
                                    class="gap-1.5 border-border/40 bg-muted/40 px-3 py-1.5 text-xs font-normal text-muted-foreground backdrop-blur-sm transition-colors duration-200 hover:border-primary/30 hover:bg-primary/10 hover:text-primary"
                                >
                                    <Code2 class="size-3 opacity-60" />
                                    Software Developer
                                </Badge>
                            </div>

                            <!-- Title -->
                            <div
                                class="reveal"
                                :style="{ animationDelay: `${stagger.title}ms` }"
                                :class="{ 'is-visible': isVisible }"
                            >
                                <h1
                                    class="text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl lg:text-5xl"
                                >
                                    {{ aboutMe.title || 'About Me' }}
                                </h1>
                            </div>

                            <!-- Divider -->
                            <div
                                class="reveal"
                                :style="{ animationDelay: `${stagger.description - 40}ms` }"
                                :class="{ 'is-visible': isVisible }"
                            >
                                <div class="h-px w-12 bg-border/60"></div>
                            </div>

                            <!-- Description -->
                            <div
                                class="reveal"
                                :style="{ animationDelay: `${stagger.description}ms` }"
                                :class="{ 'is-visible': isVisible }"
                            >
                                <p
                                    class="max-w-2xl text-base leading-relaxed text-muted-foreground lg:text-lg"
                                >
                                    {{ aboutMe.description || 'No description available.' }}
                                </p>
                            </div>

                            <!-- Stats - inline row -->
                            <div
                                class="reveal"
                                :style="{ animationDelay: `${stagger.stats}ms` }"
                                :class="{ 'is-visible': isVisible }"
                            >
                                <div class="flex flex-wrap items-center gap-x-6 gap-y-3 pt-2">
                                    <template v-for="(stat, index) in stats" :key="stat.label">
                                        <StatItem
                                            :icon="stat.icon"
                                            :label="stat.label"
                                            :value="stat.value || ''"
                                        />
                                        <Separator
                                            v-if="index < stats.length - 1"
                                            orientation="vertical"
                                            class="hidden h-8 bg-border/40 sm:block"
                                        />
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Experience & Education Section -->
                <section class="px-6 py-16">
                    <div class="mx-auto max-w-4xl">
                        <div class="grid gap-8 lg:grid-cols-[220px_1fr] lg:gap-12">
                            <!-- Left: Section Header -->
                            <div
                                class="reveal"
                                :style="{ animationDelay: `${stagger.journeyHeader}ms` }"
                                :class="{ 'is-visible': isVisible }"
                            >
                                <h2
                                    class="text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl lg:sticky lg:top-24"
                                >
                                    Professional Journey
                                    <p class="mt-2 text-sm font-normal text-muted-foreground">
                                        My career path and educational background.
                                    </p>
                                    <div class="mt-4 h-px w-12 bg-border/60"></div>
                                </h2>
                            </div>

                            <!-- Right: Timelines -->
                            <div class="space-y-6">
                                <!-- Work Experience -->
                                <div
                                    class="reveal"
                                    :style="{ animationDelay: `${stagger.experience}ms` }"
                                    :class="{ 'is-visible': isVisible }"
                                >
                                    <Timeline
                                        title="Experience"
                                        :icon="Briefcase"
                                        :items="workExperience"
                                        subtitle-key="company"
                                    />
                                </div>

                                <!-- Education -->
                                <div
                                    class="reveal"
                                    :style="{ animationDelay: `${stagger.education}ms` }"
                                    :class="{ 'is-visible': isVisible }"
                                >
                                    <Timeline
                                        title="Education"
                                        :icon="GraduationCap"
                                        :items="education"
                                        subtitle-key="institution"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Tech Stack Section -->
                <section class="px-6 py-16">
                    <div class="mx-auto max-w-4xl">
                        <div class="grid gap-8 lg:grid-cols-[220px_1fr] lg:gap-12">
                            <!-- Left: Section Header -->
                            <div
                                class="reveal"
                                :style="{ animationDelay: `${stagger.techHeader}ms` }"
                                :class="{ 'is-visible': isVisible }"
                            >
                                <div class="lg:sticky lg:top-24">
                                    <h2
                                        class="text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl"
                                    >
                                        Technology Stack
                                    </h2>
                                    <p class="mt-2 text-sm text-muted-foreground">
                                        Tools and technologies I use to build modern applications.
                                    </p>
                                    <div class="mt-4 h-px w-12 bg-border/60"></div>
                                </div>
                            </div>

                            <!-- Right: Tech Groups -->
                            <div class="space-y-8">
                                <div
                                    v-for="(techs, type, index) in groupedTechStacks"
                                    :key="type"
                                    class="reveal"
                                    :style="{
                                        animationDelay: `${stagger.techGroups + (index as number) * 80}ms`,
                                    }"
                                    :class="{ 'is-visible': isVisible }"
                                >
                                    <h3
                                        class="mb-3.5 text-[11px] font-semibold uppercase tracking-[0.15em] text-muted-foreground/70"
                                    >
                                        {{ type }}
                                    </h3>
                                    <div
                                        class="grid grid-cols-2 gap-3 sm:grid-cols-3"
                                    >
                                        <TechCard
                                            v-for="tech in techs"
                                            :key="tech.id"
                                            :name="tech.name"
                                            :logo="tech.logo"
                                            :type="tech.type"
                                            :description="tech.description"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </template>
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

@media (prefers-reduced-motion: reduce) {
    .reveal {
        opacity: 1;
        transform: none;
    }
    .reveal.is-visible {
        animation: none;
    }
}
</style>
