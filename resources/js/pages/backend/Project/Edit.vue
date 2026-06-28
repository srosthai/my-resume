<script setup>
import Icon from '@/components/Icon.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
    projectTypes: {
        type: Array,
        required: true,
    },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Projects', href: '/projects' },
    { title: 'Edit', href: `/backend/projects/${props.project.id}/edit` },
];

const form = useForm({
    title: props.project.title || '',
    description: props.project.description || '',
    image: null,
    project_type_id: props.project.project_type_id ? props.project.project_type_id.toString() : null,
    technologies: props.project.technologies || [],
    created_date: props.project.created_date || '',
    status: props.project.status || 'processing',
    links: props.project.links || [],
});

const technologiesString = ref(Array.isArray(props.project.technologies) ? props.project.technologies.join(', ') : '');

// Convert existing links from array of objects to editable format
const initializeLinks = () => {
    if (Array.isArray(props.project.links) && props.project.links.length > 0) {
        return props.project.links.map((linkObj) => {
            const key = Object.keys(linkObj)[0];
            const value = linkObj[key];
            return { label: key, url: value };
        });
    }
    return [
        { label: 'Github', url: '' },
        { label: 'View', url: '' },
    ];
};

const links = ref(initializeLinks());

const addLink = () => {
    links.value.push({ label: '', url: '' });
};

const removeLink = (index) => {
    if (links.value.length > 1) {
        links.value.splice(index, 1);
    }
};

const submit = () => {
    // Parse technologies from string
    form.technologies = technologiesString.value ? technologiesString.value.split(',').map((tech) => tech.trim()) : [];

    // Convert links to the desired JSON structure
    form.links = links.value
        .filter((link) => link.label.trim() && link.url.trim())
        .map((link) => ({ [link.label.trim()]: link.url.trim() }));

    if (form.project_type_id === null) {
        form.project_type_id = '';
    }

    form.post(route('backend.projects.update', props.project.id), {
        _method: 'PUT',
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Edit Project" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-4xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex items-center gap-4">
                <Link href="/projects">
                    <Button variant="outline" size="icon" class="rounded-xl">
                        <Icon name="arrowLeft" class="size-4" />
                    </Button>
                </Link>
                <div class="flex items-center gap-4">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                        <Icon name="folderKanban" class="size-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">Edit Project</h1>
                        <p class="text-sm text-muted-foreground">Update project information</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="rounded-2xl border bg-card shadow-sm">
                    <div class="space-y-6 p-6 sm:p-8">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
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

                            <div class="space-y-2">
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

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
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

                            <div class="space-y-2">
                                <Label for="created_date">Created Date</Label>
                                <Input
                                    id="created_date"
                                    v-model="form.created_date"
                                    type="date"
                                />
                                <InputError :message="form.errors.created_date" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="image">Project Image</Label>
                            <div v-if="project.image" class="mb-1">
                                <p class="mb-2 text-sm text-muted-foreground">Current image:</p>
                                <img
                                    :src="`/${project.image}`"
                                    :alt="project.title"
                                    class="size-32 rounded-xl border object-cover"
                                />
                            </div>
                            <Input
                                id="image"
                                type="file"
                                accept="image/*"
                                @change="form.image = $event.target.files[0]"
                            />
                            <p class="text-sm text-muted-foreground">
                                Upload a new image to replace the current one (JPEG, PNG, JPG, GIF - max 2MB)
                            </p>
                            <InputError :message="form.errors.image" />
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Enter project description"
                                :rows="4"
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="space-y-2">
                            <Label for="technologies">Technologies</Label>
                            <Input
                                id="technologies"
                                v-model="technologiesString"
                                type="text"
                                placeholder="Enter technologies (comma-separated)"
                            />
                            <p class="text-sm text-muted-foreground">
                                Enter technologies separated by commas (e.g., Vue.js, Laravel, MySQL)
                            </p>
                            <InputError :message="form.errors.technologies" />
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <Label>Project Links</Label>
                                <Button type="button" @click="addLink" variant="outline" size="sm" class="rounded-lg">
                                    <Icon name="plus" class="size-4" />
                                    Add Link
                                </Button>
                            </div>
                            <div class="space-y-3">
                                <div
                                    v-for="(link, index) in links"
                                    :key="index"
                                    class="flex items-end gap-3"
                                >
                                    <div class="flex-1 space-y-2">
                                        <Label :for="`link-label-${index}`">Label</Label>
                                        <Input
                                            :id="`link-label-${index}`"
                                            v-model="link.label"
                                            type="text"
                                            placeholder="e.g., Github, View, Demo"
                                        />
                                    </div>
                                    <div class="flex-[2] space-y-2">
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
                                        variant="ghost"
                                        size="icon"
                                        class="rounded-lg text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                        :disabled="links.length === 1"
                                    >
                                        <Icon name="trash2" class="size-4" />
                                    </Button>
                                </div>
                            </div>
                            <p class="text-sm text-muted-foreground">
                                Add project links like Github repository, live demo, etc.
                            </p>
                            <InputError :message="form.errors.links" />
                        </div>
                    </div>

                    <!-- Footer actions -->
                    <div class="flex items-center justify-end gap-3 border-t bg-muted/30 px-6 py-4 sm:px-8">
                        <Link href="/projects">
                            <Button type="button" variant="outline" class="rounded-xl">Cancel</Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing" class="rounded-xl shadow-sm">
                            <Icon v-if="form.processing" name="loaderCircle" class="size-4 animate-spin" />
                            {{ form.processing ? 'Updating...' : 'Update Project' }}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
