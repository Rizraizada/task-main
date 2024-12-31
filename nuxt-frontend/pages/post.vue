<template>
  <div>
    <h2>Filtered Posts</h2>
    <posts-index />
        <posts-list />

  </div>
</template>

<script setup>
import PostsIndex from '@/pages/posts/index.vue';
import PostsList from '@/pages/posts/list.vue';

definePageMeta({
  middleware: "auth",
});

</script>
