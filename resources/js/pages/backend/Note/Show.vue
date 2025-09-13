<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
    ArrowLeft,
    Edit,
    Book,
    User,
    Calendar,
    Star,
    Code,
    Terminal,
    Lightbulb,
    Copy,
    Check
} from 'lucide-vue-next'
import { ref } from 'vue'

const props = defineProps({
    note: {
        type: Object,
        required: true
    }
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Notes', href: '/notes' },
    { title: props.note.title, href: '' }
]

const copiedCommands = ref(new Set())

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

const getStatusColor = (status) => {
    const colors = {
        'draft': 'bg-gray-500/10 text-gray-500 border-gray-500/20',
        'published': 'bg-green-500/10 text-green-500 border-green-500/20',
        'archived': 'bg-red-500/10 text-red-500 border-red-500/20'
    }
    return colors[status] || 'bg-gray-500/10 text-gray-500 border-gray-500/20'
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
</script>

<template>
    <AppLayout>

        <Head :title="note.title" />

        <div class="space-y-6 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <Heading :title="note.title" description="Note details" :breadcrumbs="breadcrumbs" />
                <div class="flex items-center gap-3">
                    <Button asChild variant="outline">
                        <Link :href="route('notes.edit', note.id)" class="flex items-center gap-2">
                        <Edit class="h-4 w-4" />
                        Edit Note
                        </Link>
                    </Button>
                    <Button asChild variant="outline">
                        <Link :href="route('notes.index')">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Back to Notes
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Note Info -->
            <Card>
                <CardHeader>
                    <div class="flex items-start justify-between">
                        <div class="space-y-2">
                            <div class="flex items-center gap-3">
                                <component :is="getCategoryIcon(note.category)" class="h-6 w-6 text-primary" />
                                <Badge variant="outline" :class="getCategoryColor(note.category)">
                                    {{ note.category }}
                                </Badge>
                                <Badge variant="outline" :class="getStatusColor(note.status)" class="capitalize">
                                    {{ note.status }}
                                </Badge>
                                <Badge v-if="note.is_featured" variant="outline"
                                    class="bg-yellow-500/10 text-yellow-500 border-yellow-500/20">
                                    <Star class="h-3 w-3 mr-1 fill-current" />
                                    Featured
                                </Badge>
                            </div>
                            <CardTitle class="text-2xl">{{ note.title }}</CardTitle>
                            <CardDescription class="text-lg">{{ note.description }}</CardDescription>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <!-- Tags -->
                    <div v-if="note.tags && note.tags.length" class="mb-4">
                        <h4 class="text-sm font-medium mb-2">Tags</h4>
                        <div class="flex flex-wrap gap-2">
                            <Badge v-for="tag in note.tags" :key="tag" variant="secondary">
                                {{ tag }}
                            </Badge>
                        </div>
                    </div>

                    <!-- Meta Information -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-muted-foreground">
                        <div class="flex items-center gap-2">
                            <User class="h-4 w-4" />
                            <span>{{ note.user?.name || 'Admin' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Calendar class="h-4 w-4" />
                            <span>Created: {{ new Date(note.created_at).toLocaleDateString() }}</span>
                        </div>
                        <div v-if="note.published_at" class="flex items-center gap-2">
                            <Calendar class="h-4 w-4" />
                            <span>Published: {{ new Date(note.published_at).toLocaleDateString() }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Content -->
            <div class="space-y-6">
                <!-- Overview -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Lightbulb class="h-5 w-5 text-primary" />
                            Overview
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground leading-relaxed">
                            {{ note.content?.overview }}
                        </p>
                    </CardContent>
                </Card>

                <!-- Requirements -->
                <Card v-if="note.content?.requirements?.length">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Book class="h-5 w-5 text-primary" />
                            Requirements
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <ul class="space-y-2">
                            <li v-for="requirement in note.content.requirements" :key="requirement"
                                class="flex items-center gap-2 text-muted-foreground">
                                <div class="w-2 h-2 bg-primary rounded-full flex-shrink-0"></div>
                                {{ requirement }}
                            </li>
                        </ul>
                    </CardContent>
                </Card>

                <!-- Steps -->
                <div v-if="note.content?.steps?.length" class="space-y-6">
                    <h2 class="text-2xl font-bold flex items-center gap-2">
                        <Terminal class="h-6 w-6 text-primary" />
                        Installation Steps
                    </h2>

                    <div v-for="(step, stepIndex) in note.content.steps" :key="stepIndex" class="relative">
                        <!-- Timeline connector -->
                        <div v-if="stepIndex < note.content.steps.length - 1"
                            class="absolute left-6 top-16 w-px h-16 bg-gradient-to-b from-primary to-primary/20 hidden md:block">
                        </div>

                        <Card class="relative md:ml-16">
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
                            <CardContent>
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
    </AppLayout>
</template>

<style scoped>
code {
    font-family: 'Fira Code', 'Consolas', 'Monaco', monospace;
}
</style>