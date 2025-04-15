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

      <div class="square student-info">
        <!-- Student Info Text -->
        <div class="student-name-text">Student Name:</div>
        
        <!-- Text Field -->
        <input
          type="text"
          class="student-name-input"
          placeholder="Enter student name"
        />

        <div class="student-number-text">Student Number:</div>
        
        <!-- Text Field -->
        <input
          type="text"
          class="student-number-input"
          placeholder="Enter student number"
        />
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

    <Sidebar
    :activeMenu="activeMenu"
    @update:activeMenu="activeMenu = $event"
    />

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

const videoElement = ref(null); // Ref for the video element
const logs = ref([]); // Store logs
const router = useRouter(); // Use the router for navigation

// Define activeMenu and set the default value to 'Register Student'
const activeMenu = ref('Register Student');

const onVideoCanPlay = () => {
  logs.value.push("Video is ready to play.");
};

const onVideoPlay = () => {
  logs.value.push("Video started playing.");
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
    logs.value.push(`Error accessing webcam: ${error.message}`);
  }
};

onMounted(() => {
  initializeWebcam(); // Start the webcam feed
});
</script>

<style scoped>
.student-name-input,
.student-number-input {
  background-color: #fcfcfd; /* Background color */
  border: 1px solid #776f6e; /* Border color and weight */
  border-radius: 20px; /* Corner rounding */
  padding: 8px 12px; /* Padding for better spacing */
  font-size: 16px; /* Font size */
  width: 80%; /* Full width of the container */
}

.student-name-input {
  margin-top: 18%;
  margin-left: 5%;
}

.student-number-input {
  margin-top: 23%;
  margin-left: 6%;
}

.student-name-text,
.student-number-text {
  position: absolute;
  font-family: 'Bricolage Grotesque', sans-serif;
  font-size: 18px;
  font-weight: bold;
}

.student-name-text{
  margin-top: 15%;
  margin-left: 5%;
}

.student-number-text{
  margin-top:15%;
  margin-left: 5%;

}
</style>