<script setup lang="ts">
import { computed } from 'vue'
import { cn } from '@/lib/utils'

interface Props {
  direction?: 'top' | 'middle' | 'bottom'
  className?: string
}

const props = withDefaults(defineProps<Props>(), {
  direction: 'middle',
  className: ''
})

const dockClasses = computed(() => {
  const baseClasses = 'mx-auto flex h-[58px] items-center gap-1 rounded-full border p-2 backdrop-blur-md transition-all duration-300'
  const directionClasses = {
    top: 'bg-white/10 dark:bg-black/10 border-white/20 dark:border-white/20',
    middle: 'bg-white/10 dark:bg-black/10 border-white/20 dark:border-white/20',
    bottom: 'bg-white/10 dark:bg-black/10 border-white/20 dark:border-white/20'
  }
  
  return cn(
    baseClasses,
    directionClasses[props.direction],
    'supports-[backdrop-filter]:bg-white/10 supports-[backdrop-filter]:dark:bg-black/10',
    'shadow-lg hover:shadow-xl',
    // Responsive sizing
    'max-w-fit px-2 sm:px-3 md:px-4',
    'h-12 sm:h-14 md:h-[58px]',
    props.className
  )
})
</script>

<template>
  <div :class="dockClasses">
    <slot />
  </div>
</template>