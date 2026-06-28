<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { Moon, Sun } from 'lucide-vue-next';

const { appearance, updateAppearance } = useAppearance();

const tabs = [
    { value: 'light', Icon: Sun, label: 'Light' },
    { value: 'dark', Icon: Moon, label: 'Dark' },
] as const;
</script>

<template>
    <div class="inline-flex gap-1 rounded-lg border bg-muted/70 p-1">
        <button
            v-for="{ value, Icon, label } in tabs"
            :key="value"
            @click="updateAppearance(value)"
            :class="[
                'flex items-center rounded-md px-3 py-1.5 text-sm font-medium transition-colors',
                appearance === value
                    ? 'bg-background text-foreground shadow-xs'
                    : 'text-muted-foreground hover:bg-background/60 hover:text-foreground',
            ]"
            type="button"
        >
            <component :is="Icon" class="mr-1.5 size-4" />
            {{ label }}
        </button>
    </div>
</template>
