<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItemType } from '@/types'
import { Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

interface User {
    id: number
    name: string
    email: string
    position: string | null
    phone: string | null
    image: string | null
}

interface Props {
    user: User
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'ME', href: '/me' },
    { title: 'Delete', href: `/backend/users/${props.user.id}/delete` },
]

const form = useForm({})

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

const deleteUser = () => {
    form.delete(route('backend.users.destroy', props.user.id))
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex items-center gap-4">
                <Link href="/me">
                    <Button variant="outline" size="icon" class="rounded-xl">
                        <Icon name="arrowLeft" class="size-4" />
                    </Button>
                </Link>
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Delete profile</h1>
                    <p class="text-sm text-muted-foreground">This action cannot be undone</p>
                </div>
            </div>

            <!-- Confirmation card -->
            <div class="rounded-2xl border border-destructive/25 bg-card shadow-sm">
                <div class="flex flex-col items-center gap-4 p-6 text-center sm:p-8">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-destructive/10 text-destructive">
                        <Icon name="triangleAlert" class="size-6" />
                    </div>

                    <div class="flex flex-col items-center gap-1">
                        <img
                            v-if="imageSrc"
                            :src="imageSrc"
                            :alt="user.name"
                            class="size-20 rounded-2xl object-cover ring-1 ring-border"
                        />
                        <div
                            v-else
                            class="flex size-20 items-center justify-center rounded-2xl bg-muted text-2xl font-semibold text-muted-foreground"
                        >
                            {{ initials }}
                        </div>
                        <h2 class="mt-3 text-lg font-semibold tracking-tight">{{ user.name }}</h2>
                        <p class="text-sm text-muted-foreground">{{ user.position || 'No position set' }}</p>
                    </div>

                    <p class="max-w-md text-sm leading-6 text-muted-foreground">
                        This will permanently delete the profile record. Public resume and portfolio pages may lose name,
                        contact, image, and profile summary data after deletion.
                    </p>

                    <div class="grid w-full max-w-sm gap-3 text-left sm:grid-cols-2">
                        <div class="rounded-2xl border bg-background p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">Email</p>
                            <p class="mt-2 break-words text-sm font-medium">{{ user.email }}</p>
                        </div>
                        <div class="rounded-2xl border bg-background p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">Phone</p>
                            <p class="mt-2 text-sm font-medium">{{ user.phone || 'Not set' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Footer actions -->
                <form
                    class="flex items-center justify-end gap-3 border-t bg-muted/30 px-6 py-4 sm:px-8"
                    @submit.prevent="deleteUser"
                >
                    <Link href="/me">
                        <Button type="button" variant="outline" class="rounded-xl">Cancel</Button>
                    </Link>
                    <Button type="submit" variant="destructive" :disabled="form.processing" class="rounded-xl shadow-sm">
                        <Icon v-if="form.processing" name="loaderCircle" class="size-4 animate-spin" />
                        <Icon v-else name="trash2" class="size-4" />
                        Delete profile
                    </Button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
