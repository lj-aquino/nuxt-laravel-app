<template>
  <div class="viewport">
    <div class="device-sim">
      <div class="rotated-wrapper">
        <video
          ref="videoElement"
          autoplay
          playsinline
          loop
          muted
          :src="droidCamUrl"
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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const droidCamUrl = 'http://192.168.1.134:4747/video'; // Your DroidCam stream URL
const encoding = ref(null); // Store face encoding
const videoElement = ref(null); // Ref for the video element
let isCapturing = false; // Track if capturing is ongoing

// Method to handle when the video is ready to play through
const onVideoCanPlayThrough = () => {
  console.log("Video is ready to play through.");
  const video = videoElement.value;
  if (video) {
    console.log("Video dimensions:", video.videoWidth, video.videoHeight);
  }
};

// Method to handle when the video starts playing
const onVideoPlay = () => {
  console.log("Video started playing.");
};

// Function to capture a frame from the video and send to the backend
const captureAndSend = async () => {
  const video = videoElement.value;

  // Ensure the video element exists and has video dimensions
  if (!video) {
    console.error("Error: Video element not found.");
    return;
  }

  // Check if video dimensions are available
  // if (!video.videoWidth || !video.videoHeight) {
  //   console.error("Error: Video dimensions are unavailable.");
  //   return;
  // }

  if (isCapturing) return; // Prevent capturing if it's already ongoing
  isCapturing = true;

  console.log("Capturing video frame...");

  // Create a canvas element to draw the current video frame
  const canvas = document.createElement('canvas');
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;

  // Draw the current video frame onto the canvas
  canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
  console.log("Frame drawn on canvas.");

  // Convert the canvas image to a base64-encoded JPEG image
  const imageData = canvas.toDataURL('image/jpeg');
  console.log("Canvas converted to base64 image.");

  try {
    const formData = new FormData();
    formData.append('image', imageData);  // Send the base64 image

    const response = await fetch('http://localhost:8000/api/encode', {
      method: 'POST',
      body: formData,
    });

    console.log("Received response from backend.");

    const data = await response.json();
    encoding.value = data.encoding; // Display the face encoding
    console.log("Face encoding received and set.");
  } catch (error) {
    console.error("Error sending the image:", error);
  } finally {
    isCapturing = false; // Reset the capturing flag
  }
};

// Start capturing frames using requestAnimationFrame
const startCapturingFrames = () => {
  console.log("Starting to capture frames using requestAnimationFrame.");
  const captureLoop = () => {
    captureAndSend();
    requestAnimationFrame(captureLoop);
  };
  requestAnimationFrame(captureLoop);
};

onMounted(() => {
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
  max-width: 360px;  /* Max width to ensure it doesn't get too big */
  height: 80%;  /* Make the height responsive */
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
  transform: rotate(90deg);
  transform-origin: center;
  display: flex;
  justify-content: center;
  align-items: center;
}

.stream {
  width: 100%;   /* Take up full width of the container */
  height: 100%;  /* Take up full height of the container */
  border: none;
}
</style>
