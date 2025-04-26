<template>
  <div class="viewport">
    <!-- Add the TopBar component -->
    <TopBar />

    <!-- Face Scanning Text -->
    <div
      class="face-scanning-text"
      style="position: absolute; left: 370.4px; top: 195px; font-family: 'Bricolage Grotesque', sans-serif; font-size: 25px; color: black; font-weight: bold;"
    >
      Face Scanning
    </div>

    <!-- First Square -->
    <div class="square first-square">

      <button class="camera-select-btn" @click="showCameraSelect = true">
        <i class="fas fa-camera"></i>
      </button>

      <!-- Camera Select Component -->
      <CameraSelect 
        v-model="showCameraSelect"
        @camera-change="handleCameraChange"
      />
      
      <div class="rotated-wrapper">
        <video
          ref="videoElement"
          autoplay
          playsinline
          muted
          class="stream"
          @canplay="onVideoCanPlay"
          @play="onVideoPlay"
        ></video>
      </div>

      <div class="square student-info">
        <!-- Student Info Text -->
        <div class="student-name-text">Student Name:</div>
        
        <!-- Text Field -->
        <input
          type="text"
          class="student-name-input"
          placeholder="Enter student name"
          v-model="studentName"
        />

        <div class="student-number-text">Student Number:</div>
        
        <!-- Text Field -->
        <input
          type="text"
          class="student-number-input"
          placeholder="Enter student number"
          v-model="studentNumber"
        />
      </div>
      <button 
        class="register-button" 
        @click="handleRegister" 
        :disabled="loading"
      >
        {{ loading ? 'Registering...' : 'Register' }}
      </button>


    </div>

    <!-- Second Square -->
    <div class="square second-square">
      <!-- LogsSummary Component -->
      <LogsSummary :logs="logs" />
    </div>

    <Sidebar
    :activeMenu="activeMenu"
    @update:activeMenu="activeMenu = $event"
    />

    <!-- Notification Modal -->
    <div v-if="showNotification" class="notification-modal">
      <div class="notification-content">
        <!-- Close Button -->
        <button class="close-button" @click="onOkay">×</button>
    
        <!-- Main content wrapper -->
        <div class="modal-main-content">
          <div class="icon-circle" :class="isRegistrationSuccessful ? 'success-icon' : 'failure-icon'">
            <i :class="isRegistrationSuccessful ? 'fas fa-check' : 'fas fa-times'"></i>
          </div>
          <h2>{{ isRegistrationSuccessful ? 'Registration Successful' : 'Registration Failed' }}</h2>
          <p class="subtitle">
            {{ isRegistrationSuccessful ? 'Face encodings registered.' : "Face encoding registration failed." }}
          </p>
        </div>
    
        <!-- Buttons -->
        <button
          v-if="isRegistrationSuccessful"
          class="green-button"
          @click="onOkay"
        >
          Okay
        </button>
        <button
          v-else
          class="red-button"
          @click="onRetry"
        >
          Try Again
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
// Import necessary components and modules
import '~/assets/css/register-student.css'; // Import the CSS file for styles
import LogsSummary from '~/components/LogsSummary.vue';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router'; // Import the router for navigation
import Sidebar from '~/components/Sidebar.vue';
import TopBar from '~/components/TopBar.vue';
import CameraSelect from '~/components/SelectCamera.vue'; // Import the camera selection component

// Define reactive variables and refs
const videoElement = ref(null); // Ref for the video element
const studentNumber = ref(''); // Ref for the student number input
const encoding = ref(null); // Ref to store the face encoding
const logs = ref([]); // Logs for debugging
const router = useRouter(); // Use the router for navigation
const loading = ref(false); // Track if the register button is clicked
let cameraStream = null; // Variable to store the camera stream
const activeMenu = ref('Register Student'); // Default active menu
const studentName = ref(''); // Ref for the student name input
const showCameraSelect = ref(false); // Track if the camera selection modal is visible

// Notification state
const showNotification = ref(false); // Track if the notification is visible
const isRegistrationSuccessful = ref(false); // Track if registration was successful

definePageMeta({
  middleware: ['auth']
})

const handleCameraChange = async (deviceId) => {
  if (cameraStream) {
    cameraStream.getTracks().forEach(track => track.stop());
  }
  
  try {
    cameraStream = await navigator.mediaDevices.getUserMedia({
      video: { deviceId: deviceId ? { exact: deviceId } : true }
    });
    
    const video = videoElement.value;
    if (video) {
      video.srcObject = cameraStream;
      video.play();
    }
  } catch (error) {
    console.error("Error switching camera:", error);
    logs.value.push(`Error switching camera: ${error.message}`);
  }
};

// Webcam initialization
const initializeWebcam = async () => {
  try {
    cameraStream = await navigator.mediaDevices.getUserMedia({ video: true });
    const video = videoElement.value;
    if (video) {
      video.srcObject = cameraStream; // Set the webcam stream as the video source
      video.play(); // Ensure the video starts playing
    }
  } catch (error) {
    console.error("Error accessing webcam:", error);
    logs.value.push(`Error accessing webcam: ${error.message}`);
  }
};

// Stop webcam
const stopWebcam = () => {
  if (cameraStream) {
    cameraStream.getTracks().forEach((track) => track.stop());
    cameraStream = null;
  }
};

// Handle video events
const onVideoCanPlay = () => {
  logs.value.push("Video is ready to play.");
};

const onVideoPlay = () => {
  logs.value.push("Video started playing.");
};

// Handle registration
const handleRegister = async () => {
  if (!studentNumber.value.trim()) {
    alert('Student number cannot be empty.');
    return;
  }

  loading.value = true;

  const video = videoElement.value;
  if (!video) {
    alert('Video element not found.');
    loading.value = false;
    return;
  }

  const canvas = document.createElement('canvas');
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

  const imageData = canvas.toDataURL('image/jpeg');

  try {
    const formData = new FormData();
    formData.append('image', imageData);

    const response = await fetch('http://localhost:8000/api/encode', {
      method: 'POST',
      body: formData,
    });

    const rawData = await response.json();
    console.log('Backend Response:', rawData);

    const encodingString = rawData.encoding.replace(/^Face encoding: /, "").trim();

    const jsonCompatible = encodingString
      .replace(/'/g, '"')
      .replace(/\bTrue\b/g, 'true')
      .replace(/\bFalse\b/g, 'false');

    const faceData = JSON.parse(jsonCompatible);

    if (faceData.success) {
      encoding.value = faceData.encoding;
      logs.value.push(`Encoding: ${faceData.encoding}`);
      logs.value.push(`Student Number: ${studentNumber.value}`);

      const dbResponse = await fetch('https://sp-j16t.onrender.com/api/face_encodings', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-API-KEY': 'yFITiurVNg9eEXIReziZQQA4iHDlCaZSDxwUCpY9SAsMO36M6OIsRl2MErKBOn9q',
        },
        body: JSON.stringify({
          student_number: studentNumber.value,
          student_name: studentName.value,
          encoding: faceData.encoding,
        }),
      });

      const dbResult = await dbResponse.json();
      if (dbResponse.ok) {
        console.log('Database Response:', dbResult);
        isRegistrationSuccessful.value = true;
        showNotification.value = true;
      } else {
        console.error('Database Error:', dbResult);
        isRegistrationSuccessful.value = false;
        showNotification.value = true;
      }
    } else {
      isRegistrationSuccessful.value = false;
      showNotification.value = true;
    }
  } catch (error) {
    console.error('Error capturing face encoding:', error);
    logs.value.push(`Error: ${error.message}`);
    isRegistrationSuccessful.value = false;
    showNotification.value = true;
  } finally {
    loading.value = false;
  }
};

// Notification handlers
const onOkay = () => {
  showNotification.value = false;
};

const onRetry = () => {
  showNotification.value = false;
};

// Lifecycle hooks
onMounted(() => {
  initializeWebcam(); // Start the webcam feed
});

onBeforeUnmount(() => {
  stopWebcam(); // Stop the webcam feed when the component is unmounted
});
</script>