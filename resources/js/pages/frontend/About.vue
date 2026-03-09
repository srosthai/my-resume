<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Separator } from '@/components/ui/separator'
import { Skeleton } from '@/components/ui/skeleton'
import { MapPin, GraduationCap, Briefcase, Code2, Target } from 'lucide-vue-next'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import MusicPlayer from '@/components/MusicPlayer.vue'
import StatItem from '@/components/about/StatItem.vue'
import Timeline from '@/components/about/Timeline.vue'
import TechCard from '@/components/about/TechCard.vue'
import SectionHeader from '@/components/about/SectionHeader.vue'

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

const staggerDelay = {
    badge: 0,
    title: 100,
    description: 200,
    statsCard: 300,
    journey: 0,
    experience: 100,
    education: 200,
    techHeader: 0,
    techGroups: 150,
}

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
        setTimeout(() => {
            isVisible.value = true
        }, 50)
    }, 800)
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

// Stats for the quick info card
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
            <!-- Music Player -->
            <MusicPlayer />

            <!-- Skeleton Loading State -->
            <template v-if="isLoading">
                <!-- Hero Skeleton -->
                <section class="px-4 pb-16 pt-8">
                    <div class="mx-auto max-w-5xl">
                        <div class="grid gap-8 lg:grid-cols-[1fr_320px] lg:gap-12">
                            <div class="space-y-6">
                                <div>
                                    <Skeleton class="mb-4 h-7 w-44 rounded-full" />
                                    <Skeleton class="h-10 w-64 sm:h-12 sm:w-80 rounded-lg" />
                                </div>
                                <div class="space-y-3">
                                    <Skeleton class="h-4 w-full rounded-lg" />
                                    <Skeleton class="h-4 w-full rounded-lg" />
                                    <Skeleton class="h-4 w-4/5 rounded-lg" />
                                    <Skeleton class="h-4 w-3/5 rounded-lg" />
                                </div>
                            </div>
                            <Card class="h-fit border-border/40 bg-card/50">
                                <CardContent class="space-y-4 p-5">
                                    <div v-for="i in 3" :key="i" class="flex items-center gap-3">
                                        <Skeleton class="size-10 rounded-lg" />
                                        <div class="space-y-1.5 flex-1">
                                            <Skeleton class="h-3 w-16 rounded" />
                                            <Skeleton class="h-4 w-32 rounded" />
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </section>

                <!-- Journey Skeleton -->
                <section class="bg-muted/30 px-4 py-16">
                    <div class="mx-auto max-w-5xl">
                        <Skeleton class="mx-auto mb-12 h-8 w-56 rounded-lg" />
                        <div class="grid gap-12 lg:grid-cols-2">
                            <div v-for="col in 2" :key="col" class="space-y-6">
                                <Skeleton class="h-7 w-32 rounded-lg" />
                                <div v-for="i in 2" :key="i" class="space-y-3 rounded-lg border border-border/40 p-4">
                                    <div class="flex items-center justify-between">
                                        <Skeleton class="h-5 w-48 rounded" />
                                        <Skeleton class="h-6 w-24 rounded-full" />
                                    </div>
                                    <Skeleton class="h-4 w-36 rounded" />
                                    <div class="space-y-2">
                                        <Skeleton class="h-3 w-full rounded" />
                                        <Skeleton class="h-3 w-4/5 rounded" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Tech Stack Skeleton -->
                <section class="px-4 py-16">
                    <div class="mx-auto max-w-5xl">
                        <div class="mb-8 text-center">
                            <Skeleton class="mx-auto mb-2 h-8 w-48 rounded-lg" />
                            <Skeleton class="mx-auto h-4 w-72 rounded-lg" />
                        </div>
                        <div class="space-y-10">
                            <div v-for="g in 2" :key="g">
                                <Skeleton class="mb-4 h-4 w-24 rounded" />
                                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                                    <Skeleton v-for="i in 4" :key="i" class="h-20 rounded-lg" />
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </template>

            <!-- Actual Content -->
            <template v-else>
                <!-- Hero Section -->
                <section class="px-4 pb-16 pt-8">
                    <div class="mx-auto max-w-5xl">
                        <div class="grid gap-8 lg:grid-cols-[1fr_320px] lg:gap-12">
                            <!-- Main Content -->
                            <div class="space-y-6">
                                <div>
                                    <div class="stagger-fade-in"
                                        :style="{ animationDelay: `${staggerDelay.badge}ms` }"
                                        :class="{ 'animate': isVisible }">
                                        <Badge variant="secondary" class="mb-4 gap-1.5 px-3 py-1">
                                            <Code2 class="size-3.5" />
                                            Software Developer
                                        </Badge>
                                    </div>

                                    <div class="stagger-fade-in"
                                        :style="{ animationDelay: `${staggerDelay.title}ms` }"
                                        :class="{ 'animate': isVisible }">
                                        <h1 class="text-4xl font-bold tracking-tight text-foreground sm:text-5xl">
                                            {{ aboutMe.title || 'About Me' }}
                                        </h1>
                                    </div>
                                </div>

                                <div class="stagger-fade-in"
                                    :style="{ animationDelay: `${staggerDelay.description}ms` }"
                                    :class="{ 'animate': isVisible }">
                                    <p class="text-lg leading-relaxed text-muted-foreground">
                                        {{ aboutMe.description || 'No description available.' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Quick Stats Card -->
                            <div class="stagger-fade-in"
                                :style="{ animationDelay: `${staggerDelay.statsCard}ms` }"
                                :class="{ 'animate': isVisible }">
                                <Card class="h-fit border-border/40 bg-card/50">
                                    <CardContent class="space-y-4 p-5">
                                        <template v-for="(stat, index) in stats" :key="stat.label">
                                            <StatItem :icon="stat.icon" :label="stat.label" :value="stat.value || ''" />
                                            <Separator v-if="index < stats.length - 1" class="bg-border/50" />
                                        </template>
                                    </CardContent>
                                </Card>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Experience & Education Section -->
                <section class="bg-muted/30 px-4 py-16">
                    <div class="mx-auto max-w-5xl">
                        <div class="stagger-fade-in"
                            :style="{ animationDelay: `${staggerDelay.journey}ms` }"
                            :class="{ 'animate': isVisible }">
                            <h2 class="mb-12 text-center text-3xl font-bold tracking-tight text-foreground">
                                Professional Journey
                            </h2>
                        </div>

                        <div class="grid gap-12 lg:grid-cols-2">
                            <!-- Work Experience -->
                            <div class="stagger-fade-in"
                                :style="{ animationDelay: `${staggerDelay.experience}ms` }"
                                :class="{ 'animate': isVisible }">
                                <Timeline
                                    title="Experience"
                                    :icon="Briefcase"
                                    :items="workExperience"
                                    subtitle-key="company"
                                />
                            </div>

                            <!-- Education -->
                            <div class="stagger-fade-in"
                                :style="{ animationDelay: `${staggerDelay.education}ms` }"
                                :class="{ 'animate': isVisible }">
                                <Timeline
                                    title="Education"
                                    :icon="GraduationCap"
                                    :items="education"
                                    subtitle-key="institution"
                                />
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Tech Stack Section -->
                <section class="px-4 py-16">
                    <div class="mx-auto max-w-5xl">
                        <div class="stagger-fade-in"
                            :style="{ animationDelay: `${staggerDelay.techHeader}ms` }"
                            :class="{ 'animate': isVisible }">
                            <SectionHeader
                                title="Technology Stack"
                                subtitle="Tools and technologies I use to build modern applications"
                                :centered="true"
                            />
                        </div>

                        <!-- Grouped by type -->
                        <div class="space-y-10">
                            <div v-for="(techs, type, index) in groupedTechStacks" :key="type"
                                class="stagger-fade-in"
                                :style="{ animationDelay: `${staggerDelay.techGroups + (index as number) * 100}ms` }"
                                :class="{ 'animate': isVisible }">
                                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-muted-foreground">
                                    {{ type }}
                                </h3>
                                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
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
                </section>
            </template>
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
