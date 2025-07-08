<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { SelectValue, type SelectValueProps, useForwardProps } from 'reka-ui'
import { cn } from '@/lib/utils'

interface Props extends SelectValueProps {
  class?: HTMLAttributes['class']
  placeholder?: string
}

const props = defineProps<Props>()
const forwardedProps = useForwardProps(props)
</script>

<template>
  <SelectValue
    :class="cn('text-sm', props.class)"
    v-bind="forwardedProps"
  />
</template>