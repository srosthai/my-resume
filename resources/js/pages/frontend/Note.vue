<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { Search, Filter, Copy, Check, ArrowLeft, Book, Code, Terminal, Lightbulb } from 'lucide-vue-next'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import MusicPlayer from '@/components/MusicPlayer.vue'

const props = defineProps({
    title: {
        type: String,
        default: 'My Notes'
    },
    description: {
        type: String,
        default: 'My collection of programming notes and tutorials'
    },
    notes: {
        type: Array,
        default: () => []
    }
})

const isLoading = ref(true)
const isVisible = ref(false)
const searchQuery = ref('')
const selectedFilter = ref('All')
const selectedNote = ref(null)
const copiedCommands = ref(new Set())

const staggerDelay = {
    header: 0,
    description: 100,
    search: 200,
    filters: 300,
    grid: 400,
}

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
        setTimeout(() => {
            isVisible.value = true
        }, 50)
    }, 800)
})

const categories = computed(() => {
    const cats = ['All', ...new Set(props.notes.map(note => note.category))]
    return cats
})

const filteredNotes = computed(() => {
    let filtered = props.notes

    if (selectedFilter.value !== 'All') {
        filtered = filtered.filter(note => note.category === selectedFilter.value)
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(note =>
            note.title.toLowerCase().includes(query) ||
            note.description.toLowerCase().includes(query) ||
            (note.tags && note.tags.some(tag => tag.toLowerCase().includes(query)))
        )
    }

    return filtered
})

const copyCommand = async (command, stepIndex, commandIndex) => {
    try {
        await navigator.clipboard.writeText(command)
        const key = `${stepIndex}-${commandIndex}`
        copiedCommands.value.add(key)
        setTimeout(() => {
            copiedCommands.value.delete(key)
        }, 2000)
    } catch (err) {
        console.error('Failed to copy command:', err)
    }
}

const isCopied = (stepIndex, commandIndex) => {
    return copiedCommands.value.has(`${stepIndex}-${commandIndex}`)
}

const getCategoryIcon = (category) => {
    const icons = {
        'Laravel': Code,
        'Vue.js': Code,
        'Next.js': Code,
        'Bootstrap': Lightbulb,
        'React': Code,
        'JavaScript': Terminal,
        'PHP': Code
    }
    return icons[category] || Book
}

const getCategoryColor = (category) => {
    const colors = {
        'Laravel': 'bg-red-500/10 text-red-500 border-red-500/20',
        'Vue.js': 'bg-green-500/10 text-green-500 border-green-500/20',
        'Next.js': 'bg-blue-500/10 text-blue-500 border-blue-500/20',
        'Bootstrap': 'bg-purple-500/10 text-purple-500 border-purple-500/20',
        'React': 'bg-cyan-500/10 text-cyan-500 border-cyan-500/20',
        'JavaScript': 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
        'PHP': 'bg-indigo-500/10 text-indigo-500 border-indigo-500/20'
    }
    return colors[category] || 'bg-gray-500/10 text-gray-500 border-gray-500/20'
}
</script>

<template>

    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="description" />
        <meta name="keywords" content="programming notes, tutorials, coding guides, developer resources, tech documentation" />
        <meta name="author" content="Software Developer" />

        <!-- Open Graph Meta Tags -->
        <meta property="og:title" :content="title" />
        <meta property="og:description" :content="description" />
        <meta property="og:image" content="/notes-og-image.jpg" />
        <meta property="og:url" :content="$page.url" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="Programming Notes & Tutorials" />

        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="title" />
        <meta name="twitter:description" :content="description" />
        <meta name="twitter:image" content="/notes-og-image.jpg" />

        <!-- Additional SEO Meta Tags -->
        <meta name="robots" content="index, follow" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="canonical" :href="$page.url" />
    </Head>

    <FrontendLayout currentRoute="/note">
        <!-- Music Player -->
        <MusicPlayer />

        <div class="min-h-screen bg-gradient-to-br from-background via-background/95 to-background text-foreground font-sans overflow-x-hidden transition-all duration-300 pt-16">

        <!-- Notes List View -->
        <div v-if="!selectedNote" class="max-w-7xl mx-auto px-4 py-6 sm:py-8">

            <!-- Skeleton Loading State -->
            <template v-if="isLoading">
                <!-- Header Skeleton -->
                <div class="text-center mb-8 lg:mb-12 flex flex-col items-center">
                    <div class="flex items-center justify-center gap-3 mb-4">
                        <Skeleton class="h-12 w-12 sm:h-14 sm:w-14 rounded-xl" />
                        <Skeleton class="h-8 w-40 sm:h-10 sm:w-56 rounded-lg" />
                    </div>
                    <div class="space-y-2 max-w-2xl w-full">
                        <Skeleton class="h-4 w-full rounded-lg" />
                        <Skeleton class="h-4 w-3/4 rounded-lg mx-auto" />
                    </div>
                </div>

                <!-- Search Skeleton -->
                <div class="mb-8 space-y-4">
                    <Skeleton class="h-10 w-full rounded-lg" />
                    <!-- Filter Skeleton -->
                    <div class="flex flex-wrap gap-2">
                        <Skeleton v-for="i in 5" :key="i" class="h-8 w-16 sm:w-20 rounded-md" />
                    </div>
                </div>

                <!-- Grid Skeleton -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    <Skeleton v-for="i in 6" :key="i" class="h-48 sm:h-56 rounded-lg" />
                </div>
            </template>

            <!-- Actual Content -->
            <template v-else>
                <!-- Header -->
                <div
                    class="text-center mb-8 lg:mb-12 stagger-fade-in"
                    :class="{ 'animate': isVisible }"
                    :style="{ animationDelay: `${staggerDelay.header}ms` }"
                >
                    <div class="flex items-center justify-center gap-3 mb-4">
                        <div class="p-2.5 sm:p-3 bg-primary/10 rounded-xl">
                            <Book class="h-6 w-6 sm:h-8 sm:w-8 text-primary" />
                        </div>
                        <h1
                            class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                            {{ title }}
                        </h1>
                    </div>
                    <p
                        class="stagger-fade-in text-sm sm:text-base lg:text-lg text-muted-foreground max-w-2xl mx-auto"
                        :class="{ 'animate': isVisible }"
                        :style="{ animationDelay: `${staggerDelay.description}ms` }"
                    >
                        {{ description }}
                    </p>
                </div>

                <!-- Search and Filter -->
                <div class="mb-6 sm:mb-8 space-y-3 sm:space-y-4">
                    <!-- Search -->
                    <div
                        class="stagger-fade-in"
                        :class="{ 'animate': isVisible }"
                        :style="{ animationDelay: `${staggerDelay.search}ms` }"
                    >
                        <div class="relative">
                            <Search
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <Input v-model="searchQuery" placeholder="Search notes, tags, or content..."
                                class="pl-10 bg-card/50 backdrop-blur-sm border-border/50 focus:bg-card/80 transition-all duration-300" />
                        </div>
                    </div>

                    <!-- Filter -->
                    <div
                        class="stagger-fade-in"
                        :class="{ 'animate': isVisible }"
                        :style="{ animationDelay: `${staggerDelay.filters}ms` }"
                    >
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 -mx-4 px-4 sm:mx-0 sm:px-0 sm:flex-wrap sm:overflow-visible scrollbar-hide">
                            <Filter class="h-4 w-4 text-muted-foreground shrink-0" />
                            <Button v-for="category in categories" :key="category" @click="selectedFilter = category"
                                :variant="selectedFilter === category ? 'default' : 'outline'" size="sm"
                                class="transition-all duration-300 shrink-0 text-xs sm:text-sm"
                                :class="selectedFilter === category ? 'bg-primary text-primary-foreground' : 'bg-card/50 backdrop-blur-sm border-border/50 hover:bg-card/80'">
                                {{ category }}
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Notes Grid -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 stagger-fade-in"
                    :class="{ 'animate': isVisible }"
                    :style="{ animationDelay: `${staggerDelay.grid}ms` }"
                >
                    <Card v-for="(note, index) in filteredNotes" :key="note.id" @click="selectedNote = note"
                        class="bg-card/50 backdrop-blur-sm border border-border/50 hover:bg-card/80 active:bg-card/80 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl cursor-pointer group focus:outline-none focus:ring-2 focus:ring-primary/20"
                        tabindex="0">
                        <CardHeader class="pb-3 p-4 sm:p-6 sm:pb-3">
                            <div class="flex items-start justify-between mb-2">
                                <component :is="getCategoryIcon(note.category)"
                                    class="h-5 w-5 sm:h-6 sm:w-6 text-primary group-hover:scale-110 transition-transform" />
                                <Badge variant="outline" :class="getCategoryColor(note.category)" class="text-xs">
                                    {{ note.category }}
                                </Badge>
                            </div>
                            <CardTitle class="text-base sm:text-lg font-bold group-hover:text-primary transition-colors">
                                {{ note.title }}
                            </CardTitle>
                            <CardDescription class="text-xs sm:text-sm text-muted-foreground line-clamp-2">
                                {{ note.description }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="pt-0 px-4 sm:px-6 pb-4 sm:pb-6">
                            <div class="flex flex-wrap gap-1 mb-3" v-if="note.tags && note.tags.length">
                                <Badge v-for="tag in note.tags.slice(0, 3)" :key="tag" variant="secondary"
                                    class="text-xs bg-primary/10 text-primary">
                                    {{ tag }}
                                </Badge>
                                <Badge v-if="note.tags.length > 3" variant="secondary" class="text-xs bg-muted">
                                    +{{ note.tags.length - 3 }}
                                </Badge>
                            </div>
                            <div class="text-xs text-muted-foreground">
                                Created: {{ new Date(note.created_at).toLocaleDateString() }}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Empty State -->
                <div v-if="filteredNotes.length === 0" class="text-center py-12 sm:py-16">
                    <div class="p-4 bg-muted/50 rounded-full w-fit mx-auto mb-4">
                        <Search class="h-8 w-8 text-muted-foreground" />
                    </div>
                    <h3 class="text-lg font-semibold mb-2">No notes found</h3>
                    <p class="text-sm sm:text-base text-muted-foreground">
                        {{ searchQuery || selectedFilter !== 'All' ? 'Try adjusting your search or filter criteria' :
                            'No notes available yet' }}
                    </p>
                </div>
            </template>
        </div>

        <!-- Note Detail View -->
        <div v-else class="max-w-4xl mx-auto px-4 py-6 sm:py-8">
            <!-- Back Button -->
            <Button @click="selectedNote = null" variant="outline"
                class="mb-6 bg-card/50 backdrop-blur-sm border-border/50 hover:bg-card/80">
                <ArrowLeft class="h-4 w-4 mr-2" />
                Back to Notes
            </Button>

            <!-- Note Header -->
            <div class="mb-8 stagger-fade-in" :class="{ 'animate': true }">
                <div class="flex items-center gap-3 mb-4">
                    <component :is="getCategoryIcon(selectedNote.category)" class="h-6 w-6 sm:h-8 sm:w-8 text-primary" />
                    <Badge variant="outline" :class="getCategoryColor(selectedNote.category)">
                        {{ selectedNote.category }}
                    </Badge>
                </div>
                <h1
                    class="text-2xl sm:text-3xl md:text-4xl font-black mb-4 bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                    {{ selectedNote.title }}
                </h1>
                <p class="text-base sm:text-lg text-muted-foreground mb-4">
                    {{ selectedNote.description }}
                </p>
                <div class="flex flex-wrap gap-2" v-if="selectedNote.tags && selectedNote.tags.length">
                    <Badge v-for="tag in selectedNote.tags" :key="tag" variant="secondary"
                        class="bg-primary/10 text-primary">
                        {{ tag }}
                    </Badge>
                </div>
            </div>

            <!-- Note Content -->
            <div class="space-y-6 sm:space-y-8" v-if="selectedNote.content">
                <!-- Overview -->
                <Card class="bg-card/50 backdrop-blur-sm border border-border/50" v-if="selectedNote.content.overview">
                    <CardHeader class="p-4 sm:p-6 pb-3 sm:pb-3">
                        <CardTitle class="flex items-center gap-2 text-base sm:text-lg">
                            <Lightbulb class="h-4 w-4 sm:h-5 sm:w-5 text-primary" />
                            Overview
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="px-4 sm:px-6 pb-4 sm:pb-6">
                        <p class="text-sm sm:text-base text-muted-foreground leading-relaxed">
                            {{ selectedNote.content.overview }}
                        </p>
                    </CardContent>
                </Card>

                <!-- Requirements -->
                <Card class="bg-card/50 backdrop-blur-sm border border-border/50"
                    v-if="selectedNote.content.requirements && selectedNote.content.requirements.length">
                    <CardHeader class="p-4 sm:p-6 pb-3 sm:pb-3">
                        <CardTitle class="flex items-center gap-2 text-base sm:text-lg">
                            <Book class="h-4 w-4 sm:h-5 sm:w-5 text-primary" />
                            Requirements
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="px-4 sm:px-6 pb-4 sm:pb-6">
                        <ul class="space-y-2">
                            <li v-for="requirement in selectedNote.content.requirements" :key="requirement"
                                class="flex items-start gap-2 text-sm sm:text-base text-muted-foreground">
                                <div class="w-2 h-2 bg-primary rounded-full mt-1.5 shrink-0"></div>
                                {{ requirement }}
                            </li>
                        </ul>
                    </CardContent>
                </Card>

                <!-- Steps -->
                <div class="space-y-4 sm:space-y-6" v-if="selectedNote.content.steps && selectedNote.content.steps.length">
                    <h2 class="text-xl sm:text-2xl font-bold flex items-center gap-2">
                        <Terminal class="h-5 w-5 sm:h-6 sm:w-6 text-primary" />
                        Installation Steps
                    </h2>

                    <div v-for="(step, stepIndex) in selectedNote.content.steps" :key="stepIndex" class="relative">
                        <!-- Timeline connector -->
                        <div v-if="stepIndex < selectedNote.content.steps.length - 1"
                            class="absolute left-6 top-16 w-px h-16 bg-gradient-to-b from-primary to-primary/20 hidden md:block">
                        </div>

                        <Card
                            class="bg-card/50 backdrop-blur-sm border border-border/50 hover:bg-card/80 transition-all duration-300 relative md:ml-16">
                            <!-- Step number -->
                            <div
                                class="absolute -left-20 top-6 w-12 h-12 bg-primary text-primary-foreground rounded-full flex items-center justify-center font-bold text-lg hidden md:flex">
                                {{ stepIndex + 1 }}
                            </div>

                            <CardHeader class="p-4 sm:p-6 pb-3 sm:pb-3">
                                <CardTitle class="text-base sm:text-lg">
                                    <span
                                        class="md:hidden bg-primary text-primary-foreground px-2 py-0.5 rounded-full text-xs sm:text-sm mr-2">
                                        {{ stepIndex + 1 }}
                                    </span>
                                    {{ step.title }}
                                </CardTitle>
                                <CardDescription class="text-xs sm:text-sm">
                                    {{ step.description }}
                                </CardDescription>
                            </CardHeader>
                            <CardContent v-if="step.commands && step.commands.length" class="px-4 sm:px-6 pb-4 sm:pb-6 pt-0">
                                <div class="space-y-3">
                                    <div v-for="(command, commandIndex) in step.commands" :key="commandIndex"
                                        class="relative group">
                                        <div
                                            class="bg-muted/50 rounded-lg p-2.5 sm:p-3 pr-10 sm:pr-12 font-mono text-xs sm:text-sm border border-border/50 hover:border-primary/50 transition-all duration-300 overflow-x-auto">
                                            <code class="text-foreground whitespace-pre">{{ command }}</code>
                                        </div>
                                        <Button @click="copyCommand(command, stepIndex, commandIndex)" size="sm"
                                            variant="ghost"
                                            class="absolute right-1.5 sm:right-2 top-1.5 sm:top-2 h-7 w-7 sm:h-8 sm:w-8 p-0 opacity-70 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity hover:bg-primary/10">
                                            <Check v-if="isCopied(stepIndex, commandIndex)"
                                                class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-green-500" />
                                            <Copy v-else class="h-3.5 w-3.5 sm:h-4 sm:w-4" />
                                        </Button>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
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

code {
    font-family: 'Fira Code', 'Consolas', 'Monaco', monospace;
}

/* Hide scrollbar for horizontal filter scroll on mobile */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
