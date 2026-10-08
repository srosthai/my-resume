<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { onBeforeUnmount, watch } from 'vue';

const props = defineProps<{
    messages: string[] | null;
    tone?: 'error' | 'success';
}>();

const emit = defineEmits<{
    close: [];
}>();

let timer: ReturnType<typeof setTimeout> | null = null;

watch(
    () => props.messages?.join('\n') ?? '',
    (text) => {
        if (timer) {
            clearTimeout(timer);
            timer = null;
        }

        if (!text) {
            return;
        }

        timer = setTimeout(() => emit('close'), 7000);
    },
);

onBeforeUnmount(() => {
    if (timer) {
        clearTimeout(timer);
    }
});
</script>

<template>
    <div v-if="messages?.length" class="pointer-events-none fixed inset-x-0 bottom-4 z-50 flex justify-center px-4">
        <div
            class="pointer-events-auto flex w-full max-w-md items-start gap-3 rounded-xl border bg-card px-4 py-3 shadow-lg"
            :class="tone === 'success' ? 'border-border' : 'border-destructive/40'"
            :role="tone === 'success' ? 'status' : 'alert'"
        >
            <Icon
                :name="tone === 'success' ? 'circleCheck' : 'circleAlert'"
                class="mt-0.5 size-4 shrink-0"
                :class="tone === 'success' ? 'text-foreground' : 'text-destructive'"
            />
            <div class="min-w-0 flex-1 space-y-1">
                <p v-for="message in messages" :key="message" class="text-sm text-foreground">
                    {{ message }}
                </p>
            </div>
            <button type="button" class="rounded-md p-1 text-muted-foreground hover:text-foreground" aria-label="Dismiss" @click="emit('close')">
                <Icon name="x" class="size-4" />
            </button>
        </div>
    </div>
</template>
