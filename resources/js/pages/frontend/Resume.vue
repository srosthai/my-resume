<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Separator } from '@/components/ui/separator'
import {
    Phone,
    Mail,
    MapPin,
    Printer,
} from 'lucide-vue-next'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import MusicPlayer from '@/components/MusicPlayer.vue'

// Types
interface UserInfo {
    name?: string
    email?: string
    phone?: string
    position?: string
    description?: string
    image?: string
    address?: string
}

interface WorkExperience {
    id: number
    position: string
    company: string
    description: string
    responsibilities?: string
    from: string
    to?: string
}

interface Education {
    id: number
    degree: string
    institution: string
    description?: string
    from: string
    to: string
    gpa?: string
}

interface TechStack {
    id: number
    name: string
    type: string
    description?: string
}

interface Props {
    users: UserInfo
    aboutMe?: {
        title?: string
        description?: string
    }
    workExperience: WorkExperience[]
    education: Education[]
    techStacks: TechStack[]
    title?: string
    description?: string
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Resume',
    description: 'Professional Resume',
})

// Animation state
const isVisible = ref(false)

onMounted(() => {
    requestAnimationFrame(() => {
        isVisible.value = true
    })
})

// Get user initials
const getUserInitials = (name?: string): string => {
    if (!name) return 'U'
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2)
}

// Group tech stacks by type (computed for reactivity)
const groupedSkills = computed(() => {
    const groups: Record<string, TechStack[]> = {}
    props.techStacks?.forEach((tech) => {
        const type = tech.type || 'Other'
        if (!groups[type]) {
            groups[type] = []
        }
        groups[type].push(tech)
    })
    return groups
})

// Check if we have any skills data
const hasSkills = computed(() => props.techStacks?.length > 0)

// Check if we have contact info
const hasContactInfo = computed(() => {
    return props.users?.phone || props.users?.email || props.users?.address
})

// Print function
const printResume = () => {
    window.print()
}
</script>

<template>
    <FrontendLayout current-route="/resume">
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta name="keywords" content="resume, CV, professional experience, software developer, skills" />
            <meta name="author" :content="users?.name" />
            <meta property="og:title" :content="title" />
            <meta property="og:description" :content="description" />
            <meta property="og:type" content="profile" />
        </Head>

        <div class="min-h-screen bg-background pt-20 print:pt-0 print:bg-white">
            <!-- Music Player (hidden on print) -->
            <MusicPlayer class="print:hidden" />

            <!-- Action Buttons (hidden on print) -->
            <div class="fixed right-4 top-24 z-50 flex flex-col gap-2 print:hidden">
                <button
                    @click="printResume"
                    class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-lg transition-all hover:bg-primary/90"
                >
                    <Printer class="size-4" />
                    <span class="hidden sm:inline">Print</span>
                </button>
            </div>

            <!-- Resume Container -->
            <div
                class="mx-auto max-w-4xl px-4 pb-16 print:max-w-none print:px-0 print:pb-0"
                :class="{ 'opacity-100': isVisible, 'opacity-0': !isVisible }"
                style="transition: opacity 0.5s ease-out"
            >
                <!-- Resume Paper -->
                <div class="rounded-xl border border-border/40 bg-card shadow-xl print:rounded-none print:border-0 print:shadow-none">

                    <!-- Header Section -->
                    <div class="flex flex-col items-center gap-6 border-b border-border/30 p-8 sm:flex-row sm:items-start print:border-b-2 print:border-primary/20">
                        <!-- Avatar -->
                        <Avatar class="size-28 border-4 border-primary/20 shadow-md sm:size-32 print:size-24">
                            <AvatarImage
                                v-if="users?.image"
                                :src="users.image.startsWith('http') ? users.image : `/${users.image}`"
                                :alt="users?.name"
                                class="object-cover"
                            />
                            <AvatarFallback class="bg-primary/10 text-2xl font-bold text-primary">
                                {{ getUserInitials(users?.name) }}
                            </AvatarFallback>
                        </Avatar>

                        <!-- Name & Title -->
                        <div class="flex-1 text-center sm:text-left">
                            <h1 class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl print:text-3xl">
                                {{ users?.name || 'Your Name' }}
                            </h1>
                            <p class="mt-1 text-lg font-medium text-primary sm:text-xl print:text-lg">
                                {{ users?.position || 'Software Developer' }}
                            </p>
                        </div>
                    </div>

                    <!-- Two Column Layout -->
                    <div class="grid gap-0 md:grid-cols-[1fr_1.2fr] print:grid-cols-[1fr_1.2fr]">

                        <!-- Left Column -->
                        <div class="space-y-6 border-b border-border/30 p-6 md:border-b-0 md:border-r print:border-r print:border-primary/20 print:p-6">

                            <!-- Profile Section -->
                            <section>
                                <h2 class="mb-3 text-lg font-semibold text-primary">Profile</h2>
                                <p class="text-sm leading-relaxed text-muted-foreground print:text-xs">
                                    {{ users?.description || aboutMe?.description || 'A passionate developer skilled in building complete applications.' }}
                                </p>
                            </section>

                            <Separator class="bg-border/50" />

                            <!-- Contact Section -->
                            <section v-if="hasContactInfo">
                                <h2 class="mb-3 text-lg font-semibold text-primary">Contact</h2>
                                <div class="space-y-2.5">
                                    <div v-if="users?.phone" class="flex items-center gap-3 text-sm text-muted-foreground">
                                        <Phone class="size-4 shrink-0 text-primary/60" />
                                        <span class="print:text-xs">{{ users.phone }}</span>
                                    </div>
                                    <div v-if="users?.email" class="flex items-center gap-3 text-sm text-muted-foreground">
                                        <Mail class="size-4 shrink-0 text-primary/60" />
                                        <span class="print:text-xs">{{ users.email }}</span>
                                    </div>
                                    <div v-if="users?.address" class="flex items-start gap-3 text-sm text-muted-foreground">
                                        <MapPin class="mt-0.5 size-4 shrink-0 text-primary/60" />
                                        <span class="print:text-xs">{{ users.address }}</span>
                                    </div>
                                </div>
                            </section>

                            <Separator v-if="hasContactInfo && hasSkills" class="bg-border/50" />

                            <!-- Skills Section (Dynamic from techStacks grouped by type) -->
                            <section v-if="hasSkills">
                                <h2 class="mb-3 text-lg font-semibold text-primary">Skills</h2>
                                <div class="space-y-4">
                                    <div v-for="(techs, type) in groupedSkills" :key="type">
                                        <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-muted-foreground/80">
                                            {{ type }}
                                        </h3>
                                        <ul class="space-y-1.5 text-sm text-muted-foreground">
                                            <li v-for="tech in techs" :key="tech.id" class="flex items-start gap-2">
                                                <span class="mt-2 size-1.5 shrink-0 rounded-full bg-primary/60"></span>
                                                <span class="print:text-xs">
                                                    {{ tech.name }}
                                                    <span v-if="tech.description" class="text-muted-foreground/70">
                                                        : {{ tech.description }}
                                                    </span>
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6 p-6 print:p-6">

                            <!-- Education Section -->
                            <section v-if="education?.length > 0">
                                <h2 class="mb-4 text-lg font-semibold text-primary">Education</h2>
                                <div class="space-y-4">
                                    <div
                                        v-for="edu in education"
                                        :key="edu.id"
                                        class="rounded-lg bg-muted/30 p-4 print:bg-primary/5 print:p-3"
                                    >
                                        <p class="text-sm font-medium text-primary">
                                            ({{ edu.from }}-{{ edu.to }})
                                        </p>
                                        <h3 class="mt-1 font-semibold uppercase text-foreground print:text-sm">
                                            {{ edu.institution }}
                                        </h3>
                                        <p class="text-sm text-muted-foreground print:text-xs">
                                            {{ edu.degree }}
                                        </p>
                                        <p v-if="edu.description" class="mt-1 text-sm text-muted-foreground print:text-xs">
                                            {{ edu.description }}
                                        </p>
                                    </div>
                                </div>
                            </section>

                            <Separator v-if="education?.length > 0 && workExperience?.length > 0" class="bg-border/50" />

                            <!-- Experience Section -->
                            <section v-if="workExperience?.length > 0">
                                <h2 class="mb-4 text-lg font-semibold text-primary">Experience</h2>
                                <div class="space-y-4">
                                    <div
                                        v-for="work in workExperience"
                                        :key="work.id"
                                        class="rounded-lg bg-muted/30 p-4 print:bg-primary/5 print:p-3"
                                    >
                                        <p class="text-sm font-medium text-primary">
                                            ({{ work.from }} - {{ work.to || 'Now' }})
                                        </p>
                                        <h3 class="mt-1 font-semibold text-foreground print:text-sm">
                                            {{ work.company }}
                                        </h3>
                                        <p class="text-sm italic text-muted-foreground print:text-xs">
                                            {{ work.position }}
                                        </p>

                                        <!-- Responsibilities as bullet points -->
                                        <ul v-if="work.responsibilities" class="mt-2 space-y-1">
                                            <li
                                                v-for="responsibility in work.responsibilities.split('\n').filter(Boolean)"
                                                :key="responsibility"
                                                class="flex items-start gap-2 text-sm text-muted-foreground"
                                            >
                                                <span class="mt-2 size-1.5 shrink-0 rounded-full bg-primary/60"></span>
                                                <span class="print:text-xs">{{ responsibility }}</span>
                                            </li>
                                        </ul>
                                        <p v-else-if="work.description" class="mt-2 text-sm text-muted-foreground print:text-xs">
                                            {{ work.description }}
                                        </p>
                                    </div>
                                </div>
                            </section>

                            <!-- Empty State -->
                            <div
                                v-if="(!education || education.length === 0) && (!workExperience || workExperience.length === 0)"
                                class="flex flex-col items-center justify-center py-12 text-center"
                            >
                                <p class="text-muted-foreground">No education or experience data available.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="border-t border-border/30 p-4 text-center print:hidden">
                        <p class="text-xs text-muted-foreground">
                            Last updated: {{ new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' }) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </FrontendLayout>
</template>

<style scoped>
/* Print styles */
@media print {
    @page {
        size: A4;
        margin: 0.5in;
    }

    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
