<script setup lang="ts">
import DockNavigation from '@/components/DockNavigation.vue';
import MusicPlayer from '@/components/MusicPlayer.vue';
import SiteBackdrop from '@/components/SiteBackdrop.vue';
import ScrollToTopButton from '@/components/ui/ScrollToTopButton.vue';

interface Props {
    currentRoute?: string;
}

withDefaults(defineProps<Props>(), {
    currentRoute: '/',
});
</script>

<template>
    <div class="relative min-h-screen bg-gradient-to-br from-background via-background/95 to-background font-sans text-foreground">
        <!-- Graph-paper grid behind every page -->
        <SiteBackdrop />

        <!-- Sticky Dock Navigation -->
        <DockNavigation :currentRoute="currentRoute" />

        <!-- Page Content -->
        <main class="relative z-10 pb-24 md:pb-0">
            <slot />
        </main>

        <!-- Music Player -->
        <MusicPlayer />

        <!-- Scroll to Top Button -->
        <ScrollToTopButton />
    </div>
</template>
