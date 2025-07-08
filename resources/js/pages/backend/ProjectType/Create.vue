<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Project Types', href: '/project-types' },
    { title: 'Create', href: '/backend/project-types/create' }
]

const form = useForm({
    name: '',
})

const submit = () => {
    form.post(route('backend.project-types.store'))
}
</script>

<template>
    <Head title="Create Project Type" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div>
                <Heading title="Create Project Type" />
                <p class="text-sm text-muted-foreground">
                    Add new project type
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Project Type Information</CardTitle>
                    <CardDescription>
                        Enter the project type name below
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
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

                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Creating...' : 'Create Project Type' }}
                            </Button>
                            <Link href="/project-types">
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