<script setup>
import { ref, computed } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Badge } from '@/components/ui/badge'
import {
    Plus,
    Trash2,
    Save,
    ArrowLeft,
    Book,
    Tag,
    FileText,
    Settings,
    Star,
    Calendar
} from 'lucide-vue-next'

const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    }
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Notes', href: '/notes' },
    { title: 'Create Note', href: '' }
]

const form = useForm({
    title: '',
    category: '',
    description: '',
    tags: [],
    content: {
        overview: '',
        requirements: [''],
        steps: [{
            title: '',
            description: '',
            commands: ['']
        }]
    },
    status: 'draft',
    is_featured: false,
    published_at: null
})

const newTag = ref('')
const newCategory = ref('')

const addTag = () => {
    const tag = newTag.value.trim()
    if (tag && !form.tags.includes(tag)) {
        form.tags.push(tag)
        newTag.value = ''
    }
}

const removeTag = (index) => {
    form.tags.splice(index, 1)
}

const addRequirement = () => {
    form.content.requirements.push('')
}

const removeRequirement = (index) => {
    if (form.content.requirements.length > 1) {
        form.content.requirements.splice(index, 1)
    }
}

const addStep = () => {
    form.content.steps.push({
        title: '',
        description: '',
        commands: ['']
    })
}

const removeStep = (index) => {
    if (form.content.steps.length > 1) {
        form.content.steps.splice(index, 1)
    }
}

const addCommand = (stepIndex) => {
    form.content.steps[stepIndex].commands.push('')
}

const removeCommand = (stepIndex, commandIndex) => {
    if (form.content.steps[stepIndex].commands.length > 1) {
        form.content.steps[stepIndex].commands.splice(commandIndex, 1)
    }
}

const submit = () => {
    // Clean up empty values
    form.tags = form.tags.filter(tag => tag.trim())
    form.content.requirements = form.content.requirements.filter(req => req.trim())

    form.content.steps = form.content.steps.map(step => ({
        ...step,
        commands: step.commands.filter(cmd => cmd.trim())
    })).filter(step => step.title.trim() && step.description.trim() && step.commands.length > 0)

    form.post(route('notes.store'))
}

const availableCategories = computed(() => {
    const existing = props.categories || []
    if (newCategory.value && !existing.includes(newCategory.value)) {
        return [...existing, newCategory.value]
    }
    return existing
})

const selectCategory = (category) => {
    form.category = category
    newCategory.value = ''
}
</script>

<template>
    <AppLayout>

        <Head title="Create Note" />

        <div class="space-y-6 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <Heading title="Create Note" description="Create a new programming note or tutorial"
                    :breadcrumbs="breadcrumbs" />
                <Button @click="router.visit(route('notes.index'))" variant="outline">
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Back to Notes
                </Button>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Information -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Book class="h-5 w-5" />
                            Basic Information
                        </CardTitle>
                        <CardDescription>
                            Basic details about your note
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Title -->
                        <div>
                            <Label for="title">Title *</Label>
                            <Input id="title" v-model="form.title" placeholder="e.g., How to Install Laravel"
                                :class="{ 'border-red-500': form.errors.title }" />
                            <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
                        </div>

                        <!-- Category -->
                        <div>
                            <Label for="category">Category *</Label>
                            <div class="space-y-2">
                                <Input v-if="availableCategories.length === 0 || newCategory" v-model="newCategory"
                                    placeholder="Enter new category"
                                    @keyup.enter="form.category = newCategory; newCategory = ''" />
                                <div v-if="availableCategories.length > 0" class="flex flex-wrap gap-2">
                                    <Button v-for="category in availableCategories" :key="category"
                                        @click="selectCategory(category)" type="button"
                                        :variant="form.category === category ? 'default' : 'outline'" size="sm">
                                        {{ category }}
                                    </Button>
                                    <Button @click="newCategory = ''" type="button" variant="outline" size="sm"
                                        v-if="!newCategory">
                                        <Plus class="h-3 w-3 mr-1" />
                                        New
                                    </Button>
                                </div>
                                <Input v-model="form.category" placeholder="Selected category" readonly
                                    :class="{ 'border-red-500': form.errors.category }" />
                            </div>
                            <p v-if="form.errors.category" class="text-red-500 text-sm mt-1">{{ form.errors.category }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div>
                            <Label for="description">Description *</Label>
                            <Textarea id="description" v-model="form.description"
                                placeholder="Brief description of what this note covers" rows="3"
                                :class="{ 'border-red-500': form.errors.description }" />
                            <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{
                                form.errors.description }}</p>
                        </div>

                        <!-- Tags -->
                        <div>
                            <Label>Tags</Label>
                            <div class="space-y-2">
                                <div class="flex gap-2">
                                    <Input v-model="newTag" placeholder="Add a tag" @keyup.enter="addTag" />
                                    <Button @click="addTag" type="button" variant="outline">
                                        <Plus class="h-4 w-4" />
                                    </Button>
                                </div>
                                <div v-if="form.tags.length > 0" class="flex flex-wrap gap-2">
                                    <Badge v-for="(tag, index) in form.tags" :key="index" variant="secondary"
                                        class="flex items-center gap-1">
                                        {{ tag }}
                                        <button @click="removeTag(index)" type="button" class="ml-1 hover:text-red-500">
                                            <Trash2 class="h-3 w-3" />
                                        </button>
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Content -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <FileText class="h-5 w-5" />
                            Content
                        </CardTitle>
                        <CardDescription>
                            Main content of your note
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <!-- Overview -->
                        <div>
                            <Label for="overview">Overview *</Label>
                            <Textarea id="overview" v-model="form.content.overview"
                                placeholder="Provide an overview of what this note covers" rows="4"
                                :class="{ 'border-red-500': form.errors['content.overview'] }" />
                            <p v-if="form.errors['content.overview']" class="text-red-500 text-sm mt-1">{{
                                form.errors['content.overview'] }}</p>
                        </div>

                        <!-- Requirements -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <Label>Requirements *</Label>
                                <Button @click="addRequirement" type="button" variant="outline" size="sm">
                                    <Plus class="h-3 w-3 mr-1" />
                                    Add Requirement
                                </Button>
                            </div>
                            <div class="space-y-2">
                                <div v-for="(requirement, index) in form.content.requirements" :key="index"
                                    class="flex gap-2">
                                    <Input v-model="form.content.requirements[index]" placeholder="e.g., PHP >= 8.1"
                                        :class="{ 'border-red-500': form.errors[`content.requirements.${index}`] }" />
                                    <Button @click="removeRequirement(index)" type="button" variant="outline" size="sm"
                                        class="px-2" :disabled="form.content.requirements.length === 1">
                                        <Trash2 class="h-3 w-3" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <!-- Steps -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <Label>Installation Steps *</Label>
                                <Button @click="addStep" type="button" variant="outline" size="sm">
                                    <Plus class="h-3 w-3 mr-1" />
                                    Add Step
                                </Button>
                            </div>
                            <div class="space-y-6">
                                <Card v-for="(step, stepIndex) in form.content.steps" :key="stepIndex" class="relative">
                                    <CardHeader class="pb-3">
                                        <div class="flex items-center justify-between">
                                            <CardTitle class="text-base">Step {{ stepIndex + 1 }}</CardTitle>
                                            <Button @click="removeStep(stepIndex)" type="button" variant="outline"
                                                size="sm" class="text-red-600 hover:text-red-700"
                                                :disabled="form.content.steps.length === 1">
                                                <Trash2 class="h-3 w-3" />
                                            </Button>
                                        </div>
                                    </CardHeader>
                                    <CardContent class="space-y-4">
                                        <!-- Step Title -->
                                        <div>
                                            <Label>Step Title *</Label>
                                            <Input v-model="step.title" placeholder="e.g., Install Composer"
                                                :class="{ 'border-red-500': form.errors[`content.steps.${stepIndex}.title`] }" />
                                        </div>

                                        <!-- Step Description -->
                                        <div>
                                            <Label>Step Description *</Label>
                                            <Textarea v-model="step.description"
                                                placeholder="Describe what this step does" rows="2"
                                                :class="{ 'border-red-500': form.errors[`content.steps.${stepIndex}.description`] }" />
                                        </div>

                                        <!-- Commands -->
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <Label>Commands *</Label>
                                                <Button @click="addCommand(stepIndex)" type="button" variant="outline"
                                                    size="sm">
                                                    <Plus class="h-3 w-3 mr-1" />
                                                    Add Command
                                                </Button>
                                            </div>
                                            <div class="space-y-2">
                                                <div v-for="(command, commandIndex) in step.commands"
                                                    :key="commandIndex" class="flex gap-2">
                                                    <Input v-model="step.commands[commandIndex]"
                                                        placeholder="e.g., composer install" class="font-mono text-sm"
                                                        :class="{ 'border-red-500': form.errors[`content.steps.${stepIndex}.commands.${commandIndex}`] }" />
                                                    <Button @click="removeCommand(stepIndex, commandIndex)"
                                                        type="button" variant="outline" size="sm" class="px-2"
                                                        :disabled="step.commands.length === 1">
                                                        <Trash2 class="h-3 w-3" />
                                                    </Button>
                                                </div>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Settings -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Settings class="h-5 w-5" />
                            Settings
                        </CardTitle>
                        <CardDescription>
                            Publication and visibility settings
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Status -->
                        <div>
                            <Label for="status">Status *</Label>
                            <select id="status" v-model="form.status"
                                class="w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.status }">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                            <p v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</p>
                        </div>

                        <!-- Featured -->
                        <div class="flex items-center space-x-2">
                            <input id="is_featured" v-model="form.is_featured" type="checkbox"
                                class="rounded border-gray-300 text-primary focus:ring-primary" />
                            <Label for="is_featured" class="flex items-center gap-2">
                                <Star class="h-4 w-4" />
                                Featured Note
                            </Label>
                        </div>

                        <!-- Published At -->
                        <div v-if="form.status === 'published'">
                            <Label for="published_at">Publish Date</Label>
                            <Input id="published_at" v-model="form.published_at" type="datetime-local"
                                :class="{ 'border-red-500': form.errors.published_at }" />
                            <p class="text-sm text-muted-foreground mt-1">Leave empty to publish immediately</p>
                            <p v-if="form.errors.published_at" class="text-red-500 text-sm mt-1">{{
                                form.errors.published_at }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Submit -->
                <div class="flex items-center gap-3 pt-6">
                    <Button type="submit" :disabled="form.processing">
                        <Save class="h-4 w-4 mr-2" />
                        {{ form.processing ? 'Creating...' : 'Create Note' }}
                    </Button>
                    <Button @click="router.visit(route('notes.index'))" type="button" variant="outline">
                        Cancel
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>