<script setup>
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import Icon from '@/components/Icon.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    feeds: {
        type: Array,
        required: true,
    },
    activityTypes: {
        type: Array,
        default: () => [],
    },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Feeds', href: '/feeds-management' },
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
    router.delete(route('backend.feeds.destroy', itemToDelete.value.id), {
        onStart: () => (deleting.value = true),
        onFinish: () => {
            deleting.value = false;
            showDeleteConfirm.value = false;
            itemToDelete.value = null;
        },
    });
};

const formatDate = (dateString) => new Date(dateString).toLocaleDateString();
</script>

<template>
    <Head title="Feeds Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-7xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                        <Icon name="rss" class="size-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">Feeds</h1>
                        <p class="text-sm text-muted-foreground">Manage your lifestyle feeds and posts</p>
                    </div>
                </div>
                <Link :href="route('backend.feeds.create')">
                    <Button class="rounded-xl shadow-sm">
                        <Icon name="plus" class="size-4" />
                        Add Feed
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
                                <th class="px-6 py-4 text-left font-medium">Title</th>
                                <th class="px-6 py-4 text-left font-medium">Activity</th>
                                <th class="px-6 py-4 text-left font-medium">Location</th>
                                <th class="px-6 py-4 text-left font-medium">Status</th>
                                <th class="px-6 py-4 text-left font-medium">Likes</th>
                                <th class="px-6 py-4 text-left font-medium">Views</th>
                                <th class="px-6 py-4 text-left font-medium">Created</th>
                                <th class="px-6 py-4 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="(item, index) in feeds"
                                :key="item.id"
                                class="group transition-colors hover:bg-muted/40"
                            >
                                <td class="px-6 py-4">
                                    <span class="inline-flex size-7 items-center justify-center rounded-full bg-muted text-xs font-medium text-muted-foreground">
                                        {{ index + 1 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium">{{ item.title || '-' }}</td>
                                <td class="px-6 py-4">
                                    <Badge variant="secondary" class="rounded-full font-normal capitalize">{{ item.activity_type || '-' }}</Badge>
                                </td>
                                <td class="px-6 py-4 text-muted-foreground">{{ item.location || '-' }}</td>
                                <td class="px-6 py-4">
                                    <Badge variant="secondary" class="rounded-full font-normal capitalize">{{ item.status || '-' }}</Badge>
                                </td>
                                <td class="px-6 py-4 text-muted-foreground">{{ item.likes_count }}</td>
                                <td class="px-6 py-4 text-muted-foreground">{{ item.views }}</td>
                                <td class="px-6 py-4 text-muted-foreground">{{ formatDate(item.created_at) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link :href="route('backend.feeds.edit', item.id)">
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
                            <tr v-if="feeds.length === 0">
                                <td colspan="9" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-muted-foreground">
                                        <div class="flex size-12 items-center justify-center rounded-full bg-muted">
                                            <Icon name="rss" class="size-6" />
                                        </div>
                                        <p class="text-sm">No feeds found</p>
                                        <Link :href="route('backend.feeds.create')">
                                            <Button variant="outline" size="sm" class="rounded-lg">
                                                <Icon name="plus" class="size-4" />
                                                Add your first feed
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
            title="Delete feed?"
            description="This feed will be permanently removed. This action cannot be undone."
            confirm-label="Delete"
            :processing="deleting"
            @confirm="deleteItem"
        />
    </AppLayout>
</template>
