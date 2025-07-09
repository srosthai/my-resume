<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
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
  { title: 'View', href: `/backend/popular-songs/${props.popularSong.id}` }
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
    audio.value.play().catch(error => {
      console.warn('Audio play failed:', error)
      isPlaying.value = false
    })
    isPlaying.value = true
  }
}

const deleteSong = () => {
  if (confirm('Are you sure you want to delete this song?')) {
    router.delete(route('backend.popular-songs.destroy', props.popularSong.id))
  }
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
    <div class="space-y-6 p-4">
      <div>
        <Heading :title="`Song: ${popularSong.title}`" />
        <p class="text-sm text-muted-foreground">
          View song details and manage audio file
        </p>
      </div>

      <Card>
        <CardHeader>
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
              <div class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center">
                <Icon name="music" class="w-8 h-8 text-primary" />
              </div>
              <div>
                <CardTitle class="text-2xl">{{ popularSong.title }}</CardTitle>
                <CardDescription class="text-lg">{{ popularSong.artist }}</CardDescription>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <Link :href="`/backend/popular-songs/${popularSong.id}/edit`">
              <Button variant="outline">
                <Icon name="edit" class="h-4 w-4 mr-2" />
                Edit
              </Button>
              </Link>
              <Button @click="deleteSong" variant="destructive">
                <Icon name="trash" class="h-4 w-4 mr-2" />
                Delete
              </Button>
            </div>
          </div>
        </CardHeader>
        <CardContent class="space-y-6">
          <!-- Song Details Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Duration -->
            <div class="space-y-2">
              <h3 class="font-medium">Duration</h3>
              <div class="flex items-center gap-2">
                <div class="text-lg font-semibold px-3 py-1 bg-muted rounded">
                  {{ formatTime(popularSong.duration) }}
                </div>
                <span class="text-muted-foreground text-sm">
                  ({{ popularSong.duration }} seconds)
                </span>
              </div>
            </div>

            <!-- Audio File URL -->
            <div class="space-y-2">
              <h3 class="font-medium">Audio File URL</h3>
              <div class="flex items-center gap-2">
                <code class="flex-1 px-2 py-1 bg-muted rounded text-sm truncate">
                  {{ popularSong.url }}
                </code>
                <Button size="sm" variant="ghost" class="shrink-0"
                  @click="() => window.open(popularSong.url, '_blank')">
                  <Icon name="external-link" class="h-4 w-4" />
                </Button>
              </div>
            </div>
          </div>

          <!-- YouTube Player -->
          <div class="space-y-2">
            <h3 class="font-medium">YouTube Player</h3>
            <div class="p-4 bg-muted rounded-lg">
              <div class="aspect-video bg-black rounded overflow-hidden">
                <iframe :src="`https://www.youtube.com/embed/${extractYouTubeId(popularSong.url)}?rel=0`"
                  class="w-full h-full" frameborder="0" allowfullscreen></iframe>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-4 pt-4 border-t">
            <Link :href="`/backend/popular-songs/${popularSong.id}/edit`">
            <Button variant="default">
              <Icon name="edit" class="h-4 w-4 mr-2" />
              Edit Song
            </Button>
            </Link>
            <Link href="/backend/popular-songs/create">
            <Button variant="outline">
              <Icon name="plus" class="h-4 w-4 mr-2" />
              Add New Song
            </Button>
            </Link>
            <Link href="/popular-songs">
            <Button variant="outline">
              <Icon name="arrow-left" class="h-4 w-4 mr-2" />
              Back to List
            </Button>
            </Link>
            <Button @click="deleteSong" variant="destructive" class="ml-auto">
              <Icon name="trash" class="h-4 w-4 mr-2" />
              Delete Song
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>