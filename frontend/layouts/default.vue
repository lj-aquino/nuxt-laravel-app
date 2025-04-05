<!-- /frontend/layouts/default.vue -->
<template>
    <div class="flex h-screen">
      <!-- Sidebar -->
      <Sidebar />
  
      <!-- Main Content Area -->
      <div class="flex-1 flex flex-col">
        <!-- Top Bar -->
        <TopBar />
  
        <!-- Page Content will go here -->
        <main class="p-6">
          <!-- Use the <NuxtLayout /> component to render the current page content -->
          <NuxtLayout />
        </main>
      </div>
    </div>
  </template>
  
  <script setup>
  import Sidebar from '~/components/Sidebar.vue';
  import TopBar from '~/components/TopBar.vue';
  </script>
  