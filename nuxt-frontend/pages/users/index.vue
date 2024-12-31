<template>
  <div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-6">Users List</h1>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-4">
      Loading users...
    </div>

    <!-- Error Message -->
    <div v-else-if="errorMessage" class="bg-red-100 text-red-700 p-4 rounded mb-4">
      {{ errorMessage }}
    </div>

    <!-- Users List -->
    <div v-else class="space-y-4">
      <div v-for="user in users" :key="user.id" 
           class="border p-4 rounded flex justify-between items-center">
        <div class="flex items-center">
          <!-- Display Profile Picture -->
          <img 
  :src="user.profile_picture ? `${runtimeConfig.public.imageBase}/${user.profile_picture}` : '/default-profile.png'" 
            alt="Profile Picture" 
            class="w-12 h-12 rounded-full object-cover mr-4">
          
          <div>
            <h3 class="font-medium">{{ user.username }}</h3>
            <p class="text-gray-600">{{ user.email }}</p>
            <p class="text-sm text-gray-500">Role: {{ user.role }}</p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-x-2">
          <button @click="navigateTo(`/users/${user.id}/edit`)"
                  class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Edit
          </button>
          <button @click="handleDelete(user.id)"
                  class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useUsers } from '~/composables/useUsers'
import { useRouter } from 'vue-router'

const { users, errorMessage, getUsers, deleteUser } = useUsers()
const loading = ref(true)
const router = useRouter()

import { useRuntimeConfig } from '#app';

const runtimeConfig = useRuntimeConfig();


// Fetch the users data
const fetchData = async () => {
  try {
    await getUsers()
  } catch (error) {
    console.error('Error fetching users:', error)
  } finally {
    loading.value = false
  }
}

// Handle delete user
const handleDelete = async (userId) => {
  if (!confirm('Are you sure you want to delete this user?')) return
  try {
    await deleteUser(userId)
    await fetchData() // Refresh the list after deletion
  } catch (error) {
    console.error('Failed to delete user:', error)
  }
}

// Navigate to edit page
const navigateTo = (path) => {
  router.push(path)
}

onMounted(fetchData)
</script>

<style scoped>
/* Add any custom styles you want */
</style>
