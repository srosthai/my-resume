<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import type { BreadcrumbItemType } from '@/types'

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Popular Songs', href: '/popular-songs' },
    { title: 'Create', href: '/backend/popular-songs/create' },
]

const form = useForm({
    title: '',
    artist: '',
    url: '',
    duration: 180,
})

const isValidYouTubeUrl = (url: string): boolean => {
    const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/
    return youtubeRegex.test(url)
}

const extractYouTubeId = (url: string): string | null => {
    const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/)
    return match ? match[1] : null
}

const validateYouTubeUrl = () => {
    if (form.url && !isValidYouTubeUrl(form.url)) {
        if (form.url.includes('youtube.com') || form.url.includes('youtu.be')) {
            const videoId = extractYouTubeId(form.url)
            if (videoId) {
                form.url = `https://www.youtube.com/watch?v=${videoId}`
            }
        }
    }
}

const formatTime = (seconds: number): string => {
    const mins = Math.floor(seconds / 60)
    const secs = seconds % 60
    return `${mins}:${secs.toString().padStart(2, '0')}`
}

const submit = () => {
    form.post(route('backend.popular-songs.store'))
}
</script>

<template>
    <Head title="Add New Song" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex items-center gap-4">
                <Link href="/popular-songs">
                    <Button variant="outline" size="icon" class="rounded-xl">
                        <Icon name="arrowLeft" class="size-4" />
                    </Button>
                </Link>
                <div class="flex items-center gap-4">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                        <Icon name="music" class="size-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">Add New Song</h1>
                        <p class="text-sm text-muted-foreground">Add a new song to your portfolio music library</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="rounded-2xl border bg-card shadow-sm">
                    <div class="space-y-6 p-6 sm:p-8">
                        <!-- YouTube URL Input -->
                        <div class="space-y-2">
                            <Label for="url">YouTube URL *</Label>
                            <Input
                                id="url"
                                v-model="form.url"
                                type="url"
                                placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ"
                                @blur="validateYouTubeUrl"
                                required
                            />
                            <p class="text-sm text-muted-foreground">
                                Enter a YouTube video URL. Supported formats: youtube.com/watch?v=, youtu.be/, youtube.com/embed/
                            </p>
                            <div v-if="form.url && !isValidYouTubeUrl(form.url)" class="text-sm text-red-600">
                                Please enter a valid YouTube URL
                            </div>
                            <InputError :message="form.errors.url" />
                        </div>

                        <!-- YouTube Preview -->
                        <div v-if="form.url && isValidYouTubeUrl(form.url)" class="space-y-2">
                            <Label>YouTube Preview</Label>
                            <div class="rounded-lg bg-muted p-4">
                                <div class="aspect-video overflow-hidden rounded bg-black">
                                    <iframe
                                        :src="`https://www.youtube.com/embed/${extractYouTubeId(form.url)}?rel=0`"
                                        class="h-full w-full"
                                        frameborder="0"
                                        allowfullscreen
                                    ></iframe>
                                </div>
                            </div>
                        </div>

                        <!-- Song Title -->
                        <div class="space-y-2">
                            <Label for="title">Song Title *</Label>
                            <Input id="title" v-model="form.title" type="text" placeholder="Enter song title" required autofocus />
                            <InputError :message="form.errors.title" />
                        </div>

                        <!-- Artist -->
                        <div class="space-y-2">
                            <Label for="artist">Artist *</Label>
                            <Input id="artist" v-model="form.artist" type="text" placeholder="Enter artist name" required />
                            <InputError :message="form.errors.artist" />
                        </div>

                        <!-- Duration -->
                        <div class="space-y-2">
                            <Label for="duration">Duration (seconds) *</Label>
                            <div class="flex items-center gap-4">
                                <Input
                                    id="duration"
                                    v-model.number="form.duration"
                                    type="number"
                                    min="1"
                                    max="3600"
                                    placeholder="180"
                                    required
                                    class="flex-1"
                                />
                                <span class="text-sm text-muted-foreground">
                                    {{ formatTime(form.duration || 0) }}
                                </span>
                            </div>
                            <p class="text-sm text-muted-foreground">
                                Enter the duration in seconds (you can check this on YouTube)
                            </p>
                            <InputError :message="form.errors.duration" />
                        </div>

                        <!-- Preview Info -->
                        <div v-if="form.url && form.title" class="rounded-lg bg-muted p-4">
                            <h4 class="mb-2 font-medium">Preview</h4>
                            <div class="flex items-center gap-3">
                                <div class="flex size-10 items-center justify-center rounded-lg bg-primary/10">
                                    <Icon name="music" class="size-5 text-primary" />
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium">{{ form.title }}</p>
                                    <p class="text-sm text-muted-foreground">{{ form.artist }}</p>
                                </div>
                                <div class="text-sm text-muted-foreground">
                                    {{ formatTime(form.duration || 0) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer actions -->
                    <div class="flex items-center justify-end gap-3 border-t bg-muted/30 px-6 py-4 sm:px-8">
                        <Link href="/popular-songs">
                            <Button type="button" variant="outline" class="rounded-xl">Cancel</Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing || !isValidYouTubeUrl(form.url)" class="rounded-xl shadow-sm">
                            <Icon v-if="form.processing" name="loaderCircle" class="size-4 animate-spin" />
                            {{ form.processing ? 'Creating...' : 'Create Song' }}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
