<script setup lang="ts">
import { Dock, DockIcon } from '@/components/magicui';
import ThemeToggle from '@/components/ThemeToggle.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/vue3';
import { Briefcase, Command, FileText, Github, Home, Linkedin, MoreHorizontal, Phone, Send, User } from 'lucide-vue-next';

interface Props {
    currentRoute?: string;
}

const props = withDefaults(defineProps<Props>(), {
    currentRoute: '/',
});

const navigationItems = [
    { href: '/', icon: Home, label: 'Home' },
    { href: '/about', icon: User, label: 'About' },
    { href: '/portfolio', icon: Briefcase, label: 'Projects' },
    { href: '/resume', icon: FileText, label: 'Resume' },
    { href: '/contact', icon: Phone, label: 'Contact' },
    { href: '/hobby', icon: Command, label: 'Hobbies' },
    { href: '/more', icon: MoreHorizontal, label: 'More' },
];

const socialLinks = [
    {
        name: 'GitHub',
        url: 'https://github.com/Sovannthai',
        icon: Github,
    },
    {
        name: 'LinkedIn',
        url: 'https://www.linkedin.com/in/sros-thai-b491b42ab/',
        icon: Linkedin,
    },
    {
        name: 'Telegram',
        url: 'https://t.me/HE_Sovanthai',
        icon: Send,
    },
];

const isActive = (href: string) => {
    return props.currentRoute === href;
};
</script>

<template>
    <div
        class="dock-container fixed top-3 left-1/2 z-50 w-auto max-w-[calc(100vw-1rem)] -translate-x-1/2 transform overflow-x-auto sm:top-6 sm:max-w-[calc(100vw-2rem)]"
    >
        <TooltipProvider>
            <Dock direction="middle" class="border-border/30 bg-background/80 px-1 shadow-xl backdrop-blur-xl md:px-2">
                <!-- Navigation Items -->
                <DockIcon v-for="item in navigationItems" :key="item.label" class="h-10 w-10 border-0 sm:h-12 sm:w-12">
                    <Tooltip>
                        <TooltipTrigger asChild>
                            <Button
                                asChild
                                :variant="isActive(item.href) ? 'default' : 'ghost'"
                                size="icon"
                                :class="
                                    cn(
                                        'h-8 w-8 rounded-full transition-all duration-200 sm:h-10 sm:w-10',
                                        isActive(item.href)
                                            ? 'scale-110 bg-primary text-primary-foreground shadow-lg'
                                            : 'hover:scale-105 hover:bg-accent focus:scale-105 focus:bg-accent focus:ring-2 focus:ring-primary/20 focus:ring-offset-2 focus:ring-offset-background focus:outline-none active:scale-102 active:bg-accent',
                                    )
                                "
                            >
                                <Link :href="item.href" :aria-label="item.label">
                                    <component :is="item.icon" class="h-3 w-3 sm:h-4 sm:w-4" />
                                </Link>
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>
                            <p>{{ item.label }}</p>
                        </TooltipContent>
                    </Tooltip>
                </DockIcon>

                <!-- Separator -->
                <Separator orientation="vertical" class="mx-0.5 hidden h-6 sm:mx-1 sm:block sm:h-8" />

                <!-- Social Links -->
                <DockIcon v-for="social in socialLinks" :key="social.name" class="hidden h-10 w-10 border-0 sm:block sm:h-12 sm:w-12">
                    <Tooltip>
                        <TooltipTrigger asChild>
                            <Button
                                asChild
                                variant="ghost"
                                size="icon"
                                class="h-8 w-8 rounded-full transition-all duration-200 hover:scale-105 hover:bg-accent focus:scale-105 focus:bg-accent focus:ring-2 focus:ring-primary/20 focus:ring-offset-2 focus:ring-offset-background focus:outline-none active:scale-102 active:bg-accent sm:h-10 sm:w-10"
                            >
                                <a :href="social.url" target="_blank" rel="noopener noreferrer" :aria-label="social.name">
                                    <component :is="social.icon" class="h-3 w-3 sm:h-4 sm:w-4" />
                                </a>
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>
                            <p>{{ social.name }}</p>
                        </TooltipContent>
                    </Tooltip>
                </DockIcon>

                <!-- Separator -->
                <Separator orientation="vertical" class="mx-0.5 hidden h-6 sm:mx-1 sm:block sm:h-8" />

                <!-- Theme Toggle -->
                <DockIcon class="h-10 w-10 border-0 sm:h-12 sm:w-12">
                    <Tooltip>
                        <TooltipTrigger asChild>
                            <ThemeToggle
                                class="h-8 w-8 rounded-full transition-all duration-200 hover:scale-105 focus:scale-105 focus:ring-2 focus:ring-primary/20 focus:ring-offset-2 focus:ring-offset-background focus:outline-none active:scale-102 sm:h-10 sm:w-10"
                            />
                        </TooltipTrigger>
                        <TooltipContent>
                            <p>Toggle Theme</p>
                        </TooltipContent>
                    </Tooltip>
                </DockIcon>
            </Dock>
        </TooltipProvider>
    </div>
</template>
