<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import Button from '@/components/ui/button/Button.vue'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import Input from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import Label from '@/components/ui/label/Label.vue'
import Icon from '@/components/Icon.vue'
import { Link } from '@inertiajs/vue3'
import type { BreadcrumbItemType } from '@/types'

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Popular Songs', href: '/popular-songs' },
  { title: 'Create', href: '/backend/popular-songs/create' }
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
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <div>
        <Heading title="Add New Song" />
        <p class="text-sm text-muted-foreground">
          Add a new song to your portfolio music library
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Song Information</CardTitle>
          <CardDescription>
            Enter the song details below
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- YouTube URL Input -->
            <div>
              <Label for="url">YouTube URL *</Label>
              <Input 
                id="url" 
                v-model="form.url" 
                type="url" 
                placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ"
                @blur="validateYouTubeUrl"
                required
              />
              <p class="text-sm text-muted-foreground mt-1">
                Enter a YouTube video URL. Supported formats: youtube.com/watch?v=, youtu.be/, youtube.com/embed/
              </p>
              <div v-if="form.url && !isValidYouTubeUrl(form.url)" class="text-sm text-red-600 mt-1">
                Please enter a valid YouTube URL
              </div>
              <InputError :message="form.errors.url" />
            </div>

            <!-- YouTube Preview -->
            <div v-if="form.url && isValidYouTubeUrl(form.url)" class="space-y-2">
              <Label>YouTube Preview</Label>
              <div class="p-4 bg-muted rounded-lg">
                <div class="aspect-video bg-black rounded overflow-hidden">
                  <iframe 
                    :src="`https://www.youtube.com/embed/${extractYouTubeId(form.url)}?rel=0`"
                    class="w-full h-full"
                    frameborder="0"
                    allowfullscreen
                  ></iframe>
                </div>
              </div>
            </div>

            <!-- Song Title -->
            <div>
              <Label for="title">Song Title *</Label>
              <Input id="title" v-model="form.title" type="text" placeholder="Enter song title" required autofocus />
              <InputError :message="form.errors.title" />
            </div>

            <!-- Artist -->
            <div>
              <Label for="artist">Artist *</Label>
              <Input id="artist" v-model="form.artist" type="text" placeholder="Enter artist name" required />
              <InputError :message="form.errors.artist" />
            </div>

            <!-- Duration -->
            <div>
              <Label for="duration">Duration (seconds) *</Label>
              <div class="flex items-center gap-4">
                <Input id="duration" v-model.number="form.duration" type="number" min="1" max="3600" placeholder="180"
                  required class="flex-1" />
                <span class="text-sm text-muted-foreground">
                  {{ formatTime(form.duration || 0) }}
                </span>
              </div>
              <p class="text-sm text-muted-foreground mt-1">
                Enter the duration in seconds (you can check this on YouTube)
              </p>
              <InputError :message="form.errors.duration" />
            </div>


            <!-- Preview Info -->
            <div v-if="form.url && form.title" class="p-4 bg-muted rounded-lg">
              <h4 class="font-medium mb-2">Preview</h4>
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                  <Icon name="music" class="h-5 w-5 text-primary" />
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

            <!-- Submit Button -->
            <div class="flex items-center gap-4">
              <Button type="submit" :disabled="form.processing || !isValidYouTubeUrl(form.url)">
                {{ form.processing ? 'Creating...' : 'Create Song' }}
              </Button>
              <Link href="/popular-songs">
              <Button type="button" variant="outline">
                Cancel
              </Button>
              </Link>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>