<!-- /frontend/pages/dashboard.vue -->
<template>
    <div class="flex space-x-6">
      <!-- Left Column (Face Scanning) -->
      <div class="w-1/3 bg-white shadow-md rounded-lg p-6">
        <div class="text-xl font-semibold">Face Scanning</div>
        <div class="mt-4 bg-gray-200 h-32 rounded-lg"></div>
        <div class="mt-4">
          <div class="font-semibold text-lg">Student Info</div>
          <div class="text-sm">Ly*a Jo**e A. (2019-09924)</div>
          <div class="text-sm text-green-500">Verified</div>
        </div>
        <div class="mt-4">
          <button class="w-full py-2 bg-red-500 text-white rounded-lg">Enter ID</button>
          <button class="w-full mt-4 py-2 bg-yellow-500 text-white rounded-lg">Scan ID</button>
        </div>
      </div>
  
      <!-- Right Column (Logs Summary) -->
      <div class="flex-1 bg-white shadow-md rounded-lg p-6">
        <div class="text-xl font-semibold mb-4">Logs Summary</div>
        <table class="min-w-full text-sm">
          <thead>
            <tr>
              <th class="text-left p-2">Student No.</th>
              <th class="text-left p-2">Entry Time</th>
              <th class="text-left p-2">Status</th>
              <th class="text-left p-2">Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="p-2">2019-09924</td>
              <td class="p-2">Today 2m ago</td>
              <td class="p-2 text-green-500">Verified</td>
              <td class="p-2">Enrolled</td>
            </tr>
            <tr>
              <td class="p-2">2019-09924</td>
              <td class="p-2">Today 5m ago</td>
              <td class="p-2 text-green-500">Verified</td>
              <td class="p-2">Presented ID</td>
            </tr>
            <!-- Add more rows as needed -->
          </tbody>
        </table>
        <button class="mt-4 bg-gray-500 text-white px-4 py-2 rounded-lg">Show All My Transactions</button>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'Dashboard',
  };
  </script>
  
  <style scoped>
  /* Optional: Add additional styles here */
  </style>
  