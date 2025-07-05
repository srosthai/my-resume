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

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Users', href: '/me' },
  { title: 'Create', href: '/backend/users/create' }
]

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  dob: '',
  phone: '',
  address: '',
  position: '',
  description: '',
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
  form.post(route('backend.users.store'), {
    forceFormData: true
  })
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4">
      <div>
        <Heading title="Create User" />
        <p class="text-sm text-muted-foreground">
          Add a new user to the system
        </p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>User Information</CardTitle>
          <CardDescription>
            Enter the user details below
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <Label for="name">Name</Label>
              <Input id="name" v-model="form.name" type="text" required autofocus />
              <InputError :message="form.errors.name" />
            </div>

            <div>
              <Label for="email">Email</Label>
              <Input id="email" v-model="form.email" type="email" required />
              <InputError :message="form.errors.email" />
            </div>

            <div>
              <Label for="dob">Date of Birth</Label>
              <Input id="dob" v-model="form.dob" type="date" required />
              <InputError :message="form.errors.dob" />
            </div>

            <div>
              <Label for="phone">Phone</Label>
              <Input id="phone" v-model="form.phone" type="tel" required />
              <InputError :message="form.errors.phone" />
            </div>

            <div>
              <Label for="address">Address</Label>
              <Input id="address" v-model="form.address" type="text" required />
              <InputError :message="form.errors.address" />
            </div>

            <div>
              <Label for="position">Position</Label>
              <Input id="position" v-model="form.position" type="text" required />
              <InputError :message="form.errors.position" />
            </div>

            <div>
              <Label for="description">Description</Label>
              <Input id="description" v-model="form.description" type="text" />
              <InputError :message="form.errors.description" />
            </div>

            <div>
              <Label for="image">Image</Label>
              <Input id="image" type="file" @change="onImageChange" accept="image/*" />
              <InputError :message="form.errors.image" />
            </div>

            <div>
              <Label for="password">Password</Label>
              <Input id="password" v-model="form.password" type="password" required />
              <InputError :message="form.errors.password" />
            </div>

            <div>
              <Label for="password_confirmation">Confirm Password</Label>
              <Input id="password_confirmation" v-model="form.password_confirmation" type="password" required />
              <InputError :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center gap-4">
              <Button type="submit" :disabled="form.processing">
                Create User
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