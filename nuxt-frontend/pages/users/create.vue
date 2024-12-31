<template>
  <div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-6">Create User</h1>

    <!-- Show loading state while awaiting the response -->
    <div v-if="loading" class="text-center py-4">
      Loading...
    </div>

    <!-- Show form when not loading -->
    <div v-else>
      <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
        <div class="mb-4">
          <label for="username" class="block text-gray-700">Username</label>
          <input type="text" id="username" v-model="form.username" class="mt-1 p-2 border w-full rounded" required />
        </div>

        <div class="mb-4">
          <label for="email" class="block text-gray-700">Email</label>
          <input type="email" id="email" v-model="form.email" class="mt-1 p-2 border w-full rounded" required />
        </div>

        <div class="mb-4">
          <label for="password" class="block text-gray-700">Password</label>
          <input type="password" id="password" v-model="form.password" class="mt-1 p-2 border w-full rounded" required />
        </div>

        <div class="mb-4">
          <label for="profile_picture" class="block text-gray-700">Profile Picture</label>
          <input type="file" id="profile_picture" @change="handleFileChange" class="mt-1 p-2 border w-full rounded" />
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Create User</button>
      </form>

      <div v-if="errorMessage" class="bg-red-100 text-red-700 p-4 rounded mt-4">
        {{ errorMessage }}
      </div>

      <div v-if="successMessage" class="bg-green-100 text-green-700 p-4 rounded mt-4">
        {{ successMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useUsers } from '~/composables/useUsers'

const form = ref({
  username: '',
  email: '',
  password: '',
  profile_picture: null,
})

const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const { createUser } = useUsers()

// Handle form submission
const handleSubmit = async () => {
  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const userData = {
      username: form.value.username,
      email: form.value.email,
      password: form.value.password,
      profile_picture: form.value.profile_picture,
    }
    await createUser(userData)
    successMessage.value = 'User created successfully!'
  } catch (error) {
    errorMessage.value = 'Failed to create user.'
  } finally {
    loading.value = false
  }
}

// Handle file input change
const handleFileChange = (event) => {
  form.value.profile_picture = event.target.files[0]
}
</script>

<style scoped>
/* Custom styles here */
</style>
