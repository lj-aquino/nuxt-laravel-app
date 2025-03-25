<template>
  <div class="flex justify-center items-center min-h-screen bg-gray-100 p-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
      <h2 class="text-2xl font-semibold text-center mb-4">Enter Student ID</h2>

      <!-- Initial selection for student having ID or not -->
      <div v-if="!hasStudentId" class="mb-4">
        <button @click="setHasStudentId(true)" class="bg-blue-500 text-white px-4 py-2 rounded w-full mb-2">Student has ID</button>
        <button @click="setHasStudentId(false)" class="bg-red-500 text-white px-4 py-2 rounded w-full">Student has no ID</button>
      </div>

      <!-- Student ID input field if no ID is selected -->
      <div v-if="hasStudentId === false" class="mb-4">
        <label for="studentNumber" class="block text-sm font-medium text-gray-700">Student Number</label>
        <input
          id="studentNumber"
          v-model="studentNumber"
          type="text"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="Enter Student Number"
        />
      </div>

      <!-- Buttons for submitting or proceeding -->
      <div v-if="hasStudentId === false" class="flex justify-between">
        <button @click="checkStudentNumber" class="bg-green-500 text-white px-4 py-2 rounded w-full">Submit</button>
      </div>

      <div v-if="hasStudentId === true" class="mb-4">
        <!-- Show barcode scanning logic here -->
        <button @click="simulateBarcodeScan" class="bg-blue-500 text-white px-4 py-2 rounded w-full">Scan ID</button>
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
const apiUrl = 'http://localhost:8000/api';

const router = useRouter();
const studentNumber = ref('');
const hasStudentId = ref(null); // null means the student has not made a choice yet
const loading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

// Set if the student has an ID or not
const setHasStudentId = (hasId) => {
  hasStudentId.value = hasId;
  if (!hasId) {
    studentNumber.value = ''; // Reset student number if they don't have an ID
  }
};

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
    console.log('API Response:', data);

    if (data.exists) {
      // If the student exists, proceed to the face-scanning page
      router.push({ path: '/face-scanning' });
    } else {
      // If the student doesn't exist in the system, register them
      await registerStudent();
    }
  } catch (error) {
    errorMessage.value = 'An error occurred. Please try again.';
    console.error(error);
  } finally {
    loading.value = false;
  }
};

const registerStudent = async () => {
  // Make a POST request to register the student using the updated apiUrl
  const { data, error } = await $fetch(`${apiUrl}/register-student`, {
    method: 'POST',  // POST request
    body: {
      student_number: studentNumber.value,  // Send student number to register
    },
  });

  successMessage.value = 'You are added as a new user!';

  if (error) {
    errorMessage.value = 'An error occurred during registration. Please try again.';
    console.error(error);
    return;
  }

  // After successful registration, redirect to the face-scanning page
  router.push({ path: '/face-scanning' });
};
</script>
