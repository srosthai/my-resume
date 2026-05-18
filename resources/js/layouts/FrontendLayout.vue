<script setup lang="ts">
import DockNavigation from '@/components/DockNavigation.vue';
import MusicPlayer from '@/components/MusicPlayer.vue';
import ScrollToTopButton from '@/components/ui/ScrollToTopButton.vue';

interface Props {
    currentRoute?: string;
}

const props = withDefaults(defineProps<Props>(), {
    currentRoute: '/',
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-background via-background/95 to-background font-sans text-foreground">
        <!-- Sticky Dock Navigation -->
        <DockNavigation :currentRoute="currentRoute" />

        <!-- Page Content -->
        <main class="pb-24 md:pb-0">
            <slot />
        </main>

        <!-- Music Player -->
        <MusicPlayer />

        <!-- Scroll to Top Button -->
        <ScrollToTopButton />
    </div>
</template>
