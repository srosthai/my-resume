<script setup>
import Icon from '@/components/Icon.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    projectType: {
        type: Object,
        required: true,
    },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Project Types', href: '/project-types' },
    { title: 'Edit', href: `/backend/project-types/${props.projectType.id}/edit` },
];

const form = useForm({
    name: props.projectType.name || '',
});

const submit = () => {
    form.put(route('backend.project-types.update', props.projectType.id));
};
</script>

<template>
    <Head title="Edit Project Type" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-3xl space-y-8 p-4 sm:p-6">
            <!-- Page header -->
            <div class="flex items-center gap-4">
                <Link href="/project-types">
                    <Button variant="outline" size="icon" class="rounded-xl">
                        <Icon name="arrowLeft" class="size-4" />
                    </Button>
                </Link>
                <div class="flex items-center gap-4">
                    <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/70 text-primary-foreground shadow-sm">
                        <Icon name="tag" class="size-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">Edit Project Type</h1>
                        <p class="text-sm text-muted-foreground">Update this project type</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="rounded-2xl border bg-card shadow-sm">
                    <div class="space-y-6 p-6 sm:p-8">
                        <div class="space-y-2">
                            <Label for="name">Name *</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Enter project type name"
                                required
                            />
                            <InputError :message="form.errors.name" />
                        </div>
                    </div>

                    <!-- Footer actions -->
                    <div class="flex items-center justify-end gap-3 border-t bg-muted/30 px-6 py-4 sm:px-8">
                        <Link href="/project-types">
                            <Button type="button" variant="outline" class="rounded-xl">Cancel</Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing" class="rounded-xl shadow-sm">
                            <Icon v-if="form.processing" name="loaderCircle" class="size-4 animate-spin" />
                            {{ form.processing ? 'Updating...' : 'Update Project Type' }}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
