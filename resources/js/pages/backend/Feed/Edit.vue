<script setup>
import Icon from '@/components/Icon.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    feed: {
        type: Object,
        required: true,
    },
    activityTypes: {
        type: Array,
        default: () => [],
    },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Feeds', href: '/feeds-management' },
    { title: 'Edit Feed', href: '' },
];

const form = useForm({
    title: props.feed.title || '',
    body: props.feed.body || '',
    images: [],
    existing_images: props.feed.images || [],
    location: props.feed.location || '',
    mood: props.feed.mood || '',
    activity_type: props.feed.activity_type || '',
    tags: props.feed.tags || [],
    visibility: props.feed.visibility || 'public',
    status: props.feed.status || 'draft',
    is_pinned: props.feed.is_pinned || false,
    likes_count: props.feed.likes_count || 0,
    published_at: props.feed.published_at ? new Date(props.feed.published_at).toISOString().slice(0, 16) : null,
});

const newTag = ref('');
const newActivityType = ref('');
const newImagePreviews = ref([]);

const moods = [
    { value: 'happy', label: 'Happy', emoji: '😊' },
    { value: 'excited', label: 'Excited', emoji: '🎉' },
    { value: 'relaxed', label: 'Relaxed', emoji: '😌' },
    { value: 'grateful', label: 'Grateful', emoji: '🙏' },
    { value: 'adventurous', label: 'Adventurous', emoji: '🏔️' },
    { value: 'thoughtful', label: 'Thoughtful', emoji: '💭' },
    { value: 'creative', label: 'Creative', emoji: '🎨' },
    { value: 'energetic', label: 'Energetic', emoji: '⚡' },
];

const defaultActivityTypes = ['hangout', 'travel', 'food', 'lifestyle', 'work', 'event'];

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

const handleImageUpload = (event) => {
    const files = Array.from(event.target.files);
    const currentCount = form.existing_images.length + form.images.length;

    if (currentCount + files.length > 10) {
        alert('Maximum 10 images allowed');
        return;
    }

    files.forEach((file) => {
        form.images.push(file);
        const reader = new FileReader();
        reader.onload = (e) => {
            newImagePreviews.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });

    event.target.value = '';
};

const removeExistingImage = (index) => {
    form.existing_images.splice(index, 1);
};

const removeNewImage = (index) => {
    form.images.splice(index, 1);
    newImagePreviews.value.splice(index, 1);
};

const submit = () => {
    form.tags = form.tags.filter((tag) => tag.trim());

    form.post(route('backend.feeds.update', props.feed.id), {
        forceFormData: true,
    });
};

const availableActivityTypes = computed(() => {
    const existing = [...new Set([...defaultActivityTypes, ...props.activityTypes])];
    if (newActivityType.value && !existing.includes(newActivityType.value)) {
        return [...existing, newActivityType.value];
    }
    return existing;
});

const selectActivityType = (type) => {
    form.activity_type = type;
    newActivityType.value = '';
};
</script>

<template>
    <Head title="Edit Feed" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex items-center gap-4">
                <Link :href="route('feeds.index')">
                    <Button variant="outline" size="icon" class="rounded-xl">
                        <Icon name="arrowLeft" class="size-4" />
                    </Button>
                </Link>
                <div class="flex items-center gap-4">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                        <Icon name="rss" class="size-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">Edit Feed</h1>
                        <p class="text-sm text-muted-foreground">Editing: {{ feed.title || 'Feed post' }}</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="rounded-2xl border bg-card shadow-sm">
                    <div class="space-y-6 p-6 sm:p-8">
                        <!-- Title -->
                        <div class="space-y-2">
                            <Label for="title">Title (optional)</Label>
                            <Input id="title" v-model="form.title" placeholder="Give your post a title..." />
                            <InputError :message="form.errors.title" />
                        </div>

                        <!-- Body -->
                        <div class="space-y-2">
                            <Label for="body">Content *</Label>
                            <Textarea id="body" v-model="form.body" placeholder="Share what's happening in your life..." :rows="6" />
                            <InputError :message="form.errors.body" />
                        </div>

                        <!-- Activity Type -->
                        <div class="space-y-2">
                            <Label>Activity Type</Label>
                            <Input
                                v-model="newActivityType"
                                placeholder="Enter new activity type"
                                @keyup.enter="form.activity_type = newActivityType; newActivityType = ''"
                            />
                            <div v-if="availableActivityTypes.length > 0" class="flex flex-wrap gap-2">
                                <Button
                                    v-for="type in availableActivityTypes"
                                    :key="type"
                                    type="button"
                                    :variant="form.activity_type === type ? 'default' : 'outline'"
                                    size="sm"
                                    class="capitalize"
                                    @click="selectActivityType(type)"
                                >
                                    {{ type }}
                                </Button>
                            </div>
                        </div>

                        <!-- Mood -->
                        <div class="space-y-2">
                            <Label>Mood</Label>
                            <div class="flex flex-wrap gap-2">
                                <Button
                                    v-for="mood in moods"
                                    :key="mood.value"
                                    type="button"
                                    :variant="form.mood === mood.value ? 'default' : 'outline'"
                                    size="sm"
                                    @click="form.mood = form.mood === mood.value ? '' : mood.value"
                                >
                                    {{ mood.emoji }} {{ mood.label }}
                                </Button>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="space-y-2">
                            <Label for="location">
                                <span class="flex items-center gap-2">
                                    <Icon name="mapPin" class="size-4" />
                                    Location
                                </span>
                            </Label>
                            <Input id="location" v-model="form.location" placeholder="e.g., Phnom Penh, Cambodia" />
                            <InputError :message="form.errors.location" />
                        </div>

                        <!-- Media -->
                        <div class="space-y-2">
                            <Label>Media</Label>
                            <p class="text-sm text-muted-foreground">Upload photos for your post (max 10 images, 5MB each)</p>

                            <!-- Existing Images -->
                            <div v-if="form.existing_images.length > 0">
                                <Label class="mb-2 block">Current Images</Label>
                                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                                    <div v-for="(image, index) in form.existing_images" :key="'existing-' + index" class="group relative">
                                        <img :src="'/' + image" class="h-32 w-full rounded-lg object-cover" />
                                        <Button
                                            type="button"
                                            variant="destructive"
                                            size="sm"
                                            class="absolute top-1 right-1 size-6 p-0 opacity-0 transition-opacity group-hover:opacity-100"
                                            @click="removeExistingImage(index)"
                                        >
                                            <Icon name="x" class="size-3" />
                                        </Button>
                                    </div>
                                </div>
                            </div>

                            <!-- New Image Upload -->
                            <div>
                                <Label class="mb-2 block">Add New Images</Label>
                                <input
                                    type="file"
                                    accept="image/*"
                                    multiple
                                    class="w-full rounded-md border border-border px-3 py-2 focus:border-transparent focus:ring-2 focus:ring-primary focus:outline-none"
                                    @change="handleImageUpload"
                                />
                                <InputError :message="form.errors.images" />
                            </div>

                            <!-- New Image Previews -->
                            <div v-if="newImagePreviews.length > 0" class="grid grid-cols-2 gap-4 md:grid-cols-4">
                                <div v-for="(preview, index) in newImagePreviews" :key="'new-' + index" class="group relative">
                                    <img :src="preview" class="h-32 w-full rounded-lg object-cover" />
                                    <Button
                                        type="button"
                                        variant="destructive"
                                        size="sm"
                                        class="absolute top-1 right-1 size-6 p-0 opacity-0 transition-opacity group-hover:opacity-100"
                                        @click="removeNewImage(index)"
                                    >
                                        <Icon name="x" class="size-3" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="space-y-2">
                            <Label>Tags</Label>
                            <p class="text-sm text-muted-foreground">Add tags to help categorize your post</p>
                            <div class="flex gap-2">
                                <Input v-model="newTag" placeholder="Add a tag" @keyup.enter="addTag" />
                                <Button type="button" variant="outline" @click="addTag">
                                    <Icon name="plus" class="size-4" />
                                </Button>
                            </div>
                            <div v-if="form.tags.length > 0" class="flex flex-wrap gap-2">
                                <Badge v-for="(tag, index) in form.tags" :key="index" variant="secondary" class="flex items-center gap-1">
                                    {{ tag }}
                                    <button type="button" class="ml-1 hover:text-destructive" @click="removeTag(index)">
                                        <Icon name="trash2" class="size-3" />
                                    </button>
                                </Badge>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Status -->
                            <div class="space-y-2">
                                <Label for="status">Status *</Label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="w-full rounded-md border border-border px-3 py-2 focus:border-transparent focus:ring-2 focus:ring-primary focus:outline-none"
                                >
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                                <InputError :message="form.errors.status" />
                            </div>

                            <!-- Visibility -->
                            <div class="space-y-2">
                                <Label for="visibility">Visibility *</Label>
                                <select
                                    id="visibility"
                                    v-model="form.visibility"
                                    class="w-full rounded-md border border-border px-3 py-2 focus:border-transparent focus:ring-2 focus:ring-primary focus:outline-none"
                                >
                                    <option value="public">Public</option>
                                    <option value="private">Private</option>
                                </select>
                                <InputError :message="form.errors.visibility" />
                            </div>

                            <!-- Likes Count -->
                            <div class="space-y-2">
                                <Label for="likes_count">Likes Count</Label>
                                <Input id="likes_count" v-model.number="form.likes_count" type="number" min="0" />
                                <InputError :message="form.errors.likes_count" />
                            </div>

                            <!-- Published At -->
                            <div v-if="form.status === 'published'" class="space-y-2">
                                <Label for="published_at">Publish Date</Label>
                                <Input id="published_at" v-model="form.published_at" type="datetime-local" />
                                <p class="text-sm text-muted-foreground">Leave empty to publish immediately</p>
                                <InputError :message="form.errors.published_at" />
                            </div>
                        </div>

                        <!-- Pinned -->
                        <div class="flex items-center space-x-2">
                            <input id="is_pinned" v-model="form.is_pinned" type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary" />
                            <Label for="is_pinned" class="flex items-center gap-2">
                                <Icon name="pin" class="size-4" />
                                Pin to top
                            </Label>
                        </div>
                    </div>

                    <!-- Footer actions -->
                    <div class="flex items-center justify-end gap-3 border-t bg-muted/30 px-6 py-4 sm:px-8">
                        <Link :href="route('feeds.index')">
                            <Button type="button" variant="outline" class="rounded-xl">Cancel</Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing" class="rounded-xl shadow-sm">
                            <Icon v-if="form.processing" name="loaderCircle" class="size-4 animate-spin" />
                            {{ form.processing ? 'Updating...' : 'Update Feed' }}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
