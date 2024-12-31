// stores/user.js
import { defineStore } from 'pinia';

export const useUserStore = defineStore('user', {
  state: () => ({
    user: process.client ? JSON.parse(localStorage.getItem('user')) : null,
  }),
  actions: {
    async login(email, password) {
      try {
        const response = await fetch(`${useRuntimeConfig().public.apiBase}/login`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ email, password }),
          credentials: 'include',
        });

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || 'Invalid credentials');
        }

        const data = await response.json();
        this.user = data.user;

        if (process.client) {
          localStorage.setItem('user', JSON.stringify(data.user)); // Persist user info
        }
      } catch (error) {
        throw error;
      }
    },
    logout() {
      this.user = null;
      if (process.client) {
        localStorage.removeItem('user');
      }
    },
  },
});
