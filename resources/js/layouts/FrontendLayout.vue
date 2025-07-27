<script setup lang="ts">
import DockNavigation from '@/components/DockNavigation.vue'
import ScrollToTopButton from '@/components/ui/ScrollToTopButton.vue'
import MusicPlayer from '@/components/MusicPlayer.vue'

interface Props {
    currentRoute?: string
}

const props = withDefaults(defineProps<Props>(), {
    currentRoute: '/'
})
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-background via-background/95 to-background text-foreground font-sans">
        <!-- Sticky Dock Navigation -->
        <DockNavigation :currentRoute="currentRoute" />
        
        <!-- Page Content -->
        <main>
            <slot />
        </main>
        
        <!-- Music Player -->
        <MusicPlayer />
        
        <!-- Scroll to Top Button -->
        <ScrollToTopButton />
    </div>
</template>