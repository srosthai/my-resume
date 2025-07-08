<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import Icon from '@/components/Icon.vue'

const props = defineProps({
    projects: {
        type: Array,
        required: true
    }
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Projects', href: '/projects' }
]

const showDeleteConfirm = ref(false)
const itemToDelete      = ref(null)

const confirmDelete = (item) => {
    itemToDelete.value      = item
    showDeleteConfirm.value = true
}

const deleteItem = () => {
    if (itemToDelete.value) {
        router.delete(route('backend.projects.destroy', itemToDelete.value.id))
        showDeleteConfirm.value = false
        itemToDelete.value = null
    }
}

const cancelDelete = () => {
    showDeleteConfirm.value = false
    itemToDelete.value = null
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString()
}

const getStatusBadgeVariant = (status) => {
    return status === 'completed' ? 'default' : 'secondary'
}
</script>

<template>
    <Head title="Projects Management" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <Card>
                <CardHeader class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <CardTitle>Projects Management</CardTitle>
                        <CardDescription>
                            View and manage your projects
                        </CardDescription>
                    </div>
                    <div>
                        <Link href="/backend/projects/create">
                            <Button variant="default">
                                <Icon name="plus" class="h-4 w-4" />
                                Create New
                            </Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full hover:table-hover">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr class="border-b">
                                    <th class="text-left p-4">#</th>
                                    <th class="text-left p-4">Image</th>
                                    <th class="text-left p-4">Title</th>
                                    <th class="text-left p-4">Type</th>
                                    <th class="text-left p-4">Status</th>
                                    <th class="text-left p-4">Created Date</th>
                                    <th class="text-left p-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in projects" :key="item.id" class="border-b">
                                    <td class="p-4">{{ index + 1 }}</td>
                                    <td class="p-4">
                                        <div class="w-16 h-16 rounded-md overflow-hidden bg-gray-100">
                                            <img 
                                                v-if="item.image" 
                                                :src="`/${item.image}`" 
                                                :alt="item.title"
                                                class="w-full h-full object-cover"
                                            />
                                            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                                <Icon name="image" class="h-6 w-6" />
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">{{ item.title || '-' }}</td>
                                    <td class="p-4">
                                        <Badge variant="outline">
                                            {{ item.project_type?.name || '-' }}
                                        </Badge>
                                    </td>
                                    <td class="p-4">
                                        <Badge :variant="getStatusBadgeVariant(item.status)">
                                            {{ item.status }}
                                        </Badge>
                                    </td>
                                    <td class="p-4">{{ item.created_date ? formatDate(item.created_date) : '-' }}</td>
                                    <td class="p-4">
                                        <div class="flex gap-2">
                                            <Link :href="`/backend/projects/${item.id}/edit`">
                                                <Button variant="default" size="sm">
                                                    <Icon name="edit" class="h-4 w-4" />
                                                    Edit
                                                </Button>
                                            </Link>
                                            <Button 
                                                variant="destructive" 
                                                size="sm" 
                                                @click="confirmDelete(item)"
                                            >
                                                <Icon name="trash" class="h-4 w-4" />
                                                Delete
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="projects.length === 0">
                                    <td colspan="7" class="text-center py-8 text-muted-foreground">
                                        No projects found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-background rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">Confirm Delete</h3>
                <p class="text-muted-foreground mb-6">
                    Are you sure you want to delete this project? This action cannot be undone.
                </p>
                <div class="flex justify-end gap-3">
                    <Button variant="outline" @click="cancelDelete">Cancel</Button>
                    <Button variant="destructive" @click="deleteItem">Delete</Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>