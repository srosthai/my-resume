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

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Work Experience', href: '/work-experience' },
    { title: 'Create', href: '/backend/work-experience/create' }
]

const form = useForm({
    title: '',
    position: '',
    company: '',
    description: '',
    from: '',
    to: '',
})

const submit = () => {
    form.post(route('backend.work-experience.store'))
}
</script>

<template>
    <Head title="Add Work Experience" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div>
                <Heading title="Add Work Experience" />
                <p class="text-sm text-muted-foreground">
                    Create new work experience record
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Work Experience Information</CardTitle>
                    <CardDescription>
                        Enter your work experience details below
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
                                {{ form.processing ? 'Creating...' : 'Create Experience' }}
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