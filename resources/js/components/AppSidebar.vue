<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookUser, LayoutGrid, Users, GraduationCap, Briefcase, Layers, FolderOpenDot, Boxes, Music4 } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'ME',
        href: '/me',
        icon: Users,
    },
    {
        title: 'About Me',
        href: '/about-me',
        icon: BookUser,
    },
    {
        title: 'Work Experience',
        href: '/work-experience',
        icon: Briefcase,
    },
    {
        title: 'Education',
        href: '/eductions',
        icon: GraduationCap,
    },
    {
        title: 'Tech Stack',
        href: '/tech-stacks',
        icon: Layers,
    },
    {
        title: 'Project Types',
        href: '/project-types',
        icon: Boxes,
    },
    {
        title: 'Projects',
        href: '/projects',
        icon: FolderOpenDot,
    },
    {
        title: 'Music',
        href: '/popular-songs',
        icon: Music4,
    }
];

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
