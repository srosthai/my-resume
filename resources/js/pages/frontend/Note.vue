<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Search, Filter, Copy, Check, ArrowLeft, Book, Code, Terminal, Lightbulb } from 'lucide-vue-next'
import DockNavigation from '@/components/DockNavigation.vue'
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

const isVisible = ref(false)
const searchQuery = ref('')
const selectedFilter = ref('All')
const selectedNote = ref(null)
const copiedCommands = ref(new Set())

onMounted(() => {
    setTimeout(() => {
        isVisible.value = true
    }, 100)
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
    </Head>

    <div
        class="min-h-screen bg-gradient-to-br from-background via-background/95 to-background text-foreground font-sans overflow-x-hidden transition-all duration-300 pt-16">
        <!-- Dock Navigation -->
        <DockNavigation currentRoute="/note" />

        <!-- Music Player -->
        <MusicPlayer />

        <!-- Notes List View -->
        <div v-if="!selectedNote" class="max-w-7xl mx-auto px-4 py-8">
            <!-- Header -->
            <div class="text-center mb-8 lg:mb-12" :class="{ 'fade-in-up': isVisible }">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <div class="p-3 bg-primary/10 rounded-xl">
                        <Book class="h-8 w-8 text-primary" />
                    </div>
                    <h1
                        class="text-3xl sm:text-4xl md:text-5xl font-black bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                        {{ title }}
                    </h1>
                </div>
                <p class="text-base lg:text-lg text-muted-foreground max-w-2xl mx-auto">
                    {{ description }}
                </p>
            </div>

            <!-- Search and Filter -->
            <div class="mb-8 space-y-4" :class="{ 'fade-in-up': isVisible }">
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Search -->
                    <div class="relative flex-1">
                        <Search
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <Input v-model="searchQuery" placeholder="Search notes, tags, or content..."
                            class="pl-10 bg-card/50 backdrop-blur-sm border-border/50 focus:bg-card/80 transition-all duration-300" />
                    </div>

                    <!-- Filter -->
                    <div class="flex items-center gap-2">
                        <Filter class="h-4 w-4 text-muted-foreground" />
                        <div class="flex flex-wrap gap-2">
                            <Button v-for="category in categories" :key="category" @click="selectedFilter = category"
                                :variant="selectedFilter === category ? 'default' : 'outline'" size="sm"
                                class="transition-all duration-300"
                                :class="selectedFilter === category ? 'bg-primary text-primary-foreground' : 'bg-card/50 backdrop-blur-sm border-border/50 hover:bg-card/80'">
                                {{ category }}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card v-for="(note, index) in filteredNotes" :key="note.id" @click="selectedNote = note"
                    class="bg-card/50 backdrop-blur-sm border border-border/50 hover:bg-card/80 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl cursor-pointer group"
                    :class="{ 'fade-in-up': isVisible }" :style="{ animationDelay: `${index * 0.1}s` }">
                    <CardHeader class="pb-3">
                        <div class="flex items-start justify-between mb-2">
                            <component :is="getCategoryIcon(note.category)"
                                class="h-6 w-6 text-primary group-hover:scale-110 transition-transform" />
                            <Badge variant="outline" :class="getCategoryColor(note.category)">
                                {{ note.category }}
                            </Badge>
                        </div>
                        <CardTitle class="text-lg font-bold group-hover:text-primary transition-colors">
                            {{ note.title }}
                        </CardTitle>
                        <CardDescription class="text-sm text-muted-foreground">
                            {{ note.description }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="pt-0">
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
            <div v-if="filteredNotes.length === 0" class="text-center py-16">
                <div class="p-4 bg-muted/50 rounded-full w-fit mx-auto mb-4">
                    <Search class="h-8 w-8 text-muted-foreground" />
                </div>
                <h3 class="text-lg font-semibold mb-2">No notes found</h3>
                <p class="text-muted-foreground">
                    {{ searchQuery || selectedFilter !== 'All' ? 'Try adjusting your search or filter criteria' :
                        'Nonotes available yet' }}
                </p>
            </div>
        </div>

        <!-- Note Detail View -->
        <div v-else class="max-w-4xl mx-auto px-4 py-8">
            <!-- Back Button -->
            <Button @click="selectedNote = null" variant="outline"
                class="mb-6 bg-card/50 backdrop-blur-sm border-border/50 hover:bg-card/80">
                <ArrowLeft class="h-4 w-4 mr-2" />
                Back to Notes
            </Button>

            <!-- Note Header -->
            <div class="mb-8" :class="{ 'fade-in-up': isVisible }">
                <div class="flex items-center gap-3 mb-4">
                    <component :is="getCategoryIcon(selectedNote.category)" class="h-8 w-8 text-primary" />
                    <Badge variant="outline" :class="getCategoryColor(selectedNote.category)">
                        {{ selectedNote.category }}
                    </Badge>
                </div>
                <h1
                    class="text-3xl sm:text-4xl font-black mb-4 bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                    {{ selectedNote.title }}
                </h1>
                <p class="text-lg text-muted-foreground mb-4">
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
            <div class="space-y-8" v-if="selectedNote.content">
                <!-- Overview -->
                <Card class="bg-card/50 backdrop-blur-sm border border-border/50" v-if="selectedNote.content.overview">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Lightbulb class="h-5 w-5 text-primary" />
                            Overview
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground leading-relaxed">
                            {{ selectedNote.content.overview }}
                        </p>
                    </CardContent>
                </Card>

                <!-- Requirements -->
                <Card class="bg-card/50 backdrop-blur-sm border border-border/50"
                    v-if="selectedNote.content.requirements && selectedNote.content.requirements.length">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Book class="h-5 w-5 text-primary" />
                            Requirements
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <ul class="space-y-2">
                            <li v-for="requirement in selectedNote.content.requirements" :key="requirement"
                                class="flex items-center gap-2 text-muted-foreground">
                                <div class="w-2 h-2 bg-primary rounded-full"></div>
                                {{ requirement }}
                            </li>
                        </ul>
                    </CardContent>
                </Card>

                <!-- Steps -->
                <div class="space-y-6" v-if="selectedNote.content.steps && selectedNote.content.steps.length">
                    <h2 class="text-2xl font-bold flex items-center gap-2">
                        <Terminal class="h-6 w-6 text-primary" />
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

                            <CardHeader>
                                <CardTitle class="text-lg">
                                    <span
                                        class="md:hidden bg-primary text-primary-foreground px-2 py-1 rounded-full text-sm mr-2">
                                        {{ stepIndex + 1 }}
                                    </span>
                                    {{ step.title }}
                                </CardTitle>
                                <CardDescription>
                                    {{ step.description }}
                                </CardDescription>
                            </CardHeader>
                            <CardContent v-if="step.commands && step.commands.length">
                                <div class="space-y-3">
                                    <div v-for="(command, commandIndex) in step.commands" :key="commandIndex"
                                        class="relative group">
                                        <div
                                            class="bg-muted/50 rounded-lg p-3 pr-12 font-mono text-sm border border-border/50 hover:border-primary/50 transition-all duration-300">
                                            <code class="text-foreground break-all">{{ command }}</code>
                                        </div>
                                        <Button @click="copyCommand(command, stepIndex, commandIndex)" size="sm"
                                            variant="ghost"
                                            class="absolute right-2 top-2 h-8 w-8 p-0 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-primary/10">
                                            <Check v-if="isCopied(stepIndex, commandIndex)"
                                                class="h-4 w-4 text-green-500" />
                                            <Copy v-else class="h-4 w-4" />
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
</template>

<style scoped>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 1s ease-out forwards;
}

code {
    font-family: 'Fira Code', 'Consolas', 'Monaco', monospace;
}
</style>