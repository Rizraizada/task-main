// app.vue
<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-lg mb-8">
      <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
          <div class="text-xl font-bold">User Management</div>
          <div class="space-x-4">
            <NuxtLink 
              v-for="link in navLinks" 
              :key="link.path"
              :to="link.path"
              class="text-gray-600 hover:text-gray-900"
            >
              {{ link.name }}
            </NuxtLink>
          </div>
        </div>
      </div>
    </nav>

    <div class="container mx-auto px-6">
      <NuxtPage />
    </div>
  </div>
</template>

<script setup>
const navLinks = [
  { name: 'Home', path: '/login' },
  { name: 'Create User', path: '/users/create' },
  { name: 'View Users', path: '/users' }
]
</script>