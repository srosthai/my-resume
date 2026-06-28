<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useAppearance } from '@/composables/useAppearance';
import { Moon, Sun } from 'lucide-vue-next';
import { computed } from 'vue';

const { appearance, updateAppearance } = useAppearance();

const currentIcon = computed(() => (appearance.value === 'dark' ? Moon : Sun));

const label = computed(() =>
    appearance.value === 'dark' ? 'Dark mode active. Switch to light mode' : 'Light mode active. Switch to dark mode',
);

const toggleAppearance = () => {
    updateAppearance(appearance.value === 'dark' ? 'light' : 'dark');
};
</script>

<template>
    <Button
        variant="ghost"
        size="icon"
        class="h-9 w-9 border border-border/50 bg-background/50 backdrop-blur-sm transition-all duration-200 hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground focus:ring-2 focus:ring-primary/20 focus:ring-offset-2 focus:ring-offset-background focus:outline-none active:bg-accent active:text-accent-foreground"
        :aria-label="label"
        :title="label"
        @click="toggleAppearance"
    >
        <component :is="currentIcon" class="h-4 w-4 transition-all duration-200" />
        <span class="sr-only">{{ label }}</span>
    </Button>
</template>
