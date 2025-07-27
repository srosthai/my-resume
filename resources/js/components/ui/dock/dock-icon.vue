<script setup lang="ts">
import { ref, computed } from 'vue'

interface Props {
  className?: string
}

const props = withDefaults(defineProps<Props>(), {
  className: ''
})

const iconRef = ref<HTMLElement>()

const iconClasses = computed(() => {
  return [
    'flex aspect-square cursor-pointer items-center justify-center rounded-full',
    props.className
  ].filter(Boolean).join(' ')
})
</script>

<template>
  <div
    ref="iconRef"
    :class="iconClasses"
  >
    <slot />
  </div>
</template>