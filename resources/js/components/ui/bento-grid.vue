<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  className?: string
}

const props = withDefaults(defineProps<Props>(), {
  className: ''
})

const gridClasses = computed(() => {
  return [
    'grid md:auto-rows-[18rem] grid-cols-1 md:grid-cols-3 gap-4 max-w-7xl mx-auto',
    props.className
  ].filter(Boolean).join(' ')
})
</script>

<template>
  <div :class="gridClasses">
    <slot />
  </div>
</template>