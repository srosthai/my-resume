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

const breadcrumbs: BreadcrumbItemType[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'ME', href: '/me' },
    { title: 'Create', href: '/backend/users/create' },
]

const imagePreview = ref<string | null>(null)

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    dob: '',
    phone: '',
    address: '',
    position: '',
    description: '',
    image: null as File | null,
})

const previewInitials = computed(() => {
    const name = form.name || 'New Profile'

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
    imagePreview.value = file ? URL.createObjectURL(file) : null
}

const submit = () => {
    form.post(route('backend.users.store'), {
        forceFormData: true,
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
                        <h1 class="text-2xl font-semibold tracking-tight">Create profile</h1>
                        <p class="text-sm text-muted-foreground">Create a new profile record</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="rounded-2xl border bg-card shadow-sm">
                    <div class="space-y-8 p-6 sm:p-8">
                        <!-- Preview -->
                        <div class="flex items-center gap-5">
                            <img
                                v-if="imagePreview"
                                :src="imagePreview"
                                alt="Selected profile preview"
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
                                    <Input id="name" v-model="form.name" type="text" required autofocus placeholder="SROS THAI" />
                                    <InputError :message="form.errors.name" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="position">Position</Label>
                                    <Input id="position" v-model="form.position" type="text" required placeholder="Full Stack Developer" />
                                    <InputError :message="form.errors.position" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="email">Email</Label>
                                    <Input id="email" v-model="form.email" type="email" required placeholder="name@example.com" />
                                    <InputError :message="form.errors.email" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="phone">Phone</Label>
                                    <Input id="phone" v-model="form.phone" type="tel" required placeholder="+855 12 345 678" />
                                    <InputError :message="form.errors.phone" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="dob">Date of birth</Label>
                                    <Input id="dob" v-model="form.dob" type="date" required />
                                    <InputError :message="form.errors.dob" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="image">Profile image</Label>
                                    <Input id="image" type="file" accept="image/*" @change="onImageChange" />
                                    <InputError :message="form.errors.image" />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <Label for="address">Address</Label>
                                <Input id="address" v-model="form.address" type="text" required placeholder="Phnom Penh, Cambodia" />
                                <InputError :message="form.errors.address" />
                            </div>

                            <div class="space-y-2">
                                <Label for="description">Short profile summary</Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    :rows="4"
                                    placeholder="Short bio shown across resume and portfolio pages."
                                />
                                <InputError :message="form.errors.description" />
                            </div>
                        </div>

                        <!-- Access -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-sm font-medium uppercase tracking-wider text-muted-foreground">Access</h3>
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="password">Password</Label>
                                    <Input id="password" v-model="form.password" type="password" required autocomplete="new-password" />
                                    <InputError :message="form.errors.password" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="password_confirmation">Confirm password</Label>
                                    <Input id="password_confirmation" v-model="form.password_confirmation" type="password" required autocomplete="new-password" />
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
                            {{ form.processing ? 'Creating...' : 'Create profile' }}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
