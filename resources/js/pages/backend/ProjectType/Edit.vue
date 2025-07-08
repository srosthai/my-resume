<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'

const props = defineProps({
    projectType: {
        type: Object,
        required: true
    }
})

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Project Types', href: '/project-types' },
    { title: 'Edit', href: `/backend/project-types/${props.projectType.id}/edit` }
]

const form = useForm({
    name: props.projectType.name || '',
})

const submit = () => {
    form.put(route('backend.project-types.update', props.projectType.id))
}
</script>

<template>
    <Head title="Edit Project Type" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4">
            <div>
                <Heading title="Edit Project Type" />
                <p class="text-sm text-muted-foreground">
                    Update project type information
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Project Type Information</CardTitle>
                    <CardDescription>
                        Update the project type name below
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
                                {{ form.processing ? 'Updating...' : 'Update Project Type' }}
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