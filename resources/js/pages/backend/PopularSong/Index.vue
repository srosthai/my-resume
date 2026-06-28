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
    formatted_duration: string
    created_at: string
    updated_at: string
}

interface Props {
    popularSongs: {
        data: PopularSong[]
        links: any[]
        meta: any
    }
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Popular Songs', href: '/popular-songs' },
]

const showDeleteConfirm = ref(false)
const itemToDelete = ref<PopularSong | null>(null)
const deleting = ref(false)

const confirmDelete = (item: PopularSong) => {
    itemToDelete.value = item
    showDeleteConfirm.value = true
}

const deleteItem = () => {
    if (!itemToDelete.value) return
    router.delete(route('backend.popular-songs.destroy', itemToDelete.value.id), {
        onStart: () => (deleting.value = true),
        onFinish: () => {
            deleting.value = false
            showDeleteConfirm.value = false
            itemToDelete.value = null
        },
    })
}

const formatDate = (dateString: string) => new Date(dateString).toLocaleDateString()

const formatDuration = (seconds: number): string => {
    const mins = Math.floor(seconds / 60)
    const secs = seconds % 60
    return `${mins}:${secs.toString().padStart(2, '0')}`
}
</script>

<template>
    <Head title="Popular Songs" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-7xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                        <Icon name="music" class="size-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">Popular Songs</h1>
                        <p class="text-sm text-muted-foreground">Manage songs for your portfolio music player</p>
                    </div>
                </div>
                <Link href="/backend/popular-songs/create">
                    <Button class="rounded-xl shadow-sm">
                        <Icon name="plus" class="size-4" />
                        Add New Song
                    </Button>
                </Link>
            </div>

            <!-- Table card -->
            <div class="overflow-hidden rounded-2xl border bg-card shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/40 text-xs uppercase tracking-wider text-muted-foreground">
                                <th class="w-16 px-6 py-4 text-left font-medium">#</th>
                                <th class="px-6 py-4 text-left font-medium">Song</th>
                                <th class="px-6 py-4 text-left font-medium">Artist</th>
                                <th class="px-6 py-4 text-left font-medium">Duration</th>
                                <th class="px-6 py-4 text-left font-medium">Created</th>
                                <th class="px-6 py-4 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="(song, index) in popularSongs.data"
                                :key="song.id"
                                class="group transition-colors hover:bg-muted/40"
                            >
                                <td class="px-6 py-4">
                                    <span class="inline-flex size-7 items-center justify-center rounded-full bg-muted text-xs font-medium text-muted-foreground">
                                        {{ index + 1 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-9 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                            <Icon name="music" class="size-4" />
                                        </div>
                                        <span class="font-medium">{{ song.title }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-muted-foreground">{{ song.artist }}</td>
                                <td class="px-6 py-4 text-muted-foreground">{{ formatDuration(song.duration) }}</td>
                                <td class="px-6 py-4 text-muted-foreground">{{ formatDate(song.created_at) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link :href="`/backend/popular-songs/${song.id}`">
                                            <Button variant="ghost" size="sm" class="rounded-lg text-muted-foreground hover:text-foreground">
                                                <Icon name="eye" class="size-4" />
                                                View
                                            </Button>
                                        </Link>
                                        <Link :href="`/backend/popular-songs/${song.id}/edit`">
                                            <Button variant="ghost" size="sm" class="rounded-lg text-muted-foreground hover:text-foreground">
                                                <Icon name="squarePen" class="size-4" />
                                                Edit
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="rounded-lg text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                            @click="confirmDelete(song)"
                                        >
                                            <Icon name="trash2" class="size-4" />
                                            Delete
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="popularSongs.data.length === 0">
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-muted-foreground">
                                        <div class="flex size-12 items-center justify-center rounded-full bg-muted">
                                            <Icon name="music" class="size-6" />
                                        </div>
                                        <p class="text-sm">No songs found</p>
                                        <Link href="/backend/popular-songs/create">
                                            <Button variant="outline" size="sm" class="rounded-lg">
                                                <Icon name="plus" class="size-4" />
                                                Add Your First Song
                                            </Button>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="popularSongs.links.length > 3" class="flex justify-center border-t bg-muted/30 px-6 py-4">
                    <div class="flex items-center gap-1">
                        <template v-for="(link, index) in popularSongs.links" :key="index">
                            <Button
                                v-if="link.url"
                                variant="ghost"
                                size="sm"
                                class="rounded-lg"
                                :class="{ 'bg-primary text-primary-foreground': link.active }"
                                @click="router.get(link.url)"
                            >
                                <span v-html="link.label" />
                            </Button>
                            <span v-else class="px-3 py-2 text-sm text-muted-foreground" v-html="link.label" />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmDialog
            v-model:open="showDeleteConfirm"
            title="Delete this song?"
            description="This song will be permanently removed from your music library. This action cannot be undone."
            confirm-label="Delete"
            :processing="deleting"
            @confirm="deleteItem"
        />
    </AppLayout>
</template>
