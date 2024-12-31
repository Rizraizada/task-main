// composables/useUsers.js

import { ref } from 'vue'

export const useUsers = () => {
  const users = ref([])
  const user = ref(null)
  const errorMessage = ref('')
  const successMessage = ref('')

  const apiUrl = useRuntimeConfig().public.apiBase // Get the base API URL

  // Handle user creation
  const createUser = async (userData) => {
    try {
      const formData = new FormData()
      formData.append('username', userData.username)
      formData.append('email', userData.email)
      formData.append('password', userData.password)
      if (userData.profile_picture) {
        formData.append('profile_picture', userData.profile_picture)
      }
      formData.append('role', userData.role)

      const response = await fetch(`${apiUrl}/users`, {
        method: 'POST',
        body: formData,
      })

      if (!response.ok) {
        const errorData = await response.json()
        errorMessage.value = errorData.message || 'Failed to create user.'
      } else {
        const result = await response.json()
        successMessage.value = 'User created successfully!'
        return result // Optional: return created user data
      }
    } catch (error) {
      errorMessage.value = 'An error occurred while creating the user.'
    }
  }

  // Handle updating a user
  const updateUser = async (userId, updatedData) => {
    try {
      const response = await fetch(`${apiUrl}/users/${userId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(updatedData),
      })

      if (!response.ok) {
        const errorData = await response.json()
        errorMessage.value = errorData.message || 'Failed to update user.'
      } else {
        const result = await response.json()
        successMessage.value = 'User updated successfully!'
        return result // Optional: return updated user data
      }
    } catch (error) {
      errorMessage.value = 'An error occurred while updating the user.'
    }
  }

  // Handle deleting a user
  const deleteUser = async (userId) => {
    try {
      const response = await fetch(`${apiUrl}/users/${userId}`, {
        method: 'DELETE',
      })

      if (!response.ok) {
        const errorData = await response.json()
        errorMessage.value = errorData.message || 'Failed to delete user.'
      } else {
        successMessage.value = 'User deleted successfully!'
        return true // Return success flag
      }
    } catch (error) {
      errorMessage.value = 'An error occurred while deleting the user.'
    }
  }

  // Get all users
  const getUsers = async () => {
    try {
      const response = await fetch(`${apiUrl}/users`, {
        method: 'GET',
      })

      if (!response.ok) {
        const errorData = await response.json()
        errorMessage.value = errorData.message || 'Failed to fetch users.'
      } else {
        users.value = await response.json()
      }
    } catch (error) {
      errorMessage.value = 'An error occurred while fetching users.'
    }
  }

  // Get a single user by ID
  const getUserById = async (userId) => {
    try {
      const response = await fetch(`${apiUrl}/users/${userId}`, {
        method: 'GET',
      })

      if (!response.ok) {
        const errorData = await response.json()
        errorMessage.value = errorData.message || 'Failed to fetch user.'
      } else {
        user.value = await response.json()
      }
    } catch (error) {
      errorMessage.value = 'An error occurred while fetching the user.'
    }
  }

  return {
    users,
    user,
    errorMessage,
    successMessage,
    createUser,
    updateUser,
    deleteUser,
    getUsers,
    getUserById,
  }
}
