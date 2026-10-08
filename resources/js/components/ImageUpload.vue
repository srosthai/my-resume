<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { cn } from '@/lib/utils';
import { computed, onBeforeUnmount, ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        modelValue?: File | null;
        currentUrl?: string | null;
        removed?: boolean;
        progress?: number | null;
        uploading?: boolean;
        accept?: string;
        id?: string;
        invalid?: boolean;
        hint?: string;
        emptyLabel?: string;
        allowRemove?: boolean;
    }>(),
    {
        modelValue: null,
        currentUrl: null,
        removed: false,
        progress: null,
        uploading: false,
        accept: 'image/jpeg,image/png,image/webp,image/gif',
        invalid: false,
        hint: 'JPEG, PNG, GIF, or WebP. Max 2 MB.',
        emptyLabel: 'Choose an image',
        allowRemove: true,
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: File | null];
    'update:removed': [value: boolean];
}>();

const inputRef = ref<HTMLInputElement | null>(null);
const dragging = ref(false);
const previewUrl = ref<string | null>(null);

const shownUrl = computed(() => previewUrl.value || (!props.removed ? props.currentUrl : null));
const percent = computed(() =>
    props.progress === null || props.progress === undefined ? null : Math.min(100, Math.max(0, Math.round(props.progress))),
);

watch(
    () => props.modelValue,
    (file) => {
        if (previewUrl.value) {
            URL.revokeObjectURL(previewUrl.value);
        }
        previewUrl.value = file ? URL.createObjectURL(file) : null;
    },
    { immediate: true },
);

onBeforeUnmount(() => {
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
    }
});

function choose(file: File | null) {
    emit('update:modelValue', file);
    if (file) {
        emit('update:removed', false);
    }
}

function onFile(event: Event) {
    const input = event.target as HTMLInputElement;
    choose(input.files?.[0] ?? null);
    input.value = '';
}

function onDrop(event: DragEvent) {
    dragging.value = false;
    const file = event.dataTransfer?.files?.[0] ?? null;
    if (file && file.type.startsWith('image/')) {
        choose(file);
    }
}

function clearSelection() {
    choose(null);
    if (!props.currentUrl) {
        emit('update:removed', false);
    }
}

function removeCurrent() {
    choose(null);
    emit('update:removed', true);
}
</script>

<template>
    <div class="space-y-2">
        <div
            :class="
                cn(
                    'relative overflow-hidden rounded-2xl border border-dashed bg-muted/30 transition-colors',
                    dragging ? 'border-primary bg-primary/5' : 'border-input',
                    invalid && 'border-destructive',
                )
            "
            @dragover.prevent="dragging = true"
            @dragleave.prevent="dragging = false"
            @drop.prevent="onDrop"
        >
            <input :id="id" ref="inputRef" type="file" class="sr-only" :accept="accept" @change="onFile" />

            <div v-if="shownUrl" class="relative">
                <img :src="shownUrl" alt="" class="h-52 w-full object-cover" :class="uploading ? 'opacity-70' : ''" />
                <div v-if="uploading" class="absolute inset-x-0 bottom-0 bg-background/85 px-4 py-3 backdrop-blur-sm">
                    <div class="mb-1.5 flex items-center justify-between text-xs font-medium">
                        <span>Uploading</span>
                        <span v-if="percent !== null">{{ percent }}%</span>
                    </div>
                    <div class="h-1.5 overflow-hidden rounded-full bg-muted">
                        <div
                            v-if="percent !== null"
                            class="h-full rounded-full bg-primary transition-[width] duration-150"
                            :style="{ width: `${percent}%` }"
                        />
                        <div v-else class="h-full w-1/3 animate-pulse rounded-full bg-primary" />
                    </div>
                </div>
                <div
                    v-else
                    class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-2 bg-gradient-to-t from-black/70 to-transparent px-3 py-3"
                >
                    <p class="truncate text-sm text-white">{{ modelValue?.name || 'Saved image' }}</p>
                    <div class="flex shrink-0 gap-1">
                        <button
                            type="button"
                            class="rounded-lg bg-white/15 px-2.5 py-1 text-xs font-medium text-white hover:bg-white/25"
                            @click="inputRef?.click()"
                        >
                            Replace
                        </button>
                        <button
                            v-if="modelValue || allowRemove"
                            type="button"
                            class="rounded-lg bg-white/15 px-2.5 py-1 text-xs font-medium text-white hover:bg-white/25"
                            @click="modelValue ? clearSelection() : removeCurrent()"
                        >
                            Remove
                        </button>
                    </div>
                </div>
            </div>

            <button v-else type="button" class="flex w-full flex-col items-center gap-2 px-6 py-10 text-center" @click="inputRef?.click()">
                <span class="flex size-11 items-center justify-center rounded-2xl bg-background text-muted-foreground shadow-xs">
                    <Icon name="imagePlus" class="size-5" />
                </span>
                <span class="text-sm font-medium">{{ emptyLabel }}</span>
                <span class="text-xs text-muted-foreground">or drop it here</span>
            </button>
        </div>
        <p v-if="hint" class="text-sm text-muted-foreground">{{ hint }}</p>
    </div>
</template>
