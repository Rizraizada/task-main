<template>
  <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
      <!-- Header Section -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Questions</h1>
        <p class="text-gray-600">Manage and create questions for your chapters</p>
      </div>

      <!-- Questions List -->
      <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Existing Questions</h2>
        <div v-if="Array.isArray(questions) && questions.length" 
             class="divide-y divide-gray-200">
          <router-link
            v-for="question in questions"
            :key="question.id"
            :to="`/questions/${question.id}`"
            class="block py-4 hover:bg-gray-50 transition-colors group"
          >
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-medium text-gray-900 group-hover:text-blue-600">
                  {{ question.text }}
                </h3>
                <p class="text-sm text-gray-500 mt-1">{{ question.question_text }}</p>
              </div>
              <svg 
                class="w-5 h-5 text-gray-400 group-hover:text-blue-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </router-link>
        </div>
        <div v-else class="text-center py-8">
          <p class="text-gray-500">No questions available</p>
        </div>
      </div>

      <!-- Create Question Form -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Create New Question</h2>
        <form @submit.prevent="createNewQuestion" class="space-y-6">
          <!-- Chapter Select -->
          <div>
            <label for="chapter" class="block text-sm font-medium text-gray-700 mb-2">
              Select Chapter
            </label>
            <select
              v-model="newQuestion.chapter_id"
              id="chapter"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="" disabled>Select a chapter</option>
              <option
                v-for="chapter in chapters"
                :key="chapter.id"
                :value="chapter.id"
              >
                {{ chapter.name }}
              </option>
            </select>
          </div>

          <!-- Question Name -->
          <div>
            <label for="text" class="block text-sm font-medium text-gray-700 mb-2">
              Question Name
            </label>
            <input
              type="text"
              v-model="newQuestion.text"
              id="text"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              placeholder="Enter question name"
            />
          </div>

          <!-- Question Text -->
          <div>
            <label for="question_text" class="block text-sm font-medium text-gray-700 mb-2">
              Question Text
            </label>
            <textarea
              v-model="newQuestion.question_text"
              id="question_text"
              required
              rows="4"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              placeholder="Enter detailed question text"
            ></textarea>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end">
            <button
              type="submit"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            >
              <svg
                class="w-4 h-4 mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Create Question
            </button>
          </div>
        </form>

        <!-- Messages -->
        <div class="mt-6">
          <div
            v-if="successMessage"
            class="rounded-md bg-green-50 p-4"
          >
            <div class="flex">
              <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="ml-2 text-sm text-green-700">{{ successMessage }}</span>
            </div>
          </div>
          <div
            v-if="errorMessage"
            class="rounded-md bg-red-50 p-4"
          >
            <div class="flex">
              <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <span class="ml-2 text-sm text-red-700">{{ errorMessage }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useQuestions } from '~/composables/useQuestions';
import { useChapters } from '~/composables/useChapters';

const { questions, successMessage, errorMessage, getQuestions, createQuestion } = useQuestions();
const { chapters, getChapters } = useChapters();

const newQuestion = ref({
  chapter_id: '',
  text: '',
  question_text: '',
});

onMounted(() => {
  getQuestions();
  getChapters();
});

const createNewQuestion = async () => {
  const questionData = { ...newQuestion.value };
  const result = await createQuestion(questionData);
  if (result) {
    newQuestion.value = { chapter_id: '', text: '', question_text: '' };
    getQuestions();
  }
};
</script>