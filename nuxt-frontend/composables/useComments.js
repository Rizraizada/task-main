import { ref } from 'vue';
import { useRuntimeConfig, useFetch } from '#app';

export const useComments = () => {
  const comments = ref([]);
  const comment = ref(null);
  const replies = ref([]);
  const errorMessage = ref('');
  const successMessage = ref('');
  const config = useRuntimeConfig();
  const apiBase = config.public.apiBase;

  // Fetch all comments for a specific post
  const fetchComments = async (postId) => {
    try {
      const { data, error } = await useFetch(`${apiBase}/posts/${postId}/comments`);
      if (error.value) throw error.value;

      comments.value = data.value || [];
      successMessage.value = 'Comments loaded successfully.';
    } catch (err) {
      errorMessage.value = `Failed to load comments: ${err.message}`;
    }
  };

  // Add a new comment or reply
  const addComment = async (payload) => {
    try {
      const { data, error } = await useFetch(`${apiBase}/comments`, {
        method: 'POST',
        body: payload,
      });
      if (error.value) throw error.value;

      successMessage.value = 'Comment added successfully.';
      comment.value = data.value;
    } catch (err) {
      errorMessage.value = `Failed to add comment: ${err.message}`;
    }
  };

  // Update an existing comment
  const updateComment = async (commentId, payload) => {
    try {
      const { data, error } = await useFetch(`${apiBase}/comments/${commentId}`, {
        method: 'PUT',
        body: payload,
      });
      if (error.value) throw error.value;

      successMessage.value = 'Comment updated successfully.';
      comment.value = data.value;
    } catch (err) {
      errorMessage.value = `Failed to update comment: ${err.message}`;
    }
  };

  // Delete a comment
  const deleteComment = async (commentId) => {
    try {
      const { error } = await useFetch(`${apiBase}/comments/${commentId}`, {
        method: 'DELETE',
      });
      if (error.value) throw error.value;

      successMessage.value = 'Comment deleted successfully.';
    } catch (err) {
      errorMessage.value = `Failed to delete comment: ${err.message}`;
    }
  };

  // Fetch a single comment with its replies
  const fetchCommentWithReplies = async (commentId) => {
    try {
      const { data, error } = await useFetch(`${apiBase}/comments/${commentId}`);
      if (error.value) throw error.value;

      comment.value = data.value;
      replies.value = data.value?.replies || [];
      successMessage.value = 'Comment and replies loaded successfully.';
    } catch (err) {
      errorMessage.value = `Failed to load comment: ${err.message}`;
    }
  };

  return {
    comments,
    comment,
    replies,
    errorMessage,
    successMessage,
    fetchComments,
    addComment,
    updateComment,
    deleteComment,
    fetchCommentWithReplies,
  };
};
