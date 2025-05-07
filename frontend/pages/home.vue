<template>
  <div class="home-container">
    <!-- Background video - lower z-index to ensure it stays in the background -->
    <video autoplay muted loop class="background-video">
      <source src="~/assets/videos/bg_vid.mp4" type="video/mp4">
    </video>
    
    <!-- Content wrapper - will contain all UI elements that should be above the video -->
    <div class="content-wrapper">
      <!-- Logo -->
      <div class="logo-container">
        <img src="~/assets/images/uplb_logo.png" alt="UPLB Logo" class="uplb-logo">
        <div class="logo-text">
          <div class="uplb-text">UPLB</div>
          <div class="security-text">SECURITY</div>
        </div>
      </div>
      
      <!-- Navigation links -->
      <div class="nav-links">
        <a href="#" class="active">Home</a>
        <a href="#">Contact</a>
        <a href="#">About</a>
        <a href="#">Login</a>
      </div>
      
      <!-- Content container -->
      <div class="content-container">
        <!-- Title and subtitle -->
        <div class="title-section">
          <h1 class="main-title">
            <span>FACE & ID</span>
            <span>AUTHENTICATION</span>
          </h1>
          <h2 class="subtitle">Enhancing UPLB's Curfew System with Face Recognition and School ID Authentication</h2>
        </div>
        
        <!-- Input field and scan button -->
        <div class="input-section">
          <!-- Login form (default) -->
          <div v-if="!showRegistrationForm" class="input-button-container">
            <input 
              type="text" 
              placeholder="Enter your student no." 
              class="student-id-input"
              v-model="studentId">
            <button class="scan-button" @click="checkIfStudentNoExists">SCAN FACE</button>
          
            <!-- Student verification feedback -->
            <div v-if="verificationAttempted" class="verification-feedback">
              <p v-if="studentVerified" class="current-process">
                {{ processingMessage }}
                <!-- Add LoadingSpinner component -->
                <LoadingSpinner :status="spinnerStatus" />
              </p>
              <p v-else class="verification-error">
                Student number not found...
                <span style="margin-left: 2%;">
                  <a href="#" class="register-link" @click.prevent="toggleRegistrationForm">Register</a>
                </span>
              </p>
            </div>
          </div>
          
          <!-- Registration form -->
          <div v-else class="registration-form">
            <input 
              type="text" 
              placeholder="Enter Full Name" 
              class="student-id-input"
              v-model="registrationName">
            <input 
              type="text" 
              placeholder="Enter Student no." 
              class="student-id-input"
              v-model="registrationStudentId">
            <div class="register-buttons">
              <button class="register-button" @click="registerStudent">REGISTER</button>
              <button class="cancel-button" @click="toggleRegistrationForm">CANCEL</button>
            </div>
          </div>
        </div>

      </div>
      
      <!-- Bottom info section - moved to bottom right -->
      <div class="bottom-info">
        <p class="bottom-subtitle">Web app verifies students' identities using facial recognition.</p>
        <div class="camera-icon" @click="openCameraSelect">
          <i class="fas fa-camera"></i>
        </div>
      </div>
      
      <!-- Camera circle -->
      <div class="camera-circle">
        <div class="webcam-container">
          <video ref="webcam" class="webcam-feed" autoplay playsinline></video>
          <div class="face-square-overlay"><span></span></div>
        </div>
      </div>
    </div>
    
    <!-- Camera selection modal -->
    <select-camera v-model="showCameraSelect" @camera-change="onCameraChange" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import SelectCamera from '~/components/SelectCamera.vue';
import LoadingSpinner from '~/components/LoadingSpinner.vue'; // Import the loading spinner component
import { compareFaceEncoding } from '~/utils/compareFaceEncoding.js'; // Import the face recognition utility
import { getFaceEncoding } from '~/utils/getFaceEncoding.js'; // Import the face encoding utility
import { registerStudent as registerStudentUtil } from '~/utils/registerStudent.js'; // Import the register student utility
import '~/assets/css/home.css'; // Import the external CSS file

const webcam = ref(null);
const stream = ref(null);
const showCameraSelect = ref(false);
const studentId = ref('');
const studentVerified = ref(false);
const verificationAttempted = ref(false);
const processingMessage = ref('Student number found...');
const isProcessing = ref(false);
const faceMatchStatus = ref('none'); // Status for face matching

// registering form variables
const showRegistrationForm = ref(false);
const registrationName = ref('');
const registrationStudentId = ref('');

// Function to toggle between login and registration forms
const toggleRegistrationForm = () => {
  showRegistrationForm.value = !showRegistrationForm.value;
  // Reset form fields when toggling
  if (showRegistrationForm.value) {
    registrationName.value = '';
    registrationStudentId.value = '';
  }
};

// Compute spinner status based on processing state and match status
const spinnerStatus = computed(() => {
  if (isProcessing.value) return 'loading';
  if (faceMatchStatus.value === 'success') return 'success';
  if (faceMatchStatus.value === 'error') return 'error';
  return 'none';
});

// Function to validate student and then open camera if verified
const checkIfStudentNoExists = async () => {
  // Reset previous state
  faceMatchStatus.value = 'none';
  if (!studentId.value) {
    alert("Please enter your student mo.");
    return;
  }
  
  try {
    const apiUrl = useRuntimeConfig().public.apiUrl;
    const apiKey = useRuntimeConfig().public.apiKey;
    
    const response = await fetch(`${apiUrl}/students/show`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-API-KEY': apiKey
      },
      body: JSON.stringify({
        student_number: studentId.value,
      }),
    });
    
    const data = await response.json();
    console.log('API Response:', data);
    verificationAttempted.value = true;
    
    if (response.ok && data.message === "Student found successfully") {
      studentVerified.value = true;
      // Process face encoding after student is verified
      handleFaceEncoding();
    } else {
      studentVerified.value = false;
    }
  } catch (error) {
    console.error('Error validating student ID:', error);
    verificationAttempted.value = true;
    studentVerified.value = false;
  }
};

// Update this function in your home.vue
const handleFaceEncoding = async () => {
  if (!studentVerified.value) {
    return;
  }
  
  try {
    isProcessing.value = true;
    faceMatchStatus.value = 'none'; // Reset match status
    
    const apiKey = useRuntimeConfig().public.apiKey;
    
    // Get the face encoding
    const faceEncodingResult = await getFaceEncoding({
      webcam: webcam.value,
      updateMessage: (message) => {
        processingMessage.value = message;
      }
    });
    
    // If we couldn't get a face encoding, exit early
    if (!faceEncodingResult || !faceEncodingResult.success) {
      faceMatchStatus.value = 'error';
      return;
    }
    
    // Now proceed with the comparison
    processingMessage.value = "Comparing face with database...";
    
    // Call compare function with the extracted encoding
    const isMatch = await compareFaceEncoding(
      studentId.value, 
      { encoding: JSON.stringify({ encoding: faceEncodingResult.encoding }) }, 
      apiKey
    );
    
    // Update UI based on match result
    if (isMatch) {
      processingMessage.value = "Face encoding matched!";
      faceMatchStatus.value = 'success';
    } else {
      processingMessage.value = "Face did not match. Please try again.";
      faceMatchStatus.value = 'error';
    }
    
  } catch (error) {
    console.error('Error in face encoding process:', error);
    processingMessage.value = "Unexpected error occurred. Please try again.";
    faceMatchStatus.value = 'error';
  } finally {
    isProcessing.value = false;
  }
};

// Function to handle student registration via register button
const registerStudent = async () => {
  // Validate input fields
  if (!registrationName.value || !registrationStudentId.value) {
    alert('Please fill in all fields.');
    return;
  }
  
  // Show processing state
  isProcessing.value = true;
  processingMessage.value = "Starting registration process...";
  
  try {
    const apiConfig = useRuntimeConfig().public;
    
    // Call the utility function to handle face encoding and registration
    const result = await registerStudentUtil({
      webcam: webcam.value,
      studentId: registrationStudentId.value,
      studentName: registrationName.value,
      updateMessage: (message) => {
        processingMessage.value = message;
      },
      apiKey: apiConfig.apiKey
    });
    
    if (result.success) {
      alert("Registration complete! You can now scan your face to log in.");
      // Auto-fill the login form with the registered student ID
      studentId.value = registrationStudentId.value;
      toggleRegistrationForm(); // Switch back to login form
    } else {
      alert(`Registration failed: ${result.message}`);
    }
    
  } catch (error) {
    console.error('Registration error:', error);
    alert('An unexpected error occurred during registration.');
  } finally {
    isProcessing.value = false;
  }
};

// Initialize default camera on page load
onMounted(async () => {
  try {
    // Get available video devices
    const devices = await navigator.mediaDevices.enumerateDevices();
    const videoDevices = devices.filter(device => device.kind === 'videoinput');
    
    if (videoDevices.length > 0) {
      // Use the first available camera (usually the laptop's built-in camera)
      const defaultCamera = videoDevices[0].deviceId;
      
      // Start the camera
      await startCamera(defaultCamera);
    }
  } catch (error) {
    console.error('Error initializing default camera:', error);
  }
});

// Function to start camera with specified deviceId
const startCamera = async (deviceId) => {
  if (stream.value) {
    stopCamera();
  }

  try {
    stream.value = await navigator.mediaDevices.getUserMedia({
      video: { 
        deviceId: deviceId ? { exact: deviceId } : undefined,
        width: { ideal: 480 },
        height: { ideal: 480 }
      }
    });
    
    if (webcam.value) {
      webcam.value.srcObject = stream.value;
    }
  } catch (error) {
    console.error('Error accessing camera:', error);
  }
};

// Function to open camera selection modal
const openCameraSelect = () => {
  showCameraSelect.value = true;
};

// Function to handle camera change
const onCameraChange = async (deviceId) => {
  if (!deviceId) return;
  await startCamera(deviceId);
};

// Function to stop camera
const stopCamera = () => {
  if (stream.value) {
    stream.value.getTracks().forEach(track => track.stop());
    stream.value = null;
  }
};

// Clean up camera on component unmount
onUnmounted(() => {
  stopCamera();
});
</script>

<style>
/* Empty style block - all styles moved to home.css */
</style>