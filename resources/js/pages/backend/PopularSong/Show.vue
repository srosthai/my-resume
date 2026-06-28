<script setup lang="ts">
import ConfirmDialog from '@/components/ConfirmDialog.vue'
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import type { BreadcrumbItemType } from '@/types'

interface PopularSong {
    id: number
    title: string
    artist: string
    url: string
    duration: number
}

interface Props {
    popularSong: PopularSong
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Popular Songs', href: '/popular-songs' },
    { title: 'View', href: `/backend/popular-songs/${props.popularSong.id}` },
]

const isPlaying = ref(false)
const audio = ref<HTMLAudioElement | null>(null)

const togglePlay = () => {
    if (isPlaying.value) {
        audio.value?.pause()
        isPlaying.value = false
    } else {
        if (!audio.value) {
            audio.value = new Audio(props.popularSong.url)
            audio.value.addEventListener('ended', () => {
                isPlaying.value = false
            })
        }
        audio.value.play().catch((error) => {
            console.warn('Audio play failed:', error)
            isPlaying.value = false
        })
        isPlaying.value = true
    }
}

const showDeleteConfirm = ref(false)
const deleting = ref(false)

const deleteSong = () => {
    router.delete(route('backend.popular-songs.destroy', props.popularSong.id), {
        onStart: () => (deleting.value = true),
        onFinish: () => {
            deleting.value = false
            showDeleteConfirm.value = false
        },
    })
}

const formatTime = (seconds: number): string => {
    const mins = Math.floor(seconds / 60)
    const secs = seconds % 60
    return `${mins}:${secs.toString().padStart(2, '0')}`
}

// Extract YouTube video ID from URL
const extractYouTubeId = (url: string): string | null => {
    const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/)
    return match ? match[1] : null
}
</script>

<template>
    <Head :title="popularSong.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
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
                            <h1 class="text-2xl font-semibold tracking-tight">{{ popularSong.title }}</h1>
                            <p class="text-sm text-muted-foreground">{{ popularSong.artist }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="`/backend/popular-songs/${popularSong.id}/edit`">
                        <Button variant="outline" class="rounded-xl">
                            <Icon name="squarePen" class="size-4" />
                            Edit
                        </Button>
                    </Link>
                    <Button variant="destructive" class="rounded-xl" @click="showDeleteConfirm = true">
                        <Icon name="trash2" class="size-4" />
                        Delete
                    </Button>
                </div>
            </div>

            <!-- Details card -->
            <div class="rounded-2xl border bg-card shadow-sm">
                <div class="space-y-6 p-6 sm:p-8">
                    <!-- Song Details Grid -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Duration -->
                        <div class="space-y-2">
                            <h3 class="font-medium">Duration</h3>
                            <div class="flex items-center gap-2">
                                <div class="rounded bg-muted px-3 py-1 text-lg font-semibold">
                                    {{ formatTime(popularSong.duration) }}
                                </div>
                                <span class="text-sm text-muted-foreground">
                                    ({{ popularSong.duration }} seconds)
                                </span>
                            </div>
                        </div>

                        <!-- Audio File URL -->
                        <div class="space-y-2">
                            <h3 class="font-medium">Audio File URL</h3>
                            <div class="flex items-center gap-2">
                                <code class="flex-1 truncate rounded bg-muted px-2 py-1 text-sm">
                                    {{ popularSong.url }}
                                </code>
                                <Button
                                    size="sm"
                                    variant="ghost"
                                    class="shrink-0 rounded-lg"
                                    @click="() => window.open(popularSong.url, '_blank')"
                                >
                                    <Icon name="externalLink" class="size-4" />
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- YouTube Player -->
                    <div class="space-y-2">
                        <h3 class="font-medium">YouTube Player</h3>
                        <div class="rounded-lg bg-muted p-4">
                            <div class="aspect-video overflow-hidden rounded bg-black">
                                <iframe
                                    :src="`https://www.youtube.com/embed/${extractYouTubeId(popularSong.url)}?rel=0`"
                                    class="h-full w-full"
                                    frameborder="0"
                                    allowfullscreen
                                ></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer actions -->
                <div class="flex flex-wrap items-center gap-3 border-t bg-muted/30 px-6 py-4 sm:px-8">
                    <Link :href="`/backend/popular-songs/${popularSong.id}/edit`">
                        <Button class="rounded-xl shadow-sm">
                            <Icon name="squarePen" class="size-4" />
                            Edit Song
                        </Button>
                    </Link>
                    <Link href="/backend/popular-songs/create">
                        <Button variant="outline" class="rounded-xl">
                            <Icon name="plus" class="size-4" />
                            Add New Song
                        </Button>
                    </Link>
                    <Link href="/popular-songs">
                        <Button variant="outline" class="rounded-xl">
                            <Icon name="arrowLeft" class="size-4" />
                            Back to List
                        </Button>
                    </Link>
                    <Button variant="destructive" class="ml-auto rounded-xl" @click="showDeleteConfirm = true">
                        <Icon name="trash2" class="size-4" />
                        Delete Song
                    </Button>
                </div>
            </div>
        </div>

        <ConfirmDialog
            v-model:open="showDeleteConfirm"
            title="Delete this song?"
            description="This song will be permanently removed from your music library. This action cannot be undone."
            confirm-label="Delete"
            :processing="deleting"
            @confirm="deleteSong"
        />
    </AppLayout>
</template>
