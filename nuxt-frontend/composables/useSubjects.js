import { ref } from 'vue';
import { useFetch, useRuntimeConfig } from '#app';  // Ensure you are importing useRuntimeConfig

export const useSubjects = () => {
  const subjects = ref([]);
  const subject = ref(null);
  const error = ref(null);
  
  // Accessing useRuntimeConfig inside a composable
  const config = useRuntimeConfig();
  const apiBase = config.public.apiBase;

  // Fetch all subjects with chapters
  const fetchSubjects = async () => {
    try {
      const { data, error: fetchError } = await useFetch(`${apiBase}/subjects`);
      if (fetchError.value) {
        error.value = fetchError.value;
      } else {
        subjects.value = data.value;
      }
    } catch (err) {
      error.value = err.message;
    }
  };

  // Fetch a single subject by ID
  const fetchSubject = async (id) => {
    try {
      const { data, error: fetchError } = await useFetch(`${apiBase}/subjects/${id}`);
      if (fetchError.value) {
        error.value = fetchError.value;
      } else {
        subject.value = data.value;
      }
    } catch (err) {
      error.value = err.message;
    }
  };

  // Create a new subject
  const createSubject = async (newSubject) => {
    try {
      const { data, error: createError } = await useFetch(`${apiBase}/subjects`, {
        method: 'POST',
        body: newSubject,
      });
      if (createError.value) {
        error.value = createError.value;
      } else {
        subjects.value.push(data.value);
      }
    } catch (err) {
      error.value = err.message;
    }
  };

  // Update an existing subject
  const updateSubject = async (id, updatedSubject) => {
    try {
      const { data, error: updateError } = await useFetch(`${apiBase}/subjects/${id}`, {
        method: 'PUT',
        body: updatedSubject,
      });
      if (updateError.value) {
        error.value = updateError.value;
      } else {
        const index = subjects.value.findIndex((subject) => subject.id === id);
        if (index !== -1) {
          subjects.value[index] = data.value;
        }
      }
    } catch (err) {
      error.value = err.message;
    }
  };

  // Delete a subject
  const deleteSubject = async (id) => {
    try {
      const { error: deleteError } = await useFetch(`${apiBase}/subjects/${id}`, {
        method: 'DELETE',
      });
      if (deleteError.value) {
        error.value = deleteError.value;
      } else {
        subjects.value = subjects.value.filter((subject) => subject.id !== id);
      }
    } catch (err) {
      error.value = err.message;
    }
  };

  return {
    subjects,
    subject,
    error,
    fetchSubjects,
    fetchSubject,
    createSubject,
    updateSubject,
    deleteSubject,
  };
};
