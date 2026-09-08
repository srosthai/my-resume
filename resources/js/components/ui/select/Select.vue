<script setup lang="ts">
import { reactiveOmit } from '@vueuse/core'
import { SelectRoot, type SelectRootProps, useForwardPropsEmits } from 'reka-ui'

interface Props extends Omit<SelectRootProps, 'modelValue'> {
  modelValue?: string | null
}

const props = defineProps<Props>()

const emits = defineEmits<{
  'update:modelValue': [value: string | null]
  'update:open': [value: boolean]
}>()

// `modelValue` is bound explicitly below, so keep it out of the forwarded props to avoid binding it twice.
const delegatedProps = reactiveOmit(props, 'modelValue')

const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
  <SelectRoot
    v-bind="forwarded"
    :model-value="modelValue || undefined"
  >
    <slot />
  </SelectRoot>
</template>
