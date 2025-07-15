<script setup>
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    aboutMe: {
        type: Object,
        required: true,
    },
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'About Me', href: '/about-me' },
    { title: 'Edit', href: `/backend/about-me/${props.aboutMe.id}/edit` },
];

const form = useForm({
    title: props.aboutMe.title || '',
    description: props.aboutMe.description || '',
    location: props.aboutMe.location || '',
    year_experience: props.aboutMe.year_experience || '',
    fucus_on: props.aboutMe.fucus_on || '',
});

const submit = () => {
    form.put(route('backend.about-me.update', props.aboutMe.id));
};
</script>

<template>
    <Head title="Edit About Me" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div>
                <Heading title="Edit About Me" />
                <p class="text-sm text-muted-foreground">Update about me information</p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Edit About Me Information</CardTitle>
                    <CardDescription> Update your personal and professional details below </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <Label for="title">Title</Label>
                                <Input id="title" v-model="form.title" type="text" placeholder="Enter title" />
                                <InputError :message="form.errors.title" />
                            </div>

                            <div>
                                <Label for="location">Location</Label>
                                <Input id="location" v-model="form.location" type="text" placeholder="Enter location" />
                                <InputError :message="form.errors.location" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <Label for="year_experience">Years of Experience</Label>
                                <Input id="year_experience" v-model="form.year_experience" type="text" placeholder="Enter years of experience" />
                                <InputError :message="form.errors.year_experience" />
                            </div>

                            <div>
                                <Label for="fucus_on">Focus On</Label>
                                <Input id="fucus_on" v-model="form.fucus_on" type="text" placeholder="Enter focus area" />
                                <InputError :message="form.errors.fucus_on" />
                            </div>
                        </div>

                        <div>
                            <Label for="description">Description</Label>
                            <Textarea id="description" v-model="form.description" placeholder="Enter description" :rows="4" />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Updating...' : 'Update About Me' }}
                            </Button>
                            <Link href="/about-me">
                                <Button type="button" variant="outline"> Cancel </Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
