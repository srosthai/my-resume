<script setup lang="ts">
import { computed } from 'vue'
import { useAppearance } from '@/composables/useAppearance'
import { Sun, Moon } from 'lucide-vue-next'

interface Props {
  class?: string
}

const props = withDefaults(defineProps<Props>(), {
  class: ''
})

const { appearance, updateAppearance } = useAppearance()

// Determine current theme for display
const currentTheme = computed(() => {
  if (appearance.value === 'system') {
    // Check system preference
    if (typeof window !== 'undefined') {
      return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    }
    return 'light'
  }
  return appearance.value
})

// Get the icon to display (opposite of current theme)
const toggleIcon = computed(() => {
  return currentTheme.value === 'dark' ? Sun : Moon
})

// Get tooltip text
const tooltipText = computed(() => {
  return currentTheme.value === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode'
})

// Toggle between light and dark (skip system for simplicity)
const toggleTheme = () => {
  const newTheme = currentTheme.value === 'dark' ? 'light' : 'dark'
  updateAppearance(newTheme)
}
</script>

<template>
  <button
    @click="toggleTheme"
    :aria-label="tooltipText"
    :title="tooltipText"
    class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-full text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
  >
    <component 
      :is="toggleIcon" 
      :class="[props.class, 'transition-transform duration-200 hover:rotate-12']"
    />
  </button>
</template>