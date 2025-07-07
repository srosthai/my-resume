<script setup lang="ts">
import { cn } from '@/lib/utils'
import { cva, type VariantProps } from 'class-variance-authority'

const dockVariants = cva(
  'mx-auto w-max h-[48px] sm:h-[58px] p-1 sm:p-2 flex gap-1 sm:gap-2 rounded-xl sm:rounded-2xl border supports-backdrop-blur:bg-background/10 supports-backdrop-blur:dark:bg-background/10 backdrop-blur-md',
  {
    variants: {
      direction: {
        top: 'items-end flex-row',
        middle: 'items-center flex-row',
        bottom: 'items-start flex-row',
      },
    },
    defaultVariants: {
      direction: 'middle',
    },
  },
)

export interface DockProps extends /* @vue-ignore */ VariantProps<typeof dockVariants> {
  class?: string
}

defineProps<DockProps>()
</script>

<template>
  <div
    :class="cn(dockVariants({ direction }), $props.class)"
  >
    <slot />
  </div>
</template>