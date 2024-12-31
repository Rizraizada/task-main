import { ref } from 'vue';
import { useRuntimeConfig } from '#app';

export const useQuestions = () => {
    const questions = ref([]);
    const question = ref(null);
    const errorMessage = ref('');
    const successMessage = ref('');
    const config = useRuntimeConfig();
    const apiBase = config.public.apiBase;

    const getQuestions = async () => {
        try {
            const { data, error } = await useFetch(`${apiBase}/questions`);
            if (error.value) {
                errorMessage.value = error.value.message || 'Failed to fetch questions.';
            } else {
                questions.value = data.value;
            }
        } catch {
            errorMessage.value = 'An error occurred while fetching questions.';
        }
    };

    const getQuestionById = async (questionId) => {
        try {
            const { data, error } = await useFetch(`${apiBase}/questions/${questionId}`);
            if (error.value) {
                errorMessage.value = error.value.message || 'Failed to fetch question.';
            } else {
                question.value = data.value;
            }
        } catch {
            errorMessage.value = 'An error occurred while fetching the question.';
        }
    };

    const createQuestion = async (questionData) => {
        try {
            const { data, error } = await useFetch(`${apiBase}/questions`, {
                method: 'POST',
                body: questionData,
            });
            if (error.value) {
                errorMessage.value = error.value.message || 'Failed to create question.';
            } else {
                successMessage.value = 'Question created successfully!';
                return data.value;
            }
        } catch {
            errorMessage.value = 'An error occurred while creating the question.';
        }
    };

    const updateQuestion = async (questionId, updatedData) => {
        try {
            const { data, error } = await useFetch(`${apiBase}/questions/${questionId}`, {
                method: 'PUT',
                body: updatedData,
            });
            if (error.value) {
                errorMessage.value = error.value.message || 'Failed to update question.';
            } else {
                successMessage.value = 'Question updated successfully!';
                return data.value;
            }
        } catch {
            errorMessage.value = 'An error occurred while updating the question.';
        }
    };

    const deleteQuestion = async (questionId) => {
        try {
            const { error } = await useFetch(`${apiBase}/questions/${questionId}`, {
                method: 'DELETE',
            });
            if (error.value) {
                errorMessage.value = error.value.message || 'Failed to delete question.';
            } else {
                successMessage.value = 'Question deleted successfully!';
                return true;
            }
        } catch {
            errorMessage.value = 'An error occurred while deleting the question.';
        }
    };

    return {
        questions,
        question,
        errorMessage,
        successMessage,
        getQuestions,
        getQuestionById,
        createQuestion,
        updateQuestion,
        deleteQuestion,
    };
};
