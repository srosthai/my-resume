<script setup lang="ts">
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
    { title: 'Work Experience', href: '/work-experience' },
    { title: 'Create', href: '/backend/work-experience/create' },
];

const form = useForm({
    title: '',
    position: '',
    company: '',
    description: '',
    from: '',
    to: '',
});

const submit = () => {
    form.post(route('backend.work-experience.store'));
};
</script>

<template>
    <Head title="Add Work Experience" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex items-center gap-4">
                <Link href="/work-experience">
                    <Button variant="outline" size="icon" class="rounded-xl">
                        <Icon name="arrowLeft" class="size-4" />
                    </Button>
                </Link>
                <div class="flex items-center gap-4">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                        <Icon name="briefcase" class="size-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">Add Work Experience</h1>
                        <p class="text-sm text-muted-foreground">Create a new work experience record</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="rounded-2xl border bg-card shadow-sm">
                    <div class="space-y-6 p-6 sm:p-8">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="title">Display title</Label>
                                <Input id="title" v-model="form.title" type="text" placeholder="Senior Full Stack Developer" />
                                <InputError :message="form.errors.title" />
                            </div>
                            <div class="space-y-2">
                                <Label for="position">Position badge</Label>
                                <Input id="position" v-model="form.position" type="text" placeholder="Senior Developer" />
                                <InputError :message="form.errors.position" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="company">Company</Label>
                            <Input id="company" v-model="form.company" type="text" placeholder="Tech Solutions Co., Ltd." />
                            <InputError :message="form.errors.company" />
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="from">Start period</Label>
                                <Input id="from" v-model="form.from" type="text" placeholder="2022" />
                                <InputError :message="form.errors.from" />
                            </div>
                            <div class="space-y-2">
                                <Label for="to">End period</Label>
                                <Input id="to" v-model="form.to" type="text" placeholder="Present" />
                                <InputError :message="form.errors.to" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Summarize responsibilities, achievements, and business impact."
                                :rows="6"
                            />
                            <InputError :message="form.errors.description" />
                        </div>
                    </div>

                    <!-- Footer actions -->
                    <div class="flex items-center justify-end gap-3 border-t bg-muted/30 px-6 py-4 sm:px-8">
                        <Link href="/work-experience">
                            <Button type="button" variant="outline" class="rounded-xl">Cancel</Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing" class="rounded-xl shadow-sm">
                            <Icon v-if="form.processing" name="loaderCircle" class="size-4 animate-spin" />
                            {{ form.processing ? 'Creating...' : 'Create Experience' }}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
