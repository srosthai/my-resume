<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItemType } from '@/types'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

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
    created_at?: string | null
}

interface Props {
    user: User
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'ME', href: '/me' },
    { title: 'Details', href: `/backend/users/${props.user.id}` },
]

const imageSrc = computed(() => {
    const image = props.user.image

    if (!image) return null

    return image.startsWith('http') || image.startsWith('/') ? image : `/${image}`
})

const initials = computed(() =>
    props.user.name
        .split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase(),
)

const formatDate = (dateString: string | null | undefined) => {
    if (!dateString) return 'Not set'

    return new Intl.DateTimeFormat('en-US', {
        month: 'long',
        day: '2-digit',
        year: 'numeric',
    }).format(new Date(dateString))
}

const detailItems = computed(() => [
    { label: 'Email', value: props.user.email || 'Not set', icon: 'mail' },
    { label: 'Phone', value: props.user.phone || 'Not set', icon: 'phone' },
    { label: 'Date of birth', value: formatDate(props.user.dob), icon: 'calendar' },
    { label: 'Address', value: props.user.address || 'Not set', icon: 'mapPin' },
    { label: 'Position', value: props.user.position || 'Not set', icon: 'briefcase' },
    { label: 'Record ID', value: `#${props.user.id}`, icon: 'badgeCheck' },
])
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/me">
                        <Button variant="outline" size="icon" class="rounded-xl">
                            <Icon name="arrowLeft" class="size-4" />
                        </Button>
                    </Link>
                    <div class="flex items-center gap-4">
                        <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                            <Icon name="users" class="size-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold tracking-tight">View profile</h1>
                            <p class="text-sm text-muted-foreground">Complete profile overview</p>
                        </div>
                    </div>
                </div>
                <Link :href="`/backend/users/${user.id}/edit`">
                    <Button class="rounded-xl shadow-sm">
                        <Icon name="squarePen" class="size-4" />
                        Edit profile
                    </Button>
                </Link>
            </div>

            <!-- Profile card -->
            <div class="overflow-hidden rounded-2xl border bg-card shadow-sm">
                <div class="flex flex-col gap-5 border-b p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                    <div class="flex items-center gap-5">
                        <img
                            v-if="imageSrc"
                            :src="imageSrc"
                            :alt="user.name"
                            class="size-24 rounded-2xl object-cover ring-1 ring-border"
                        />
                        <div
                            v-else
                            class="flex size-24 items-center justify-center rounded-2xl bg-muted text-3xl font-semibold text-muted-foreground"
                        >
                            {{ initials }}
                        </div>
                        <div>
                            <h2 class="text-2xl font-semibold tracking-tight">{{ user.name }}</h2>
                            <p class="text-sm text-muted-foreground">{{ user.position || 'Position not set' }}</p>
                            <p class="mt-1 text-sm text-muted-foreground">{{ user.email }}</p>
                        </div>
                    </div>
                    <div class="rounded-full border bg-muted/40 px-3 py-1 text-xs text-muted-foreground">
                        {{ formatDate(user.created_at) }}
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <div class="rounded-2xl border bg-muted/25 p-5">
                        <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wider text-muted-foreground">
                            <Icon name="fileText" class="size-3.5" />
                            Summary
                        </div>
                        <p class="mt-3 text-sm leading-7 text-foreground/80">
                            {{ user.description || 'No profile description yet.' }}
                        </p>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
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
        </div>
    </AppLayout>
</template>
