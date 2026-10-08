<script setup lang="ts" generic="T extends string">
import Icon from '@/components/Icon.vue';
import { cn } from '@/lib/utils';
import { PopoverContent, PopoverPortal, PopoverRoot, PopoverTrigger } from 'reka-ui';
import { computed, nextTick, ref, useId, watch } from 'vue';

export interface SearchSelectOption<T extends string = string> {
    value: T;
    label: string;
    disabled?: boolean;
}

const props = withDefaults(
    defineProps<{
        modelValue?: T | null;
        options: SearchSelectOption<T>[];
        placeholder?: string;
        searchPlaceholder?: string;
        emptyText?: string;
        id?: string;
        disabled?: boolean;
        invalid?: boolean;
        clearable?: boolean;
    }>(),
    {
        modelValue: null,
        placeholder: 'Select',
        searchPlaceholder: 'Search',
        emptyText: 'No matches',
        disabled: false,
        invalid: false,
        clearable: false,
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: T | null];
}>();

const open = ref(false);
const query = ref('');
const activeIndex = ref(0);
const searchRef = ref<HTMLInputElement | null>(null);
const listRef = ref<HTMLElement | null>(null);
const listId = useId();

const selected = computed(() => props.options.find((option) => option.value === props.modelValue));

const filtered = computed(() => {
    const term = query.value.trim().toLowerCase();
    if (!term) {
        return props.options;
    }

    return props.options.filter((option) => option.label.toLowerCase().includes(term));
});

const activeOptionId = computed(() => {
    const option = filtered.value[activeIndex.value];
    return option ? `${listId}-${option.value}` : undefined;
});

watch(open, (isOpen) => {
    if (!isOpen) {
        query.value = '';
        return;
    }

    const selectedIndex = filtered.value.findIndex((option) => option.value === props.modelValue);
    activeIndex.value = selectedIndex >= 0 ? selectedIndex : 0;
});

function focusSearch(event: Event) {
    event.preventDefault();
    nextTick(() => {
        searchRef.value?.focus();
        listRef.value?.querySelector('[data-active="true"]')?.scrollIntoView({ block: 'nearest' });
    });
}

function onQueryInput() {
    activeIndex.value = 0;
}

function move(step: number) {
    if (filtered.value.length === 0) {
        return;
    }

    let index = activeIndex.value;
    for (let attempt = 0; attempt < filtered.value.length; attempt += 1) {
        index = (index + step + filtered.value.length) % filtered.value.length;
        if (!filtered.value[index]?.disabled) {
            activeIndex.value = index;
            nextTick(() => listRef.value?.querySelector('[data-active="true"]')?.scrollIntoView({ block: 'nearest' }));
            return;
        }
    }
}

function select(option: SearchSelectOption<T>) {
    if (option.disabled) {
        return;
    }

    emit('update:modelValue', option.value);
    open.value = false;
}

function clear() {
    emit('update:modelValue', null);
    open.value = false;
}

function onSearchKeydown(event: KeyboardEvent) {
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        move(1);
        return;
    }

    if (event.key === 'ArrowUp') {
        event.preventDefault();
        move(-1);
        return;
    }

    if (event.key === 'Enter') {
        event.preventDefault();
        const option = filtered.value[activeIndex.value];
        if (option) {
            select(option);
        }
        return;
    }

    if (event.key === 'Escape') {
        event.preventDefault();
        open.value = false;
    }
}
</script>

<template>
    <PopoverRoot v-model:open="open">
        <div class="relative">
            <PopoverTrigger as-child>
                <button
                    :id="id"
                    type="button"
                    role="combobox"
                    :aria-expanded="open"
                    :aria-controls="listId"
                    :aria-invalid="invalid || undefined"
                    :disabled="disabled"
                    :class="
                        cn(
                            'flex h-10 w-full items-center justify-between gap-2 rounded-xl border border-input bg-background px-3 text-left text-sm shadow-xs transition-colors outline-none',
                            'focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50',
                            'disabled:cursor-not-allowed disabled:opacity-50',
                            invalid && 'border-destructive ring-destructive/20',
                        )
                    "
                >
                    <span class="truncate" :class="[selected ? 'text-foreground' : 'text-muted-foreground', clearable && modelValue ? 'pr-6' : '']">
                        {{ selected?.label ?? placeholder }}
                    </span>
                    <Icon name="chevronDown" class="size-4 shrink-0 text-muted-foreground transition-transform" :class="open ? 'rotate-180' : ''" />
                </button>
            </PopoverTrigger>
            <button
                v-if="clearable && modelValue"
                type="button"
                class="absolute top-1/2 right-8 flex size-6 -translate-y-1/2 items-center justify-center rounded-md text-muted-foreground hover:bg-muted hover:text-foreground"
                aria-label="Clear selection"
                @click="clear"
            >
                <Icon name="x" class="size-3.5" />
            </button>
        </div>

        <PopoverPortal>
            <PopoverContent
                align="start"
                :side-offset="6"
                :collision-padding="8"
                class="z-50 w-[var(--reka-popper-anchor-width)] overflow-hidden rounded-xl border bg-popover text-popover-foreground shadow-lg"
                @open-auto-focus="focusSearch"
            >
                <div class="flex items-center gap-2 border-b px-3">
                    <Icon name="search" class="size-4 shrink-0 text-muted-foreground" />
                    <input
                        ref="searchRef"
                        v-model="query"
                        type="text"
                        role="searchbox"
                        autocomplete="off"
                        :placeholder="searchPlaceholder"
                        :aria-controls="listId"
                        :aria-activedescendant="activeOptionId"
                        class="h-10 w-full bg-transparent text-sm outline-none placeholder:text-muted-foreground"
                        @input="onQueryInput"
                        @keydown="onSearchKeydown"
                    />
                </div>
                <div :id="listId" ref="listRef" role="listbox" class="max-h-60 overflow-y-auto p-1">
                    <p v-if="filtered.length === 0" class="px-3 py-6 text-center text-sm text-muted-foreground">
                        {{ emptyText }}
                    </p>
                    <button
                        v-for="(option, index) in filtered"
                        :id="`${listId}-${option.value}`"
                        :key="option.value"
                        type="button"
                        role="option"
                        :aria-selected="option.value === modelValue"
                        :data-active="index === activeIndex ? 'true' : undefined"
                        :disabled="option.disabled"
                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        :class="index === activeIndex ? 'bg-accent text-accent-foreground' : 'text-foreground'"
                        @mouseenter="activeIndex = index"
                        @click="select(option)"
                    >
                        <span class="min-w-0 flex-1 truncate">{{ option.label }}</span>
                        <Icon v-if="option.value === modelValue" name="check" class="size-4 shrink-0 text-primary" />
                    </button>
                </div>
            </PopoverContent>
        </PopoverPortal>
    </PopoverRoot>
</template>
