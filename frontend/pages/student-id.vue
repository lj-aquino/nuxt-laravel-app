<template>
  <div class="flex justify-center items-center min-h-screen bg-gray-100 p-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
      <h2 class="text-2xl font-semibold text-center mb-4">Enter Student ID</h2>

      <div v-if="hasStudentId" class="mb-4">
        <button @click="simulateBarcodeScan" class="bg-blue-500 text-white px-4 py-2 rounded w-full">Scan ID</button>
      </div>

      <div v-else class="mb-4">
        <label for="studentNumber" class="block text-sm font-medium text-gray-700">Student Number</label>
        <input
          id="studentNumber"
          v-model="studentNumber"
          type="text"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="Enter Student Number"
        />
      </div>

      <div class="flex justify-between">
        <button @click="checkStudentNumber" class="bg-green-500 text-white px-4 py-2 rounded w-full">Submit</button>
      </div>

      <!-- Feedback Messages -->
      <div v-if="loading" class="text-center mt-4 text-gray-600">Checking...</div>
      <div v-if="errorMessage" class="text-center mt-4 text-red-600">{{ errorMessage }}</div>
      <div v-if="successMessage" class="text-center mt-4 text-green-600">{{ successMessage }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

// Base URL of the Laravel API
const apiUrl = 'http://localhost:8000/api'; // Replace with your backend URL if needed

const router = useRouter();
const studentNumber = ref('');
const hasStudentId = ref(false);
const loading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

// Simulate the barcode scan
const simulateBarcodeScan = () => {
  studentNumber.value = '2021-12345'; // Replace with a sample student number
  hasStudentId.value = false;
};

// Check if the student number exists in the database
const checkStudentNumber = async () => {
  if (!studentNumber.value) {
    errorMessage.value = 'Please enter a student number!';
    return;
  }

  loading.value = true;
  errorMessage.value = '';
  successMessage.value = '';

  try {
    // Use $fetch to make the GET request
    const data = await $fetch(`${apiUrl}/check-student/${studentNumber.value}`);
    console.log('API Response:', data); // 👈 LOGGING RESPONSE

    if (data.exists) {
      // If the student exists, proceed to the face-scanning page
      router.push({ path: '/face-scanning' });

      // Show a success message if the student is new
      if (data.remarks === 'New user') {
        successMessage.value = 'You are added as a new user!';
      }
    } else {
      // If the student doesn't exist in the system
      errorMessage.value = 'Student not found in the system.';
    }
  } catch (error) {
    errorMessage.value = 'An error occurred. Please try again.';
    console.error(error);
  } finally {
    loading.value = false;
  }
};

// Function to register the student and log the action
const registerStudent = async () => {
  // Make a POST request to register the student using the updated apiUrl
  const { data, error } = await $fetch(`${apiUrl}/register-student`, {
    method: 'POST',  // POST request
    body: {
      student_number: studentNumber.value,  // Send student number to register
    },
  });

  if (error) {
    errorMessage.value = 'An error occurred during registration. Please try again.';
    console.error(error);
    return;
  }

  // Handle the response and show success or error message
  if (data.exists) {
    // Proceed to face-scanning page
    router.push({ path: '/face-scanning' });

    // Show success message if student is new
    if (data.remarks === 'New user') {
      successMessage.value = 'You are added as a new user!';
    }
  }
};
</script>
