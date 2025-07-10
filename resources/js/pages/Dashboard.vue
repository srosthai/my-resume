<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    summary: {
        aboutMe: any;
        user: any;
        projects: {
            total: number;
            byType: any[];
            recent: any[];
        };
        techStacks: {
            byType: Record<string, number>;
            total: number;
        };
        workExperience: {
            total: number;
            recent: any[];
        };
        education: {
            total: number;
            latest: any;
        };
        songs: {
            total: number;
            recent: any[];
        };
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    }
];

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short'
    });
};

const getTechStackIcon = (type: string) => {
    const icons: Record<string, string> = {
        'frontend': '🎨',
        'backend': '⚙️',
        'database': '🗄️',
        'devops': '🚀',
        'mobile': '📱',
        'desktop': '💻',
        'tool': '🔧'
    };
    return icons[type.toLowerCase()] || '💻';
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30 rounded-xl p-6 border border-blue-200/50 dark:border-blue-800/50">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold">
                        {{ summary.user?.name?.charAt(0) || 'U' }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Welcome back, {{ summary.user?.name || 'User' }}!
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300">
                            {{ summary.aboutMe?.title || summary.user?.position || 'Developer' }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ summary.aboutMe?.year_experience || 0 }} years of experience in {{ summary.aboutMe?.fucus_on || 'development' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Projects Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Projects</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ summary.projects.total }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-1">
                        <span v-for="type in summary.projects.byType" :key="type.id" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                            {{ type.name }}: {{ type.projects_count }}
                        </span>
                    </div>
                </div>

                <!-- Tech Stack Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tech Stack</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ summary.techStacks.total }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <div v-for="(count, type) in summary.techStacks.byType" :key="type" class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                                <span>{{ getTechStackIcon(type) }}</span>
                                {{ type }}
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ count }}</span>
                        </div>
                    </div>
                </div>

                <!-- Work Experience Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Experience</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ summary.workExperience.total }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ summary.education.total }} education records
                    </p>
                </div>

                <!-- Music Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Popular Songs</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ summary.songs.total }}</p>
                        </div>
                        <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Music collection
                    </p>
                </div>
            </div>

            <!-- Detailed Sections -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Recent Projects -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Projects</h3>
                        <a href="/projects" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View all</a>
                    </div>
                    <div class="space-y-3">
                        <div v-for="project in summary.projects.recent" :key="project.id" class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-sm font-bold">
                                {{ project.title.charAt(0) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ project.title }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ project.project_type?.name || 'Project' }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ formatDate(project.created_date) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-if="summary.projects.recent.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            No projects yet
                        </div>
                    </div>
                </div>

                <!-- Work Experience Timeline -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Work Experience</h3>
                        <a href="/work-experience" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View all</a>
                    </div>
                    <div class="space-y-4">
                        <div v-for="experience in summary.workExperience.recent" :key="experience.id" class="relative pl-6 border-l-2 border-gray-200 dark:border-gray-600 last:border-l-0">
                            <div class="absolute w-3 h-3 bg-blue-500 rounded-full -left-2 top-1"></div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ experience.position }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ experience.company }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                    {{ formatDate(experience.from) }} - {{ experience.to ? formatDate(experience.to) : 'Present' }}
                                </p>
                            </div>
                        </div>
                        <div v-if="summary.workExperience.recent.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            No work experience recorded
                        </div>
                    </div>
                </div>

                <!-- Education Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Education</h3>
                        <a href="/eductions" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View all</a>
                    </div>
                    <div v-if="summary.education.latest" class="p-4 rounded-lg bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-950/30 dark:to-emerald-950/30 border border-green-200/50 dark:border-green-800/50">
                        <h4 class="font-medium text-gray-900 dark:text-white">{{ summary.education.latest.title }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ summary.education.latest.major }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ summary.education.latest.institution }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                            {{ formatDate(summary.education.latest.from) }} - {{ formatDate(summary.education.latest.to) }}
                        </p>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                        No education records
                    </div>
                </div>

                <!-- Recent Songs -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Songs</h3>
                        <a href="/popular-songs" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View all</a>
                    </div>
                    <div class="space-y-3">
                        <div v-for="song in summary.songs.recent" :key="song.id" class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                            <div class="w-10 h-10 bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ song.title }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ song.artist }}</p>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ song.formatted_duration || `${Math.floor(song.duration / 60)}:${(song.duration % 60).toString().padStart(2, '0')}` }}
                            </span>
                        </div>
                        <div v-if="summary.songs.recent.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            No songs in collection
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
