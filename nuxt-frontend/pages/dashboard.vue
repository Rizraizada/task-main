<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Top Navigation Bar -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <h1 class="text-xl font-semibold text-gray-800">Dashboard</h1>
          <div class="flex items-center gap-4">
            <span v-if="user" class="text-gray-600">
              Welcome, <span class="font-medium">{{ user.username }}</span>
            </span>
            <button
              @click="logout"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-2"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                />
              </svg>
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Navigation Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Subjects Card -->
        <router-link
          to="/subjects"
          class="block group"
        >
          <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-200 border border-gray-100 hover:border-blue-500">
            <div class="flex flex-col items-center">
              <div class="p-3 bg-blue-100 rounded-full group-hover:bg-blue-200 transition-colors">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
              <h2 class="mt-4 font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">Subjects</h2>
              <p class="mt-1 text-sm text-gray-500">View all subjects</p>
            </div>
          </div>
        </router-link>

        <!-- Create Chapter Card -->
        <router-link
          to="/chapter"
          class="block group"
        >
          <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-200 border border-gray-100 hover:border-green-500">
            <div class="flex flex-col items-center">
              <div class="p-3 bg-green-100 rounded-full group-hover:bg-green-200 transition-colors">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <h2 class="mt-4 font-semibold text-gray-900 group-hover:text-green-600 transition-colors">Create Chapter</h2>
              <p class="mt-1 text-sm text-gray-500">Add new chapter</p>
            </div>
          </div>
        </router-link>

        <!-- Create Question Card -->
        <router-link
          to="/question"
          class="block group"
        >
          <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-200 border border-gray-100 hover:border-purple-500">
            <div class="flex flex-col items-center">
              <div class="p-3 bg-purple-100 rounded-full group-hover:bg-purple-200 transition-colors">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h2 class="mt-4 font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">Create Question</h2>
              <p class="mt-1 text-sm text-gray-500">Add new question</p>
            </div>
          </div>
        </router-link>

        <!-- Create Post Card -->
        <router-link
          to="/post"
          class="block group"
        >
          <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-200 border border-gray-100 hover:border-orange-500">
            <div class="flex flex-col items-center">
              <div class="p-3 bg-orange-100 rounded-full group-hover:bg-orange-200 transition-colors">
                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </div>
              <h2 class="mt-4 font-semibold text-gray-900 group-hover:text-orange-600 transition-colors">Create Post</h2>
              <p class="mt-1 text-sm text-gray-500">Add new post</p>
            </div>
          </div>
        </router-link>
      </div>

      <!-- Router View Container -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useUserStore } from "~/stores/user";
import { useRouter } from "vue-router";

const userStore = useUserStore();
const user = computed(() => userStore.user);
const router = useRouter();

const logout = () => {
  userStore.logout();
  router.push("/login");
};
</script>