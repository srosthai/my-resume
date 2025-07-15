<script setup>
import Icon from '@/components/Icon.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    educations: {
        type: Array,
        required: true,
    },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Education', href: '/eductions' },
];

const showDeleteConfirm = ref(false);
const itemToDelete = ref(null);

const confirmDelete = (item) => {
    itemToDelete.value = item;
    showDeleteConfirm.value = true;
};

const deleteItem = () => {
    if (itemToDelete.value) {
        router.delete(route('backend.eductions.destroy', itemToDelete.value.id));
        showDeleteConfirm.value = false;
        itemToDelete.value = null;
    }
};

const cancelDelete = () => {
    showDeleteConfirm.value = false;
    itemToDelete.value = null;
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString();
};
</script>

<template>
    <Head title="Education Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <Card>
                <CardHeader class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <CardTitle>Education Management</CardTitle>
                        <CardDescription> View and manage your education records </CardDescription>
                    </div>
                    <div>
                        <Link href="backend/eductions/create">
                            <Button variant="default">
                                <Icon name="plus" class="h-4 w-4" />
                                Add Education
                            </Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="hover:table-hover w-full">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr class="border-b">
                                    <th class="p-4 text-left">#</th>
                                    <th class="p-4 text-left">Title</th>
                                    <th class="p-4 text-left">Major</th>
                                    <th class="p-4 text-left">Institution</th>
                                    <th class="p-4 text-left">Period</th>
                                    <th class="p-4 text-left">Created</th>
                                    <th class="p-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in educations" :key="item.id" class="border-b">
                                    <td class="p-4">{{ index + 1 }}</td>
                                    <td class="p-4">{{ item.title || '-' }}</td>
                                    <td class="p-4">
                                        <Badge variant="outline">{{ item.major || '-' }}</Badge>
                                    </td>
                                    <td class="p-4">{{ item.institution || '-' }}</td>
                                    <td class="p-4">{{ item.from || '-' }} - {{ item.to || 'Present' }}</td>
                                    <td class="p-4">{{ formatDate(item.created_at) }}</td>
                                    <td class="p-4">
                                        <div class="flex gap-2">
                                            <Link :href="`/backend/eductions/${item.id}/edit`">
                                                <Button variant="default" size="sm">
                                                    <Icon name="edit" class="h-4 w-4" />
                                                    Edit
                                                </Button>
                                            </Link>
                                            <Button variant="destructive" size="sm" @click="confirmDelete(item)">
                                                <Icon name="trash" class="h-4 w-4" />
                                                Delete
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="educations.length === 0">
                                    <td colspan="7" class="py-8 text-center text-muted-foreground">No education records found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteConfirm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="mx-4 w-full max-w-md rounded-lg bg-background p-6">
                <h3 class="mb-4 text-lg font-semibold">Confirm Delete</h3>
                <p class="mb-6 text-muted-foreground">Are you sure you want to delete this education record? This action cannot be undone.</p>
                <div class="flex justify-end gap-3">
                    <Button variant="outline" @click="cancelDelete">Cancel</Button>
                    <Button variant="destructive" @click="deleteItem">Delete</Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
