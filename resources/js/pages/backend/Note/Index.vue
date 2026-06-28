<script setup>
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import Icon from '@/components/Icon.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    notes: {
        type: Array,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Notes', href: '/notes' },
];

const showDeleteConfirm = ref(false);
const itemToDelete = ref(null);
const deleting = ref(false);
const searchQuery = ref('');
const selectedCategory = ref('All');

const confirmDelete = (item) => {
    itemToDelete.value = item;
    showDeleteConfirm.value = true;
};

const deleteItem = () => {
    if (!itemToDelete.value) return;
    router.delete(route('notes.destroy', itemToDelete.value.id), {
        onStart: () => (deleting.value = true),
        onFinish: () => {
            deleting.value = false;
            showDeleteConfirm.value = false;
            itemToDelete.value = null;
        },
    });
};

const toggleFeatured = (note) => {
    router.patch(route('notes.toggle-featured', note.id), {}, {
        preserveScroll: true,
    });
};

const duplicateNote = (note) => {
    router.post(route('notes.duplicate', note.id));
};

const filteredNotes = computed(() => {
    let filtered = props.notes;

    if (selectedCategory.value !== 'All') {
        filtered = filtered.filter((note) => note.category === selectedCategory.value);
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (note) =>
                note.title.toLowerCase().includes(query) ||
                note.description.toLowerCase().includes(query) ||
                note.category.toLowerCase().includes(query) ||
                (note.tags && note.tags.some((tag) => tag.toLowerCase().includes(query))),
        );
    }

    return filtered;
});

const getStatusColor = (status) => {
    const colors = {
        draft: 'bg-gray-500/10 text-gray-500 border-gray-500/20',
        published: 'bg-green-500/10 text-green-500 border-green-500/20',
        archived: 'bg-red-500/10 text-red-500 border-red-500/20',
    };
    return colors[status] || 'bg-gray-500/10 text-gray-500 border-gray-500/20';
};

const categories = computed(() => ['All', ...props.categories]);

const formatDate = (dateString) => new Date(dateString).toLocaleDateString();
</script>

<template>
    <Head title="Notes Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-7xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                        <Icon name="stickyNote" class="size-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">Notes</h1>
                        <p class="text-sm text-muted-foreground">Manage your programming notes and tutorials</p>
                    </div>
                </div>
                <Link :href="route('notes.create')">
                    <Button class="rounded-xl shadow-sm">
                        <Icon name="plus" class="size-4" />
                        Add Note
                    </Button>
                </Link>
            </div>

            <!-- Filters -->
            <div class="flex flex-col gap-4 rounded-2xl border bg-card p-4 shadow-sm sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <Icon name="search" class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search notes..."
                        class="w-full rounded-xl border border-input bg-background py-2 pl-10 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <Icon name="filter" class="size-4 text-muted-foreground" />
                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-for="category in categories"
                            :key="category"
                            type="button"
                            :variant="selectedCategory === category ? 'default' : 'outline'"
                            size="sm"
                            class="rounded-lg"
                            @click="selectedCategory = category"
                        >
                            {{ category }}
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Table card -->
            <div class="overflow-hidden rounded-2xl border bg-card shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/40 text-xs uppercase tracking-wider text-muted-foreground">
                                <th class="w-16 px-6 py-4 text-left font-medium">#</th>
                                <th class="px-6 py-4 text-left font-medium">Title</th>
                                <th class="px-6 py-4 text-left font-medium">Category</th>
                                <th class="px-6 py-4 text-left font-medium">Status</th>
                                <th class="px-6 py-4 text-left font-medium">Featured</th>
                                <th class="px-6 py-4 text-left font-medium">Author</th>
                                <th class="px-6 py-4 text-left font-medium">Created</th>
                                <th class="px-6 py-4 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="(note, index) in filteredNotes"
                                :key="note.id"
                                class="group transition-colors hover:bg-muted/40"
                            >
                                <td class="px-6 py-4">
                                    <span class="inline-flex size-7 items-center justify-center rounded-full bg-muted text-xs font-medium text-muted-foreground">
                                        {{ index + 1 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium">{{ note.title || '-' }}</td>
                                <td class="px-6 py-4">
                                    <Badge variant="secondary" class="rounded-full font-normal">{{ note.category || '-' }}</Badge>
                                </td>
                                <td class="px-6 py-4">
                                    <Badge variant="outline" :class="getStatusColor(note.status)" class="rounded-full font-normal capitalize">
                                        {{ note.status }}
                                    </Badge>
                                </td>
                                <td class="px-6 py-4">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="rounded-lg text-muted-foreground hover:text-foreground"
                                        @click="toggleFeatured(note)"
                                    >
                                        <Icon
                                            name="star"
                                            class="size-4"
                                            :class="note.is_featured ? 'fill-yellow-400 text-yellow-400' : ''"
                                        />
                                    </Button>
                                </td>
                                <td class="px-6 py-4 text-muted-foreground">{{ note.user?.name || 'Admin' }}</td>
                                <td class="px-6 py-4 text-muted-foreground">{{ formatDate(note.created_at) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link :href="route('notes.show', note.id)">
                                            <Button variant="ghost" size="sm" class="rounded-lg text-muted-foreground hover:text-foreground">
                                                <Icon name="eye" class="size-4" />
                                                View
                                            </Button>
                                        </Link>
                                        <Link :href="route('notes.edit', note.id)">
                                            <Button variant="ghost" size="sm" class="rounded-lg text-muted-foreground hover:text-foreground">
                                                <Icon name="squarePen" class="size-4" />
                                                Edit
                                            </Button>
                                        </Link>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="rounded-lg text-muted-foreground hover:text-foreground"
                                            @click="duplicateNote(note)"
                                        >
                                            <Icon name="copy" class="size-4" />
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="rounded-lg text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                            @click="confirmDelete(note)"
                                        >
                                            <Icon name="trash2" class="size-4" />
                                            Delete
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredNotes.length === 0">
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-muted-foreground">
                                        <div class="flex size-12 items-center justify-center rounded-full bg-muted">
                                            <Icon name="stickyNote" class="size-6" />
                                        </div>
                                        <p class="text-sm">No notes found</p>
                                        <Link :href="route('notes.create')">
                                            <Button variant="outline" size="sm" class="rounded-lg">
                                                <Icon name="plus" class="size-4" />
                                                Add your first note
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
            title="Delete note?"
            description="This note will be permanently removed. This action cannot be undone."
            confirm-label="Delete"
            :processing="deleting"
            @confirm="deleteItem"
        />
    </AppLayout>
</template>
