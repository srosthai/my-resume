<script setup lang="ts">
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { useInitials } from '@/composables/useInitials';
import type { BreadcrumbItemType, User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ChevronsUpDown } from 'lucide-vue-next';
import { computed } from 'vue';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const user = page.props.auth.user as User;
const { getInitials } = useInitials();
const showAvatar = computed(() => user.avatar && user.avatar !== '');
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-3 border-b border-sidebar-border/70 px-4 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-5"
    >
        <div class="flex min-w-0 items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            <AppearanceTabs class="hidden sm:inline-flex" />

            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <button
                        type="button"
                        class="flex h-10 items-center gap-2 rounded-lg border bg-background px-2 text-sm font-medium shadow-xs transition-colors hover:bg-muted"
                    >
                        <Avatar class="size-7 rounded-md">
                            <AvatarImage v-if="showAvatar" :src="user.avatar" :alt="user.name" />
                            <AvatarFallback class="rounded-md text-xs">
                                {{ getInitials(user.name) }}
                            </AvatarFallback>
                        </Avatar>
                        <span class="hidden max-w-28 truncate sm:inline">{{ user.name }}</span>
                        <ChevronsUpDown class="size-4 text-muted-foreground" />
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent class="min-w-56 rounded-lg" side="bottom" align="end" :side-offset="8">
                    <UserMenuContent :user="user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>
