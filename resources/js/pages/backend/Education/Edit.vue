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
    education: {
        type: Object,
        required: true
    }
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Education', href: '/eductions' },
    { title: 'Edit', href: `/backend/education/${props.education.id}/edit` }
]

const form = useForm({
    title: props.education.title || '',
    major: props.education.major || '',
    institution: props.education.institution || '',
    description: props.education.description || '',
    from: props.education.from || '',
    to: props.education.to || '',
})

const submit = () => {
    form.put(route('backend.eductions.update', props.education.id))
}
</script>

<template>
    <Head title="Edit Education" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div>
                <Heading title="Edit Education" />
                <p class="text-sm text-muted-foreground">
                    Update education record
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Edit Education Information</CardTitle>
                    <CardDescription>
                        Update your education details below
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <Label for="title">Degree Title</Label>
                                <Input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    placeholder="Enter degree title"
                                />
                                <InputError :message="form.errors.title" />
                            </div>

                            <div>
                                <Label for="major">Major/Field of Study</Label>
                                <Input
                                    id="major"
                                    v-model="form.major"
                                    type="text"
                                    placeholder="Enter major or field of study"
                                />
                                <InputError :message="form.errors.major" />
                            </div>
                        </div>

                        <div>
                            <Label for="institution">Institution</Label>
                            <Input
                                id="institution"
                                v-model="form.institution"
                                type="text"
                                placeholder="Enter institution name"
                            />
                            <InputError :message="form.errors.institution" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <Label for="from">From</Label>
                                <Input
                                    id="from"
                                    v-model="form.from"
                                    type="text"
                                    placeholder="Start date (e.g., 2021)"
                                />
                                <InputError :message="form.errors.from" />
                            </div>

                            <div>
                                <Label for="to">To</Label>
                                <Input
                                    id="to"
                                    v-model="form.to"
                                    type="text"
                                    placeholder="End date (e.g., 2025)"
                                />
                                <InputError :message="form.errors.to" />
                            </div>
                        </div>

                        <div>
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Describe your education and achievements"
                                :rows="4"
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Updating...' : 'Update Education' }}
                            </Button>
                            <Link href="/eductions">
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