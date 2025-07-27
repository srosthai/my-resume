<script setup lang="ts">
import FloatingDock from './ui/floating-dock.vue';
import { Briefcase, Command, FileText, Home, Phone, User, Github } from 'lucide-vue-next';

interface Props {
    currentRoute?: string;
}

const props = withDefaults(defineProps<Props>(), {
    currentRoute: '/',
});

const links = [
    {
        title: "Home",
        icon: Home,
        href: "/",
        isExternal: false,
    },
    {
        title: "About",
        icon: User,
        href: "/about",
        isExternal: false,
    },
    {
        title: "Projects",
        icon: Briefcase,
        href: "/portfolio",
        isExternal: false,
    },
    {
        title: "Resume",
        icon: FileText,
        href: "/resume",
        isExternal: false,
    },
    {
        title: "Contact",
        icon: Phone,
        href: "/contact",
        isExternal: false,
    },
    {
        title: "Hobbies",
        icon: Command,
        href: "/hobby",
        isExternal: false,
    }
];
</script>

<template>
    <div class="sticky top-0 z-50 flex items-center justify-center w-full py-4 px-4 bg-background/80 backdrop-blur-md border-b border-border/20">
        <FloatingDock 
            :items="links"
            direction="middle"
            className="shadow-xl"
            mobileClassName="max-w-full overflow-x-auto"
        />
    </div>
</template>