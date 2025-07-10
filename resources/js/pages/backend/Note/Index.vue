<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import Icon from '@/components/Icon.vue'
import {
    Book,
    Plus,
    Edit,
    Trash2,
    Eye,
    Star,
    Copy,
    Search,
    Filter,
    Calendar,
    User,
    Code,
    Terminal,
    Lightbulb
} from 'lucide-vue-next'

const props = defineProps({
    notes: {
        type: Array,
        required: true
    },
    categories: {
        type: Array,
        default: () => []
    }
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Notes', href: '/notes' }
]

const showDeleteConfirm = ref(false)
const itemToDelete = ref(null)
const searchQuery = ref('')
const selectedCategory = ref('All')

const confirmDelete = (item) => {
    itemToDelete.value = item
    showDeleteConfirm.value = true
}

const deleteItem = () => {
    if (itemToDelete.value) {
        router.delete(route('notes.destroy', itemToDelete.value.id), {
            onSuccess: () => {
                showDeleteConfirm.value = false
                itemToDelete.value = null
            }
        })
    }
}

const toggleFeatured = (note) => {
    router.patch(route('notes.toggle-featured', note.id), {}, {
        preserveScroll: true
    })
}

const duplicateNote = (note) => {
    router.post(route('notes.duplicate', note.id))
}

const filteredNotes = computed(() => {
    let filtered = props.notes

    if (selectedCategory.value !== 'All') {
        filtered = filtered.filter(note => note.category === selectedCategory.value)
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(note =>
            note.title.toLowerCase().includes(query) ||
            note.description.toLowerCase().includes(query) ||
            note.category.toLowerCase().includes(query) ||
            (note.tags && note.tags.some(tag => tag.toLowerCase().includes(query)))
        )
    }

    return filtered
})

const getStatusColor = (status) => {
    const colors = {
        'draft': 'bg-gray-500/10 text-gray-500 border-gray-500/20',
        'published': 'bg-green-500/10 text-green-500 border-green-500/20',
        'archived': 'bg-red-500/10 text-red-500 border-red-500/20'
    }
    return colors[status] || 'bg-gray-500/10 text-gray-500 border-gray-500/20'
}

const getCategoryColor = (category) => {
    const colors = {
        'Laravel': 'bg-red-500/10 text-red-500 border-red-500/20',
        'Vue.js': 'bg-green-500/10 text-green-500 border-green-500/20',
        'Next.js': 'bg-blue-500/10 text-blue-500 border-blue-500/20',
        'Bootstrap': 'bg-purple-500/10 text-purple-500 border-purple-500/20',
        'React': 'bg-cyan-500/10 text-cyan-500 border-cyan-500/20',
        'JavaScript': 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
        'PHP': 'bg-indigo-500/10 text-indigo-500 border-indigo-500/20'
    }
    return colors[category] || 'bg-gray-500/10 text-gray-500 border-gray-500/20'
}

const getCategoryIcon = (category) => {
    const icons = {
        'Laravel': Code,
        'Vue.js': Code,
        'Next.js': Code,
        'Bootstrap': Lightbulb,
        'React': Code,
        'JavaScript': Terminal,
        'PHP': Code
    }
    return icons[category] || Book
}

const categories = computed(() => ['All', ...props.categories])
</script>

<template>
    <AppLayout>

        <Head title="Notes Management" />

        <div class="space-y-6 p-4">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <Heading title="Notes Management" description="Manage your programming notes and tutorials"
                        :breadcrumbs="breadcrumbs" />
                </div>
                <div class="flex items-center gap-3">
                    <Button asChild>
                        <Link :href="route('notes.create')" class="flex items-center gap-2">
                        <Plus class="h-4 w-4" />
                        Create Note
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <Card>
                    <CardContent class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-500/10 rounded-lg">
                                <Book class="h-5 w-5 text-blue-500" />
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Total Notes</p>
                                <p class="text-2xl font-bold">{{ notes.length }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-green-500/10 rounded-lg">
                                <Eye class="h-5 w-5 text-green-500" />
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Published</p>
                                <p class="text-2xl font-bold">{{notes.filter(n => n.status === 'published').length}}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-yellow-500/10 rounded-lg">
                                <Edit class="h-5 w-5 text-yellow-500" />
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Drafts</p>
                                <p class="text-2xl font-bold">{{notes.filter(n => n.status === 'draft').length}}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-purple-500/10 rounded-lg">
                                <Star class="h-5 w-5 text-purple-500" />
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Featured</p>
                                <p class="text-2xl font-bold">{{notes.filter(n => n.is_featured).length}}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Filters -->
            <Card>
                <CardContent class="p-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Search -->
                        <div class="relative flex-1">
                            <Search
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <input v-model="searchQuery" type="text" placeholder="Search notes..."
                                class="pl-10 w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" />
                        </div>

                        <!-- Category Filter -->
                        <div class="flex items-center gap-2">
                            <Filter class="h-4 w-4 text-muted-foreground" />
                            <div class="flex flex-wrap gap-2">
                                <Button v-for="category in categories" :key="category"
                                    @click="selectedCategory = category"
                                    :variant="selectedCategory === category ? 'default' : 'outline'" size="sm">
                                    {{ category }}
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Notes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card v-for="note in filteredNotes" :key="note.id"
                    class="hover:shadow-lg transition-all duration-200 group">
                    <CardHeader class="pb-3">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <component :is="getCategoryIcon(note.category)" class="h-5 w-5 text-primary" />
                                <Badge variant="outline" :class="getCategoryColor(note.category)" class="text-xs">
                                    {{ note.category }}
                                </Badge>
                            </div>
                            <div class="flex items-center gap-2">
                                <Button @click="toggleFeatured(note)" variant="ghost" size="sm" class="p-1 h-6 w-6">
                                    <Star
                                        :class="['h-3 w-3', note.is_featured ? 'fill-yellow-400 text-yellow-400' : 'text-gray-400']" />
                                </Button>
                                <Badge variant="outline" :class="getStatusColor(note.status)"
                                    class="text-xs capitalize">
                                    {{ note.status }}
                                </Badge>
                            </div>
                        </div>

                        <CardTitle class="text-lg font-semibold line-clamp-2">
                            {{ note.title }}
                        </CardTitle>
                        <CardDescription class="text-sm line-clamp-3">
                            {{ note.description }}
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="pt-0">
                        <!-- Tags -->
                        <div class="flex flex-wrap gap-1 mb-3" v-if="note.tags && note.tags.length">
                            <Badge v-for="tag in note.tags.slice(0, 3)" :key="tag" variant="secondary" class="text-xs">
                                {{ tag }}
                            </Badge>
                            <Badge v-if="note.tags.length > 3" variant="secondary" class="text-xs">
                                +{{ note.tags.length - 3 }}
                            </Badge>
                        </div>

                        <!-- Meta Info -->
                        <div class="flex items-center justify-between text-xs text-muted-foreground mb-4">
                            <div class="flex items-center gap-2">
                                <User class="h-3 w-3" />
                                {{ note.user?.name || 'Admin' }}
                            </div>
                            <div class="flex items-center gap-2">
                                <Calendar class="h-3 w-3" />
                                {{ new Date(note.created_at).toLocaleDateString() }}
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <Button asChild variant="outline" size="sm" class="flex-1">
                                <Link :href="route('notes.show', note.id)"
                                    class="flex items-center gap-2 justify-center">
                                <Eye class="h-3 w-3" />
                                View
                                </Link>
                            </Button>

                            <Button asChild variant="outline" size="sm" class="flex-1">
                                <Link :href="route('notes.edit', note.id)"
                                    class="flex items-center gap-2 justify-center">
                                <Edit class="h-3 w-3" />
                                Edit
                                </Link>
                            </Button>

                            <Button @click="duplicateNote(note)" variant="outline" size="sm" class="px-2">
                                <Copy class="h-3 w-3" />
                            </Button>

                            <Button @click="confirmDelete(note)" variant="outline" size="sm"
                                class="px-2 text-red-600 hover:text-red-700 hover:bg-red-50">
                                <Trash2 class="h-3 w-3" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Empty State -->
            <div v-if="filteredNotes.length === 0" class="text-center py-16">
                <div class="p-4 bg-muted/50 rounded-full w-fit mx-auto mb-4">
                    <Book class="h-8 w-8 text-muted-foreground" />
                </div>
                <h3 class="text-lg font-semibold mb-2">No notes found</h3>
                <p class="text-muted-foreground mb-4">
                    {{ searchQuery || selectedCategory !== 'All' ? 'Try adjusting your search or filter criteria' :
                    'Getstarted by creating your first note' }}
                </p>
                <Button asChild v-if="!searchQuery && selectedCategory === 'All'">
                    <Link :href="route('notes.create')" class="flex items-center gap-2">
                    <Plus class="h-4 w-4" />
                    Create Note
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold mb-2">Confirm Delete</h3>
                <p class="text-muted-foreground mb-4">
                    Are you sure you want to delete "{{ itemToDelete?.title }}"? This action cannot be undone.
                </p>
                <div class="flex gap-3 justify-end">
                    <Button @click="showDeleteConfirm = false" variant="outline">
                        Cancel
                    </Button>
                    <Button @click="deleteItem" variant="destructive">
                        Delete
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>