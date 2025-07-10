<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import InputError from '@/components/InputError.vue'

const props = defineProps({
    techStack: {
        type: Object,
        required: true
    }
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tech Stack', href: '/backend/tech-stack' },
    { title: 'Edit', href: `/backend/tech-stack/${props.techStack.id}/edit` }
]

const form = useForm({
    name: props.techStack.name || '',
    logo: props.techStack.logo || '',
    type: props.techStack.type || '',
    description: props.techStack.description || '',
})

const submit = () => {
    form.put(route('backend.tech-stack.update', props.techStack.id))
}
</script>

<template>
    <Head title="Edit Technology" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div>
                <Heading title="Edit Technology" />
                <p class="text-sm text-muted-foreground">
                    Update technology information
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Edit Technology Information</CardTitle>
                    <CardDescription>
                        Update the technology details below
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
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

                            <div>
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

                        <div>
                            <Label for="logo">Logo URL</Label>
                            <Input
                                id="logo"
                                v-model="form.logo"
                                type="url"
                                placeholder="Enter logo URL (optional)"
                            />
                            <InputError :message="form.errors.logo" />
                            <p class="text-xs text-muted-foreground mt-1">
                                You can use CDN URLs like devicons or upload your own logo
                            </p>
                        </div>

                        <div>
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Describe the technology and your experience with it"
                                :rows="4"
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Updating...' : 'Update Technology' }}
                            </Button>
                            <Link href="/backend/tech-stack">
                                <Button type="button" variant="outline">
                                    Cancel
                                </Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>