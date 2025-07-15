<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { useAppearance } from '@/composables/useAppearance';
import { Monitor, Moon, Sun } from 'lucide-vue-next';
import { computed } from 'vue';

const { appearance, updateAppearance } = useAppearance();

const themes = [
    { key: 'light' as const, label: 'Light', icon: Sun },
    { key: 'dark' as const, label: 'Dark', icon: Moon },
    { key: 'system' as const, label: 'System', icon: Monitor },
];

const currentTheme = computed(() => themes.find((theme) => theme.key === appearance.value) || themes[2]);
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger asChild>
            <Button
                variant="ghost"
                size="icon"
                class="h-9 w-9 border border-border/50 bg-background/50 backdrop-blur-sm transition-all duration-200 hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground focus:ring-2 focus:ring-primary/20 focus:ring-offset-2 focus:ring-offset-background focus:outline-none active:bg-accent active:text-accent-foreground"
            >
                <component :is="currentTheme.icon" class="h-4 w-4 transition-all duration-200" />
                <span class="sr-only">Toggle theme</span>
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="end" class="w-40 border border-border/50 bg-background/95 backdrop-blur-sm">
            <DropdownMenuItem
                v-for="theme in themes"
                :key="theme.key"
                @click="updateAppearance(theme.key)"
                class="flex cursor-pointer items-center gap-2 transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground focus:outline-none active:bg-accent active:text-accent-foreground"
                :class="{ 'bg-accent text-accent-foreground': appearance === theme.key }"
            >
                <component :is="theme.icon" class="h-4 w-4" />
                <span>{{ theme.label }}</span>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
