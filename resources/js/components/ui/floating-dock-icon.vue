<script setup lang="ts">
import { ref, computed } from 'vue'

interface DockItem {
  title: string
  icon: any
  href: string
}

interface Props {
  mouseX: number | null
  item: DockItem
}

const props = defineProps<Props>()
const iconRef = ref<HTMLElement>()

const distance = computed(() => {
  if (!props.mouseX || !iconRef.value) return 0
  
  const rect = iconRef.value.getBoundingClientRect()
  const iconCenterX = rect.left + rect.width / 2
  const dockRect = iconRef.value.closest('[ref="dockRef"]')?.getBoundingClientRect()
  if (!dockRect) return 0
  
  return Math.abs((props.mouseX + dockRect.left) - iconCenterX)
})

const scale = computed(() => {
  if (!props.mouseX) return 1
  
  const maxDistance = 120
  const maxScale = 1.3
  const minScale = 1
  
  if (distance.value > maxDistance) return minScale
  
  const scaleFactor = 1 - distance.value / maxDistance
  return minScale + (maxScale - minScale) * scaleFactor
})

const iconStyle = computed(() => ({
  transform: `scale(${scale.value})`,
  transition: props.mouseX ? 'transform 0.2s ease-out' : 'transform 0.3s ease-out'
}))
</script>

<template>
  <div
    ref="iconRef"
    class="aspect-square cursor-pointer rounded-full border bg-gray-200 dark:bg-neutral-800 p-3 transition-colors hover:bg-gray-300 dark:hover:bg-neutral-700"
    :style="iconStyle"
  >
    <a
      :href="item.href"
      class="flex h-full w-full items-center justify-center"
      :title="item.title"
      :target="item.href.startsWith('http') ? '_blank' : '_self'"
      :rel="item.href.startsWith('http') ? 'noopener noreferrer' : undefined"
    >
      <component :is="item.icon" class="h-full w-full text-neutral-500 dark:text-neutral-300" />
    </a>
  </div>
</template>