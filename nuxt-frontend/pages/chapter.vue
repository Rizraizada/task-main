<template>
  <div>
    <h1>Chapters</h1>

    <!-- Chapters List -->
    <ul v-if="chapters && chapters.length">
      <li v-for="chapter in chapters" :key="chapter.id">
        <router-link :to="`/chapters/${chapter.id}`">{{
          chapter.name
        }}</router-link>
      </li>
    </ul>

    <!-- Add New Chapter Form -->
    <h2>Create New Chapter</h2>
    <form @submit.prevent="createNewChapter">
      <div>
        <label for="subject">Subject</label>
        <select v-model="newChapter.subject_id" id="subject" required>
          <option
            v-for="subject in subjects"
            :key="subject.id"
            :value="subject.id"
          >
            {{ subject.code }}
          </option>
        </select>
      </div>

      <div>
        <label for="name">Chapter Name</label>
        <input type="text" v-model="newChapter.name" id="name" required />
      </div>

      <div>
        <label for="number">Chapter Number</label>
        <input type="text" v-model="newChapter.number" id="number" required />
      </div>

      <button type="submit">Create Chapter</button>
    </form>

    <p v-if="successMessage" class="success">{{ successMessage }}</p>
    <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useChapters } from "~/composables/useChapters";
import { useSubjects } from "~/composables/useSubjects";

const { chapters, errorMessage, getChapters, createChapter, successMessage } =
  useChapters();
const { subjects, fetchSubjects } = useSubjects();
definePageMeta({
  middleware: "auth",
});

// For creating new chapter
const newChapter = ref({
  subject_id: "",
  name: "",
  number: "",
});

onMounted(() => {
  getChapters();
  fetchSubjects();
});

const createNewChapter = async () => {
  const chapterData = {
    subject_id: newChapter.value.subject_id,
    name: newChapter.value.name,
    number: newChapter.value.number,
  };

  const result = await createChapter(chapterData);
  if (result) {
    // Clear the form on successful chapter creation
    newChapter.value = {
      subject_id: "",
      name: "",
      number: "",
    };
    getChapters(); // Refresh the list of chapters
  }
};
</script>

<style scoped>
.error {
  color: red;
}

.success {
  color: green;
}

form {
  margin-top: 20px;
}

form div {
  margin-bottom: 10px;
}
</style>
