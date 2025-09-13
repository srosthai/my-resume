<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

interface Props {
  spread?: number
  glow?: boolean
  disabled?: boolean
  proximity?: number
  inactiveZone?: number
}

const props = withDefaults(defineProps<Props>(), {
  spread: 40,
  glow: true,
  disabled: false,
  proximity: 64,
  inactiveZone: 0.01
})

const containerRef = ref<HTMLElement>()
const mouseX = ref(0)
const mouseY = ref(0)
const isHovering = ref(false)

const handleMouseMove = (e: MouseEvent) => {
  if (!containerRef.value || props.disabled) return

  const rect = containerRef.value.getBoundingClientRect()
  const x = e.clientX - rect.left
  const y = e.clientY - rect.top
  
  mouseX.value = x
  mouseY.value = y
}

const handleMouseEnter = () => {
  isHovering.value = true
}

const handleMouseLeave = () => {
  isHovering.value = false
}

onMounted(() => {
  if (containerRef.value) {
    containerRef.value.addEventListener('mousemove', handleMouseMove)
    containerRef.value.addEventListener('mouseenter', handleMouseEnter)
    containerRef.value.addEventListener('mouseleave', handleMouseLeave)
  }
})

onUnmounted(() => {
  if (containerRef.value) {
    containerRef.value.removeEventListener('mousemove', handleMouseMove)
    containerRef.value.removeEventListener('mouseenter', handleMouseEnter)
    containerRef.value.removeEventListener('mouseleave', handleMouseLeave)
  }
})
</script>

<template>
  <div 
    ref="containerRef"
    class="absolute inset-0 overflow-hidden rounded-2xl md:rounded-3xl"
    :class="{ 'pointer-events-none': disabled }"
  >
    <div
      v-if="glow && isHovering && !disabled"
      class="absolute opacity-50 blur-xl transition-opacity duration-300"
      :style="{
        left: `${mouseX - spread}px`,
        top: `${mouseY - spread}px`,
        width: `${spread * 2}px`,
        height: `${spread * 2}px`,
        background: 'radial-gradient(circle, rgba(59, 130, 246, 0.5) 0%, transparent 70%)',
        transform: 'translate(-50%, -50%)'
      }"
    />
    <div
      v-if="isHovering && !disabled"
      class="absolute opacity-30 transition-opacity duration-300"
      :style="{
        left: `${mouseX - proximity}px`,
        top: `${mouseY - proximity}px`,
        width: `${proximity * 2}px`,
        height: `${proximity * 2}px`,
        background: 'radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, transparent 70%)',
        transform: 'translate(-50%, -50%)'
      }"
    />
  </div>
</template>