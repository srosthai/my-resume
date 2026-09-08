<script setup lang="ts">
import { cn } from '@/lib/utils'
import type { HTMLAttributes } from 'vue'

const props = withDefaults(
  defineProps<{
    class?: HTMLAttributes['class']
    id?: string
    placeholder?: string
    disabled?: boolean
    readonly?: boolean
    required?: boolean
    rows?: number | string
    cols?: number
    maxlength?: number
    minlength?: number
    name?: string
    form?: string
    autocomplete?: string
    autofocus?: boolean
    modelValue?: string
  }>(),
  {
    class: '',
    rows: 3,
  },
)

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const handleInput = (event: Event) => {
  emit('update:modelValue', (event.target as HTMLTextAreaElement).value)
}
</script>

<template>
  <textarea
    :class="cn(
      'flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
      props.class,
    )"
    :id="id"
    :placeholder="placeholder"
    :disabled="disabled"
    :readonly="readonly"
    :required="required"
    :rows="rows"
    :cols="cols"
    :maxlength="maxlength"
    :minlength="minlength"
    :name="name"
    :form="form"
    :autocomplete="autocomplete"
    :autofocus="autofocus"
    :value="modelValue"
    @input="handleInput"
  />
</template>
