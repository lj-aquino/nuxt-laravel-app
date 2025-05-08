// Example of using the header in another page
<template>
  <div class="page-container">
    <AppHeader />
    
    <!-- Page-specific content goes here -->
    <main>
      <h1>Page Title</h1>
      <p>Page content...</p>
    </main>
  </div>
</template>

<script setup>
import AppHeader from '~/components/AppHeader.vue';
</script>