<script setup lang="ts">
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Separator } from '@/components/ui/separator'
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

            <!-- Hero Section -->
            <section class="px-4 pb-16 pt-8">
                <div class="mx-auto max-w-5xl">
                    <div class="grid gap-8 lg:grid-cols-[1fr_320px] lg:gap-12">
                        <!-- Main Content -->
                        <div class="space-y-6">
                            <div>
                                <Badge variant="secondary" class="mb-4 gap-1.5 px-3 py-1">
                                    <Code2 class="size-3.5" />
                                    Software Developer
                                </Badge>

                                <h1 class="text-4xl font-bold tracking-tight text-foreground sm:text-5xl">
                                    {{ aboutMe.title || 'About Me' }}
                                </h1>
                            </div>

                            <p class="text-lg leading-relaxed text-muted-foreground">
                                {{ aboutMe.description || 'No description available.' }}
                            </p>
                        </div>

                        <!-- Quick Stats Card -->
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
            </section>

            <!-- Experience & Education Section -->
            <section class="bg-muted/30 px-4 py-16">
                <div class="mx-auto max-w-5xl">
                    <h2 class="mb-12 text-center text-3xl font-bold tracking-tight text-foreground">
                        Professional Journey
                    </h2>

                    <div class="grid gap-12 lg:grid-cols-2">
                        <!-- Work Experience -->
                        <Timeline
                            title="Experience"
                            :icon="Briefcase"
                            :items="workExperience"
                            subtitle-key="company"
                        />

                        <!-- Education -->
                        <Timeline
                            title="Education"
                            :icon="GraduationCap"
                            :items="education"
                            subtitle-key="institution"
                        />
                    </div>
                </div>
            </section>

            <!-- Tech Stack Section -->
            <section class="px-4 py-16">
                <div class="mx-auto max-w-5xl">
                    <SectionHeader
                        title="Technology Stack"
                        subtitle="Tools and technologies I use to build modern applications"
                        :centered="true"
                    />

                    <!-- Grouped by type -->
                    <div class="space-y-10">
                        <div v-for="(techs, type) in groupedTechStacks" :key="type">
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
        </div>
    </FrontendLayout>
</template>
