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

      <!-- Show stored encodings -->
      <div v-if="Object.keys(studentEncodings).length > 0" class="stored-encodings">
        <h3>Stored Encodings</h3>
        <div v-for="(value, key) in studentEncodings" :key="key" class="encoding-entry">
          <div class="student-number">{{ key }}</div>
          <div class="encoding-status">Encoding stored</div>
        </div>
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
import { ref, onMounted, reactive } from 'vue';
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

// Store student encodings with student numbers as keys
const studentEncodings = reactive({});

// Function to store encoding with student number
const storeEncoding = (studentNum, encodingData) => {
  console.log("storeEncoding called with student number:", studentNum);
  console.log("encodingData type:", typeof encodingData);
  console.log("encodingData value:", encodingData);
  
  if (!studentNum || !encodingData) {
    console.log("Missing student number or encoding data");
    return false;
  }
  
  try {
    // Parse the encoding from the response if it's a string
    let encodingArray;
    if (typeof encodingData === 'string') {
      console.log("Processing encoding as string");
      // Extract the encoding array from the string response
      const match = encodingData.match(/\[.*\]/);
      if (match) {
        console.log("Regex match found:", match[0].substring(0, 50) + "...");
        encodingArray = JSON.parse(match[0]);
        console.log("Successfully parsed encoding array, length:", encodingArray.length);
      } else {
        console.log("No regex match found for encoding array");
        throw new Error("Could not extract encoding array from response");
      }
    } else if (typeof encodingData === 'object' && encodingData.encoding) {
      console.log("Processing encoding as object with encoding property");
      encodingArray = encodingData.encoding;
      console.log("Extracted encoding array, length:", encodingArray.length);
    } else {
      console.log("Processing encoding as direct array");
      // If it's already the array itself
      encodingArray = encodingData;
      console.log("Using direct array, length:", encodingArray.length);
    }
    
    // Store the encoding with the student number as key
    studentEncodings[studentNum] = encodingArray;
    console.log("Stored encoding for student:", studentNum);
    console.log("Current student encodings count:", Object.keys(studentEncodings).length);
    
    // Display the full dictionary of student encodings
    console.log("STUDENT ENCODINGS DICTIONARY:", JSON.stringify(studentEncodings, null, 2));
    
    // Add to logs
    logs.value.push(`Encoding stored for student: ${studentNum}`);
    
    // Save to localStorage for persistence (optional)
    try {
      localStorage.setItem('studentEncodings', JSON.stringify(studentEncodings));
      console.log("Successfully saved to localStorage");
    } catch (e) {
      console.error("Error saving to localStorage:", e);
    }
    
    return true;
  } catch (error) {
    console.error("Error storing encoding:", error);
    logs.value.push(`Error storing encoding: ${error.message}`);
    return false;
  }
};

// Function to retrieve encoding for a student number
const getEncoding = (studentNum) => {
  return studentEncodings[studentNum] || null;
};

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

  let encodingArray = null; // Declare encodingArray in the outer scope

  try {
    const formData = new FormData();
    formData.append('image', imageData); // Send the base64 image

    const response = await fetch('http://localhost:8000/api/encode', {
      method: 'POST',
      body: formData,
    });

    const data = await response.json();
    console.log("Raw response data:", data.encoding); // Log the raw response for debugging

    // Extract the success value and encoding array from the response
    const match = data.encoding.match(/'success':\s*(true|false)/i);
    const success = match ? match[1].toLowerCase() === 'true' : false;
    console.log("Success value and type:", success, typeof success);

    const encodingMatch = data.encoding.match(/\[([^\]]+)\]/); // Match the array inside square brackets
    if (encodingMatch) {
      try {
        encodingArray = JSON.parse(`[${encodingMatch[1]}]`); // Parse the matched array
        console.log("Encoding array and type:", encodingArray, typeof encodingArray);
        console.log("Is encodingArray truthy?", !!encodingArray);
      } catch (error) {
        console.error("Failed to parse encoding array:", error);
        backendDebugMessages.value.push("Error parsing encoding array.");
      }
    } else {
      console.error("No encoding array found in response:", data.encoding);
      backendDebugMessages.value.push("No encoding array found in response.");
    }

    console.log("Student number and type:", studentNumber.value, typeof studentNumber.value);

    // Ensure student number is valid
    if (!studentNumber.value.trim()) {
      console.error("Student number is empty or invalid.");
      backendDebugMessages.value.push("Student number is empty or invalid.");
      return;
    }

    if (success && encodingArray && encodingArray.length > 0) {
      encoding.value = encodingArray; // Display the face encoding
      console.log("You've successfully scanned the face!");

      // Store the encoding with the student number
      const stored = storeEncoding(studentNumber.value, encodingArray);

      if (stored) {
        logs.value.push(`Successfully stored encoding for student: ${studentNumber.value}`);
      } else {
        console.error("Failed to store encoding.");
        backendDebugMessages.value.push("Failed to store encoding.");
      }
    } else {
      backendDebugMessages.value.push("Failed to extract encoding or success value.");
    }

    // Update backend debug messages
    backendDebugMessages.value = data.debug_logs || [];
  } catch (error) {
    console.error("Error sending the image:", error);
    backendDebugMessages.value.push(`Error sending the image: ${error.message}`);
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

// Load saved encodings from localStorage if available
const loadSavedEncodings = () => {
  try {
    const savedEncodings = localStorage.getItem('studentEncodings');
    if (savedEncodings) {
      const parsed = JSON.parse(savedEncodings);
      Object.assign(studentEncodings, parsed);
      logs.value.push(`Loaded ${Object.keys(parsed).length} saved encodings`);
    }
  } catch (error) {
    console.error("Error loading saved encodings:", error);
  }
};

onMounted(() => {
  initializeWebcam(); // Start the webcam feed
  loadSavedEncodings(); // Load any previously saved encodings
});
</script>

<style>
/* Additional styles for the encoding display */
.stored-encodings {
  margin-top: 20px;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  max-height: 300px;
  overflow-y: auto;
}

.encoding-entry {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #eee;
  padding: 8px 0;
}

.student-number {
  font-weight: bold;
}

.encoding-status {
  color: #4CAF50;
  font-size: 0.9em;
}
</style>