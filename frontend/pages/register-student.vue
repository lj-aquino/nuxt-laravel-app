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

  </div>
</template>

<script setup>
// Import necessary components and modules
import '~/assets/css/register-student.css'; // Import the CSS file for styles
import LogsSummary from '~/components/LogsSummary.vue';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import '~/assets/css/face-scanning.css'; // Import the CSS file for styles
import { useRouter } from 'vue-router'; // Import the router for navigation
import Sidebar from '~/components/Sidebar.vue';
import TopBar from '~/components/TopBar.vue';

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

    let attempts = 0;
    const maxAttempts = 3; // Retry up to 3 times
    let success = false;

    while (attempts < maxAttempts && !success) {
      const response = await fetch('http://localhost:8000/api/encode', {
        method: 'POST',
        body: formData,
      });

      const rawData = await response.json();
      console.log('Backend Response:', rawData);

      // Extract the encoding string and remove the debug prefix
      const encodingString = rawData.encoding.replace(/^Face encoding: /, "").trim();

      try {
        // Convert Python-style JSON to JavaScript-compatible JSON
        const jsonCompatible = encodingString
          .replace(/'/g, '"') // Replace single quotes with double quotes
          .replace(/\bTrue\b/g, 'true') // Replace True with true
          .replace(/\bFalse\b/g, 'false'); // Replace False with false

        // Parse the inner JSON
        const faceData = JSON.parse(jsonCompatible);

        // Access the success value
        if (faceData.success) {
          encoding.value = faceData.encoding;
          logs.value.push(`Encoding: ${faceData.encoding}`);
          logs.value.push(`Student Number: ${studentNumber.value}`);
          success = true;

          // Send the face encoding and student number to the database
          const dbResponse = await fetch('https://sp-j16t.onrender.com/api/face_encodings', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-API-KEY': 'yFITiurVNg9eEXIReziZQQA4iHDlCaZSDxwUCpY9SAsMO36M6OIsRl2MErKBOn9q',
            },
            body: JSON.stringify({
              student_number: studentNumber.value,
              student_name: studentName.value, // Include the student name
              encoding: faceData.encoding,
            }),
          });

          const dbResult = await dbResponse.json();
          if (dbResponse.ok) {
            console.log('Database Response:', dbResult);
            alert('Registration successful!');
          } else {
            console.error('Database Error:', dbResult);
            alert('Failed to save face encoding to the database.');
          }
        } else {
          attempts++;
          logs.value.push(`Attempt ${attempts}: Backend returned success = ${faceData.success}`);
          if (attempts < maxAttempts) {
            await new Promise((resolve) => setTimeout(resolve, 1000)); // Wait 1 second before retrying
          }
        }
      } catch (error) {
        console.error("Error parsing face encoding:", error);
        logs.value.push(`Error parsing face encoding: ${error.message}`);
      }
    }

    if (!success) {
      alert('Failed to get face encoding after multiple attempts.');
    }
  } catch (error) {
    console.error('Error capturing face encoding:', error);
    logs.value.push(`Error: ${error.message}`);
    alert('An error occurred during registration.');
  } finally {
    loading.value = false;
  }
};

// Lifecycle hooks
onMounted(() => {
  initializeWebcam(); // Start the webcam feed
});

onBeforeUnmount(() => {
  stopWebcam(); // Stop the webcam feed when the component is unmounted
});
</script>
