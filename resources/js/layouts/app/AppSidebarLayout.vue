<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import FormToast from '@/components/FormToast.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const flashMessages = ref<string[] | null>(null);
const flashTone = ref<'success' | 'error'>('success');

watch(
    () => [page.props.flash?.success, page.props.flash?.error] as const,
    ([success, error]) => {
        if (error) {
            flashMessages.value = [error];
            flashTone.value = 'error';
            return;
        }

        if (success) {
            flashMessages.value = [success];
            flashTone.value = 'success';
            return;
        }

        flashMessages.value = null;
    },
    { immediate: true },
);
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
        <FormToast :messages="flashMessages" :tone="flashTone" @close="flashMessages = null" />
    </AppShell>
</template>
