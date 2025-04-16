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
          v-model="studentNumber"
        />
      </div>
      <button class="register-button" @click="handleRegister">Register</button>

      <div v-if="encoding" class="encoding-display">
        <h3>Face Encoding</h3>
        <pre>{{ encoding }}</pre>
        <h3>Student Number</h3>
        <p>{{ studentNumber }}</p>
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
const studentNumber = ref(''); // Ref for the student number input
const encoding = ref(null); // Ref to store the face encoding
const logs = ref([]); // Logs for debugging
const router = useRouter(); // Use the router for navigation

const loading = ref(false); // Track if the register button is clicked

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

const handleRegister = async () => {
  if (!studentNumber.value.trim()) {
    alert('Student number cannot be empty.');
    return;
  }

  const video = videoElement.value;
  if (!video) {
    alert('Video element not found.');
    return;
  }

  // Capture the current video frame
  const canvas = document.createElement('canvas');
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

  // Convert the canvas image to a base64-encoded JPEG
  const imageData = canvas.toDataURL('image/jpeg');

  try {
    const formData = new FormData();
    formData.append('image', imageData);

    const response = await fetch('http://localhost:8000/api/encode', {
      method: 'POST',
      body: formData,
    });

    const data = await response.json();

    if (data.success) {
      encoding.value = data.encoding; // Store the face encoding
      logs.value.push(`Encoding: ${data.encoding}`);
      logs.value.push(`Student Number: ${studentNumber.value}`);
    } else {
      alert('Failed to get face encoding.');
    }
  } catch (error) {
    console.error('Error capturing face encoding:', error);
    logs.value.push(`Error: ${error.message}`);
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
  margin-top: 22%;
  margin-left: 5%;
}

.student-number-input {
  margin-top: 12%;
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
  margin-top:5%;
  margin-left: 5%;
}

.student-name-input::placeholder,
.student-number-input::placeholder {
  font-family: 'Bricolage Grotesque', sans-serif;
}

.register-button {
  position: absolute;
  left: 50%;
  bottom: 2%;
  transform: translate(-50%, -50%);
  background-color: #71231c; /* Background color */
  border: none; /* No border */
  border-radius: 20px; /* Rounded corners */
  padding: 10px 20px; /* Padding for better spacing */
  font-size: 16px; /* Font size */
  cursor: pointer; /* Pointer cursor on hover */
  font-family: 'Bricolage Grotesque', sans-serif; /* Font family */
  color: white; /* Text color */ 
  font-weight: bold; /* Font weight */
  width: 90%;
}

.encoding-display {
  margin-top: 20px;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background-color: #f9f9f9;
}

</style>