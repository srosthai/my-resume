<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { 
  Play, 
  Pause, 
  SkipBack, 
  SkipForward, 
  Volume2, 
  VolumeX, 
  Music,
  Minimize2,
  Maximize2,
  X
} from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip'

interface Song {
  id: number
  title: string
  artist: string
  src: string
  duration: number
}

const musicLibrary: Song[] = [
  {
    id: 1,
    title: "បងក្រ",
    artist: "Tena Feat. YCN Rakhie",
    src: "/audio/bong-kror.mp3",
    duration: 250
  }
]

const isPlaying = ref(false)
const currentSongIndex = ref(0)
const currentTime = ref(0)
const volume = ref(0.5)
const isMuted = ref(false)
const isMinimized = ref(true)  // Start minimized by default
const isExpanded = ref(false)  // Controls full player visibility
const audio = ref<HTMLAudioElement | null>(null)

const currentSong = ref<Song>(musicLibrary[0])

const formatTime = (seconds: number): string => {
  const mins = Math.floor(seconds / 60)
  const secs = Math.floor(seconds % 60)
  return `${mins}:${secs.toString().padStart(2, '0')}`
}

const togglePlay = () => {
  if (!audio.value) return
  
  if (isPlaying.value) {
    audio.value.pause()
  } else {
    audio.value.play().catch((error) => {
      console.warn('Audio play failed:', error)
      isPlaying.value = false
    })
  }
}

const previousSong = () => {
  if (currentSongIndex.value > 0) {
    currentSongIndex.value--
  } else {
    currentSongIndex.value = musicLibrary.length - 1
  }
  loadCurrentSong()
}

const nextSong = () => {
  if (currentSongIndex.value < musicLibrary.length - 1) {
    currentSongIndex.value++
  } else {
    currentSongIndex.value = 0
  }
  loadCurrentSong()
}

const loadCurrentSong = () => {
  currentSong.value = musicLibrary[currentSongIndex.value]
  if (audio.value) {
    audio.value.src = currentSong.value.src
    audio.value.load()
    if (isPlaying.value) {
      audio.value.play().catch((error) => {
        console.warn('Audio play failed:', error)
        isPlaying.value = false
      })
    }
  }
}

const toggleMute = () => {
  if (!audio.value) return
  
  isMuted.value = !isMuted.value
  audio.value.muted = isMuted.value
}

const updateVolume = (event: Event) => {
  const target = event.target as HTMLInputElement
  volume.value = parseFloat(target.value)
  if (audio.value) {
    audio.value.volume = volume.value
  }
}

const updateProgress = (event: Event) => {
  const target = event.target as HTMLInputElement
  const newTime = parseFloat(target.value)
  currentTime.value = newTime
  if (audio.value) {
    audio.value.currentTime = newTime
  }
}

const toggleMinimize = () => {
  isMinimized.value = !isMinimized.value
}

const toggleExpand = () => {
  isExpanded.value = !isExpanded.value
  if (!isExpanded.value) {
    isMinimized.value = true
  }
}

const handleMusicButtonClick = () => {
  if (!isExpanded.value) {
    // First click: expand the player
    isExpanded.value = true
    isMinimized.value = false
  } else {
    // Subsequent clicks: toggle play/pause
    togglePlay()
  }
}

onMounted(() => {
  audio.value = new Audio(currentSong.value.src)
  audio.value.volume = volume.value
  
  // Audio event listeners
  audio.value.addEventListener('loadedmetadata', () => {
    if (audio.value) {
      currentSong.value.duration = audio.value.duration
    }
  })
  
  audio.value.addEventListener('timeupdate', () => {
    if (audio.value) {
      currentTime.value = audio.value.currentTime
    }
  })
  
  audio.value.addEventListener('play', () => {
    isPlaying.value = true
  })
  
  audio.value.addEventListener('pause', () => {
    isPlaying.value = false
  })
  
  audio.value.addEventListener('ended', () => {
    nextSong()
  })
  
  audio.value.addEventListener('error', (e) => {
    console.warn('Audio error:', e)
    isPlaying.value = false
    // Try next song if current one fails to load
    if (musicLibrary.length > 1) {
      setTimeout(() => nextSong(), 1000)
    }
  })
})

onUnmounted(() => {
  if (audio.value) {
    audio.value.pause()
    audio.value.src = ''
  }
})

watch(currentSongIndex, () => {
  loadCurrentSong()
})
</script>

<template>
  <!-- Small Floating Music Button (Always Visible) -->
  <div class="fixed bottom-6 right-6 z-50 pointer-events-auto">
    <TooltipProvider>
      <Tooltip>
        <TooltipTrigger asChild>
          <Button
            @click="handleMusicButtonClick"
            size="lg"
            class="h-14 w-14 rounded-full bg-primary/90 text-primary-foreground hover:bg-primary active:bg-primary/80 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-110 active:scale-105"
            :class="{
              'animate-pulse': isPlaying && !isExpanded,
              'bg-primary': isExpanded
            }"
          >
            <component 
              :is="isExpanded ? (isPlaying ? Pause : Play) : Music" 
              class="w-6 h-6" 
            />
          </Button>
        </TooltipTrigger>
        <TooltipContent>
          <p>{{ isExpanded ? (isPlaying ? 'Pause Music' : 'Play Music') : 'Open Music Player' }}</p>
        </TooltipContent>
      </Tooltip>
    </TooltipProvider>
  </div>

  <!-- Expanded Music Player -->
  <div 
    v-if="isExpanded" 
    class="fixed bottom-4 left-4 right-4 z-40 pointer-events-none"
  >
    <div class="max-w-6xl mx-auto pointer-events-auto">
      <TooltipProvider>
        <Card 
          class="bg-background/95 border-border/50 shadow-2xl transition-all duration-300 music-player"
        >
          <CardContent class="p-3">
            <!-- Minimized View -->
            <div v-if="isMinimized" class="flex items-center gap-2">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center">
                  <Music class="w-4 h-4 text-primary" />
                </div>
                <Button
                  @click="togglePlay"
                  size="sm"
                  variant="ghost"
                  class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                >
                  <component :is="isPlaying ? Pause : Play" class="w-4 h-4" />
                </Button>
              </div>
              <div class="flex items-center gap-1">
                <Tooltip>
                  <TooltipTrigger asChild>
                    <Button
                      @click="toggleMinimize"
                      size="sm"
                      variant="ghost"
                      class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                    >
                      <Maximize2 class="w-3 h-3" />
                    </Button>
                  </TooltipTrigger>
                  <TooltipContent>
                    <p>Expand Player</p>
                  </TooltipContent>
                </Tooltip>
                
                <Tooltip>
                  <TooltipTrigger asChild>
                    <Button
                      @click="toggleExpand"
                      size="sm"
                      variant="ghost"
                      class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                    >
                      <X class="w-3 h-3" />
                    </Button>
                  </TooltipTrigger>
                  <TooltipContent>
                    <p>Close Player</p>
                  </TooltipContent>
                </Tooltip>
              </div>
            </div>

            <!-- Full View -->
            <div v-else class="space-y-3">
              <!-- Song Info and Controls -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                  <div class="w-10 h-10 bg-gradient-to-br from-primary/20 to-primary/10 rounded-lg flex items-center justify-center shrink-0">
                    <Music class="w-5 h-5 text-primary" />
                  </div>
                  <div class="flex-1 min-w-0">
                    <h3 class="font-medium text-foreground truncate">{{ currentSong.title }}</h3>
                    <p class="text-sm text-muted-foreground truncate">{{ currentSong.artist }}</p>
                  </div>
                  <Badge variant="secondary" class="hidden sm:flex">
                    {{ currentSongIndex + 1 }} / {{ musicLibrary.length }}
                  </Badge>
                </div>
                
                <div class="flex items-center gap-1">
                  <Tooltip>
                    <TooltipTrigger asChild>
                      <Button
                        @click="toggleMinimize"
                        size="sm"
                        variant="ghost"
                        class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                      >
                        <Minimize2 class="w-3 h-3" />
                      </Button>
                    </TooltipTrigger>
                    <TooltipContent>
                      <p>Minimize Player</p>
                    </TooltipContent>
                  </Tooltip>
                  
                  <Tooltip>
                    <TooltipTrigger asChild>
                      <Button
                        @click="toggleExpand"
                        size="sm"
                        variant="ghost"
                        class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                      >
                        <X class="w-3 h-3" />
                      </Button>
                    </TooltipTrigger>
                    <TooltipContent>
                      <p>Close Player</p>
                    </TooltipContent>
                  </Tooltip>
                </div>
              </div>

              <!-- Progress Bar -->
              <div class="flex items-center gap-2 text-xs text-muted-foreground">
                <span class="w-10 text-right">{{ formatTime(currentTime) }}</span>
                <input
                  type="range"
                  :min="0"
                  :max="currentSong.duration"
                  :value="currentTime"
                  @input="updateProgress"
                  class="flex-1 h-2 bg-accent rounded-lg appearance-none cursor-pointer slider"
                />
                <span class="w-10">{{ formatTime(currentSong.duration) }}</span>
              </div>

              <!-- Control Buttons -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-1">
                  <Tooltip>
                    <TooltipTrigger asChild>
                      <Button
                        @click="previousSong"
                        size="sm"
                        variant="ghost"
                        class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                        :disabled="musicLibrary.length <= 1"
                      >
                        <SkipBack class="w-4 h-4" />
                      </Button>
                    </TooltipTrigger>
                    <TooltipContent>
                      <p>Previous Song</p>
                    </TooltipContent>
                  </Tooltip>

                  <Tooltip>
                    <TooltipTrigger asChild>
                      <Button
                        @click="togglePlay"
                        size="sm"
                        class="h-10 w-10 rounded-full bg-primary text-primary-foreground hover:bg-primary/90 active:bg-primary/80"
                      >
                        <component :is="isPlaying ? Pause : Play" class="w-4 h-4" />
                      </Button>
                    </TooltipTrigger>
                    <TooltipContent>
                      <p>{{ isPlaying ? 'Pause' : 'Play' }}</p>
                    </TooltipContent>
                  </Tooltip>

                  <Tooltip>
                    <TooltipTrigger asChild>
                      <Button
                        @click="nextSong"
                        size="sm"
                        variant="ghost"
                        class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                        :disabled="musicLibrary.length <= 1"
                      >
                        <SkipForward class="w-4 h-4" />
                      </Button>
                    </TooltipTrigger>
                    <TooltipContent>
                      <p>Next Song</p>
                    </TooltipContent>
                  </Tooltip>
                </div>

                <!-- Volume Control -->
                <div class="hidden sm:flex items-center gap-2">
                  <Tooltip>
                    <TooltipTrigger asChild>
                      <Button
                        @click="toggleMute"
                        size="sm"
                        variant="ghost"
                        class="h-8 w-8 rounded-full hover:bg-accent active:bg-accent"
                      >
                        <component :is="isMuted ? VolumeX : Volume2" class="w-4 h-4" />
                      </Button>
                    </TooltipTrigger>
                    <TooltipContent>
                      <p>{{ isMuted ? 'Unmute' : 'Mute' }}</p>
                    </TooltipContent>
                  </Tooltip>
                  <input
                    type="range"
                    min="0"
                    max="1"
                    step="0.1"
                    :value="volume"
                    @input="updateVolume"
                    class="w-20 h-2 bg-accent rounded-lg appearance-none cursor-pointer slider"
                  />
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </TooltipProvider>
    </div>
  </div>
</template>

<style scoped>
/* Custom slider styling */
.slider {
  background: hsl(var(--accent));
}

.slider::-webkit-slider-thumb {
  appearance: none;
  width: 16px;
  height: 16px;
  background: hsl(var(--primary));
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid hsl(var(--background));
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.slider::-moz-range-thumb {
  width: 16px;
  height: 16px;
  background: hsl(var(--primary));
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid hsl(var(--background));
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.slider::-webkit-slider-track {
  background: hsl(var(--accent));
  height: 8px;
  border-radius: 4px;
}

.slider::-moz-range-track {
  background: hsl(var(--accent));
  height: 8px;
  border-radius: 4px;
}

/* Music button pulse animation */
@keyframes music-pulse {
  0%, 100% {
    box-shadow: 0 0 0 0 hsl(var(--primary) / 0.7);
  }
  50% {
    box-shadow: 0 0 0 10px hsl(var(--primary) / 0);
  }
}

.animate-pulse {
  animation: music-pulse 2s infinite;
}
</style>