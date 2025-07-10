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
    workExperience: {
        type: Object,
        required: true
    }
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Work Experience', href: '/work-experience' },
    { title: 'Edit', href: `/backend/work-experience/${props.workExperience.id}/edit` }
]

const form = useForm({
    title: props.workExperience.title || '',
    position: props.workExperience.position || '',
    company: props.workExperience.company || '',
    description: props.workExperience.description || '',
    from: props.workExperience.from || '',
    to: props.workExperience.to || '',
})

const submit = () => {
    form.put(route('backend.work-experience.update', props.workExperience.id))
}
</script>

<template>
    <Head title="Edit Work Experience" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div>
                <Heading title="Edit Work Experience" />
                <p class="text-sm text-muted-foreground">
                    Update work experience record
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Edit Work Experience Information</CardTitle>
                    <CardDescription>
                        Update your work experience details below
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <Label for="title">Job Title</Label>
                                <Input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    placeholder="Enter job title"
                                />
                                <InputError :message="form.errors.title" />
                            </div>

                            <div>
                                <Label for="position">Position</Label>
                                <Input
                                    id="position"
                                    v-model="form.position"
                                    type="text"
                                    placeholder="Enter position"
                                />
                                <InputError :message="form.errors.position" />
                            </div>
                        </div>

                        <div>
                            <Label for="company">Company</Label>
                            <Input
                                id="company"
                                v-model="form.company"
                                type="text"
                                placeholder="Enter company name"
                            />
                            <InputError :message="form.errors.company" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <Label for="from">From</Label>
                                <Input
                                    id="from"
                                    v-model="form.from"
                                    type="text"
                                    placeholder="Start date (e.g., Jan 2023)"
                                />
                                <InputError :message="form.errors.from" />
                            </div>

                            <div>
                                <Label for="to">To</Label>
                                <Input
                                    id="to"
                                    v-model="form.to"
                                    type="text"
                                    placeholder="End date (e.g., Present)"
                                />
                                <InputError :message="form.errors.to" />
                            </div>
                        </div>

                        <div>
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Describe your responsibilities and achievements"
                                :rows="4"
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Updating...' : 'Update Experience' }}
                            </Button>
                            <Link href="/work-experience">
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