<template>
  <div class="min-h-screen bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md">
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900">Subjects</h1>
        <button @click="showCreateModal = true" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200 ease-in-out">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Create New Subject
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="pending" class="p-6 text-center">
        <p class="text-gray-500">Loading...</p>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-red-600">{{ error }}</p>
      </div>

      <!-- Subject List -->
      <div class="p-6">
        <div v-if="subjects?.length" class="space-y-4">
          <div v-for="subject in subjects" :key="subject.id"
               class="flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:shadow-md transition duration-200">
            <div class="flex items-center space-x-4">
              <div class="flex-1">
                <h3 class="text-lg font-medium text-gray-900">{{ subject.teacher }}</h3>
                <p class="text-sm text-gray-500">Code: {{ subject.code }}</p>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <button @click="editSubject(subject.id)" 
                      class="p-2 text-gray-600 hover:text-blue-600 rounded-full hover:bg-blue-50 transition duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </button>
              <button @click="handleDeleteSubject(subject.id)"
                      class="p-2 text-gray-600 hover:text-red-600 rounded-full hover:bg-red-50 transition duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
        <div v-else-if="!pending" class="text-center py-12">
          <p class="text-gray-500">No subjects available.</p>
        </div>
      </div>

      <!-- Modal -->
      <div v-if="showCreateModal" 
           class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6" 
             @click.stop>
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
              {{ isEdit ? 'Edit' : 'Create' }} Subject
            </h2>
            <button @click="closeModal" 
                    class="text-gray-400 hover:text-gray-500">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <div>
              <label for="teacher" class="block text-sm font-medium text-gray-700 mb-2">
                Teacher
              </label>
              <input type="text" 
                     id="teacher" 
                     v-model="subjectForm.teacher" 
                     required
                     class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
            </div>
            
            <div>
              <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                Code
              </label>
              <input type="text" 
                     id="code" 
                     v-model="subjectForm.code" 
                     required
                     class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
            </div>

            <div class="flex space-x-4">
              <button type="submit" 
                      class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                {{ isEdit ? 'Update' : 'Create' }}
              </button>
              <button type="button" 
                      @click="closeModal"
                      class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition duration-200">
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useSubjects } from '~/composables/useSubjects';
definePageMeta({
  middleware: 'auth',
});

const {
  subjects,
  error,
  pending,
  fetchSubjects,
  createSubject,
  updateSubject,
  deleteSubject,
  fetchSubject
} = useSubjects();

const showCreateModal = ref(false);
const isEdit = ref(false);
const subjectForm = ref({ teacher: '', code: '' });
const selectedSubjectId = ref(null);

// Fetch subjects when the component mounts
await fetchSubjects();

const openCreateModal = () => {
  isEdit.value = false;
  subjectForm.value = { teacher: '', code: '' };
  showCreateModal.value = true;
};

const editSubject = async (id) => {
  isEdit.value = true;
  selectedSubjectId.value = id;
  const subject = await fetchSubject(id);
  if (subject) {
    subjectForm.value = { 
      teacher: subject.teacher, 
      code: subject.code 
    };
    showCreateModal.value = true;
  }
};

const handleSubmit = async () => {
  try {
    if (isEdit.value) {
      await updateSubject(selectedSubjectId.value, subjectForm.value);
    } else {
      await createSubject(subjectForm.value);
    }
    showCreateModal.value = false;
    await fetchSubjects(); // Refresh the list
  } catch (e) {
    console.error('Error submitting form:', e);
  }
};

const handleDeleteSubject = async (id) => {
  if (confirm('Are you sure you want to delete this subject?')) {
    try {
      await deleteSubject(id);
      await fetchSubjects(); // Refresh the list
    } catch (e) {
      console.error('Error deleting subject:', e);
    }
  }
};

const closeModal = () => {
  showCreateModal.value = false;
  subjectForm.value = { teacher: '', code: '' };
};
</script>