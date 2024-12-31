import { ref } from 'vue'
import { useRuntimeConfig } from '#app'; // Ensure you are using useRuntimeConfig to access API base URL

export const useChapters = () => {
  const chapters = ref([])
  const chapter = ref(null)
  const errorMessage = ref('')
  const successMessage = ref('')
  const config = useRuntimeConfig();
  const apiBase = config.public.apiBase;

  // Get all chapters with questions
  const getChapters = async () => {
    try {
      const { data, error } = await useFetch(`${apiBase}/chapters`);
      if (error.value) {
        errorMessage.value = error.value.message || 'Failed to fetch chapters.';
      } else {
        chapters.value = data.value;
      }
    } catch (err) {
      errorMessage.value = 'An error occurred while fetching chapters.';
    }
  };

  // Get a single chapter by ID
  const getChapterById = async (chapterId) => {
    try {
      const { data, error } = await useFetch(`${apiBase}/chapters/${chapterId}`);
      if (error.value) {
        errorMessage.value = error.value.message || 'Failed to fetch chapter.';
      } else {
        chapter.value = data.value;
      }
    } catch (err) {
      errorMessage.value = 'An error occurred while fetching the chapter.';
    }
  };

  // Create a new chapter
  const createChapter = async (chapterData) => {
    try {
      const { data, error } = await useFetch(`${apiBase}/chapters`, {
        method: 'POST',
        body: chapterData, // No need to stringify because `useFetch` handles it automatically
      });
      if (error.value) {
        errorMessage.value = error.value.message || 'Failed to create chapter.';
      } else {
        successMessage.value = 'Chapter created successfully!';
        return data.value;
      }
    } catch (err) {
      errorMessage.value = 'An error occurred while creating the chapter.';
    }
  };

  // Update an existing chapter
  const updateChapter = async (chapterId, updatedData) => {
    try {
      const { data, error } = await useFetch(`${apiBase}/chapters/${chapterId}`, {
        method: 'PUT',
        body: updatedData, // No need to stringify because `useFetch` handles it automatically
      });
      if (error.value) {
        errorMessage.value = error.value.message || 'Failed to update chapter.';
      } else {
        successMessage.value = 'Chapter updated successfully!';
        return data.value;
      }
    } catch (err) {
      errorMessage.value = 'An error occurred while updating the chapter.';
    }
  };

  // Delete a chapter
  const deleteChapter = async (chapterId) => {
    try {
      const { error } = await useFetch(`${apiBase}/chapters/${chapterId}`, {
        method: 'DELETE',
      });
      if (error.value) {
        errorMessage.value = error.value.message || 'Failed to delete chapter.';
      } else {
        successMessage.value = 'Chapter deleted successfully!';
        return true;
      }
    } catch (err) {
      errorMessage.value = 'An error occurred while deleting the chapter.';
    }
  };

  return {
    chapters,
    chapter,
    errorMessage,
    successMessage,
    getChapters,
    getChapterById,
    createChapter,
    updateChapter,
    deleteChapter,
  };
};
