<script setup lang="ts">
import FormToast from '@/components/FormToast.vue';
import Icon from '@/components/Icon.vue';
import InputError from '@/components/InputError.vue';
import SearchSelect from '@/components/SearchSelect.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { fieldError, validationMessages } from '@/lib/formErrors';
import type { Project, ProjectLink, ProjectStatus, ProjectType } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    project: Project;
    projectTypes: ProjectType[];
}>();

const projectTypeOptions = computed(() => props.projectTypes.map((type) => ({ value: type.id.toString(), label: type.name || 'Untitled type' })));

const statusOptions = [
    { value: 'processing' as const, label: 'Processing' },
    { value: 'completed' as const, label: 'Completed' },
];

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Projects', href: route('backend.projects.index') },
    { title: 'Edit', href: route('backend.projects.edit', props.project.id) },
];

const form = useForm({
    title: props.project.title || '',
    description: props.project.description || '',
    image: null as File | null,
    remove_image: false,
    gallery: [] as File[],
    existing_gallery: [...(props.project.gallery ?? [])] as string[],
    project_type_id: (props.project.project_type_id ? props.project.project_type_id.toString() : null) as string | null,
    technologies: (props.project.technologies || []) as string[],
    created_date: props.project.created_date || '',
    status: (props.project.status || 'processing') as ProjectStatus,
    links: (props.project.links || []) as ProjectLink[],
});

const technologiesString = ref(Array.isArray(props.project.technologies) ? props.project.technologies.join(', ') : '');
const galleryPreviews = ref<string[]>([]);
const galleryNotice = ref('');
const galleryLimit = 12;
const toast = ref<string[] | null>(null);

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

const handleImageChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    form.image = input.files?.[0] ?? null;
};

const handleGalleryChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const files = Array.from(input.files ?? []);
    galleryNotice.value = '';

    if (form.existing_gallery.length + form.gallery.length + files.length > galleryLimit) {
        galleryNotice.value = 'You can add up to 12 gallery images.';
        toast.value = [galleryNotice.value];
        input.value = '';
        return;
    }

    files.forEach((file) => {
        const index = form.gallery.length;
        form.gallery.push(file);
        const reader = new FileReader();
        reader.onload = () => {
            if (typeof reader.result === 'string') {
                galleryPreviews.value[index] = reader.result;
            }
        };
        reader.readAsDataURL(file);
    });

    input.value = '';
};

const removeExistingGalleryImage = (index: number) => {
    form.existing_gallery.splice(index, 1);
};

const removeGalleryImage = (index: number) => {
    form.gallery.splice(index, 1);
    galleryPreviews.value.splice(index, 1);
};

const addLink = () => {
    links.value.push({ label: '', url: '' });
};

const removeLink = (index: number) => {
    if (links.value.length > 1) {
        links.value.splice(index, 1);
    }
};

const submit = () => {
    // Parse technologies from string
    form.technologies = technologiesString.value ? technologiesString.value.split(',').map((tech) => tech.trim()) : [];

    // Convert links to the desired JSON structure
    form.links = links.value.filter((link) => link.label.trim() && link.url.trim()).map((link) => ({ [link.label.trim()]: link.url.trim() }));

    if (form.project_type_id === null) {
        form.project_type_id = '';
    }

    // Browsers cannot send multipart PUT, so spoof the method on a POST.
    form.transform((data) => ({
        ...data,
        _method: 'put',
        existing_gallery: data.existing_gallery.length > 0 ? data.existing_gallery : [''],
    })).post(route('backend.projects.update', props.project.id), {
        forceFormData: true,
        onError: (errors) => {
            toast.value = validationMessages(errors);
            const field = Object.keys(errors)[0]?.split('.')[0];
            // Saved paths validate as existing_gallery; the control on the page is the gallery input.
            const targetId = field === 'existing_gallery' ? 'gallery' : field;
            if (targetId) {
                document.getElementById(targetId)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        },
    });
};
</script>

<template>
    <Head title="Edit Project" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormToast :messages="toast" tone="error" @close="toast = null" />
        <div class="mx-auto w-full max-w-4xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex items-center gap-4">
                <Link :href="route('backend.projects.index')">
                    <Button variant="outline" size="icon" class="rounded-xl" aria-label="Back">
                        <Icon name="arrowLeft" class="size-4" />
                    </Button>
                </Link>
                <div class="flex items-center gap-4">
                    <div
                        class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm"
                    >
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
                                    :aria-invalid="fieldError(form.errors, 'title') ? true : undefined"
                                />
                                <InputError :message="fieldError(form.errors, 'title')" />
                            </div>

                            <div class="space-y-2">
                                <Label for="project_type_id">Project Type</Label>
                                <SearchSelect
                                    id="project_type_id"
                                    v-model="form.project_type_id"
                                    :options="projectTypeOptions"
                                    placeholder="Select project type"
                                    search-placeholder="Search project types"
                                    clearable
                                    :invalid="!!fieldError(form.errors, 'project_type_id')"
                                />
                                <InputError :message="fieldError(form.errors, 'project_type_id')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="status">Status *</Label>
                                <SearchSelect
                                    id="status"
                                    v-model="form.status"
                                    :options="statusOptions"
                                    placeholder="Select status"
                                    search-placeholder="Search statuses"
                                    :invalid="!!fieldError(form.errors, 'status')"
                                />
                                <InputError :message="fieldError(form.errors, 'status')" />
                            </div>

                            <div class="space-y-2">
                                <Label for="created_date">Created Date</Label>
                                <Input
                                    id="created_date"
                                    v-model="form.created_date"
                                    type="date"
                                    :aria-invalid="fieldError(form.errors, 'created_date') ? true : undefined"
                                />
                                <InputError :message="fieldError(form.errors, 'created_date')" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="image">Project Image</Label>
                            <div v-if="project.image" class="mb-1">
                                <p class="mb-2 text-sm text-muted-foreground">Current image:</p>
                                <img
                                    :src="`/${project.image}`"
                                    :alt="project.title ?? undefined"
                                    class="size-32 rounded-xl border object-cover"
                                    :class="{ 'opacity-40 grayscale': form.remove_image }"
                                    width="128"
                                    height="128"
                                    loading="lazy"
                                    decoding="async"
                                />
                                <label class="mt-2 flex items-center gap-2 text-sm text-muted-foreground">
                                    <input v-model="form.remove_image" type="checkbox" class="size-4 rounded border" />
                                    Remove current image
                                </label>
                            </div>
                            <Input id="image" type="file" accept="image/*" @change="handleImageChange" />
                            <p class="text-sm text-muted-foreground">Upload a new image to replace the current one (JPEG, PNG, JPG, GIF - max 2MB)</p>
                            <InputError :message="fieldError(form.errors, 'image')" />
                        </div>

                        <div class="space-y-2">
                            <Label for="gallery">Gallery</Label>
                            <p class="text-sm text-muted-foreground">Extra screenshots shown on the project page. Up to 12 images, 5MB each.</p>
                            <div v-if="form.existing_gallery.length" class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                <div v-for="(image, index) in form.existing_gallery" :key="image" class="group relative">
                                    <img
                                        :src="`/${image}`"
                                        :alt="`Gallery image ${index + 1}`"
                                        class="h-24 w-full rounded-xl border object-cover"
                                        width="160"
                                        height="96"
                                        loading="lazy"
                                        decoding="async"
                                    />
                                    <Button
                                        type="button"
                                        variant="destructive"
                                        size="sm"
                                        class="absolute top-1 right-1 size-6 p-0"
                                        :aria-label="`Remove gallery image ${index + 1}`"
                                        @click="removeExistingGalleryImage(index)"
                                    >
                                        <Icon name="x" class="size-3" />
                                    </Button>
                                </div>
                            </div>
                            <Input
                                id="gallery"
                                type="file"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                                multiple
                                @change="handleGalleryChange"
                            />
                            <InputError
                                :message="galleryNotice || fieldError(form.errors, 'gallery') || fieldError(form.errors, 'existing_gallery')"
                            />
                            <div v-if="galleryPreviews.length" class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                <div v-for="(preview, index) in galleryPreviews" :key="preview || index" class="group relative">
                                    <img
                                        :src="preview"
                                        :alt="`New gallery image ${index + 1}`"
                                        class="h-24 w-full rounded-xl border object-cover"
                                        width="160"
                                        height="96"
                                    />
                                    <Button
                                        type="button"
                                        variant="destructive"
                                        size="sm"
                                        class="absolute top-1 right-1 size-6 p-0"
                                        :aria-label="`Remove new gallery image ${index + 1}`"
                                        @click="removeGalleryImage(index)"
                                    >
                                        <Icon name="x" class="size-3" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Textarea id="description" v-model="form.description" placeholder="Enter project description" :rows="4" />
                            <InputError :message="fieldError(form.errors, 'description')" />
                        </div>

                        <div class="space-y-2">
                            <Label for="technologies">Technologies</Label>
                            <Input id="technologies" v-model="technologiesString" type="text" placeholder="Enter technologies (comma-separated)" />
                            <p class="text-sm text-muted-foreground">Enter technologies separated by commas (e.g., Vue.js, Laravel, MySQL)</p>
                            <InputError :message="fieldError(form.errors, 'technologies')" />
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
                                <div v-for="(link, index) in links" :key="index" class="flex items-end gap-3">
                                    <div class="flex-1 space-y-2">
                                        <Label :for="`link-label-${index}`">Label</Label>
                                        <Input :id="`link-label-${index}`" v-model="link.label" type="text" placeholder="e.g., Github, View, Demo" />
                                    </div>
                                    <div class="flex-[2] space-y-2">
                                        <Label :for="`link-url-${index}`">URL</Label>
                                        <Input :id="`link-url-${index}`" v-model="link.url" type="url" placeholder="https://example.com" />
                                    </div>
                                    <Button
                                        type="button"
                                        @click="removeLink(index)"
                                        aria-label="Remove link"
                                        variant="ghost"
                                        size="icon"
                                        class="rounded-lg text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                        :disabled="links.length === 1"
                                    >
                                        <Icon name="trash2" class="size-4" />
                                    </Button>
                                </div>
                            </div>
                            <p class="text-sm text-muted-foreground">Add project links like Github repository, live demo, etc.</p>
                            <InputError :message="fieldError(form.errors, 'links')" />
                        </div>
                    </div>

                    <!-- Footer actions -->
                    <div class="flex items-center justify-end gap-3 border-t bg-muted/30 px-6 py-4 sm:px-8">
                        <Link :href="route('backend.projects.index')">
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
