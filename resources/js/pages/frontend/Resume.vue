<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Skeleton } from '@/components/ui/skeleton'
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

const isLoading = ref(true)
const isVisible = ref(false)

const stagger = {
    header: 0,
    left: 120,
    right: 200,
    footer: 320,
}

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
        setTimeout(() => {
            isVisible.value = true
        }, 50)
    }, 600)
})

const getUserInitials = (name?: string): string => {
    if (!name) return 'U'
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2)
}

const groupedSkills = computed(() => {
    const groups: Record<string, TechStack[]> = {}
    props.techStacks?.forEach((tech) => {
        const type = tech.type || 'Other'
        if (!groups[type]) groups[type] = []
        groups[type].push(tech)
    })
    return groups
})

const hasSkills = computed(() => props.techStacks?.length > 0)
const hasContactInfo = computed(() => props.users?.phone || props.users?.email || props.users?.address)

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

        <div class="min-h-screen bg-background pt-20 print:bg-white print:pt-0">
            <MusicPlayer class="print:hidden" />

            <!-- Print Button -->
            <div class="fixed right-4 top-24 z-50 print:hidden">
                <button
                    @click="printResume"
                    class="group flex items-center gap-2 rounded-lg border border-border/30 bg-card/80 px-3.5 py-2 text-xs font-medium text-muted-foreground shadow-sm backdrop-blur-sm transition-all duration-200 hover:border-primary/30 hover:bg-primary/5 hover:text-primary"
                >
                    <Printer class="size-3.5 transition-transform duration-200 group-hover:scale-110" />
                    <span class="hidden sm:inline">Print</span>
                </button>
            </div>

            <!-- Resume Container -->
            <div class="mx-auto max-w-4xl px-4 pb-16 print:max-w-none print:px-0 print:pb-0">

                <!-- Skeleton -->
                <div v-if="isLoading" class="resume-paper rounded-xl border border-border/30 bg-card/50">
                    <div class="border-b border-border/20 p-8">
                        <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-start">
                            <Skeleton class="size-24 rounded-full sm:size-28" />
                            <div class="flex-1 space-y-3 text-center sm:text-left">
                                <Skeleton class="mx-auto h-8 w-48 rounded-lg sm:mx-0 sm:w-64" />
                                <Skeleton class="mx-auto h-5 w-36 rounded-lg sm:mx-0" />
                                <Skeleton class="mx-auto h-4 w-full max-w-md rounded sm:mx-0" />
                                <Skeleton class="mx-auto h-4 w-3/4 max-w-md rounded sm:mx-0" />
                            </div>
                        </div>
                    </div>
                    <div class="grid gap-0 md:grid-cols-[280px_1fr]">
                        <div class="space-y-5 border-b border-border/20 p-6 md:border-b-0 md:border-r">
                            <div class="space-y-2">
                                <Skeleton class="h-4 w-16 rounded" />
                                <Skeleton class="h-3 w-full rounded" />
                                <Skeleton class="h-3 w-full rounded" />
                            </div>
                            <div class="space-y-2">
                                <Skeleton class="h-4 w-14 rounded" />
                                <Skeleton class="h-3 w-24 rounded" />
                                <Skeleton class="h-3 w-28 rounded" />
                                <Skeleton class="h-3 w-20 rounded" />
                            </div>
                        </div>
                        <div class="space-y-5 p-6">
                            <div class="space-y-3">
                                <Skeleton class="h-4 w-24 rounded" />
                                <Skeleton class="h-20 w-full rounded-lg" />
                                <Skeleton class="h-20 w-full rounded-lg" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resume Content -->
                <template v-else>
                    <div class="resume-paper rounded-xl border border-border/30 bg-card/50 print:rounded-none print:border-0 print:bg-white print:shadow-none">

                        <!-- Header -->
                        <div
                            class="reveal border-b border-border/20 print:border-b print:border-gray-200"
                            :style="{ animationDelay: `${stagger.header}ms` }"
                            :class="{ 'is-visible': isVisible }"
                        >
                            <div class="p-6 sm:p-8">
                                <div class="flex flex-col items-center gap-5 sm:flex-row sm:items-start">
                                    <!-- Avatar -->
                                    <Avatar class="size-22 border-2 border-border/30 shadow-sm sm:size-26 print:size-20 print:border-gray-200">
                                        <AvatarImage
                                            v-if="users?.image"
                                            :src="users.image.startsWith('http') ? users.image : `/${users.image}`"
                                            :alt="users?.name"
                                            class="object-cover"
                                        />
                                        <AvatarFallback class="bg-muted/50 text-xl font-semibold text-foreground/60">
                                            {{ getUserInitials(users?.name) }}
                                        </AvatarFallback>
                                    </Avatar>

                                    <!-- Info -->
                                    <div class="flex-1 text-center sm:text-left">
                                        <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl print:text-2xl">
                                            {{ users?.name || 'Your Name' }}
                                        </h1>
                                        <p class="mt-1 text-sm font-medium text-primary/80 sm:text-base print:text-sm">
                                            {{ users?.position || 'Software Developer' }}
                                        </p>

                                        <!-- Contact inline -->
                                        <div v-if="hasContactInfo" class="mt-3 flex flex-wrap items-center justify-center gap-x-5 gap-y-1.5 sm:justify-start">
                                            <span v-if="users?.phone" class="flex items-center gap-1.5 text-sm text-muted-foreground/60 print:text-xs">
                                                <Phone class="size-3.5" />
                                                {{ users.phone }}
                                            </span>
                                            <span v-if="users?.email" class="flex items-center gap-1.5 text-sm text-muted-foreground/60 print:text-xs">
                                                <Mail class="size-3.5" />
                                                {{ users.email }}
                                            </span>
                                            <span v-if="users?.address" class="flex items-center gap-1.5 text-sm text-muted-foreground/60 print:text-xs">
                                                <MapPin class="size-3.5" />
                                                {{ users.address }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Two Column Layout -->
                        <div class="grid gap-0 md:grid-cols-[280px_1fr] print:grid-cols-[240px_1fr]">

                            <!-- Left Sidebar -->
                            <div
                                class="reveal border-b border-border/20 md:border-b-0 md:border-r print:border-r print:border-gray-200"
                                :style="{ animationDelay: `${stagger.left}ms` }"
                                :class="{ 'is-visible': isVisible }"
                            >
                                <div class="space-y-6 p-6 print:p-5">

                                    <!-- Profile -->
                                    <section>
                                        <h2 class="mb-2.5 text-xs font-semibold uppercase tracking-[0.15em] text-muted-foreground/50 print:text-[10px] print:text-gray-400">
                                            Profile
                                        </h2>
                                        <p class="text-sm leading-[1.7] text-muted-foreground/70 print:text-xs print:leading-relaxed">
                                            {{ users?.description || aboutMe?.description || 'A passionate developer skilled in building complete applications.' }}
                                        </p>
                                    </section>

                                    <!-- Skills -->
                                    <section v-if="hasSkills">
                                        <h2 class="mb-3 text-xs font-semibold uppercase tracking-[0.15em] text-muted-foreground/50 print:text-[10px] print:text-gray-400">
                                            Skills
                                        </h2>
                                        <div class="space-y-4">
                                            <div v-for="(techs, type) in groupedSkills" :key="type">
                                                <h3 class="mb-1.5 text-xs font-semibold text-foreground/60 print:text-[11px]">
                                                    {{ type }}
                                                </h3>
                                                <div class="flex flex-wrap gap-1.5">
                                                    <span
                                                        v-for="tech in techs"
                                                        :key="tech.id"
                                                        class="rounded-md bg-muted/40 px-2 py-0.5 text-xs font-medium text-muted-foreground/70 print:bg-gray-100 print:text-[10px]"
                                                    >
                                                        {{ tech.name }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>

                            <!-- Right Main Content -->
                            <div
                                class="reveal"
                                :style="{ animationDelay: `${stagger.right}ms` }"
                                :class="{ 'is-visible': isVisible }"
                            >
                                <div class="space-y-6 p-6 print:p-5">

                                    <!-- Experience -->
                                    <section v-if="workExperience?.length > 0">
                                        <h2 class="mb-4 text-xs font-semibold uppercase tracking-[0.15em] text-muted-foreground/50 print:text-[10px] print:text-gray-400">
                                            Experience
                                        </h2>
                                        <div class="space-y-5">
                                            <div
                                                v-for="(work, index) in workExperience"
                                                :key="work.id"
                                            >
                                                <div class="flex flex-col gap-0.5 sm:flex-row sm:items-start sm:justify-between">
                                                    <div>
                                                        <h3 class="text-base font-semibold text-foreground print:text-sm">
                                                            {{ work.position }}
                                                        </h3>
                                                        <p class="text-sm font-medium text-primary/70 print:text-xs">
                                                            {{ work.company }}
                                                        </p>
                                                    </div>
                                                    <span class="mt-0.5 shrink-0 text-xs font-medium text-muted-foreground/40 sm:mt-0.5 print:text-[10px]">
                                                        {{ work.from }} — {{ work.to || 'Present' }}
                                                    </span>
                                                </div>

                                                <!-- Responsibilities -->
                                                <ul v-if="work.responsibilities" class="mt-2 space-y-1.5">
                                                    <li
                                                        v-for="responsibility in work.responsibilities.split('\n').filter(Boolean)"
                                                        :key="responsibility"
                                                        class="flex items-start gap-2 text-sm leading-relaxed text-muted-foreground/65 print:text-xs"
                                                    >
                                                        <span class="mt-2 size-1 shrink-0 rounded-full bg-primary/40" />
                                                        {{ responsibility }}
                                                    </li>
                                                </ul>
                                                <p v-else-if="work.description" class="mt-1.5 text-sm leading-relaxed text-muted-foreground/65 print:text-xs">
                                                    {{ work.description }}
                                                </p>

                                                <!-- Divider between items -->
                                                <div
                                                    v-if="index < workExperience.length - 1"
                                                    class="mt-4 border-b border-border/10 print:border-gray-100"
                                                />
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Separator between sections -->
                                    <div
                                        v-if="workExperience?.length > 0 && education?.length > 0"
                                        class="border-b border-border/15 print:border-gray-150"
                                    />

                                    <!-- Education -->
                                    <section v-if="education?.length > 0">
                                        <h2 class="mb-4 text-xs font-semibold uppercase tracking-[0.15em] text-muted-foreground/50 print:text-[10px] print:text-gray-400">
                                            Education
                                        </h2>
                                        <div class="space-y-4">
                                            <div
                                                v-for="(edu, index) in education"
                                                :key="edu.id"
                                            >
                                                <div class="flex flex-col gap-0.5 sm:flex-row sm:items-start sm:justify-between">
                                                    <div>
                                                        <h3 class="text-base font-semibold text-foreground print:text-sm">
                                                            {{ edu.degree }}
                                                        </h3>
                                                        <p class="text-sm font-medium text-primary/70 print:text-xs">
                                                            {{ edu.institution }}
                                                        </p>
                                                    </div>
                                                    <span class="mt-0.5 shrink-0 text-xs font-medium text-muted-foreground/40 sm:mt-0.5 print:text-[10px]">
                                                        {{ edu.from }} — {{ edu.to }}
                                                    </span>
                                                </div>
                                                <p v-if="edu.description" class="mt-1.5 text-sm leading-relaxed text-muted-foreground/65 print:text-xs">
                                                    {{ edu.description }}
                                                </p>
                                                <p v-if="edu.gpa" class="mt-1 text-xs text-muted-foreground/50 print:text-[10px]">
                                                    GPA: {{ edu.gpa }}
                                                </p>

                                                <div
                                                    v-if="index < education.length - 1"
                                                    class="mt-3.5 border-b border-border/10 print:border-gray-100"
                                                />
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Empty State -->
                                    <div
                                        v-if="(!education || education.length === 0) && (!workExperience || workExperience.length === 0)"
                                        class="py-12 text-center"
                                    >
                                        <p class="text-xs text-muted-foreground/40">No experience or education data available.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="reveal border-t border-border/15 print:hidden"
                            :style="{ animationDelay: `${stagger.footer}ms` }"
                            :class="{ 'is-visible': isVisible }"
                        >
                            <div class="flex items-center justify-between px-6 py-3">
                                <p class="text-[10px] text-muted-foreground/30">
                                    Last updated {{ new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' }) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </FrontendLayout>
</template>

<style scoped>
.reveal {
    opacity: 0;
    transform: translateY(16px);
    transition:
        opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1),
        transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.reveal.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.resume-paper {
    box-shadow:
        0 1px 3px rgba(0, 0, 0, 0.04),
        0 6px 16px rgba(0, 0, 0, 0.04);
}

@media (prefers-reduced-motion: reduce) {
    .reveal {
        opacity: 1;
        transform: none;
        transition: none;
    }
}

@media print {
    @page {
        size: A4;
        margin: 0.5in;
    }

    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .resume-paper {
        box-shadow: none;
    }
}
</style>
