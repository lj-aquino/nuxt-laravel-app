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
          @canplaythrough="onVideoCanPlayThrough"
          @play="onVideoPlay"
        ></video>
      </div>
    </div>

    <!-- Second Square -->
    <div class="square second-square">
      <div v-if="encoding">
        <h3>Face Encoding</h3>
        <pre>{{ encoding }}</pre>
      </div>
    </div>

    <!-- Student Info Square -->
    <div class="square student-info"></div>

    <!-- Buttons -->
    <button
      class="enter-id-button animated-button"
      style="position: absolute; left: 370.4px; top: 690.7px;"
    >
      <i class="fas fa-id-card" style="font-size: 16px;"></i>
      Enter ID
    </button>
    
    <button
      class="scan-id-button animated-button"
      style="position: absolute; left: 555.4px; top: 690.7px;"
    >
      <i class="fas fa-qrcode" style="font-size: 16px;"></i>
      Scan ID
    </button>

    <!-- Add the Sidebar component -->
    <Sidebar activeMenu="Dashboard" />

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
  height: 90vh;
  width: 90vw;
  background-color: white;
  left: 350px;
  top: 200.2px;
}

.square {
  border: 1px solid #e8e8e8;
  border-radius: 22px;
  position: absolute;
}

.first-square {
  width: 400.1px;
  height: 582.8px;
  left: 350px;
  top: 180.2px;
}

.second-square {
  width: 700.5px;
  height: 581.2px;
  left: 780.5px;
  top: 180.2px;
}

.student-info {
  width: 355.7px;
  height: 210px;
  left: 370.4px;
  top: 465px;
  background-color: #ffffff;
  border: 1px solid #e8e8e8;
}

.stream {
  border-radius: 22px;
  background-color: #9f9f9f; /* Default color when video is not streaming */
  width: 355.7px;
  height: 210px;
  position: absolute;
  left: 22px;
  top: 55.2px;
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

/* Global styles to remove scrollbars */
html,
body {
  margin: 0;
  padding: 0;
  overflow: hidden; /* Prevent scrollbars on the entire page */
}

.animated-button {
  background-color: #71231c;
  color: white;
  border: none;
  border-radius: 12px;
  width: 170.7px;
  height: 57.2px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-family: 'Bricolage Grotesque', sans-serif;
  font-size: 20px;
  transition: transform 0.3s ease, background-color 0.3s ease;
}

.animated-button:hover {
  transform: scale(1.1); /* Slightly enlarge the button */
  background-color: #9a2d24; /* Change background color on hover */
}

</style>