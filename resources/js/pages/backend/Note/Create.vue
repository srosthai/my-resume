<script setup>
import Icon from '@/components/Icon.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Notes', href: '/notes' },
    { title: 'Create Note', href: '' },
];

const form = useForm({
    title: '',
    category: '',
    description: '',
    tags: [],
    content: {
        overview: '',
        requirements: [''],
        steps: [
            {
                title: '',
                description: '',
                commands: [''],
            },
        ],
    },
    status: 'draft',
    is_featured: false,
    published_at: null,
});

const newTag = ref('');
const newCategory = ref('');

const addTag = () => {
    const tag = newTag.value.trim();
    if (tag && !form.tags.includes(tag)) {
        form.tags.push(tag);
        newTag.value = '';
    }
};

const removeTag = (index) => {
    form.tags.splice(index, 1);
};

const addRequirement = () => {
    form.content.requirements.push('');
};

const removeRequirement = (index) => {
    if (form.content.requirements.length > 1) {
        form.content.requirements.splice(index, 1);
    }
};

const addStep = () => {
    form.content.steps.push({
        title: '',
        description: '',
        commands: [''],
    });
};

const removeStep = (index) => {
    if (form.content.steps.length > 1) {
        form.content.steps.splice(index, 1);
    }
};

const addCommand = (stepIndex) => {
    form.content.steps[stepIndex].commands.push('');
};

const removeCommand = (stepIndex, commandIndex) => {
    if (form.content.steps[stepIndex].commands.length > 1) {
        form.content.steps[stepIndex].commands.splice(commandIndex, 1);
    }
};

const submit = () => {
    // Clean up empty values
    form.tags = form.tags.filter((tag) => tag.trim());
    form.content.requirements = form.content.requirements.filter((req) => req.trim());

    form.content.steps = form.content.steps
        .map((step) => ({
            ...step,
            commands: step.commands.filter((cmd) => cmd.trim()),
        }))
        .filter((step) => step.title.trim() && step.description.trim() && step.commands.length > 0);

    form.post(route('notes.store'));
};

const availableCategories = computed(() => {
    const existing = props.categories || [];
    if (newCategory.value && !existing.includes(newCategory.value)) {
        return [...existing, newCategory.value];
    }
    return existing;
});

const selectCategory = (category) => {
    form.category = category;
    newCategory.value = '';
};
</script>

<template>
    <Head title="Create Note" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex items-center gap-4">
                <Link :href="route('notes.index')">
                    <Button variant="outline" size="icon" class="rounded-xl">
                        <Icon name="arrowLeft" class="size-4" />
                    </Button>
                </Link>
                <div class="flex items-center gap-4">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                        <Icon name="stickyNote" class="size-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">Create Note</h1>
                        <p class="text-sm text-muted-foreground">Create a new programming note or tutorial</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="rounded-2xl border bg-card shadow-sm">
                    <div class="space-y-6 p-6 sm:p-8">
                        <!-- Basic Information -->
                        <div class="space-y-1">
                            <h2 class="flex items-center gap-2 text-base font-semibold tracking-tight">
                                <Icon name="book" class="size-5 text-muted-foreground" />
                                Basic Information
                            </h2>
                            <p class="text-sm text-muted-foreground">Basic details about your note</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="title">Title *</Label>
                            <Input
                                id="title"
                                v-model="form.title"
                                placeholder="e.g., How to Install Laravel"
                                :class="{ 'border-red-500': form.errors.title }"
                            />
                            <p v-if="form.errors.title" class="mt-1 text-sm text-red-500">{{ form.errors.title }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="category">Category *</Label>
                            <div class="space-y-2">
                                <Input
                                    v-model="newCategory"
                                    placeholder="Enter new category"
                                    @keyup.enter="form.category = newCategory; newCategory = ''"
                                />
                                <div v-if="availableCategories.length > 0" class="flex flex-wrap gap-2">
                                    <Button
                                        v-for="category in availableCategories"
                                        :key="category"
                                        type="button"
                                        :variant="form.category === category ? 'default' : 'outline'"
                                        size="sm"
                                        class="rounded-lg"
                                        @click="selectCategory(category)"
                                    >
                                        {{ category }}
                                    </Button>
                                    <Button
                                        v-if="!newCategory"
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        class="rounded-lg"
                                        @click="newCategory = ''"
                                    >
                                        <Icon name="plus" class="size-3" />
                                        New
                                    </Button>
                                </div>
                                <Input
                                    v-model="form.category"
                                    placeholder="Selected category"
                                    readonly
                                    :class="{ 'border-red-500': form.errors.category }"
                                />
                            </div>
                            <p v-if="form.errors.category" class="mt-1 text-sm text-red-500">{{ form.errors.category }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description *</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Brief description of what this note covers"
                                rows="3"
                                :class="{ 'border-red-500': form.errors.description }"
                            />
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{ form.errors.description }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label>Tags</Label>
                            <div class="space-y-2">
                                <div class="flex gap-2">
                                    <Input v-model="newTag" placeholder="Add a tag" @keyup.enter="addTag" />
                                    <Button type="button" variant="outline" class="rounded-xl" @click="addTag">
                                        <Icon name="plus" class="size-4" />
                                    </Button>
                                </div>
                                <div v-if="form.tags.length > 0" class="flex flex-wrap gap-2">
                                    <Badge
                                        v-for="(tag, index) in form.tags"
                                        :key="index"
                                        variant="secondary"
                                        class="flex items-center gap-1 rounded-full font-normal"
                                    >
                                        {{ tag }}
                                        <button type="button" class="ml-1 hover:text-red-500" @click="removeTag(index)">
                                            <Icon name="trash2" class="size-3" />
                                        </button>
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="space-y-6 border-t p-6 sm:p-8">
                        <div class="space-y-1">
                            <h2 class="flex items-center gap-2 text-base font-semibold tracking-tight">
                                <Icon name="fileText" class="size-5 text-muted-foreground" />
                                Content
                            </h2>
                            <p class="text-sm text-muted-foreground">Main content of your note</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="overview">Overview *</Label>
                            <Textarea
                                id="overview"
                                v-model="form.content.overview"
                                placeholder="Provide an overview of what this note covers"
                                rows="4"
                                :class="{ 'border-red-500': form.errors['content.overview'] }"
                            />
                            <p v-if="form.errors['content.overview']" class="mt-1 text-sm text-red-500">{{ form.errors['content.overview'] }}</p>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <Label>Requirements *</Label>
                                <Button type="button" variant="outline" size="sm" class="rounded-lg" @click="addRequirement">
                                    <Icon name="plus" class="size-3" />
                                    Add Requirement
                                </Button>
                            </div>
                            <div class="space-y-2">
                                <div v-for="(requirement, index) in form.content.requirements" :key="index" class="flex gap-2">
                                    <Input
                                        v-model="form.content.requirements[index]"
                                        placeholder="e.g., PHP >= 8.1"
                                        :class="{ 'border-red-500': form.errors[`content.requirements.${index}`] }"
                                    />
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        class="rounded-lg px-2"
                                        :disabled="form.content.requirements.length === 1"
                                        @click="removeRequirement(index)"
                                    >
                                        <Icon name="trash2" class="size-3" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <Label>Installation Steps *</Label>
                                <Button type="button" variant="outline" size="sm" class="rounded-lg" @click="addStep">
                                    <Icon name="plus" class="size-3" />
                                    Add Step
                                </Button>
                            </div>
                            <div class="space-y-4">
                                <div
                                    v-for="(step, stepIndex) in form.content.steps"
                                    :key="stepIndex"
                                    class="space-y-4 rounded-xl border bg-muted/20 p-4"
                                >
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold">Step {{ stepIndex + 1 }}</h3>
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            class="rounded-lg text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                            :disabled="form.content.steps.length === 1"
                                            @click="removeStep(stepIndex)"
                                        >
                                            <Icon name="trash2" class="size-3" />
                                        </Button>
                                    </div>

                                    <div class="space-y-2">
                                        <Label>Step Title *</Label>
                                        <Input
                                            v-model="step.title"
                                            placeholder="e.g., Install Composer"
                                            :class="{ 'border-red-500': form.errors[`content.steps.${stepIndex}.title`] }"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <Label>Step Description *</Label>
                                        <Textarea
                                            v-model="step.description"
                                            placeholder="Describe what this step does"
                                            rows="2"
                                            :class="{ 'border-red-500': form.errors[`content.steps.${stepIndex}.description`] }"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <Label>Commands *</Label>
                                            <Button type="button" variant="outline" size="sm" class="rounded-lg" @click="addCommand(stepIndex)">
                                                <Icon name="plus" class="size-3" />
                                                Add Command
                                            </Button>
                                        </div>
                                        <div class="space-y-2">
                                            <div v-for="(command, commandIndex) in step.commands" :key="commandIndex" class="flex gap-2">
                                                <Input
                                                    v-model="step.commands[commandIndex]"
                                                    placeholder="e.g., composer install"
                                                    class="font-mono text-sm"
                                                    :class="{ 'border-red-500': form.errors[`content.steps.${stepIndex}.commands.${commandIndex}`] }"
                                                />
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    size="sm"
                                                    class="rounded-lg px-2"
                                                    :disabled="step.commands.length === 1"
                                                    @click="removeCommand(stepIndex, commandIndex)"
                                                >
                                                    <Icon name="trash2" class="size-3" />
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="space-y-6 border-t p-6 sm:p-8">
                        <div class="space-y-1">
                            <h2 class="flex items-center gap-2 text-base font-semibold tracking-tight">
                                <Icon name="settings" class="size-5 text-muted-foreground" />
                                Settings
                            </h2>
                            <p class="text-sm text-muted-foreground">Publication and visibility settings</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="status">Status *</Label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full rounded-xl border border-input bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                                :class="{ 'border-red-500': form.errors.status }"
                            >
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-sm text-red-500">{{ form.errors.status }}</p>
                        </div>

                        <div class="flex items-center space-x-2">
                            <input
                                id="is_featured"
                                v-model="form.is_featured"
                                type="checkbox"
                                class="rounded border-gray-300 text-primary focus:ring-primary"
                            />
                            <Label for="is_featured" class="flex items-center gap-2">
                                <Icon name="star" class="size-4" />
                                Featured Note
                            </Label>
                        </div>

                        <div v-if="form.status === 'published'" class="space-y-2">
                            <Label for="published_at">Publish Date</Label>
                            <Input
                                id="published_at"
                                v-model="form.published_at"
                                type="datetime-local"
                                :class="{ 'border-red-500': form.errors.published_at }"
                            />
                            <p class="text-sm text-muted-foreground">Leave empty to publish immediately</p>
                            <p v-if="form.errors.published_at" class="mt-1 text-sm text-red-500">{{ form.errors.published_at }}</p>
                        </div>
                    </div>

                    <!-- Footer actions -->
                    <div class="flex items-center justify-end gap-3 border-t bg-muted/30 px-6 py-4 sm:px-8">
                        <Link :href="route('notes.index')">
                            <Button type="button" variant="outline" class="rounded-xl">Cancel</Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing" class="rounded-xl shadow-sm">
                            <Icon v-if="form.processing" name="loaderCircle" class="size-4 animate-spin" />
                            {{ form.processing ? 'Creating...' : 'Create Note' }}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
