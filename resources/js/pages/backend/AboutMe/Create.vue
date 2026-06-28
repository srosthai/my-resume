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
    { title: 'About Me', href: '/about-me' },
    { title: 'Create', href: '/backend/about-me/create' },
];

const form = useForm({
    title: '',
    description: '',
    location: '',
    year_experience: '',
    fucus_on: '',
});

const submit = () => {
    form.post(route('backend.about-me.store'));
};
</script>

<template>
    <Head title="Create About Me" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex items-center gap-4">
                <Link href="/about-me">
                    <Button variant="outline" size="icon" class="rounded-xl">
                        <Icon name="arrowLeft" class="size-4" />
                    </Button>
                </Link>
                <div class="flex items-center gap-4">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                        <Icon name="user" class="size-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">Create About Me</h1>
                        <p class="text-sm text-muted-foreground">Create a new about me record</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="rounded-2xl border bg-card shadow-sm">
                    <div class="space-y-6 p-6 sm:p-8">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="title">Title</Label>
                                <Input id="title" v-model="form.title" type="text" placeholder="SROS THAI" />
                                <InputError :message="form.errors.title" />
                            </div>
                            <div class="space-y-2">
                                <Label for="location">Location</Label>
                                <Input id="location" v-model="form.location" type="text" placeholder="Phnom Penh, Cambodia" />
                                <InputError :message="form.errors.location" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="year_experience">Experience</Label>
                                <Input id="year_experience" v-model="form.year_experience" type="text" placeholder="5+ Years" />
                                <InputError :message="form.errors.year_experience" />
                            </div>
                            <div class="space-y-2">
                                <Label for="fucus_on">Focus area</Label>
                                <Input id="fucus_on" v-model="form.fucus_on" type="text" placeholder="Web Development" />
                                <InputError :message="form.errors.fucus_on" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Textarea id="description" v-model="form.description" placeholder="Tell visitors what you build and why it matters." :rows="6" />
                            <InputError :message="form.errors.description" />
                        </div>
                    </div>

                    <!-- Footer actions -->
                    <div class="flex items-center justify-end gap-3 border-t bg-muted/30 px-6 py-4 sm:px-8">
                        <Link href="/about-me">
                            <Button type="button" variant="outline" class="rounded-xl">Cancel</Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing" class="rounded-xl shadow-sm">
                            <Icon v-if="form.processing" name="loaderCircle" class="size-4 animate-spin" />
                            {{ form.processing ? 'Creating...' : 'Create About Me' }}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
