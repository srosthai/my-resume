<script setup>
import Icon from '@/components/Icon.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tech Stack', href: '/tech-stacks' },
    { title: 'Create', href: '/backend/tech-stacks/create' },
];

const form = useForm({
    name: '',
    logo: '',
    type: '',
    description: '',
});

const submit = () => {
    form.post(route('backend.tech-stacks.store'));
};
</script>

<template>
    <Head title="Add Technology" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex items-center gap-4">
                <Link href="/tech-stacks">
                    <Button variant="outline" size="icon" class="rounded-xl">
                        <Icon name="arrowLeft" class="size-4" />
                    </Button>
                </Link>
                <div class="flex items-center gap-4">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                        <Icon name="layers" class="size-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">Add Technology</h1>
                        <p class="text-sm text-muted-foreground">Add new technology to your stack</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="rounded-2xl border bg-card shadow-sm">
                    <div class="space-y-6 p-6 sm:p-8">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="name">Technology Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Enter technology name"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-2">
                                <Label for="type">Type/Category</Label>
                                <Input
                                    id="type"
                                    v-model="form.type"
                                    type="text"
                                    placeholder="e.g., Frontend, Backend, Database"
                                />
                                <InputError :message="form.errors.type" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="logo">Logo URL</Label>
                            <Input
                                id="logo"
                                v-model="form.logo"
                                type="url"
                                placeholder="Enter logo URL (optional)"
                            />
                            <InputError :message="form.errors.logo" />
                            <p class="text-xs text-muted-foreground">
                                You can use CDN URLs like devicons or upload your own logo
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Describe the technology and your experience with it"
                                :rows="4"
                            />
                            <InputError :message="form.errors.description" />
                        </div>
                    </div>

                    <!-- Footer actions -->
                    <div class="flex items-center justify-end gap-3 border-t bg-muted/30 px-6 py-4 sm:px-8">
                        <Link href="/tech-stacks">
                            <Button type="button" variant="outline" class="rounded-xl">Cancel</Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing" class="rounded-xl shadow-sm">
                            <Icon v-if="form.processing" name="loaderCircle" class="size-4 animate-spin" />
                            {{ form.processing ? 'Creating...' : 'Create Technology' }}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
