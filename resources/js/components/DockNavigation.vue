<script setup lang="ts">
import { Briefcase, Command, Home, Phone, Rss, User } from 'lucide-vue-next';
import FloatingDock from './ui/floating-dock.vue';

interface Props {
    currentRoute?: string;
}

const props = withDefaults(defineProps<Props>(), {
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
        class="fixed inset-x-0 bottom-0 z-50 flex w-full items-center justify-center px-3 pt-3 pb-[calc(env(safe-area-inset-bottom)+0.75rem)] md:sticky md:top-0 md:bottom-auto md:border-b md:border-border/20 md:bg-background/80 md:px-4 md:py-4 md:backdrop-blur-md"
    >
        <FloatingDock
            :items="links"
            :current-route="currentRoute"
            direction="middle"
            :show-theme-toggle="false"
            className="shadow-xl"
            mobileClassName="max-w-full overflow-x-auto backdrop-blur-xl"
        />
    </div>
</template>
