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
        <a href="#">Register</a>
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
          <div class="input-button-container">
            <input 
              type="text" 
              placeholder="Enter your student ID" 
              class="student-id-input"
              v-model="studentId">
            <button class="scan-button" @click="checkIfStudentNoExists">SCAN FACE</button>
          </div>
          <!-- Student verification feedback -->
          <div v-if="verificationAttempted" class="verification-feedback">
            <p v-if="studentVerified" class="current-process">{{ processingMessage }}</p>
            <p v-else class="verification-error">
              Student number not found... 
              <NuxtLink to="/register" class="register-link">Register</NuxtLink>
            </p>
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
import '~/assets/css/home.css'; // Import the external CSS file

const webcam = ref(null);
const stream = ref(null);
const showCameraSelect = ref(false);
const studentId = ref('');
const studentVerified = ref(false);
const verificationAttempted = ref(false);
const processingMessage = ref('Student number found...');
const isProcessing = ref(false);

// Function to validate student and then open camera if verified
const checkIfStudentNoExists = async () => {
  if (!studentId.value) {
    alert("Please enter your student ID");
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
      getFaceEncoding();
    } else {
      studentVerified.value = false;
    }
  } catch (error) {
    console.error('Error validating student ID:', error);
    verificationAttempted.value = true;
    studentVerified.value = false;
  }
};

const getFaceEncoding = async () => {
  // Remove the check for stream.value since we need to run this even if initial verification happened
  if (!studentVerified.value) {
    return;
  }
  
  try {
    isProcessing.value = true;
    processingMessage.value = "Scanning face, please position your face in the square...";
    
    // Create a canvas to capture the current webcam frame
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    
    // Make sure webcam is initialized and has dimensions
    if (!webcam.value || !webcam.value.videoWidth) {
      processingMessage.value = "Camera not ready. Please try again.";
      isProcessing.value = false;
      return;
    }
    
    canvas.width = webcam.value.videoWidth;
    canvas.height = webcam.value.videoHeight;
    
    // Draw the current webcam frame on the canvas
    context.drawImage(webcam.value, 0, 0, canvas.width, canvas.height);
    
    // Convert the canvas to a base64 image - key difference #1: don't split the data URL
    const imageData = canvas.toDataURL('image/jpeg');
    
    // Log some information for debugging
    console.log("Student ID being sent:", studentId.value);
    
    // Use the local API URL
    const localApiUrl = 'http://localhost:8000/api';
    
    // Key difference #2: Use FormData instead of JSON
    const formData = new FormData();
    formData.append('image', imageData); // Send the full imageData URL
    
    console.log(`Sending request to: ${localApiUrl}/encode`);
    
    // Key difference #3: Don't set Content-Type header, let the browser set it for FormData
    const response = await fetch(`${localApiUrl}/encode`, {
      method: 'POST',
      body: formData,
    });
    
    console.log("Response status:", response.status);
    
    // For any non-OK response, try to capture detailed error information
    if (!response.ok) {
      let errorDetails = "";
      try {
        const errorText = await response.text();
        errorDetails = errorText.substring(0, 200);
        console.error("Server error details:", errorDetails);
      } catch (textError) {
        console.error("Could not read error response body");
      }
      
      if (response.status === 404) {
        processingMessage.value = "API endpoint not found. Please check server configuration.";
      } else if (response.status === 500) {
        processingMessage.value = "Server error processing the request. Check server logs.";
        console.error("Server returned 500 error. Possible causes:", errorDetails);
      } else {
        processingMessage.value = `Server error: ${response.status}. Please try again.`;
      }
      
      isProcessing.value = false;
      return;
    }
    
    const data = await response.json();
    console.log("Response data:", data);
    
    // Key difference #4: Handle the specific response format from your API
    // Extract the success value and encoding array from the response
    const match = data.encoding ? data.encoding.match(/'success':\s*(true|false)/i) : null;
    const success = match ? match[1].toLowerCase() === 'true' : false;
    
    const encodingMatch = data.encoding ? data.encoding.match(/\[([^\]]+)\]/) : null; // Match the array inside square brackets
    let encodingArray = null;
    
    if (encodingMatch) {
      try {
        encodingArray = JSON.parse(`[${encodingMatch[1]}]`); // Parse the matched array
        processingMessage.value = "Face encoding done...";
        console.log("Face encoding received:", encodingArray.length, "values");
      } catch (error) {
        console.error("Failed to parse encoding array:", error);
        processingMessage.value = "Error processing face encoding.";
      }
    } else {
      console.error("No encoding array found in response");
      processingMessage.value = "No face encoding detected. Please try again.";
    }
    
  } catch (error) {
    console.error('Error processing face encoding:', error);
    processingMessage.value = "Error processing face. Please try again.";
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