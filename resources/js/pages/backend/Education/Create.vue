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
    { title: 'Education', href: '/eductions' },
    { title: 'Create', href: '/backend/eductions/create' },
];

const form = useForm({
    title: '',
    major: '',
    institution: '',
    description: '',
    from: '',
    to: '',
});

const submit = () => {
    form.post(route('backend.eductions.store'));
};
</script>

<template>
    <Head title="Add Education" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex items-center gap-4">
                <Link href="/eductions">
                    <Button variant="outline" size="icon" class="rounded-xl">
                        <Icon name="arrowLeft" class="size-4" />
                    </Button>
                </Link>
                <div class="flex items-center gap-4">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                        <Icon name="graduationCap" class="size-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">Add Education</h1>
                        <p class="text-sm text-muted-foreground">Create a new education record</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="rounded-2xl border bg-card shadow-sm">
                    <div class="space-y-6 p-6 sm:p-8">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="title">Degree Title</Label>
                                <Input id="title" v-model="form.title" type="text" placeholder="e.g. Bachelor of Computer Science" />
                                <InputError :message="form.errors.title" />
                            </div>
                            <div class="space-y-2">
                                <Label for="major">Major / Field of Study</Label>
                                <Input id="major" v-model="form.major" type="text" placeholder="e.g. Software Engineering" />
                                <InputError :message="form.errors.major" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="institution">Institution</Label>
                            <Input id="institution" v-model="form.institution" type="text" placeholder="e.g. Royal University of Phnom Penh" />
                            <InputError :message="form.errors.institution" />
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="from">From</Label>
                                <Input id="from" v-model="form.from" type="text" placeholder="Start year (e.g. 2021)" />
                                <InputError :message="form.errors.from" />
                            </div>
                            <div class="space-y-2">
                                <Label for="to">To</Label>
                                <Input id="to" v-model="form.to" type="text" placeholder="End year (e.g. 2025)" />
                                <InputError :message="form.errors.to" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Textarea id="description" v-model="form.description" placeholder="Describe your education and achievements" :rows="4" />
                            <InputError :message="form.errors.description" />
                        </div>
                    </div>

                    <!-- Footer actions -->
                    <div class="flex items-center justify-end gap-3 border-t bg-muted/30 px-6 py-4 sm:px-8">
                        <Link href="/eductions">
                            <Button type="button" variant="outline" class="rounded-xl">Cancel</Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing" class="rounded-xl shadow-sm">
                            <Icon v-if="form.processing" name="loaderCircle" class="size-4 animate-spin" />
                            {{ form.processing ? 'Creating...' : 'Create Education' }}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
