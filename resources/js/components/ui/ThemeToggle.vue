<script setup lang="ts">
import { computed } from 'vue'
import { useAppearance } from '@/composables/useAppearance'
import { Moon, Sun } from 'lucide-vue-next'

interface Props {
  class?: string
  buttonClass?: string
}

const props = withDefaults(defineProps<Props>(), {
  class: '',
  buttonClass: ''
})

const { appearance, updateAppearance } = useAppearance()

const currentTheme = computed(() => appearance.value)

const toggleIcon = computed(() => {
  return currentTheme.value === 'dark' ? Moon : Sun
})

const tooltipText = computed(() => {
  return currentTheme.value === 'dark' ? 'Dark mode active. Switch to light mode' : 'Light mode active. Switch to dark mode'
})

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
    :class="[
      'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-full text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring',
      props.buttonClass
    ]"
  >
    <component 
      :is="toggleIcon" 
      :class="[props.class, 'transition-transform duration-200 hover:rotate-12']"
    />
    <span class="sr-only">{{ tooltipText }}</span>
  </button>
</template>
