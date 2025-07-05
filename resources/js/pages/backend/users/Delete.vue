<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <div>
        <Heading title="Delete User" />
        <p class="text-sm text-muted-foreground">
          Confirm user deletion
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle class="text-destructive">Delete User: {{ user.name }}</CardTitle>
          <CardDescription>
            Are you sure you want to delete this user? This action cannot be undone.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div class="bg-muted p-4 rounded-lg">
              <h4 class="font-medium mb-2">User Information</h4>
              <div class="space-y-2 text-sm">
                <p><strong>Name:</strong> {{ user.name }}</p>
                <p><strong>Email:</strong> {{ user.email }}</p>
                <p><strong>Position:</strong> {{ user.position }}</p>
                <p><strong>Phone:</strong> {{ user.phone }}</p>
              </div>
            </div>

            <div class="flex items-center gap-4">
              <form @submit.prevent="deleteUser">
                <Button type="submit" variant="destructive" :disabled="form.processing">
                  <Icon name="trash" class="h-4 w-4 mr-2" />
                  Yes, Delete User
                </Button>
              </form>
              <Link href="/backend/users">
                <Button type="button" variant="outline">
                  Cancel
                </Button>
              </Link>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import Button from '@/components/ui/button/Button.vue'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import Icon from '@/components/Icon.vue'
import { Link } from '@inertiajs/vue3'
import type { BreadcrumbItemType } from '@/types'

interface User {
  id: number
  name: string
  email: string
  position: string
  phone: string
}

interface Props {
  user: User
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Users', href: '/backend/users' },
  { title: 'Delete', href: `/backend/users/${props.user.id}/delete` }
]

const form = useForm({})

const deleteUser = () => {
  form.delete(route('backend.users.destroy', props.user.id))
}
</script>