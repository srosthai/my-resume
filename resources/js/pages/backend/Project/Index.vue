<script setup>
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import Icon from '@/components/Icon.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    projects: {
        type: Array,
        required: true,
    },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Projects', href: '/projects' },
];

const showDeleteConfirm = ref(false);
const itemToDelete = ref(null);
const deleting = ref(false);

const confirmDelete = (item) => {
    itemToDelete.value = item;
    showDeleteConfirm.value = true;
};

const deleteItem = () => {
    if (!itemToDelete.value) return;
    router.delete(route('backend.projects.destroy', itemToDelete.value.id), {
        onStart: () => (deleting.value = true),
        onFinish: () => {
            deleting.value = false;
            showDeleteConfirm.value = false;
            itemToDelete.value = null;
        },
    });
};

const formatDate = (dateString) => new Date(dateString).toLocaleDateString();

const getStatusBadgeVariant = (status) => {
    return status === 'completed' ? 'default' : 'secondary';
};
</script>

<template>
    <Head title="Projects Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-7xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                        <Icon name="folderKanban" class="size-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">Projects</h1>
                        <p class="text-sm text-muted-foreground">View and manage your projects</p>
                    </div>
                </div>
                <Link href="/backend/projects/create">
                    <Button class="rounded-xl shadow-sm">
                        <Icon name="plus" class="size-4" />
                        Add Project
                    </Button>
                </Link>
            </div>

            <!-- Table card -->
            <div class="overflow-hidden rounded-2xl border bg-card shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/40 text-xs uppercase tracking-wider text-muted-foreground">
                                <th class="w-16 px-6 py-4 text-left font-medium">#</th>
                                <th class="px-6 py-4 text-left font-medium">Image</th>
                                <th class="px-6 py-4 text-left font-medium">Title</th>
                                <th class="px-6 py-4 text-left font-medium">Type</th>
                                <th class="px-6 py-4 text-left font-medium">Status</th>
                                <th class="px-6 py-4 text-left font-medium">Created Date</th>
                                <th class="px-6 py-4 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="(item, index) in projects"
                                :key="item.id"
                                class="group transition-colors hover:bg-muted/40"
                            >
                                <td class="px-6 py-4">
                                    <span class="inline-flex size-7 items-center justify-center rounded-full bg-muted text-xs font-medium text-muted-foreground">
                                        {{ index + 1 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="size-14 overflow-hidden rounded-xl border bg-muted">
                                        <img
                                            v-if="item.image"
                                            :src="`/${item.image}`"
                                            :alt="item.title"
                                            class="size-full object-cover"
                                        />
                                        <div v-else class="flex size-full items-center justify-center text-muted-foreground">
                                            <Icon name="image" class="size-5" />
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium">{{ item.title || '-' }}</td>
                                <td class="px-6 py-4">
                                    <Badge variant="secondary" class="rounded-full font-normal">{{ item.project_type?.name || '-' }}</Badge>
                                </td>
                                <td class="px-6 py-4">
                                    <Badge :variant="getStatusBadgeVariant(item.status)" class="rounded-full font-normal">
                                        {{ item.status }}
                                    </Badge>
                                </td>
                                <td class="px-6 py-4 text-muted-foreground">{{ item.created_date ? formatDate(item.created_date) : '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link :href="`/backend/projects/${item.id}/edit`">
                                            <Button variant="ghost" size="sm" class="rounded-lg text-muted-foreground hover:text-foreground">
                                                <Icon name="squarePen" class="size-4" />
                                                Edit
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="rounded-lg text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                            @click="confirmDelete(item)"
                                        >
                                            <Icon name="trash2" class="size-4" />
                                            Delete
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="projects.length === 0">
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-muted-foreground">
                                        <div class="flex size-12 items-center justify-center rounded-full bg-muted">
                                            <Icon name="folderKanban" class="size-6" />
                                        </div>
                                        <p class="text-sm">No projects found</p>
                                        <Link href="/backend/projects/create">
                                            <Button variant="outline" size="sm" class="rounded-lg">
                                                <Icon name="plus" class="size-4" />
                                                Add your first project
                                            </Button>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <ConfirmDialog
            v-model:open="showDeleteConfirm"
            title="Delete project?"
            description="This project will be permanently removed. This action cannot be undone."
            confirm-label="Delete"
            :processing="deleting"
            @confirm="deleteItem"
        />
    </AppLayout>
</template>
