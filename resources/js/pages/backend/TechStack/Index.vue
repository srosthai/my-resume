<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import Icon from '@/components/Icon.vue'

const props = defineProps({
    techStacks: {
        type: Array,
        required: true
    }
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tech Stack', href: '/tech-stacks' }
]

const showDeleteConfirm = ref(false)
const itemToDelete = ref(null)

const confirmDelete = (item) => {
    itemToDelete.value = item
    showDeleteConfirm.value = true
}

const deleteItem = () => {
    if (itemToDelete.value) {
        router.delete(route('backend.tech-stack.destroy', itemToDelete.value.id))
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
</script>

<template>
    <Head title="Tech Stack Management" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <Card>
                <CardHeader class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <CardTitle>Tech Stack Management</CardTitle>
                        <CardDescription>
                            View and manage your technology stack
                        </CardDescription>
                    </div>
                    <div>
                        <Link href="backend/tech-stacks/create">
                            <Button variant="default">
                                <Icon name="plus" class="h-4 w-4" />
                                Add Technology
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
                                    <th class="text-left p-4">Technology</th>
                                    <th class="text-left p-4">Type</th>
                                    <th class="text-left p-4">Description</th>
                                    <th class="text-left p-4">Created</th>
                                    <th class="text-left p-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in techStacks" :key="item.id" class="border-b">
                                    <td class="p-4">{{ index + 1 }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <Avatar class="h-8 w-8">
                                                <AvatarImage v-if="item.logo" :src="item.logo" :alt="item.name" class="object-contain p-1" />
                                                <AvatarFallback class="bg-primary/20 text-primary text-xs font-bold">
                                                    {{ item.name?.slice(0, 2) || 'T' }}
                                                </AvatarFallback>
                                            </Avatar>
                                            <div class="font-medium">{{ item.name || '-' }}</div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <Badge variant="outline">{{ item.type || '-' }}</Badge>
                                    </td>
                                    <td class="p-4">
                                        <div class="max-w-xs truncate text-sm text-muted-foreground">
                                            {{ item.description || 'No description' }}
                                        </div>
                                    </td>
                                    <td class="p-4">{{ formatDate(item.created_at) }}</td>
                                    <td class="p-4">
                                        <div class="flex gap-2">
                                            <Link :href="`/backend/tech-stacks/${item.id}/edit`">
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
                                <tr v-if="techStacks.length === 0">
                                    <td colspan="6" class="text-center py-8 text-muted-foreground">
                                        No tech stack records found
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
                    Are you sure you want to delete this technology from your stack? This action cannot be undone.
                </p>
                <div class="flex justify-end gap-3">
                    <Button variant="outline" @click="cancelDelete">Cancel</Button>
                    <Button variant="destructive" @click="deleteItem">Delete</Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>