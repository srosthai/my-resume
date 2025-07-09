<script setup lang="ts">
import { SelectRoot, type SelectRootProps, type SelectRootEmits, useForwardPropsEmits } from 'reka-ui'

interface Props extends SelectRootProps {
  modelValue?: string | null
}

const props = defineProps<Props>()

const emits = defineEmits<SelectRootEmits & {
  'update:modelValue': [value: string | null]
}>()

const forwarded = useForwardPropsEmits(props, emits)

const handleValueChange = (value: string) => {
  emits('update:modelValue', value)
}
</script>

<template>
  <SelectRoot
    :model-value="modelValue || undefined"
    @update:model-value="handleValueChange"
    v-bind="forwarded"
  >
    <slot />
  </SelectRoot>
</template>