<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  className?: string
  title?: string
  description?: string
  icon?: any
}

const props = withDefaults(defineProps<Props>(), {
  className: '',
  title: '',
  description: ''
})

const itemClasses = computed(() => {
  return [
    'row-span-1 rounded-xl group/bento hover:shadow-xl transition duration-200 shadow-input dark:shadow-none p-4 dark:bg-black dark:border-white/[0.2] bg-white border border-transparent justify-between flex flex-col space-y-4 cursor-pointer',
    props.className
  ].filter(Boolean).join(' ')
})
</script>

<template>
  <div :class="itemClasses">
    <slot name="header" />
    <div class="group-hover/bento:translate-x-2 transition duration-200">
      <div class="flex items-center space-x-2 mb-2" v-if="icon || title">
        <component :is="icon" v-if="icon" />
        <div class="font-sans font-bold text-neutral-600 dark:text-neutral-200" v-if="title">
          {{ title }}
        </div>
      </div>
      <div class="font-sans font-normal text-neutral-600 text-xs dark:text-neutral-300" v-if="description">
        {{ description }}
      </div>
    </div>
  </div>
</template>