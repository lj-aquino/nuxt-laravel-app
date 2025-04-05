<template>
  <div class="viewport">
    <!-- Add the TopBar component -->
    <TopBar />

    <div class="device-sim">
      <div class="rotated-wrapper">
        <video
          ref="videoElement"
          autoplay
          playsinline
          muted
          class="stream"
          @canplaythrough="onVideoCanPlayThrough"
          @play="onVideoPlay"
        ></video>
      </div>
    </div>

    <div v-if="encoding">
      <h3>Face Encoding</h3>
      <pre>{{ encoding }}</pre>
    </div>

    <!-- Add the Sidebar component -->
    <Sidebar />

    <!-- Backend Debug Messages Section -->
    <div class="debug-messages">
      <h4>Backend Debug Messages</h4>
      <ul>
        <li v-for="(message, index) in backendDebugMessages" :key="index">{{ message }}</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// Import the Sidebar and TopBar components
import Sidebar from '~/components/Sidebar.vue';
import TopBar from '~/components/TopBar.vue';

const encoding = ref(null); // Store face encoding
const videoElement = ref(null); // Ref for the video element
const backendDebugMessages = ref([]); // Store backend debug messages
let isCapturing = false; // Track if capturing is ongoing

// Function to capture a frame from the video and send to the backend
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

// Method to handle when the video starts playing
const onVideoPlay = () => {
  backendDebugMessages.value.push("Video started playing.");
};

// Start capturing frames using requestAnimationFrame
const startCapturingFrames = () => {
  backendDebugMessages.value.push("Starting to capture frames using requestAnimationFrame.");
  const captureLoop = () => {
    captureAndSend();
    requestAnimationFrame(captureLoop);
  };
  requestAnimationFrame(captureLoop);
};

// Initialize the webcam feed
const initializeWebcam = async () => {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    const video = videoElement.value;
    if (video) {
      video.srcObject = stream; // Set the webcam stream as the video source
    }
  } catch (error) {
    backendDebugMessages.value.push(`Error accessing webcam: ${error}`);
  }
};

onMounted(() => {
  initializeWebcam(); // Start the webcam feed
  startCapturingFrames(); // Start capturing frames using requestAnimationFrame
});
</script>

<style scoped>
.viewport {
  height: 100vh;
  width: 100vw;
  background-color: #1a1a1a;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.device-sim {
  width: 100%;
  max-width: 360px; /* Max width to ensure it doesn't get too big */
  height: 80%; /* Make the height responsive */
  border: 4px solid #22c55e;
  border-radius: 1rem;
  box-shadow: 0 0 20px rgba(34, 197, 94, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden; /* Prevent scrolling */
}

.rotated-wrapper {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.stream {
  width: 100%; /* Take up full width of the container */
  height: 100%; /* Take up full height of the container */
  border: none;
  transform: rotate(0deg); /* Rotate the video feed by 180 degrees */
  transform-origin: center;
}

.debug-messages {
  position: absolute;
  bottom: 10px;
  left: 10px;
  width: 300px;
  max-height: 200px;
  overflow-y: auto;
  background-color: rgba(0, 0, 0, 0.8);
  color: white;
  padding: 10px;
  border-radius: 8px;
  font-size: 12px;
}
</style>