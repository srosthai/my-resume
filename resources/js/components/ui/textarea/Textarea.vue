<script setup>
import { cn } from '@/lib/utils'

const props = defineProps({
  class: {
    type: String,
    default: ''
  },
  id: String,
  placeholder: String,
  disabled: Boolean,
  readonly: Boolean,
  required: Boolean,
  rows: {
    type: Number,
    default: 3
  },
  cols: Number,
  maxlength: Number,
  minlength: Number,
  name: String,
  form: String,
  autocomplete: String,
  autofocus: Boolean,
  modelValue: String
})

const emit = defineEmits(['update:modelValue'])

const handleInput = (event) => {
  emit('update:modelValue', event.target.value)
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