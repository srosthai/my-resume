<script setup lang="ts">
import ConfirmDialog from '@/components/ConfirmDialog.vue'
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItemType } from '@/types'
import { Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

interface User {
    id: number
    name: string
    email: string
    dob: string | null
    phone: string | null
    address: string | null
    position: string | null
    description: string | null
    image: string | null
    created_at: string | null
}

interface Props {
    user: User | null
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'ME', href: '/me' },
]

const profile = computed(() => props.user)

const imageSrc = computed(() => {
    const image = profile.value?.image

    if (!image) return null

    return image.startsWith('http') || image.startsWith('/') ? image : `/${image}`
})

const initials = computed(() => {
    const name = profile.value?.name || 'Me'

    return name
        .split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase()
})

const formatDate = (dateString: string | null) => {
    if (!dateString) return 'Not set'

    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric',
    }).format(new Date(dateString))
}

const detailItems = computed(() => [
    { label: 'Email', value: profile.value?.email || 'Not set', icon: 'mail' },
    { label: 'Phone', value: profile.value?.phone || 'Not set', icon: 'phone' },
    { label: 'Date of birth', value: formatDate(profile.value?.dob || null), icon: 'calendar' },
    { label: 'Address', value: profile.value?.address || 'Not set', icon: 'mapPin' },
    { label: 'Position', value: profile.value?.position || 'Not set', icon: 'briefcase' },
    { label: 'Created', value: formatDate(profile.value?.created_at || null), icon: 'clock' },
])

const showDeleteConfirm = ref(false)
const deleting = ref(false)

const confirmDelete = () => {
    showDeleteConfirm.value = true
}

const deleteItem = () => {
    if (!profile.value) return
    router.delete(route('backend.users.destroy', profile.value.id), {
        onStart: () => (deleting.value = true),
        onFinish: () => {
            deleting.value = false
            showDeleteConfirm.value = false
        },
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-7xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                        <Icon name="users" class="size-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">ME</h1>
                        <p class="text-sm text-muted-foreground">View and manage your profile record</p>
                    </div>
                </div>
                <Link v-if="!profile" href="/backend/users/create">
                    <Button class="rounded-xl shadow-sm">
                        <Icon name="plus" class="size-4" />
                        Add Profile
                    </Button>
                </Link>
            </div>

            <!-- Profile card -->
            <div v-if="profile" class="overflow-hidden rounded-2xl border bg-card shadow-sm">
                <div class="flex flex-col gap-6 border-b p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                    <div class="flex items-center gap-5">
                        <img
                            v-if="imageSrc"
                            :src="imageSrc"
                            :alt="profile.name"
                            class="size-20 rounded-2xl object-cover ring-1 ring-border"
                        />
                        <div
                            v-else
                            class="flex size-20 items-center justify-center rounded-2xl bg-muted text-2xl font-semibold text-muted-foreground"
                        >
                            {{ initials }}
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold tracking-tight">{{ profile.name }}</h2>
                            <p class="text-sm text-muted-foreground">{{ profile.position || 'Position not set' }}</p>
                            <p class="mt-1 text-sm text-muted-foreground">{{ profile.email }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <Link :href="`/backend/users/${profile.id}/edit`">
                            <Button variant="ghost" size="sm" class="rounded-lg text-muted-foreground hover:text-foreground">
                                <Icon name="squarePen" class="size-4" />
                                Edit
                            </Button>
                        </Link>
                        <Link :href="`/backend/users/${profile.id}`">
                            <Button variant="ghost" size="sm" class="rounded-lg text-muted-foreground hover:text-foreground">
                                <Icon name="eye" class="size-4" />
                                View
                            </Button>
                        </Link>
                        <Button
                            variant="ghost"
                            size="sm"
                            class="rounded-lg text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                            @click="confirmDelete"
                        >
                            <Icon name="trash2" class="size-4" />
                            Delete
                        </Button>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <p class="max-w-3xl text-sm leading-7 text-muted-foreground">
                        {{ profile.description || 'No profile description yet.' }}
                    </p>

                    <div class="mt-7 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        <div
                            v-for="item in detailItems"
                            :key="item.label"
                            class="rounded-2xl border bg-background p-4"
                        >
                            <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wider text-muted-foreground">
                                <Icon :name="item.icon" class="size-3.5" />
                                {{ item.label }}
                            </div>
                            <div class="mt-3 break-words text-sm font-medium leading-6">
                                {{ item.value }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="rounded-2xl border bg-card shadow-sm">
                <div class="flex flex-col items-center gap-3 px-6 py-16 text-center text-muted-foreground">
                    <div class="flex size-12 items-center justify-center rounded-full bg-muted">
                        <Icon name="users" class="size-6" />
                    </div>
                    <p class="text-sm">No profile record yet</p>
                    <Link href="/backend/users/create">
                        <Button variant="outline" size="sm" class="rounded-lg">
                            <Icon name="plus" class="size-4" />
                            Create your profile
                        </Button>
                    </Link>
                </div>
            </div>
        </div>

        <ConfirmDialog
            v-model:open="showDeleteConfirm"
            title="Delete profile record?"
            description="This profile record will be permanently removed. This action cannot be undone."
            confirm-label="Delete"
            :processing="deleting"
            @confirm="deleteItem"
        />
    </AppLayout>
</template>
