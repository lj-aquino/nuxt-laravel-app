<template>
  <div class="flex justify-center items-center min-h-screen bg-gray-100 p-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
      <h2 class="text-2xl font-semibold text-center mb-4">Enter Student ID</h2>

      <!-- Initial selection for student having ID or not -->
      <div v-if="hasStudentId === null" class="mb-4">
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

      <!-- Ask if the student can show a valid ID only if the student number is not found in the database -->
      <div v-if="!studentFound && !identityVerified" class="mb-4">
        <label for="canPresentId" class="block text-sm font-medium text-gray-700">Can you present a valid ID?</label>
        <input
          type="checkbox"
          v-model="canPresentValidId"
          id="canPresentId"
          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
      </div>

      <!-- Dropdown for selecting a valid ID (hidden until checkbox is checked) -->
      <div v-if="canPresentValidId" class="mb-4">
        <label for="validId" class="block text-sm font-medium text-gray-700">Select Valid ID</label>
        <select v-model="validId" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          <option disabled value="">Please select a valid ID</option>
          <option value="Philippine Passport">Philippine Passport</option>
          <option value="Philippine National ID">Philippine National ID</option>
          <option value="Driver's License">Driver’s License</option>
          <option value="UMID">UMID</option>
          <option value="SSS ID">SSS ID</option>
          <option value="PRC ID">PRC ID</option>
          <option value="Voter's ID">Voter’s ID</option>
          <option value="Postal ID">Postal ID</option>
          <option value="Senior Citizen ID">Senior Citizen ID</option>
          <option value="PWD ID">PWD ID</option>
          <option value="PhilHealth ID">PhilHealth ID</option>
          <option value="Pag-IBIG Loyalty Card Plus">Pag-IBIG Loyalty Card Plus</option>
          <option value="Barangay ID">Barangay ID</option>
          <option value="NBI Clearance">NBI Clearance</option>
          <option value="Police Clearance">Police Clearance</option>
        </select>
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
const identityVerified = ref(false); // If the student has a valid ID
const validId = ref(''); // The ID presented by the student
const canPresentValidId = ref(false); // Checkbox for presenting valid ID
const studentFound = ref(true); // To check if student number exists

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
    const data = await $fetch(`${apiUrl}/check-student/${studentNumber.value}`);
    console.log('API Response:', data);

    if (data.exists) {
      studentFound.value = true;
      router.push({ path: '/face-scanning' });
    } else {
      studentFound.value = false;  // Student not found, show checkbox for valid ID
      if (canPresentValidId.value && validId.value) {
        // Redirect to the face scanning page after selecting a valid ID
        router.push({ path: '/face-scanning' });
      }
    }
  } catch (error) {
    errorMessage.value = 'An error occurred. Please try again.';
    console.error(error);
  } finally {
    loading.value = false;
  }
};

// Register the student with only the student number
const registerStudent = async () => {
  const { data, error } = await $fetch(`${apiUrl}/register-student`, {
    method: 'POST',
    body: {
      student_number: studentNumber.value, // Only student number is posted in students table
    },
  });

  if (error) {
    errorMessage.value = 'An error occurred during registration. Please try again.';
    console.error(error);
    return;
  }

  successMessage.value = 'You are added as a new user!';
  router.push({ path: '/face-scanning' });
};
</script>
