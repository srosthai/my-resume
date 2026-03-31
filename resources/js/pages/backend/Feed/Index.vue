<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
    Rss,
    Plus,
    Edit,
    Trash2,
    Eye,
    Pin,
    Search,
    Filter,
    Calendar,
    User,
    MapPin,
    Smile,
    Image,
    Heart
} from 'lucide-vue-next'

const props = defineProps({
    feeds: {
        type: Array,
        required: true
    },
    activityTypes: {
        type: Array,
        default: () => []
    }
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Feeds', href: '/feeds-management' }
]

const showDeleteConfirm = ref(false)
const itemToDelete = ref(null)
const searchQuery = ref('')
const selectedActivity = ref('All')

const confirmDelete = (item) => {
    itemToDelete.value = item
    showDeleteConfirm.value = true
}

const deleteItem = () => {
    if (itemToDelete.value) {
        router.delete(route('backend.feeds.destroy', itemToDelete.value.id), {
            onSuccess: () => {
                showDeleteConfirm.value = false
                itemToDelete.value = null
            }
        })
    }
}

const togglePinned = (feed) => {
    router.patch(route('feeds.toggle-pinned', feed.id), {}, {
        preserveScroll: true
    })
}

const filteredFeeds = computed(() => {
    let filtered = props.feeds

    if (selectedActivity.value !== 'All') {
        filtered = filtered.filter(feed => feed.activity_type === selectedActivity.value)
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(feed =>
            (feed.title && feed.title.toLowerCase().includes(query)) ||
            feed.body.toLowerCase().includes(query) ||
            (feed.location && feed.location.toLowerCase().includes(query)) ||
            (feed.tags && feed.tags.some(tag => tag.toLowerCase().includes(query)))
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

const getActivityColor = (type) => {
    const colors = {
        'hangout': 'bg-blue-500/10 text-blue-500 border-blue-500/20',
        'travel': 'bg-green-500/10 text-green-500 border-green-500/20',
        'food': 'bg-orange-500/10 text-orange-500 border-orange-500/20',
        'lifestyle': 'bg-pink-500/10 text-pink-500 border-pink-500/20',
        'work': 'bg-purple-500/10 text-purple-500 border-purple-500/20',
        'event': 'bg-cyan-500/10 text-cyan-500 border-cyan-500/20'
    }
    return colors[type] || 'bg-gray-500/10 text-gray-500 border-gray-500/20'
}

const getMoodEmoji = (mood) => {
    const emojis = {
        'happy': '😊',
        'excited': '🎉',
        'relaxed': '😌',
        'grateful': '🙏',
        'adventurous': '🏔️',
        'thoughtful': '💭',
        'creative': '🎨',
        'energetic': '⚡'
    }
    return emojis[mood] || '😊'
}

const activities = computed(() => ['All', ...props.activityTypes])
</script>

<template>
    <AppLayout>

        <Head title="Feeds Management" />

        <div class="space-y-6 p-4">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <Heading title="Feeds Management" description="Manage your lifestyle feeds and posts"
                        :breadcrumbs="breadcrumbs" />
                </div>
                <div class="flex items-center gap-3">
                    <Button asChild>
                        <Link :href="route('backend.feeds.create')" class="flex items-center gap-2">
                        <Plus class="h-4 w-4" />
                        Create Feed
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
                                <Rss class="h-5 w-5 text-blue-500" />
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Total Feeds</p>
                                <p class="text-2xl font-bold">{{ feeds.length }}</p>
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
                                <p class="text-2xl font-bold">{{ feeds.filter(f => f.status === 'published').length }}</p>
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
                                <p class="text-2xl font-bold">{{ feeds.filter(f => f.status === 'draft').length }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-purple-500/10 rounded-lg">
                                <Pin class="h-5 w-5 text-purple-500" />
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Pinned</p>
                                <p class="text-2xl font-bold">{{ feeds.filter(f => f.is_pinned).length }}</p>
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
                            <input v-model="searchQuery" type="text" placeholder="Search feeds..."
                                class="pl-10 w-full px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" />
                        </div>

                        <!-- Activity Filter -->
                        <div class="flex items-center gap-2">
                            <Filter class="h-4 w-4 text-muted-foreground" />
                            <div class="flex flex-wrap gap-2">
                                <Button v-for="activity in activities" :key="activity"
                                    @click="selectedActivity = activity"
                                    :variant="selectedActivity === activity ? 'default' : 'outline'" size="sm"
                                    class="capitalize">
                                    {{ activity }}
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Feeds Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Card v-for="feed in filteredFeeds" :key="feed.id"
                    class="hover:shadow-lg transition-all duration-200 group">
                    <CardHeader class="pb-3">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <Badge v-if="feed.activity_type" variant="outline"
                                    :class="getActivityColor(feed.activity_type)" class="text-xs capitalize">
                                    {{ feed.activity_type }}
                                </Badge>
                                <span v-if="feed.mood" class="text-sm">{{ getMoodEmoji(feed.mood) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <Button @click="togglePinned(feed)" variant="ghost" size="sm" class="p-1 h-6 w-6">
                                    <Pin
                                        :class="['h-3 w-3', feed.is_pinned ? 'fill-purple-400 text-purple-400' : 'text-gray-400']" />
                                </Button>
                                <Badge variant="outline" :class="getStatusColor(feed.status)"
                                    class="text-xs capitalize">
                                    {{ feed.status }}
                                </Badge>
                            </div>
                        </div>

                        <!-- Image thumbnail -->
                        <div v-if="feed.images && feed.images.length > 0" class="mb-3 rounded-lg overflow-hidden">
                            <div class="relative">
                                <img :src="'/' + feed.images[0]" :alt="feed.title || 'Feed image'"
                                    class="w-full h-40 object-cover" />
                                <Badge v-if="feed.images.length > 1" variant="secondary"
                                    class="absolute bottom-2 right-2 text-xs">
                                    <Image class="h-3 w-3 mr-1" />
                                    +{{ feed.images.length - 1 }}
                                </Badge>
                            </div>
                        </div>

                        <CardTitle v-if="feed.title" class="text-lg font-semibold line-clamp-2">
                            {{ feed.title }}
                        </CardTitle>
                        <CardDescription class="text-sm line-clamp-3">
                            {{ feed.body }}
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="pt-0">
                        <!-- Location -->
                        <div v-if="feed.location" class="flex items-center gap-1 text-xs text-muted-foreground mb-2">
                            <MapPin class="h-3 w-3" />
                            {{ feed.location }}
                        </div>

                        <!-- Tags -->
                        <div class="flex flex-wrap gap-1 mb-3" v-if="feed.tags && feed.tags.length">
                            <Badge v-for="tag in feed.tags.slice(0, 3)" :key="tag" variant="secondary" class="text-xs">
                                {{ tag }}
                            </Badge>
                            <Badge v-if="feed.tags.length > 3" variant="secondary" class="text-xs">
                                +{{ feed.tags.length - 3 }}
                            </Badge>
                        </div>

                        <!-- Meta Info -->
                        <div class="flex items-center justify-between text-xs text-muted-foreground mb-4">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center gap-1">
                                    <Heart class="h-3 w-3" />
                                    {{ feed.likes_count }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <Eye class="h-3 w-3" />
                                    {{ feed.views }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <Calendar class="h-3 w-3" />
                                {{ new Date(feed.created_at).toLocaleDateString() }}
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <Button asChild variant="outline" size="sm" class="flex-1">
                                <Link :href="route('backend.feeds.edit', feed.id)"
                                    class="flex items-center gap-2 justify-center">
                                <Edit class="h-3 w-3" />
                                Edit
                                </Link>
                            </Button>

                            <Button @click="confirmDelete(feed)" variant="outline" size="sm"
                                class="px-2 text-red-600 hover:text-red-700 hover:bg-red-50">
                                <Trash2 class="h-3 w-3" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Empty State -->
            <div v-if="filteredFeeds.length === 0" class="text-center py-16">
                <div class="p-4 bg-muted/50 rounded-full w-fit mx-auto mb-4">
                    <Rss class="h-8 w-8 text-muted-foreground" />
                </div>
                <h3 class="text-lg font-semibold mb-2">No feeds found</h3>
                <p class="text-muted-foreground mb-4">
                    {{ searchQuery || selectedActivity !== 'All' ? 'Try adjusting your search or filter criteria' :
                    'Get started by creating your first feed' }}
                </p>
                <Button asChild v-if="!searchQuery && selectedActivity === 'All'">
                    <Link :href="route('backend.feeds.create')" class="flex items-center gap-2">
                    <Plus class="h-4 w-4" />
                    Create Feed
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold mb-2">Confirm Delete</h3>
                <p class="text-muted-foreground mb-4">
                    Are you sure you want to delete this feed? This action cannot be undone.
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
