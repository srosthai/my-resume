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
import Input from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import Label from '@/components/ui/label/Label.vue'
import { Link } from '@inertiajs/vue3'
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
  { title: 'Users', href: '/me' },
  { title: 'Edit', href: `/backend/users/${props.user.id}/edit` }
]

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  dob: props.user.dob,
  phone: props.user.phone,
  address: props.user.address,
  position: props.user.position,
  description: props.user.description,
  image: null
})

function onImageChange(event: Event) {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    form.image = target.files[0]
  } else {
    form.image = null
  }
}

const submit = () => {
  form.transform((data) => ({
    ...data,
    _method: 'PUT'
  })).post(route('backend.users.update', props.user.id), {
    forceFormData: true
  })
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <div>
        <Heading title="Edit my information" />
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Edit info of: {{ user.name }}</CardTitle>
          <CardDescription>
            Update my information
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <div class="flex flex-wrap -mx-2">
              <div class="w-full sm:w-1/3 px-2 mb-4">
                <Label for="name">Name</Label>
                <Input id="name" v-model="form.name" type="text" required autofocus />
                <InputError :message="form.errors.name" />
              </div>

              <div class="w-full sm:w-1/3 px-2 mb-4">
                <Label for="email">Email</Label>
                <Input id="email" v-model="form.email" type="email" required />
                <InputError :message="form.errors.email" />
              </div>

              <div class="w-full sm:w-1/3 px-2 mb-4">
                <Label for="dob">Date of Birth</Label>
                <Input id="dob" v-model="form.dob" type="date" required />
                <InputError :message="form.errors.dob" />
              </div>

              <div class="w-full sm:w-1/3 px-2 mb-4">
                <Label for="phone">Phone</Label>
                <Input id="phone" v-model="form.phone" type="tel" required />
                <InputError :message="form.errors.phone" />
              </div>

              <div class="w-full sm:w-1/3 px-2 mb-4">
                <Label for="address">Address</Label>
                <Input id="address" v-model="form.address" type="text" required />
                <InputError :message="form.errors.address" />
              </div>

              <div class="w-full sm:w-1/3 px-2 mb-4">
                <Label for="position">Position</Label>
                <Input id="position" v-model="form.position" type="text" required />
                <InputError :message="form.errors.position" />
              </div>

              <div class="w-full sm:w-1/3 px-2 mb-4">
                <Label for="description">Description</Label>
                <Input id="description" v-model="form.description" type="text" />
                <InputError :message="form.errors.description" />
              </div>

              <div class="w-full sm:w-1/3 px-2 mb-4">
                <Label for="image">Image</Label>
                <Input id="image" type="file" @change="onImageChange" accept="image/*" />
                <InputError :message="form.errors.image" />
              </div>

              <div class="w-full sm:w-1/3 px-2 mb-4">
                <Label for="password">Password</Label>
                <Input id="password" v-model="form.password" type="password"
                  placeholder="Leave blank to keep current password" />
                <InputError :message="form.errors.password" />
              </div>

              <div v-if="form.password" class="w-full sm:w-1/3 px-2 mb-4">
                <Label for="password_confirmation">Confirm Password</Label>
                <Input id="password_confirmation" v-model="form.password_confirmation" type="password" />
                <InputError :message="form.errors.password_confirmation" />
              </div>
            </div>

            <div class="flex items-center gap-4">
              <Button type="submit" :disabled="form.processing">
                Update User
              </Button>
              <Link href="/me">
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
