// middleware/auth.js
export default defineNuxtRouteMiddleware((to, from) => {
    const userStore = useUserStore();
    if (!userStore.user) {
      return navigateTo('/login');
    }
  });
  