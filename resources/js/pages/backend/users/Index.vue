<script setup lang="ts">
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
  dob: string | null
  phone: string | null
  address: string | null
  position: string | null
  description: string | null
  image: string | null
  created_at: string
}

interface Props {
  users: {
    data: User[]
  }
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'ME', href: '/me' }
]


const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString()
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">

    <div class="space-y-6 p-4">
      <Card>
        <CardHeader class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <CardTitle>Users List</CardTitle>
            <CardDescription>
              View and manage all system users
            </CardDescription>
          </div>
          <div>
            <Link href="/backend/users/create">
            <Button variant="default">
              <Icon name="plus" class="h-4 w-4" />
              Create User
            </Button>
            </Link>
          </div>
        </CardHeader>
        <CardContent>
          <div class="overflow-x-auto">
            <table class="w-full hover:table-hover">
              <thead class="bg-gray-100 dark:bg-gray-800">
                <tr class="border-b">
                  <th class="text-left p-4">Name</th>
                  <th class="text-left p-4">Email</th>
                  <th class="text-left p-4">DOB</th>
                  <th class="text-left p-4">Phone</th>
                  <th class="text-left p-4">Address</th>
                  <th class="text-left p-4">Position</th>
                  <th class="text-left p-4">Description</th>
                  <th class="text-left p-4">Image</th>
                  <th class="text-left p-4">Created</th>
                  <th class="text-left p-4">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users.data" :key="user.id" class="border-b">
                  <td class="p-4">{{ user.name }}</td>
                  <td class="p-4">{{ user.email }}</td>
                  <td class="p-4">{{ user.dob || '-' }}</td>
                  <td class="p-4">{{ user.phone || '-' }}</td>
                  <td class="p-4">{{ user.address || '-' }}</td>
                  <td class="p-4">{{ user.position || '-' }}</td>
                  <td class="p-4">{{ user.description || '-' }}</td>
                  <td class="p-4">
                    <img
                      v-if="user.image"
                      :src="`${user.image}`"
                      alt="User Image"
                      class="h-10 w-10 rounded-full object-cover"
                    />
                    <span v-else>-</span>
                  </td>
                  <td class="p-4">{{ formatDate(user.created_at) }}</td>
                  <td class="p-4">
                    <div class="flex gap-2">
                      <Link :href="`/backend/users/${user.id}`">
                        <Button variant="outline" size="sm">
                          <Icon name="eye" class="h-4 w-4" />
                          View
                        </Button>
                      </Link>
                      <Link :href="`/backend/users/${user.id}/edit`">
                        <Button variant="default" size="sm">
                          <Icon name="edit" class="h-4 w-4" />
                          Edit
                        </Button>
                      </Link>
                      <Link :href="`/backend/users/${user.id}/delete`">
                        <Button variant="destructive" size="sm">
                          <Icon name="trash" class="h-4 w-4" />
                          Delete
                        </Button>
                      </Link>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>
    </div>

  </AppLayout>
</template>