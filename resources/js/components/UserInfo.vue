<script setup lang="ts">
import type { User } from '@/types';
import AppLogoIcon from './AppLogoIcon.vue';

interface Props {
    user: User;
    showEmail?: boolean;
}

withDefaults(defineProps<Props>(), {
    showEmail: false,
});
</script>

<template>
    <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
        <AppLogoIcon class="size-5 fill-current" />
    </div>
    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-medium">{{ user.name }}</span>
        <span v-if="showEmail" class="truncate text-xs text-muted-foreground">{{ user.email }}</span>
    </div>
</template>
