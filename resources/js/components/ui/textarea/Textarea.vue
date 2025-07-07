<script setup lang="ts">
import { cn } from '@/lib/utils'

export interface TextareaProps {
  class?: string
  id?: string
  placeholder?: string
  disabled?: boolean
  readonly?: boolean
  required?: boolean
  rows?: number
  cols?: number
  maxlength?: number
  minlength?: string
  name?: string
  form?: string
  autocomplete?: string
  autofocus?: boolean
  wrap?: 'hard' | 'soft'
  resize?: 'none' | 'both' | 'horizontal' | 'vertical'
}

defineProps<TextareaProps>()

defineEmits<{
  'update:modelValue': [value: string]
}>()
</script>

<template>
  <textarea
    :class="cn(
      'flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
      $props.class,
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
    :wrap="wrap"
    :style="resize ? { resize } : undefined"
    @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
  />
</template>