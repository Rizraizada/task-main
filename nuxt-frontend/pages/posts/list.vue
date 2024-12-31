<template>
  <div class="max-w-4xl mx-auto p-4">
    <!-- Filter Section -->
    <div class="flex items-center justify-between mb-6 bg-white p-4 rounded-lg shadow">
      <h1 class="text-xl font-semibold">Recent Posts</h1>
      <div class="flex gap-4">
        <select v-model="selectedUser" class="rounded-md border-gray-300">
          <option value="">All Users</option>
          <option v-for="user in users" :key="user.id" :value="user.id">
            {{ user.username }}
          </option>
        </select>
        <select v-model="selectedSubject" class="rounded-md border-gray-300">
          <option value="">Select a Subject</option>
          <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
            {{ subject.name }}
          </option>
        </select>
        <select v-model="selectedChapter" class="rounded-md border-gray-300">
          <option value="">Select a Chapter</option>
          <option v-for="chapter in chapters" :key="chapter.id" :value="chapter.id">
            {{ chapter.name }}
          </option>
        </select>
        <button @click="resetFilters" class="text-blue-500 hover:text-blue-600">
          Reset Filter
        </button>
      </div>
    </div>

    <!-- Posts List -->
    <div class="space-y-6">
      <div v-for="post in filteredPosts" :key="post.id" 
           class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Post Header -->
        <div class="p-4 border-b">
          <div class="flex items-center space-x-3">
            <img :src="getProfileImageUrl(post.user_profile_picture)" 
                 :alt="post.user_username"
                 class="w-10 h-10 rounded-full object-cover"/>
            <div>
              <div class="font-medium">{{ post.user_username }}</div>
              <div class="text-sm text-gray-500">{{ formatDate(post.date) }}</div>
            </div>
          </div>
        </div>

        <!-- Post Content -->
        <div class="p-4">
          <div class="mb-2">
            <span class="font-semibold">{{ post.subject_code }}</span>
            <span class="mx-2">•</span>
            <span>{{ post.chapter_name }}</span>
          </div>
          <h2 class="text-lg font-medium mb-2">{{ post.text }}</h2>
          <p class="text-gray-700">{{ post.question_text }}</p>

          <!-- Media Content -->
          <div class="mt-4 space-y-4">
            <div v-if="post.media && post.media.length" class="grid grid-cols-2 gap-4">
              <template v-for="(media, index) in post.media" :key="index">
                <!-- Image -->
                <img v-if="isImage(media)" 
                     :src="getMediaUrl(media)"
                     class="rounded-lg max-h-64 w-full object-cover"
                     :alt="post.text"/>
                <!-- Audio -->
                <audio v-if="isAudio(media)"
                       :src="getMediaUrl(media)"
                       controls
                       class="w-full">
                </audio>
              </template>
            </div>
          </div>
        </div>

        <!-- Comment Section -->
        <div class="p-4 bg-gray-50">
          <!-- Add Comment Form -->
          <form @submit.prevent="submitComment(post.id)" class="mb-6">
            <div class="flex items-start space-x-4">
              <img :src="getProfileImageUrl(currentUser.profile_picture)"
                   :alt="currentUser.username"
                   class="w-8 h-8 rounded-full"/>
              <div class="flex-grow">
                <textarea v-model="newComments[post.id]"
                         rows="2"
                         class="w-full p-3 border rounded-lg resize-none"
                         placeholder="Write a comment..."></textarea>
                
                <!-- Media Upload Controls -->
                <div class="flex items-center mt-2 space-x-4">
                  <div class="flex space-x-2">
                    <!-- Voice Recording -->
                    <button type="button" 
                            @click="toggleRecording(post.id)"
                            class="p-2 rounded-full hover:bg-gray-200">
                      <div :class="['w-4 h-4 rounded-full', 
                                  isRecording === post.id ? 'bg-red-500 animate-pulse' : 'bg-gray-600']">
                      </div>
                    </button>

                    <!-- Image Upload -->
                    <label class="p-2 rounded-full hover:bg-gray-200 cursor-pointer">
                      <input type="file"
                             @change="handleMediaUpload($event, post.id)"
                             accept="image/*"
                             class="hidden"
                             multiple/>
                      <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                    </label>
                  </div>

                  <button type="submit"
                          class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
                          :disabled="!newComments[post.id]">
                    Post
                  </button>
                </div>

                <!-- Media Previews -->
                <div v-if="mediaPreview[post.id]" class="mt-4 flex flex-wrap gap-4">
                  <div v-for="(preview, index) in mediaPreview[post.id]" 
                       :key="index" 
                       class="relative">
                    <img v-if="preview.type === 'image'" 
                         :src="preview.url" 
                         class="h-20 w-20 object-cover rounded"/>
                    <audio v-if="preview.type === 'audio'" 
                           :src="preview.url" 
                           controls 
                           class="w-48"></audio>
                    <button @click="removeMedia(post.id, index)"
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center">
                      ×
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </form>

          <!-- Comments List -->
          <div class="space-y-4">
            <div v-for="comment in post.comments" 
                 :key="comment.id" 
                 class="pl-8">
              <div class="flex items-start space-x-3">
                <img :src="getProfileImageUrl(comment.user.profile_picture)"
                     :alt="comment.user.username"
                     class="w-8 h-8 rounded-full"/>
                <div class="flex-grow">
                  <div class="bg-white p-3 rounded-lg shadow-sm">
                    <div class="font-medium">{{ comment.user.username }}</div>
                    <p class="text-gray-700">{{ comment.comment_text }}</p>
                  </div>
                  
                  <!-- Comment Media -->
                  <div v-if="comment.media" class="mt-2 flex gap-2">
                    <template v-for="(media, index) in comment.media" :key="index">
                      <img v-if="isImage(media)" 
                           :src="getMediaUrl(media)"
                           class="h-20 w-20 object-cover rounded"/>
                      <audio v-if="isAudio(media)"
                             :src="getMediaUrl(media)"
                             controls
                             class="w-48"></audio>
                    </template>
                  </div>

                  <!-- Comment Actions -->
                  <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                    <button @click="toggleReply(comment.id)"
                            class="hover:text-blue-500">
                      Reply
                    </button>
                    <button v-if="canDelete(comment)"
                            @click="deleteComment(comment.id, post.id)"
                            class="hover:text-red-500">
                      Delete
                    </button>
                  </div>
                </div>
              </div>

              <!-- Reply Form -->
              <div v-if="showReplyForm === comment.id" class="mt-2 pl-11">
                <form @submit.prevent="submitReply(comment.id, post.id)">
                  <textarea v-model="newReplies[comment.id]"
                           rows="2"
                           class="w-full p-3 border rounded-lg resize-none"
                           placeholder="Write a reply..."></textarea>
                  <div class="mt-2 flex justify-end">
                    <button type="submit"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600"
                            :disabled="!newReplies[comment.id]">
                      Reply
                    </button>
                  </div>
                </form>
              </div>

              <!-- Replies -->
              <div v-for="reply in comment.replies" 
                   :key="reply.id" 
                   class="mt-2 pl-11">
                <div class="flex items-start space-x-3">
                  <img :src="getProfileImageUrl(reply.user.profile_picture)"
                       :alt="reply.user.username"
                       class="w-8 h-8 rounded-full"/>
                  <div class="flex-grow">
                    <div class="bg-white p-3 rounded-lg shadow-sm">
                      <div class="font-medium">{{ reply.user.username }}</div>
                      <p class="text-gray-700">{{ reply.comment_text }}</p>
                    </div>
                    <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                      <button v-if="canDelete(reply)"
                              @click="deleteComment(reply.id, post.id)"
                              class="hover:text-red-500">
                        Delete
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue';
import { useUserStore } from '@/stores/user';
import { usePosts } from '@/composables/usePosts';
import { useComments } from '@/composables/useComments';
import { useRuntimeConfig } from '#app';

const runtimeConfig = useRuntimeConfig();
const userStore = useUserStore();
const { fetchPosts, posts } = usePosts();
const { addComment, deleteComment: deleteCommentAPI } = useComments();

// State management
const selectedUser = ref('');
const selectedSubject = ref('');
const selectedChapter = ref('');
const newComments = ref({});
const newReplies = ref({});
const showReplyForm = ref(null);
const isRecording = ref(null);
const mediaPreview = ref({});
const mediaFiles = ref({});
const errorMessage = ref('');
const successMessage = ref('');

// Recording state
let mediaRecorder = null;
let audioChunks = [];

// Computed properties
const currentUser = computed(() => userStore.user);
const filteredPosts = computed(() => {
  let filtered = posts.value;
  if (selectedUser.value) filtered = filtered.filter(post => post.user_id === selectedUser.value);
  if (selectedSubject.value) filtered = filtered.filter(post => post.subject_code === selectedSubject.value);
  if (selectedChapter.value) filtered = filtered.filter(post => post.chapter_id === selectedChapter.value);
  return filtered;
});

// Lifecycle hooks
onMounted(async () => {
  await fetchPosts();
});

// Media handling methods
const startRecording = async (postId) => {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
    mediaRecorder = new MediaRecorder(stream);
    audioChunks = [];

    mediaRecorder.ondataavailable = (event) => {
      audioChunks.push(event.data);
    };

    mediaRecorder.onstop = () => {
      const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
      if (!mediaPreview.value[postId]) {
        mediaPreview.value[postId] = [];
        mediaFiles.value[postId] = [];
      }
      mediaPreview.value[postId].push({
        type: 'audio',
        url: URL.createObjectURL(audioBlob)
      });
      mediaFiles.value[postId].push(new File([audioBlob], `recording_${Date.now()}.wav`));
    };

    mediaRecorder.start();
    isRecording.value = postId;
  } catch (error) {
    errorMessage.value = 'Error accessing microphone';
    console.error('Microphone error:', error);
  }
};

const stopRecording = () => {
  if (mediaRecorder && mediaRecorder.state === 'recording') {
    mediaRecorder.stop();
    isRecording.value = null;
    mediaRecorder.stream.getTracks().forEach(track => track.stop());
  }
};

const handleMediaUpload = (event, postId) => {
  const files = Array.from(event.target.files);
  if (!mediaPreview.value[postId]) {
    mediaPreview.value[postId] = [];
    mediaFiles.value[postId] = [];
  }

  files.forEach(file => {
    if (file.size > 10 * 1024 * 1024) {
      errorMessage.value = 'File size should not exceed 10MB';
      return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
      mediaPreview.value[postId].push({
        type: file.type.startsWith('image/') ? 'image' : 'audio',
        url: e.target.result
      });
    };
    reader.readAsDataURL(file);
    mediaFiles.value[postId].push(file);
  });
};

const removeMedia = (postId, index) => {
  mediaPreview.value[postId].splice(index, 1);
  mediaFiles.value[postId].splice(index, 1);
};

// Comment and reply methods
const submitComment = async (postId) => {
  try {
    const formData = new FormData();
    formData.append('post_id', postId);
    formData.append('user_id', currentUser.value.id);
    formData.append('comment_text', newComments.value[postId]);

    if (mediaFiles.value[postId]) {
      mediaFiles.value[postId].forEach(file => {
        formData.append('media[]', file);
      });
    }

    await addComment(formData);
    newComments.value[postId] = '';
    mediaPreview.value[postId] = [];
    mediaFiles.value[postId] = [];
    await fetchPosts();
    successMessage.value = 'Comment added successfully';
  } catch (error) {
    errorMessage.value = 'Failed to add comment';
    console.error('Comment error:', error);
  }
};

const submitReply = async (commentId, postId) => {
  try {
    const formData = new FormData();
    formData.append('post_id', postId);
    formData.append('user_id', currentUser.value.id);
    formData.append('comment_text', newReplies.value[commentId]);
    formData.append('parent_comment_id', commentId);

    await addComment(formData);
    newReplies.value[commentId] = '';
    showReplyForm.value = null;
    await fetchPosts();
    successMessage.value = 'Reply added successfully';
  } catch (error) {
    errorMessage.value = 'Failed to add reply';
    console.error('Reply error:', error);
  }
};

const deleteComment = async (commentId, postId) => {
  try {
    await deleteCommentAPI(commentId);
    await fetchPosts();
    successMessage.value = 'Comment deleted successfully';
  } catch (error) {
    errorMessage.value = 'Failed to delete comment';
    console.error('Delete error:', error);
  }
};

// Utility methods
const resetFilters = () => {
  selectedUser.value = '';
  selectedSubject.value = '';
  selectedChapter.value = '';
};

const getProfileImageUrl = (path) => {
  return path ? `${runtimeConfig.public.imageBase}/${path}` : '/default-avatar.png';
};

const getMediaUrl = (path) => {
  return `${runtimeConfig.public.imageBase}/${path}`;
};

const formatDate = (date) => {
  return new Date(date).toLocaleString();
};

const isImage = (path) => {
  return /\.(jpg|jpeg|png|gif|webp)$/i.test(path);
};

const isAudio = (path) => {
  return /\.(mp3|wav|ogg)$/i.test(path);
};

const canDelete = (comment) => {
  return comment.user_id === currentUser.value.id;
};

const toggleReply = (commentId) => {
  showReplyForm.value = showReplyForm.value === commentId ? null : commentId;
};

// Clear messages after delay
const clearMessages = () => {
  setTimeout(() => {
    errorMessage.value = '';
    successMessage.value = '';
  }, 3000);
};

// Watch for message changes
watch([errorMessage, successMessage], () => {
  if (errorMessage.value || successMessage.value) {
    clearMessages();
  }
});
</script>