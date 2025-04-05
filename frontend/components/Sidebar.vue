<!-- components/Sidebar.vue -->
<template>
    <div class="bg-gray-800 text-white w-64 h-screen p-4">
      <div class="space-y-4">
        <div class="text-lg font-bold text-yellow-400">Face Recognition</div>
        <div class="space-y-2">
          <ul>
            <li><a href="#" class="block py-2 hover:bg-gray-700 px-2 rounded">Dashboard</a></li>
            <li><a href="#" class="block py-2 hover:bg-gray-700 px-2 rounded">Detailed Logs</a></li>
            <li><a href="#" class="block py-2 hover:bg-gray-700 px-2 rounded">Register Student</a></li>
            <li><a href="#" class="block py-2 hover:bg-gray-700 px-2 rounded">Update Info</a></li>
            <li><a href="#" class="block py-2 hover:bg-gray-700 px-2 rounded">Notifications</a></li>
          </ul>
        </div>
        <div class="mt-10">
          <ul>
            <li><a href="#" class="block py-2 hover:bg-gray-700 px-2 rounded">Account</a></li>
            <li><a href="#" class="block py-2 hover:bg-gray-700 px-2 rounded">Saved Logs</a></li>
            <li><a href="#" class="block py-2 hover:bg-gray-700 px-2 rounded">Settings</a></li>
          </ul>
        </div>
        <div class="absolute bottom-4 w-full px-2">
          <ul>
            <li><a href="#" class="block py-2 hover:bg-gray-700 px-2 rounded">Admin</a></li>
            <li><a href="#" class="block py-2 hover:bg-gray-700 px-2 rounded">Help</a></li>
            <li><a href="#" class="block py-2 hover:bg-gray-700 px-2 rounded">Log Out</a></li>
          </ul>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'Sidebar',
  };
  </script>
  
  <style scoped>
  /* Add your styles if needed */
  </style>
  