import { ref } from 'vue'
import { useRuntimeConfig } from '#app'

export const usePosts = () => {
  const posts = ref([])
  const post = ref(null)
  const errorMessage = ref('')
  const successMessage = ref('')
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  // Create a new post
  const createPost = async (newPost) => {
    try {
      const { data, error } = await useFetch(`${apiBase}/posts`, {
        method: 'POST',
        body: newPost,
      })

      if (error.value) {
        throw error.value
      }

      successMessage.value = 'Post created successfully!'
      return data.value
    } catch (err) {
      console.error('Error creating post:', err)
      errorMessage.value = err.data?.message || 'Failed to create post'
      return null
    }
  }

  // Fetch posts with pagination support
  const fetchPosts = async (page = 1, postsPerPage = 5) => {
    try {
      const { data, error } = await useFetch(`${apiBase}/posts?page=${page}&limit=${postsPerPage}`)
  
      if (error.value) {
        errorMessage.value = error.value.message || 'Failed to fetch posts.'
        return []
      } else {
        // Sort posts by created_at (assuming it's a valid date field)
        posts.value = data.value.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
      }
    } catch (err) {
      errorMessage.value = 'An error occurred while fetching posts.'
      console.error(err)
      return []
    }
  }
  

  // Get a single post by ID
  const getPostById = async (postId) => {
    try {
      const { data, error } = await useFetch(`${apiBase}/posts/${postId}`)

      if (error.value) {
        throw error.value
      }

      post.value = data.value
      return data.value
    } catch (err) {
      console.error('Error fetching post:', err)
      errorMessage.value = 'Failed to fetch post'
      return null
    }
  }

  // Update a post
  const updatePost = async (postId, updatedData) => {
    try {
      const { data, error } = await useFetch(`${apiBase}/posts/${postId}`, {
        method: 'PUT',
        body: updatedData,
      })

      if (error.value) {
        errorMessage.value = 'Failed to update post'
        return null
      }

      successMessage.value = 'Post updated successfully!'
      return data.value
    } catch (err) {
      console.error('Error updating post:', err)
      errorMessage.value = 'Failed to update post'
      return null
    }
  }

  // Delete a post
  const deletePost = async (postId) => {
    try {
      const { data, error } = await useFetch(`${apiBase}/posts/${postId}`, {
        method: 'DELETE',
      })

      if (error.value) {
        errorMessage.value = 'Failed to delete post'
        return null
      }

      successMessage.value = 'Post deleted successfully!'
      // Remove the post from the UI list
      posts.value = posts.value.filter(post => post.id !== postId)
      return data.value
    } catch (err) {
      console.error('Error deleting post:', err)
      errorMessage.value = 'Failed to delete post'
      return null
    }
  }

  // Clear messages
  const clearMessages = () => {
    errorMessage.value = ''
    successMessage.value = ''
  }

  return {
    posts,
    post,
    createPost,
    fetchPosts,
    getPostById,
    updatePost,
    deletePost,
    errorMessage,
    successMessage,
    clearMessages,
  }
}
