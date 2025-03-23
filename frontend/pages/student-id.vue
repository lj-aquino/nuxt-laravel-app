<!-- /frontend/pages/student-id.vue -->
<template>
  <div class="flex justify-center items-center min-h-screen bg-gray-100 p-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
      <h2 class="text-2xl font-semibold text-center mb-4">Enter Student ID hello !!</h2>

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
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useNuxtApp } from '#app';

const router = useRouter();
const { $axios } = useNuxtApp();

const studentNumber = ref('');
const hasStudentId = ref(false);
const loading = ref(false);
const errorMessage = ref('');

const simulateBarcodeScan = () => {
  studentNumber.value = '201909924';
  hasStudentId.value = false;
};

const checkStudentNumber = async () => {
  if (!studentNumber.value) {
    errorMessage.value = 'Please enter a student number!';
    return;
  }

  loading.value = true;
  errorMessage.value = '';

  try {
    const response = await $axios.$get(`/api/check-student/${studentNumber.value}`);

    if (response.exists) {
      router.push({ path: '/face-scanning' });
    } else {
      await $axios.$post('/api/register-student', {
        student_number: studentNumber.value,
      });
      router.push({ path: '/face-scanning' });
    }
  } catch (error) {
    errorMessage.value = 'An error occurred. Please try again.';
    console.error(error);
  } finally {
    loading.value = false;
  }
};
</script>
