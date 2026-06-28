<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItemType } from '@/types'
import { Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

interface User {
    id: number
    name: string
    email: string
    dob: string
    phone: string
    address: string
    position: string
    description: string | null
    image: string | null
}

interface Props {
    user: User
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'ME', href: '/me' },
    { title: 'Edit', href: `/backend/users/${props.user.id}/edit` },
]

const currentImage = computed(() => {
    const image = props.user.image

    if (!image) return null

    return image.startsWith('http') || image.startsWith('/') ? image : `/${image}`
})

const selectedImagePreview = ref<string | null>(null)

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    dob: props.user.dob,
    phone: props.user.phone,
    address: props.user.address,
    position: props.user.position,
    description: props.user.description || '',
    image: null as File | null,
})

const previewImage = computed(() => selectedImagePreview.value || currentImage.value)

const previewInitials = computed(() => {
    const name = form.name || 'Profile'

    return name
        .split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase()
})

function onImageChange(event: Event) {
    const target = event.target as HTMLInputElement
    const file = target.files?.[0] || null

    form.image = file
    selectedImagePreview.value = file ? URL.createObjectURL(file) : null
}

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            _method: 'PUT',
        }))
        .post(route('backend.users.update', props.user.id), {
            forceFormData: true,
            preserveScroll: true,
        })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
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
                        <h1 class="text-2xl font-semibold tracking-tight">Edit profile</h1>
                        <p class="text-sm text-muted-foreground">Update this profile record</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="rounded-2xl border bg-card shadow-sm">
                    <div class="space-y-8 p-6 sm:p-8">
                        <!-- Preview -->
                        <div class="flex items-center gap-5">
                            <img
                                v-if="previewImage"
                                :src="previewImage"
                                :alt="form.name"
                                class="size-20 rounded-2xl object-cover ring-1 ring-border"
                            />
                            <div
                                v-else
                                class="flex size-20 items-center justify-center rounded-2xl bg-muted text-2xl font-semibold text-muted-foreground"
                            >
                                {{ previewInitials }}
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold tracking-tight">{{ form.name || 'Your name' }}</h2>
                                <p class="text-sm text-muted-foreground">{{ form.position || 'Professional title' }}</p>
                            </div>
                        </div>

                        <!-- Basic info -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-sm font-medium uppercase tracking-wider text-muted-foreground">Basic info</h3>
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="name">Full name</Label>
                                    <Input id="name" v-model="form.name" type="text" required autofocus />
                                    <InputError :message="form.errors.name" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="position">Position</Label>
                                    <Input id="position" v-model="form.position" type="text" required />
                                    <InputError :message="form.errors.position" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="email">Email</Label>
                                    <Input id="email" v-model="form.email" type="email" required />
                                    <InputError :message="form.errors.email" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="phone">Phone</Label>
                                    <Input id="phone" v-model="form.phone" type="tel" required />
                                    <InputError :message="form.errors.phone" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="dob">Date of birth</Label>
                                    <Input id="dob" v-model="form.dob" type="date" required />
                                    <InputError :message="form.errors.dob" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="image">Replace image</Label>
                                    <Input id="image" type="file" accept="image/*" @change="onImageChange" />
                                    <InputError :message="form.errors.image" />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="address">Address</Label>
                                <Input id="address" v-model="form.address" type="text" required />
                                <InputError :message="form.errors.address" />
                            </div>

                            <div class="space-y-2">
                                <Label for="description">Short profile summary</Label>
                                <Textarea id="description" v-model="form.description" :rows="4" />
                                <InputError :message="form.errors.description" />
                            </div>
                        </div>

                        <!-- Access -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-sm font-medium uppercase tracking-wider text-muted-foreground">Optional password change</h3>
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="password">New password</Label>
                                    <Input id="password" v-model="form.password" type="password" autocomplete="new-password" placeholder="Leave blank to keep current" />
                                    <InputError :message="form.errors.password" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="password_confirmation">Confirm new password</Label>
                                    <Input id="password_confirmation" v-model="form.password_confirmation" type="password" autocomplete="new-password" :disabled="!form.password" />
                                    <InputError :message="form.errors.password_confirmation" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer actions -->
                    <div class="flex items-center justify-end gap-3 border-t bg-muted/30 px-6 py-4 sm:px-8">
                        <Link href="/me">
                            <Button type="button" variant="outline" class="rounded-xl">Cancel</Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing" class="rounded-xl shadow-sm">
                            <Icon v-if="form.processing" name="loaderCircle" class="size-4 animate-spin" />
                            {{ form.processing ? 'Saving...' : 'Save changes' }}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
