<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import InputError from '@/components/InputError.vue'

const props = defineProps({
    projectTypes: {
        type: Array,
        required: true
    }
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Projects', href: '/projects' },
    { title: 'Create', href: '/backend/projects/create' }
]

const form = useForm({
    title: '',
    description: '',
    image: null,
    project_type_id: null,
    technologies: [],
    created_date: '',
    status: 'processing',
    links: [],
})

const technologiesString = ref('')
const links = ref([
    { label: 'Github', url: '' },
    { label: 'View', url: '' }
])

const addLink = () => {
    links.value.push({ label: '', url: '' })
}

const removeLink = (index) => {
    if (links.value.length > 1) {
        links.value.splice(index, 1)
    }
}

const submit = () => {
    // Parse technologies from string
    form.technologies = technologiesString.value ? technologiesString.value.split(',').map(tech => tech.trim()) : []
    
    // Convert links to the desired JSON structure
    form.links = links.value
        .filter(link => link.label.trim() && link.url.trim())
        .map(link => ({ [link.label.trim()]: link.url.trim() }))
    
    // Convert null project_type_id to empty string for backend
    if (form.project_type_id === null) {
        form.project_type_id = ''
    }
    
    form.post(route('backend.projects.store'), {
        forceFormData: true,
    })
}
</script>

<template>
    <Head title="Create Project" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div>
                <Heading title="Create Project" />
                <p class="text-sm text-muted-foreground">
                    Add new project information
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Project Information</CardTitle>
                    <CardDescription>
                        Enter the project details below
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <Label for="title">Title *</Label>
                                <Input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    placeholder="Enter project title"
                                    required
                                />
                                <InputError :message="form.errors.title" />
                            </div>

                            <div>
                                <Label for="project_type_id">Project Type</Label>
                                <Select v-model="form.project_type_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select project type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem 
                                            v-for="type in projectTypes" 
                                            :key="type.id" 
                                            :value="type.id.toString()"
                                        >
                                            {{ type.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.project_type_id" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <Label for="status">Status *</Label>
                                <Select v-model="form.status">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="processing">Processing</SelectItem>
                                        <SelectItem value="completed">Completed</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.status" />
                            </div>

                            <div>
                                <Label for="created_date">Created Date</Label>
                                <Input
                                    id="created_date"
                                    v-model="form.created_date"
                                    type="date"
                                />
                                <InputError :message="form.errors.created_date" />
                            </div>
                        </div>

                        <div>
                            <Label for="image">Project Image</Label>
                            <Input
                                id="image"
                                type="file"
                                accept="image/*"
                                @change="form.image = $event.target.files[0]"
                            />
                            <p class="text-sm text-muted-foreground mt-1">
                                Upload an image for the project (JPEG, PNG, JPG, GIF - max 2MB)
                            </p>
                            <InputError :message="form.errors.image" />
                        </div>

                        <div>
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Enter project description"
                                :rows="4"
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div>
                            <Label for="technologies">Technologies</Label>
                            <Input
                                id="technologies"
                                v-model="technologiesString"
                                type="text"
                                placeholder="Enter technologies (comma-separated)"
                            />
                            <p class="text-sm text-muted-foreground mt-1">
                                Enter technologies separated by commas (e.g., Vue.js, Laravel, MySQL)
                            </p>
                            <InputError :message="form.errors.technologies" />
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <Label>Project Links</Label>
                                <Button type="button" @click="addLink" variant="outline" size="sm">
                                    <Icon name="plus" class="h-4 w-4 mr-1" />
                                    Add Link
                                </Button>
                            </div>
                            <div class="space-y-3">
                                <div 
                                    v-for="(link, index) in links" 
                                    :key="index"
                                    class="flex gap-3 items-end"
                                >
                                    <div class="flex-1">
                                        <Label :for="`link-label-${index}`">Label</Label>
                                        <Input
                                            :id="`link-label-${index}`"
                                            v-model="link.label"
                                            type="text"
                                            placeholder="e.g., Github, View, Demo"
                                        />
                                    </div>
                                    <div class="flex-[2]">
                                        <Label :for="`link-url-${index}`">URL</Label>
                                        <Input
                                            :id="`link-url-${index}`"
                                            v-model="link.url"
                                            type="url"
                                            placeholder="https://example.com"
                                        />
                                    </div>
                                    <Button 
                                        type="button" 
                                        @click="removeLink(index)"
                                        variant="destructive" 
                                        size="sm"
                                        :disabled="links.length === 1"
                                    >
                                        <Icon name="trash" class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                            <p class="text-sm text-muted-foreground mt-1">
                                Add project links like Github repository, live demo, etc.
                            </p>
                            <InputError :message="form.errors.links" />
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Creating...' : 'Create Project' }}
                            </Button>
                            <Link href="/projects">
                                <Button type="button" variant="outline">
                                    Cancel
                                </Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>