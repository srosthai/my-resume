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
    Rss,
    Tag,
    Settings,
    Pin,
    Calendar,
    MapPin,
    Smile,
    Image,
    X
} from 'lucide-vue-next'

const props = defineProps({
    activityTypes: {
        type: Array,
        default: () => []
    }
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Feeds', href: '/feeds-management' },
    { title: 'Create Feed', href: '' }
]

const form = useForm({
    title: '',
    body: '',
    images: [],
    location: '',
    mood: '',
    activity_type: '',
    tags: [],
    visibility: 'public',
    status: 'draft',
    is_pinned: false,
    likes_count: 0,
    published_at: null
})

const newTag = ref('')
const newActivityType = ref('')
const imagePreviews = ref([])

const moods = [
    { value: 'happy', label: 'Happy', emoji: '😊' },
    { value: 'excited', label: 'Excited', emoji: '🎉' },
    { value: 'relaxed', label: 'Relaxed', emoji: '😌' },
    { value: 'grateful', label: 'Grateful', emoji: '🙏' },
    { value: 'adventurous', label: 'Adventurous', emoji: '🏔️' },
    { value: 'thoughtful', label: 'Thoughtful', emoji: '💭' },
    { value: 'creative', label: 'Creative', emoji: '🎨' },
    { value: 'energetic', label: 'Energetic', emoji: '⚡' }
]

const defaultActivityTypes = ['hangout', 'travel', 'food', 'lifestyle', 'work', 'event']

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

const handleImageUpload = (event) => {
    const files = Array.from(event.target.files)
    const currentCount = form.images.length

    if (currentCount + files.length > 10) {
        alert('Maximum 10 images allowed')
        return
    }

    files.forEach(file => {
        form.images.push(file)
        const reader = new FileReader()
        reader.onload = (e) => {
            imagePreviews.value.push(e.target.result)
        }
        reader.readAsDataURL(file)
    })

    event.target.value = ''
}

const removeImage = (index) => {
    form.images.splice(index, 1)
    imagePreviews.value.splice(index, 1)
}

const submit = () => {
    form.tags = form.tags.filter(tag => tag.trim())

    form.post(route('backend.feeds.store'), {
        forceFormData: true
    })
}

const availableActivityTypes = computed(() => {
    const existing = [...new Set([...defaultActivityTypes, ...props.activityTypes])]
    if (newActivityType.value && !existing.includes(newActivityType.value)) {
        return [...existing, newActivityType.value]
    }
    return existing
})

const selectActivityType = (type) => {
    form.activity_type = type
    newActivityType.value = ''
}
</script>

<template>
    <AppLayout>

        <Head title="Create Feed" />

        <div class="space-y-6 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <Heading title="Create Feed" description="Share a new lifestyle post or update"
                    :breadcrumbs="breadcrumbs" />
                <Button @click="router.visit(route('feeds.index'))" variant="outline">
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Back to Feeds
                </Button>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Information -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Rss class="h-5 w-5" />
                            Basic Information
                        </CardTitle>
                        <CardDescription>
                            What's on your mind?
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Title -->
                        <div>
                            <Label for="title">Title (optional)</Label>
                            <Input id="title" v-model="form.title" placeholder="Give your post a title..."
                                :class="{ 'border-red-500': form.errors.title }" />
                            <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
                        </div>

                        <!-- Body -->
                        <div>
                            <Label for="body">Content *</Label>
                            <Textarea id="body" v-model="form.body"
                                placeholder="Share what's happening in your life..." rows="6"
                                :class="{ 'border-red-500': form.errors.body }" />
                            <p v-if="form.errors.body" class="text-red-500 text-sm mt-1">{{ form.errors.body }}</p>
                        </div>

                        <!-- Activity Type -->
                        <div>
                            <Label>Activity Type</Label>
                            <div class="space-y-2">
                                <Input v-model="newActivityType" placeholder="Enter new activity type"
                                    @keyup.enter="form.activity_type = newActivityType; newActivityType = ''" />
                                <div v-if="availableActivityTypes.length > 0" class="flex flex-wrap gap-2">
                                    <Button v-for="type in availableActivityTypes" :key="type"
                                        @click="selectActivityType(type)" type="button"
                                        :variant="form.activity_type === type ? 'default' : 'outline'" size="sm"
                                        class="capitalize">
                                        {{ type }}
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <!-- Mood -->
                        <div>
                            <Label>Mood</Label>
                            <div class="flex flex-wrap gap-2">
                                <Button v-for="mood in moods" :key="mood.value"
                                    @click="form.mood = form.mood === mood.value ? '' : mood.value" type="button"
                                    :variant="form.mood === mood.value ? 'default' : 'outline'" size="sm">
                                    {{ mood.emoji }} {{ mood.label }}
                                </Button>
                            </div>
                        </div>

                        <!-- Location -->
                        <div>
                            <Label for="location">
                                <span class="flex items-center gap-2">
                                    <MapPin class="h-4 w-4" />
                                    Location
                                </span>
                            </Label>
                            <Input id="location" v-model="form.location"
                                placeholder="e.g., Phnom Penh, Cambodia"
                                :class="{ 'border-red-500': form.errors.location }" />
                            <p v-if="form.errors.location" class="text-red-500 text-sm mt-1">{{ form.errors.location }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Media -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Image class="h-5 w-5" />
                            Media
                        </CardTitle>
                        <CardDescription>
                            Upload photos for your post (max 10 images, 5MB each)
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <input type="file" accept="image/*" multiple @change="handleImageUpload"
                                class="w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" />
                            <p v-if="form.errors.images" class="text-red-500 text-sm mt-1">{{ form.errors.images }}</p>
                        </div>

                        <!-- Image Previews -->
                        <div v-if="imagePreviews.length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div v-for="(preview, index) in imagePreviews" :key="index" class="relative group">
                                <img :src="preview" class="w-full h-32 object-cover rounded-lg" />
                                <Button @click="removeImage(index)" type="button" variant="destructive" size="sm"
                                    class="absolute top-1 right-1 h-6 w-6 p-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <X class="h-3 w-3" />
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Tags -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Tag class="h-5 w-5" />
                            Tags
                        </CardTitle>
                        <CardDescription>
                            Add tags to help categorize your post
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
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

                        <!-- Visibility -->
                        <div>
                            <Label for="visibility">Visibility *</Label>
                            <select id="visibility" v-model="form.visibility"
                                class="w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.visibility }">
                                <option value="public">Public</option>
                                <option value="private">Private</option>
                            </select>
                            <p v-if="form.errors.visibility" class="text-red-500 text-sm mt-1">{{
                                form.errors.visibility }}</p>
                        </div>

                        <!-- Pinned -->
                        <div class="flex items-center space-x-2">
                            <input id="is_pinned" v-model="form.is_pinned" type="checkbox"
                                class="rounded border-gray-300 text-primary focus:ring-primary" />
                            <Label for="is_pinned" class="flex items-center gap-2">
                                <Pin class="h-4 w-4" />
                                Pin to top
                            </Label>
                        </div>

                        <!-- Likes Count -->
                        <div>
                            <Label for="likes_count">Likes Count</Label>
                            <Input id="likes_count" v-model.number="form.likes_count" type="number" min="0"
                                :class="{ 'border-red-500': form.errors.likes_count }" />
                            <p v-if="form.errors.likes_count" class="text-red-500 text-sm mt-1">{{
                                form.errors.likes_count }}</p>
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
                        {{ form.processing ? 'Creating...' : 'Create Feed' }}
                    </Button>
                    <Button @click="router.visit(route('feeds.index'))" type="button" variant="outline">
                        Cancel
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
