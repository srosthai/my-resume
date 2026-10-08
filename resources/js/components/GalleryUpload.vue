<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { computed, onBeforeUnmount, ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        modelValue?: File[];
        existing?: string[];
        max?: number;
        progress?: number | null;
        uploading?: boolean;
        accept?: string;
        id?: string;
        invalid?: boolean;
        hint?: string;
    }>(),
    {
        modelValue: () => [],
        existing: () => [],
        max: 12,
        progress: null,
        uploading: false,
        accept: 'image/jpeg,image/png,image/webp,image/gif',
        invalid: false,
        hint: '',
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: File[]];
    'update:existing': [value: string[]];
}>();

const inputRef = ref<HTMLInputElement | null>(null);
const dragging = ref(false);
const notice = ref('');
const previews = ref<string[]>([]);

const count = computed(() => props.existing.length + props.modelValue.length);
const full = computed(() => count.value >= props.max);
const percent = computed(() =>
    props.progress === null || props.progress === undefined ? null : Math.min(100, Math.max(0, Math.round(props.progress))),
);

watch(
    () => props.modelValue,
    (files) => {
        previews.value.forEach((url) => URL.revokeObjectURL(url));
        previews.value = files.map((file) => URL.createObjectURL(file));
    },
    { immediate: true, deep: true },
);

onBeforeUnmount(() => {
    previews.value.forEach((url) => URL.revokeObjectURL(url));
});

function imageUrl(path: string): string {
    if (path.startsWith('http') || path.startsWith('/')) {
        return path;
    }

    return `/${path}`;
}

function add(files: File[]) {
    const images = files.filter((file) => file.type.startsWith('image/'));
    if (images.length === 0) {
        return;
    }

    const room = props.max - count.value;
    if (images.length > room) {
        notice.value = `You can add up to ${props.max} images.`;
        return;
    }

    notice.value = '';
    emit('update:modelValue', [...props.modelValue, ...images]);
}

function onFile(event: Event) {
    const input = event.target as HTMLInputElement;
    add(Array.from(input.files ?? []));
    input.value = '';
}

function onDrop(event: DragEvent) {
    dragging.value = false;
    add(Array.from(event.dataTransfer?.files ?? []));
}

function removeNew(index: number) {
    emit(
        'update:modelValue',
        props.modelValue.filter((_, item) => item !== index),
    );
}

function removeExisting(index: number) {
    emit(
        'update:existing',
        props.existing.filter((_, item) => item !== index),
    );
}
</script>

<template>
    <div class="space-y-3">
        <input :id="id" ref="inputRef" type="file" class="sr-only" :accept="accept" multiple @change="onFile" />

        <div v-if="uploading" class="rounded-xl border bg-muted/40 px-3 py-2.5">
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

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div v-for="(image, index) in existing" :key="`saved-${image}-${index}`" class="group relative">
                <img :src="imageUrl(image)" :alt="`Saved image ${index + 1}`" class="h-28 w-full rounded-xl border object-cover" />
                <button
                    type="button"
                    class="absolute top-1.5 right-1.5 flex size-6 items-center justify-center rounded-lg bg-background/90 text-foreground shadow-sm hover:bg-destructive hover:text-destructive-foreground"
                    :aria-label="`Remove saved image ${index + 1}`"
                    @click="removeExisting(index)"
                >
                    <Icon name="x" class="size-3.5" />
                </button>
            </div>

            <div v-for="(preview, index) in previews" :key="`new-${preview}`" class="group relative">
                <img :src="preview" :alt="`New image ${index + 1}`" class="h-28 w-full rounded-xl border object-cover" />
                <button
                    type="button"
                    class="absolute top-1.5 right-1.5 flex size-6 items-center justify-center rounded-lg bg-background/90 text-foreground shadow-sm hover:bg-destructive hover:text-destructive-foreground"
                    :aria-label="`Remove new image ${index + 1}`"
                    @click="removeNew(index)"
                >
                    <Icon name="x" class="size-3.5" />
                </button>
            </div>

            <button
                type="button"
                class="flex h-28 flex-col items-center justify-center gap-1 rounded-xl border border-dashed text-sm transition-colors"
                :class="
                    dragging
                        ? 'border-primary bg-primary/5 text-foreground'
                        : invalid
                          ? 'border-destructive text-muted-foreground'
                          : 'border-input text-muted-foreground hover:border-primary/40 hover:text-foreground'
                "
                :disabled="full"
                @click="inputRef?.click()"
                @dragover.prevent="dragging = true"
                @dragleave.prevent="dragging = false"
                @drop.prevent="onDrop"
            >
                <Icon name="imagePlus" class="size-5" />
                <span>{{ full ? 'Limit reached' : 'Add images' }}</span>
            </button>
        </div>

        <p class="text-sm text-muted-foreground">
            {{ count }} of {{ max }}
            <template v-if="hint"> · {{ hint }}</template>
        </p>
        <p v-if="notice" class="text-sm text-destructive">{{ notice }}</p>
    </div>
</template>
