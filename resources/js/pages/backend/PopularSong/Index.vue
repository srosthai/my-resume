<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import Button from '@/components/ui/button/Button.vue'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import Icon from '@/components/Icon.vue'
import { Link } from '@inertiajs/vue3'
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
  { title: 'Popular Songs', href: '/popular-songs' }
]

const deleteSong = (id: number) => {
  if (confirm('Are you sure you want to delete this song?')) {
    router.delete(route('backend.popular-songs.destroy', id))
  }
}

const currentlyPlaying = ref<number | null>(null)
const audioElements = ref<{ [key: number]: HTMLAudioElement }>({})

const togglePreview = (song: PopularSong) => {
  if (currentlyPlaying.value === song.id) {
    if (audioElements.value[song.id]) {
      audioElements.value[song.id].pause()
      audioElements.value[song.id].currentTime = 0
    }
    currentlyPlaying.value = null
  } else {
    if (currentlyPlaying.value && audioElements.value[currentlyPlaying.value]) {
      audioElements.value[currentlyPlaying.value].pause()
      audioElements.value[currentlyPlaying.value].currentTime = 0
    }

    if (!audioElements.value[song.id]) {
      audioElements.value[song.id] = new Audio(song.url)
      audioElements.value[song.id].addEventListener('ended', () => {
        currentlyPlaying.value = null
      })
    }

    audioElements.value[song.id].play().catch(error => {
      console.warn('Audio play failed:', error)
      currentlyPlaying.value = null
    })

    currentlyPlaying.value = song.id
  }
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString()
}

const formatDuration = (seconds: number): string => {
  const mins = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${mins}:${secs.toString().padStart(2, '0')}`
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <Card>
        <CardHeader class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <CardTitle>Popular Songs</CardTitle>
            <CardDescription>
              Manage songs for your portfolio music player
            </CardDescription>
          </div>
          <div>
            <Link href="/backend/popular-songs/create">
            <Button variant="default">
              <Icon name="plus" class="h-4 w-4" />
              Add New Song
            </Button>
            </Link>
          </div>
        </CardHeader>
        <CardContent>
          <div v-if="popularSongs.data.length === 0" class="text-center py-8">
            <Icon name="music" class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
            <h3 class="text-lg font-semibold mb-2">No songs found</h3>
            <p class="text-muted-foreground mb-4">Get started by adding your first song to the music library.</p>
            <Link href="/backend/popular-songs/create">
            <Button variant="default">
              <Icon name="plus" class="h-4 w-4 mr-2" />
              Add Your First Song
            </Button>
            </Link>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full hover:table-hover">
              <thead class="bg-gray-100 dark:bg-gray-800">
                <tr class="border-b">
                  <th class="text-left p-4">Song</th>
                  <th class="text-left p-4">Artist</th>
                  <th class="text-left p-4">Duration</th>
                  <th class="text-left p-4">Created</th>
                  <th class="text-left p-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="song in popularSongs.data" :key="song.id" class="border-b hover:bg-muted/50">
                  <td class="p-4">
                    <div class="flex items-center gap-2">
                      <div class="w-8 h-8 bg-primary/10 rounded flex items-center justify-center">
                        <Icon name="music" class="h-4 w-4 text-primary" />
                      </div>
                      <span class="font-medium">{{ song.title }}</span>
                    </div>
                  </td>
                  <td class="p-4">{{ song.artist }}</td>
                  <td class="p-4">{{ formatDuration(song.duration) }}</td>
                  <td class="p-4">{{ formatDate(song.created_at) }}</td>
                  <td class="p-4">
                    <div class="flex gap-2">
                      <Link :href="`/backend/popular-songs/${song.id}`">
                      <Button variant="outline" size="sm">
                        <Icon name="eye" class="h-4 w-4" />
                        View
                      </Button>
                      </Link>
                      <Link :href="`/backend/popular-songs/${song.id}/edit`">
                      <Button variant="default" size="sm">
                        <Icon name="edit" class="h-4 w-4" />
                        Edit
                      </Button>
                      </Link>
                      <Button @click="deleteSong(song.id)" variant="destructive" size="sm">
                        <Icon name="trash" class="h-4 w-4" />
                        Delete
                      </Button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="popularSongs.links.length > 3" class="flex justify-center mt-6">
            <div class="flex items-center space-x-1">
              <template v-for="(link, index) in popularSongs.links" :key="index">
                <Button v-if="link.url" @click="router.get(link.url)" variant="ghost" size="sm"
                  :class="{ 'bg-primary text-primary-foreground': link.active }" v-html="link.label" />
                <span v-else class="px-3 py-2 text-sm text-muted-foreground" v-html="link.label" />
              </template>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>