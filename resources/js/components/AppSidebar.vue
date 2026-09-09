<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookUser, Boxes, Briefcase, FolderOpenDot, GraduationCap, Layers, LayoutGrid, Music4, Rss, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
        icon: LayoutGrid,
    },
    {
        title: 'ME',
        href: route('backend.users.index'),
        icon: Users,
    },
    {
        title: 'About Me',
        href: route('backend.about-me.index'),
        icon: BookUser,
    },
    {
        title: 'Work Experience',
        href: route('backend.work-experience.index'),
        icon: Briefcase,
    },
    {
        title: 'Education',
        href: route('backend.education.index'),
        icon: GraduationCap,
    },
    {
        title: 'Tech Stack',
        href: route('backend.tech-stacks.index'),
        icon: Layers,
    },
    {
        title: 'Project Types',
        href: route('backend.project-types.index'),
        icon: Boxes,
    },
    {
        title: 'Projects',
        href: route('backend.projects.index'),
        icon: FolderOpenDot,
    },
    {
        title: 'Music',
        href: route('backend.popular-songs.index'),
        icon: Music4,
    },
    {
        title: 'Notes',
        href: route('backend.notes.index'),
        icon: BookUser,
    },
    {
        title: 'Feeds',
        href: route('backend.feeds.index'),
        icon: Rss,
    },
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
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
