<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { Button } from '@/components/ui/button'
import { ChevronUp } from 'lucide-vue-next'
import { cn } from '@/lib/utils'

const isVisible = ref(false)
const scrollThreshold = 300

const checkScrollPosition = () => {
  isVisible.value = window.scrollY > scrollThreshold
}

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

onMounted(() => {
  window.addEventListener('scroll', checkScrollPosition)
  checkScrollPosition() // Check initial position
})

onUnmounted(() => {
  window.removeEventListener('scroll', checkScrollPosition)
})
</script>

<template>
  <Transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="opacity-0 translate-y-2 scale-95"
    enter-to-class="opacity-100 translate-y-0 scale-100"
    leave-active-class="transition-all duration-200 ease-in"
    leave-from-class="opacity-100 translate-y-0 scale-100"
    leave-to-class="opacity-0 translate-y-2 scale-95"
  >
    <Button
      v-if="isVisible"
      @click="scrollToTop"
      :class="cn(
        'fixed bottom-6 right-24 z-40 size-12 rounded-full shadow-lg',
        'bg-secondary hover:bg-secondary/90 text-secondary-foreground',
        'border border-border/20 backdrop-blur-md',
        'hover:scale-105 active:scale-95 transition-all duration-200',
        'focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2',
        // Responsive positioning
        'sm:right-28 md:right-32'
      )"
      size="icon"
      aria-label="Scroll to top"
    >
      <ChevronUp class="size-5" />
    </Button>
  </Transition>
</template>