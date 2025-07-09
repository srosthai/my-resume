<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <Heading>User Details</Heading>
          <p class="text-sm text-muted-foreground">
            View user information
          </p>
        </div>
        <div class="flex items-center gap-4">
          <Button variant="outline" @click="$inertia.visit(route('backend.users.edit', user.id))">
            Edit User
          </Button>
          <Button variant="outline" @click="$inertia.visit(route('backend.users.index'))">
            Back to Users
          </Button>
        </div>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>{{ user.name }}</CardTitle>
          <CardDescription>
            {{ user.position }}
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-if="user.image" class="md:col-span-2">
              <Label class="text-sm font-medium">Profile Image</Label>
              <div class="mt-2">
                <img :src="`${user.image}`" :alt="user.name" class="w-32 h-32 object-cover rounded-lg">
              </div>
            </div>

            <div>
              <Label class="text-sm font-medium">Name</Label>
              <p class="mt-1">{{ user.name }}</p>
            </div>

            <div>
              <Label class="text-sm font-medium">Email</Label>
              <p class="mt-1">{{ user.email }}</p>
            </div>

            <div>
              <Label class="text-sm font-medium">Date of Birth</Label>
              <p class="mt-1">{{ formatDate(user.dob) }}</p>
            </div>

            <div>
              <Label class="text-sm font-medium">Phone</Label>
              <p class="mt-1">{{ user.phone }}</p>
            </div>

            <div>
              <Label class="text-sm font-medium">Position</Label>
              <p class="mt-1">{{ user.position }}</p>
            </div>

            <div>
              <Label class="text-sm font-medium">Address</Label>
              <p class="mt-1">{{ user.address }}</p>
            </div>

            <div v-if="user.description" class="md:col-span-2">
              <Label class="text-sm font-medium">Description</Label>
              <p class="mt-1">{{ user.description }}</p>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import Button from '@/components/ui/button/Button.vue'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import Label from '@/components/ui/label/Label.vue'
import type { BreadcrumbItemType } from '@/types'

interface User {
  id: number
  name: string
  email: string
  dob: string
  phone: string
  address: string
  position: string
  description: string
  image: string
}

interface Props {
  user: User
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Users', href: '/backend/users' },
  { title: 'Details', href: `/backend/users/${props.user.id}` }
]

const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>