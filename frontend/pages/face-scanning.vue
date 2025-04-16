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

      <!-- Student Info Square -->
      <div class="square student-info">
        <!-- Student Info Text -->
        <div class="student-info-text">Student Info</div>

        <!-- Verified Rectangle -->
        <div class="verified-box">
          <div class="verified-circle">
            <i class="fas fa-check"></i>
          </div>
          <span>Verified</span>
        </div>

        <!-- Student Name -->
        <div class="student-name">
          Ly*a Jo**e A.
        </div>

        <!-- Student ID -->
        <div class="student-id">
          2019-09924
        </div>

        <div class="enrolled">
          Enrolled
        </div>

        <div class="remarks">
          Remarks
        </div>

        <div class="remarks-note">
          Has ID
        </div>
      </div>

          <!-- Buttons -->
      <div v-if="!enterIdMode">
        <button
          class="enter-id-button animated-button"
          @click="onEnterIdClick"
        >
          <i class="fas fa-id-card"></i>
          Enter ID
        </button>
        
        <button
          class="scan-id-button animated-button"
        >
          <i class="fas fa-qrcode"></i>
          Scan ID
        </button>
      </div>

      <div v-else>
        <input
          type="text"
          v-model="studentNumber"
          placeholder="Enter Student Number"
          class="student-number-input-dashboard"
        />
        <button
          class="scan-face-button animated-button"
          @click="onScanFaceClick"
        >
          Scan Face
        </button>
      </div>
    </div>

    <!-- Second Square -->
    <div class="square second-square">
      <div v-if="encoding">
        <h3>Face Encoding</h3>
        <pre>{{ encoding }}</pre>
      </div>

      <!-- LogsSummary Component -->
      <LogsSummary :logs="logs" />
    </div>

    <!-- Add the Sidebar component -->
    <Sidebar activeMenu="Dashboard" />
  </div>
</template>

<script setup>
import LogsSummary from '~/components/LogsSummary.vue';
import { ref, onMounted } from 'vue';
import '~/assets/css/face-scanning.css'; // Import the CSS file for styles
import { useRouter } from 'vue-router'; // Import the router for navigation

// Import the Sidebar and TopBar components
import Sidebar from '~/components/Sidebar.vue';
import TopBar from '~/components/TopBar.vue';

const logs = ref([]); // Store logs
const encoding = ref(null); // Store face encoding
const videoElement = ref(null); // Ref for the video element
const backendDebugMessages = ref([]); // Store backend debug messages
let isCapturing = false; // Track if capturing is ongoing
const router = useRouter(); // Use the router for navigation

const enterIdMode = ref(false); // Track if "Enter ID" mode is active
const studentNumber = ref(''); // Store the entered student number

const onEnterIdClick = () => {
  enterIdMode.value = true; // Enable "Enter ID" mode
};

const navigateToLogsSummary = () => {
  router.push('/logs-summary'); // Navigate to the logs summary page
};

const formatStudentName = (name) => {
  const parts = name.split(' ');
  const formattedParts = parts.map((part, index) => {
    if (index === parts.length - 1) {
      // Last name: Show only the first letter
      return part.charAt(0) + '.';
    } else {
      // First and middle names: Show first and last letters with asterisks in between
      return part.charAt(0) + '*'.repeat(part.length - 2) + part.charAt(part.length - 1);
    }
  });
  return formattedParts.join(' ');
};

const onVideoCanPlay = () => {
  backendDebugMessages.value.push("Video is ready to play.");
};

const onVideoPlay = () => {
  backendDebugMessages.value.push("Video started playing.");
};

const onScanFaceClick = () => {
  if (studentNumber.value.trim() === '') {
    alert('Please enter a valid student number.');
    return;
  }
  captureAndSend(); // Call the face encoding API
};

const captureAndSend = async () => {
  const video = videoElement.value;

  // Ensure the video element exists and has video dimensions
  if (!video) {
    backendDebugMessages.value.push("Error: Video element not found.");
    return;
  }

  if (isCapturing) return; // Prevent capturing if it's already ongoing
  isCapturing = true;

  // Create a canvas element to draw the current video frame
  const canvas = document.createElement('canvas');
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;

  // Draw the current video frame onto the canvas
  canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

  // Convert the canvas image to a base64-encoded JPEG image
  const imageData = canvas.toDataURL('image/jpeg');

  try {
    const formData = new FormData();
    formData.append('image', imageData); // Send the base64 image

    const response = await fetch('http://localhost:8000/api/encode', {
      method: 'POST',
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      encoding.value = data.encoding; // Display the face encoding
    }

    // Update backend debug messages
    backendDebugMessages.value = data.debug_logs || [];
  } catch (error) {
    backendDebugMessages.value.push(`Error sending the image: ${error}`);
  } finally {
    isCapturing = false; // Reset the capturing flag
  }
};

// Method to handle when the video is ready to play through
const onVideoCanPlayThrough = () => {
  backendDebugMessages.value.push("Video is ready to play through.");
  const video = videoElement.value;
  if (video) {
    backendDebugMessages.value.push(`Video dimensions: ${video.videoWidth}x${video.videoHeight}`);
  }
};

// Initialize the webcam feed
const initializeWebcam = async () => {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    const video = videoElement.value;
    if (video) {
      video.srcObject = stream; // Set the webcam stream as the video source
      video.play(); // Ensure the video starts playing
    }
  } catch (error) {
    console.error("Error accessing webcam:", error); // Log the error
    backendDebugMessages.value.push(`Error accessing webcam: ${error.message}`);
  }
};

onMounted(() => {
  initializeWebcam(); // Start the webcam feed
});
</script>