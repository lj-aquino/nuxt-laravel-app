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
    </div>

    <!-- Second Square -->
    <div class="square second-square">
      <div v-if="encoding">
        <h3>Face Encoding</h3>
        <pre>{{ encoding }}</pre>
      </div>

      <div class = "logs-summary">
        Logs Summary
      </div>  

      <div class = "status-filter">
        Status: 
      </div>  

      <!-- Dropdown for Verified/Unverified -->
      <select class="status-dropdown">
        <option value="verified">Verified</option>
        <option value="unverified">Unverified</option>
        <option value="all">All</option>
      </select>
      
      <!-- Table -->
      <table class="logs-table">
        <thead>
          <tr>
            <th>Student No.</th>
            <th>Entry Time</th>
            <th>Status</th>
            <th>Remarks</th>
          </tr>
        </thead>
      </table>

      <!-- Table -->
      <div class="table-container-wrapper">
        <table class="info-table">
          <tbody>
            <tr v-for="log in logs" :key="log.id">
              <td>
                <div class="student-number">{{ log.student_number }}</div>
                <div class="student-name-logs">{{ formatStudentName(log.student_name) }}</div>
              </td>
              <td>
                <div class="time">{{ log.time }}</div>
                <div class="date">{{ log.date }}</div>
              </td>
              <td style="color: #3871c1;">{{ log.status.charAt(0).toUpperCase() + log.status.slice(1) }}</td>
              <td style="color: black;">{{ log.has_id ? 'Presented ID' : 'No ID Presented' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <button class="print-button">
        <i class="fas fa-print"></i>
        Print
      </button>
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
        2023-12345
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
import '~/assets/css/face-scanning.css'; // Import the CSS file for styles

// Import the Sidebar and TopBar components
import Sidebar from '~/components/Sidebar.vue';
import TopBar from '~/components/TopBar.vue';

const encoding = ref(null); // Store face encoding
const videoElement = ref(null); // Ref for the video element
const backendDebugMessages = ref([]); // Store backend debug messages
let isCapturing = false; // Track if capturing is ongoing

const logs = ref([]); // Store logs fetched from the backend

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

// Fetch logs from the backend
const fetchLogs = async () => {
  try {
    const result = await $fetch('https://sp-j16t.onrender.com/api/logs/recent', {
      method: 'GET',
      headers: {
        'X-API-KEY': 'yFITiurVNg9eEXIReziZQQA4iHDlCaZSDxwUCpY9SAsMO36M6OIsRl2MErKBOn9q',
      },
    });

    console.log('API Response:', result); // Log the full API response

    if (result.success) {
      logs.value = result.data
        .map(log => {
          const [date, time] = log.entry_time.split(' ');
          const formattedTime = time.slice(0, 5); // Remove seconds
          return { ...log, date, time: formattedTime };
        })
        .sort((a, b) => new Date(b.entry_time) - new Date(a.entry_time)); // Sort by latest time

      console.log('Processed Logs:', logs.value); // Log the processed logs
    } else {
      console.error('Failed to fetch logs:', result);
    }
  } catch (error) {
    console.error('Error fetching logs:', error);
  }
};

const onVideoCanPlay = () => {
  backendDebugMessages.value.push("Video is ready to play.");
};

const onVideoPlay = () => {
  backendDebugMessages.value.push("Video started playing.");
};

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
      video.play(); // Ensure the video starts playing
    }
  } catch (error) {
    console.error("Error accessing webcam:", error); // Log the error
    backendDebugMessages.value.push(`Error accessing webcam: ${error.message}`);
  }
};

onMounted(() => {
  initializeWebcam(); // Start the webcam feed
  startCapturingFrames(); // Start capturing frames using requestAnimationFrame
  fetchLogs(); // Fetch logs when the component is mounted
});
</script>