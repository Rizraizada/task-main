<template>
  <div class="max-w-2xl mx-auto p-4">
    <!-- Form Header -->
    <h1 class="text-2xl font-bold mb-4">Ask a New Question</h1>

    <!-- Error and Success Messages -->
    <div v-if="errorMessage" class="p-4 mb-4 bg-red-50 text-red-500 rounded-lg">
      {{ errorMessage }}
    </div>
    <div v-if="successMessage" class="p-4 mb-4 bg-green-50 text-green-500 rounded-lg">
      {{ successMessage }}
    </div>

    <!-- Post Creation Form -->
    <form @submit.prevent="createNewPost" class="space-y-4">
      <!-- Subject and Chapter Selection -->
      <div class="flex gap-4">
        <select
          v-model="newPost.subject_id"
          required
          class="block w-1/2 px-3 py-2 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="" disabled>Select a Subject</option>
          <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
            {{ subject.code }}
          </option>
        </select>

        <select
          v-model="newPost.chapter_id"
          required
          class="block w-1/2 px-3 py-2 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="" disabled>Select a Chapter</option>
          <option v-for="chapter in chapters" :key="chapter.id" :value="chapter.id">
            {{ chapter.name }}
          </option>
        </select>

        <select
          v-model="newPost.question_id"
          class="block w-1/2 px-3 py-2 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="" disabled>Select a Question</option>
          <option v-for="question in questions" :key="question.id" :value="question.id">
            {{ question.text }}
          </option>
        </select>
      </div>

      <!-- Question Title Input -->
      <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
        <input
          v-model="newPost.text"
          type="text"
          placeholder="Enter your question title..."
          class="w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        />
      </div>

      <!-- Question Content Area -->
      <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
        <textarea
          v-model="newPost.post_text"
          placeholder="Write your question details..."
          class="w-full px-4 py-3 min-h-[120px] focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
          required
        ></textarea>

        <!-- Media Controls -->
        <div class="flex items-center px-4 py-3 border-t border-gray-200 bg-gray-50">
          <!-- Voice Recording Button -->
          <button
            type="button"
            @click="isRecording ? stopRecording() : startRecording()"
            class="p-2 rounded-full hover:bg-gray-200 transition-colors"
          >
            <div class="w-6 h-6 flex items-center justify-center">
              <div
                :class="[
                  'w-4 h-4 rounded-full',
                  isRecording ? 'bg-red-500 animate-pulse' : 'bg-gray-600',
                ]"
              ></div>
            </div>
          </button>

          <!-- Image Upload Button -->
          <label class="p-2 rounded-full hover:bg-gray-200 transition-colors cursor-pointer ml-2">
            <input
              type="file"
              @change="handleImageUpload"
              accept="image/*"
              class="hidden"
            />
            <div class="w-6 h-6 flex items-center justify-center text-gray-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
          </label>

          <!-- Preview Section -->
          <div v-if="imagePreview || audioUrl" class="flex items-center gap-2 mx-2">
            <!-- Image Preview -->
            <div v-if="imagePreview" class="relative">
              <img
                :src="imagePreview"
                alt="Preview"
                class="max-w-[150px] h-[100px] object-cover rounded-lg border border-gray-300"
              />
              <button
                type="button"
                @click="removeImage"
                class="absolute -top-1 -right-1 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors w-5 h-5 flex items-center justify-center text-xs"
              >
                ×
              </button>
            </div>

            <!-- Audio Preview -->
            <div v-if="audioUrl" class="relative">
              <audio :src="audioUrl" controls class="h-8 w-32"></audio>
              <button
                type="button"
                @click="removeAudio"
                class="absolute -top-1 -right-1 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors w-5 h-5 flex items-center justify-center text-xs"
              >
                ×
              </button>
            </div>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            class="ml-auto p-2 rounded-full bg-blue-500 hover:bg-blue-600 transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useUserStore } from "@/stores/user";
import { usePosts } from "@/composables/usePosts";
import { useChapters } from "@/composables/useChapters";
import { useSubjects } from "@/composables/useSubjects";
import { useQuestions } from "@/composables/useQuestions";
import { useComments } from "@/composables/useComments";

definePageMeta({
  middleware: "auth",
});

// States and functions for posts
const userStore = useUserStore();
const {
  createPost,
  fetchPosts,
  errorMessage,
  successMessage,
  posts,
  updatePost,
  deletePost: deletePostAPI,
} = usePosts();
const { getChapters, chapters } = useChapters();
const { fetchSubjects, subjects } = useSubjects();
const { getQuestions, questions } = useQuestions();

// Post data with default empty user_id
const newPost = ref({
  user_id: "",
  subject_id: "",
  chapter_id: "",
  question_id: "",
  post_text: "",
  text: "",
  voice_url: "",
  image_url: "",
});

// Watch for changes in userStore.user
watch(() => userStore.user, (newUser) => {
  if (newUser) {
    newPost.value.user_id = newUser.id;
  }
}, { immediate: true });

// Media states
const imagePreview = ref(null);
const audioUrl = ref(null);
const isRecording = ref(false);
let mediaRecorder = null;
let audioChunks = [];

// Methods for handling image upload
const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const removeImage = () => {
  imagePreview.value = null;
  newPost.value.image_url = "";
};

// Methods for handling audio recording
const startRecording = async () => {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
    mediaRecorder = new MediaRecorder(stream);
    audioChunks = [];

    mediaRecorder.ondataavailable = (event) => {
      audioChunks.push(event.data);
    };

    mediaRecorder.onstop = () => {
      const audioBlob = new Blob(audioChunks, { type: "audio/wav" });
      audioUrl.value = URL.createObjectURL(audioBlob);
      newPost.value.voice_url = audioUrl.value;
    };

    mediaRecorder.start();
    isRecording.value = true;
  } catch (error) {
    console.error("Error accessing microphone:", error);
  }
};

const stopRecording = () => {
  if (mediaRecorder && isRecording.value) {
    mediaRecorder.stop();
    isRecording.value = false;
  }
};

const removeAudio = () => {
  audioUrl.value = null;
  newPost.value.voice_url = "";
};

// Create a new post
const createNewPost = async () => {
  try {
    // Check if user is authenticated
    if (!userStore.user) {
      errorMessage.value = "Please log in to create a post";
      return;
    }
    
    // Create FormData for file upload
    const formData = new FormData();
    
    // Append all form fields
    Object.keys(newPost.value).forEach(key => {
      formData.append(key, newPost.value[key]);
    });

    // Handle image file
    if (imagePreview.value) {
      const response = await fetch(imagePreview.value);
      const blob = await response.blob();
      formData.append('image', blob);
    }

    // Handle audio file
    if (audioUrl.value) {
      const response = await fetch(audioUrl.value);
      const blob = await response.blob();
      formData.append('audio', blob);
    }

    const response = await createPost(formData);
    
    if (response) {
      // Reset form after successful submission
      newPost.value = {
        user_id: userStore.user.id,
        subject_id: "",
        chapter_id: "",
        question_id: "",
        post_text: "",
        text: "",
        voice_url: "",
        image_url: "",
      };
      imagePreview.value = null;
      audioUrl.value = null;
    }
  } catch (error) {
    console.error('Error creating post:', error);
    errorMessage.value = "Error creating post. Please try again.";
  }
};

// Fetch data on mount
onMounted(async () => {
  await fetchSubjects();
  await getChapters();
  await getQuestions();
});
</script>