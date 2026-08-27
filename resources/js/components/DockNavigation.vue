<script setup lang="ts">
import { Briefcase, Command, Home, Phone, Rss, User } from 'lucide-vue-next';
import FloatingDock from './ui/floating-dock.vue';

interface Props {
    currentRoute?: string;
}

withDefaults(defineProps<Props>(), {
    currentRoute: '/',
});

const links = [
    {
        title: 'Home',
        icon: Home,
        href: '/',
        isExternal: false,
    },
    {
        title: 'About',
        icon: User,
        href: '/about',
        isExternal: false,
    },
    {
        title: 'Projects',
        icon: Briefcase,
        href: '/portfolio',
        isExternal: false,
    },
    {
        title: 'Contact',
        icon: Phone,
        href: '/contact',
        isExternal: false,
    },
    {
        title: 'Theme',
        icon: null,
        href: '#theme',
        isExternal: false,
        isTheme: true,
    },
    {
        title: 'Hobbies',
        icon: Command,
        href: '/hobby',
        isExternal: false,
    },
    {
        title: 'Feeds',
        icon: Rss,
        href: '/feeds',
        isExternal: false,
    },
];
</script>

<template>
    <div
        class="pointer-events-none fixed inset-x-0 bottom-0 z-50 flex w-full items-center justify-center px-3 pt-3 pb-[calc(env(safe-area-inset-bottom)+0.75rem)] md:sticky md:top-0 md:bottom-auto md:px-4 md:py-4"
    >
        <!-- Only the pill is a surface; the strip around it is transparent and lets clicks through -->
        <FloatingDock
            class="pointer-events-auto"
            :items="links"
            :current-route="currentRoute"
            direction="middle"
            :show-theme-toggle="false"
            className="card-3d dock-3d bg-background/80 backdrop-blur-xl supports-[backdrop-filter]:bg-background/70 dark:bg-background/80 supports-[backdrop-filter]:dark:bg-background/70"
            mobileClassName="card-3d dock-3d max-w-full overflow-x-auto backdrop-blur-xl"
        />
    </div>
</template>
