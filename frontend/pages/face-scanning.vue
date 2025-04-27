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
        <div class="video-container">
          <video
            ref="videoElement"
            autoplay
            playsinline
            muted
            class="stream"
            @canplay="onVideoCanPlay"
            @play="onVideoPlay"
          ></video>
          <div class="face-square">
            <span></span>
          </div>
        </div>
      </div>

      <!-- Student Info Text -->
      <div class="square student-info">
        <!-- Student Info Text -->
        <div class="student-info-text">Student Info</div>

        <!-- Loading Indicator -->
        <div v-if="isRecognizing" class="loading-indicator">
          <i class="fas fa-spinner fa-spin"></i>
        </div>

        <!-- Ready for Scan State -->
        <div v-if="!hasRecordedEntry && !isRecognizing">
          <!-- Ready Box -->
          <div class="verified-box ready-box">
            <div class="recording-indicator"></div>
            <span>Ready</span>
          </div>
          
          <!-- Placeholder Info -->
          <div class="student-name ready-placeholder">
            Ready for scan
          </div>
          
          <div class="student-id">
            Position face in camera
          </div>
          
          <div class="enrolled">
            Waiting
          </div>
          
          <div class="remarks">
            Remarks
          </div>
          
          <div class="remarks-note">
            Waiting for student
          </div>
          
          <div class="entry-time">
            {{ new Date().toLocaleString() }}
          </div>
        </div>

        <!-- Student Info Display - Only shown when there's a recent log -->
        <div v-else>
          <!-- Verified Rectangle -->
          <div class="verified-box">
            <div v-if="recentLog?.status === 'verified'" class="verified-circle">
              <i class="fas fa-check"></i>
            </div>
            <div v-else class="unverified-circle">
              <i class="fas fa-exclamation"></i>
            </div>
            <span>{{ recentLog?.status === 'verified' ? 'Verified' : 'Unverified' }}</span>
          </div>
        
          <!-- Student Name -->
          <div class="student-name">
            {{ recentLog ? formatStudentName(recentLog.student_name) : 'N/A' }}
          </div>
        
          <!-- Student ID -->
          <div class="student-id">
            {{ recentLog?.student_number || 'N/A' }}
          </div>
        
          <div class="enrolled">
            {{ recentLog?.enrolled ? 'Enrolled' : 'Not Enrolled' }}
          </div>
          
          <div class="entry-time">
            {{ recentLog?.entry_time ? new Date(recentLog.entry_time).toLocaleString() : 'No Entry Time Available' }}
          </div>
          
          <div class="remarks">
            Remarks
          </div>
        
          <div class="remarks-note">
            {{ recentLog?.has_id ? 'Presented ID' : 'No ID Presented' }}
          </div>
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
          @click="onScanIdClick" 
        >
          <i class="fas fa-qrcode"></i>
          Scan ID
        </button>

        <div v-if="studentNumber" class="scanned-number-display">
          <div class="scanned-info">
            <i class="fas fa-barcode"></i>
            <span>Scanned ID: {{ studentNumber }}</span>
          </div>
        </div>
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
      <!-- LogsSummary Component -->
      <LogsSummary :logs="logs" />
    </div>

    <!-- Add the Sidebar component -->
    <Sidebar activeMenu="Dashboard" />

    <div v-if="showNotification" class="notification-modal">
      <div class="notification-content">
        <!-- Close Button -->
        <button class="close-button" @click="onOkay">×</button>
    
        <!-- Main content wrapper -->
        <div class="modal-main-content">
          <div class="icon-circle" :class="isVerified ? 'success-icon' : 'failure-icon'">
            <i :class="isVerified ? 'fas fa-check' : 'fas fa-times'"></i>
          </div>
          <h2>{{ notificationTitle || (isVerified ? 'Verification Successful' : 'Verification Failed') }}</h2>
          <p class="subtitle">
            {{ notificationMessage || (isVerified ? 'Face encodings matched.' : "Face encoding didn't match.") }}
          </p>
        </div>
    
        <!-- Buttons -->
        <button
          v-if="notificationAction"
          class="green-button"
          @click="notificationAction.handler"
        >
          {{ notificationAction.label }}
        </button>
        <template v-else>
          <button
            v-if="isVerified"
            class="green-button"
            @click="onOkay"
          >
            Go Dashboard
          </button>
          <button
            v-else
            class="red-button"
            @click="onRetry"
          >
            Try Again
          </button>
        </template>
      </div>
    </div>
    
    <BarcodeScanner
        v-model:show="showBarcodeScanner"
        @barcode-scanned="handleBarcodeScan"
      />

  </div>
</template>

<script setup>
import LogsSummary from '~/components/LogsSummary.vue';
import CameraSelect from '~/components/SelectCamera.vue'; // Import the camera selection component
import BarcodeScanner from '~/components/BarcodeScanner.vue';
import { ref, onMounted, reactive } from 'vue';
import '~/assets/css/face-scanning.css'; // Import the CSS file for styles
import { useRouter } from 'vue-router'; // Import the router for navigation

// Import the Sidebar and TopBar components
import Sidebar from '~/components/Sidebar.vue';
import TopBar from '~/components/TopBar.vue';

const logs = ref([]); // Store logs
const recentLog = ref(null); // Store recent logs

const videoElement = ref(null); // Ref for the video element
const backendDebugMessages = ref([]); // Store backend debug messages
let isCapturing = false; // Track if capturing is ongoing
let cameraStream = null; // Store the camera stream
const router = useRouter(); // Use the router for navigation

const enterIdMode = ref(false); // Track if "Enter ID" mode is active
const studentNumber = ref(''); // Store the entered student number

// Store student encodings with student numbers as keys
const studentEncodings = reactive({});

const isRecognizing = ref(false); // Track if the recognize endpoint is being called

const isVerified = ref(false); // Track if the student is verified
const has_id = ref(true); // Track if the ID was entered
const showNotification = ref(false); // Track if the notification is visible

// Add new reactive references
const notificationTitle = ref('');
const notificationMessage = ref('');
const notificationAction = ref(null);

const showCameraSelect = ref(false); // Track if the camera selection modal is visible
const showBarcodeScanner = ref(false); // Track if the barcode scanner modal is visible
const hasRecordedEntry = ref(false); // Track if an entry has been recorded

definePageMeta({
  middleware: ['auth']
})

const onScanIdClick = () => {
  has_id.value = true;
  showBarcodeScanner.value = true;
};

const handleBarcodeScan = (scannedBarcode) => {
  studentNumber.value = scannedBarcode;
  has_id.value = true;
  
  // Show notification with scanned number
  notificationTitle.value = 'ID Scanned Successfully';
  notificationMessage.value = `Student Number: ${scannedBarcode}`;
  showNotification.value = true;
  
  // Add a delay before proceeding to face scanning
  setTimeout(() => {
    showNotification.value = false;
    onScanFaceClick();
  }, 2000);
};

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

const recordEntryAttempt = async () => {
  try {
    if (!studentNumber.value.trim()) {
      console.error('Student number is required.');
      logs.value.push('Error: Student number is required.');
      return;
    }

    const payload = {
      student_number: studentNumber.value,
      has_id: has_id.value,
      remarks: "No remarks",
      status: isVerified.value ? 'verified' : 'unverified',
    };

    console.log('Payload being sent:', payload);

    const response = await $fetch('https://sp-j16t.onrender.com/api/logs', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-API-KEY': 'yFITiurVNg9eEXIReziZQQA4iHDlCaZSDxwUCpY9SAsMO36M6OIsRl2MErKBOn9q',
      },
      body: JSON.stringify(payload),
    });

    if (response && response.message === 'Log created successfully') {
      console.log('Log created successfully:', response.data);
      logs.value.push(`Log created successfully for student: ${studentNumber.value}`);
      hasRecordedEntry.value = true; // Set to true after successful record
      await fetchRecentLog();
    } else {
      console.error('Failed to create log:', response);
      logs.value.push('Error: Failed to create log.');
    }
  } catch (error) {
    console.error('Error recording entry attempt:', error);
    logs.value.push(`Error recording entry attempt: ${error.message}`);
  }
};

const onOkay = async () => {
  showNotification.value = false;
  await recordEntryAttempt(); // Call the function to record the entry attempt
};

// Function to handle retry
const onRetry = () => {
  showNotification.value = false;
};

const stopWebcam = () => {
  if (cameraStream) {
    cameraStream.getTracks().forEach((track) => track.stop()); // Stop all tracks
    cameraStream = null;
  }
};

const fetchRecentLog = async () => {
  try {
    const result = await $fetch('https://sp-j16t.onrender.com/api/logs/recent', {
      method: 'GET',
      headers: {
        'X-API-KEY': 'yFITiurVNg9eEXIReziZQQA4iHDlCaZSDxwUCpY9SAsMO36M6OIsRl2MErKBOn9q',
      },
    });

    if (result.success && result.data.length > 0) {
      logs.value = result.data;
      recentLog.value = logs.value[logs.value.length - 1]; // Get the most recent log (last entry)
    } else {
      console.error("Failed to fetch logs:", result);
    }
  } catch (error) {
    console.error("Error fetching logs:", error);
  }
};

// Function to format student names
const formatStudentName = (name) => {
  if (!name) return 'N/A';
  const parts = name.split(" ");
  const formattedParts = parts.map((part, index) => {
    if (index === parts.length - 1) {
      return part.charAt(0) + ".";
    } else {
      return part.charAt(0) + "*".repeat(part.length - 2) + part.charAt(part.length - 1);
    }
  });
  return formattedParts.join(" ");
};

const compareFaceEncoding = async (studentNum, scannedEncoding) => {
  console.log("compareFaceEncoding called with:", { studentNum, scannedEncoding });

  if (!studentNum || !scannedEncoding || scannedEncoding.length === 0) {
    console.error("Invalid student number or scanned encoding.");
    logs.value.push("Error: Invalid student number or scanned encoding.");
    return;
  }

  isRecognizing.value = true; // Start loading indicator

  try {
    console.log("Preparing payload...");
    const payload = {
      student_number: studentNum,
      scanned_face: scannedEncoding,
    };
    console.log("Payload prepared:", payload);

    console.log("Making API call...");
    const response = await $fetch('https://sp-j16t.onrender.com/api/face_encodings/recognize', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-API-KEY': 'yFITiurVNg9eEXIReziZQQA4iHDlCaZSDxwUCpY9SAsMO36M6OIsRl2MErKBOn9q',
      },
      body: JSON.stringify(payload),
    });
    console.log("API call completed. Response received:", response);

    if (response) {
      const isMatch = response.match;
      isVerified.value = isMatch; // Update verification status
      showNotification.value = true; // Show notification
      console.log("show notification:", showNotification.value);
      console.log(`Comparison Result: ${isMatch ? "Match" : "No Match"}`);
      logs.value.push(`Comparison Result for ${studentNum}: ${isMatch ? "Match" : "No Match"}`);
    } else {
      showNotification.value = false; // Hide notification if not a match
      console.error("Comparison failed or unexpected response:", response);
      logs.value.push("Error: Comparison failed or unexpected response.");
      console.log("show notification:", showNotification.value);
    }
  } catch (error) {
    console.error("Error during face encoding comparison:", error);
    logs.value.push(`Error during face encoding comparison: ${error.message}`);
  } finally {
    isRecognizing.value = false; // Stop loading indicator
  }
};
// Function to store encoding with student number
const storeEncoding = (studentNum, encodingData) => {
  if (!studentNum || !encodingData) {
    console.log("Missing student number or encoding data");
    logs.value.push(`Error: Missing student number or encoding data`);
    return false;
  }

  try {
    // Parse the encoding from the response if it's a string
    let encodingArray;
    if (typeof encodingData === 'string') {
      // Extract the encoding array from the string response
      const match = encodingData.match(/\[.*\]/);
      if (match) {
        encodingArray = JSON.parse(match[0]);
      } else {
        throw new Error("Could not extract encoding array from response");
      }
    } else if (typeof encodingData === 'object' && encodingData.encoding) {
      encodingArray = encodingData.encoding;
    } else {
      // If it's already the array itself
      encodingArray = encodingData;
    }

    // Always create a new entry for the student number, even if it already exists
    studentEncodings[studentNum] = encodingArray;

    // Add to logs with more detailed information
    logs.value.push(`Encoding stored for student: ${studentNum}`);
    logs.value.push(`Student-Encoding Pair: "${studentNum}" - ${encodingArray.length} values [${encodingArray.slice(0, 3).map(v => v.toFixed(4))}...]`);

    // Log to console for debugging
    console.log(`Created Student-Encoding Pair:`, {
      studentNumber: studentNum,
      encodingLength: encodingArray.length,
      encodingSample: encodingArray.slice(0, 5),
    });

    // Save to localStorage for persistence (optional)
    try {
      localStorage.setItem('studentEncodings', JSON.stringify(studentEncodings));
    } catch (e) {
      console.error("Error saving to localStorage:", e);
    }

    // Call compareFaceEncoding immediately after storing the encoding
    compareFaceEncoding(studentNum, encodingArray);

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

const navigateToLogsSummary = () => {
  router.push('/logs-summary'); // Navigate to the logs summary page
};

const onVideoCanPlay = () => {
  backendDebugMessages.value.push("Video is ready to play.");
};

const onVideoPlay = () => {
  backendDebugMessages.value.push("Video started playing.");
};

const checkFaceEncoding = async (studentNum) => {
  try {
    const response = await $fetch('https://sp-j16t.onrender.com/api/face_encodings/show', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-API-KEY': 'yFITiurVNg9eEXIReziZQQA4iHDlCaZSDxwUCpY9SAsMO36M6OIsRl2MErKBOn9q',
      },
      body: JSON.stringify({
        student_number: studentNum
      }),
    });

    return response;
  } catch (error) {
    console.error('Error checking face encoding:', error);
    return null;
  }
};

const navigateToRegister = () => {
  router.push('/register-student');
};

const onScanFaceClick = async () => {
  if (studentNumber.value.trim() === '') {
    alert('Please enter a valid student number.');
    return;
  }

  // First check if face encoding exists
  const encodingResult = await checkFaceEncoding(studentNumber.value);

  if (!encodingResult || !encodingResult.data) {
    // Show notification for unregistered face
    showNotification.value = true;
    isVerified.value = false;
    notificationTitle.value = 'Face Not Registered';
    notificationMessage.value = 'Face still not registered.';
    notificationAction.value = {
      label: 'Register Face',
      handler: navigateToRegister
    };
    return;
  }

  // If face encoding exists, proceed with capture and comparison
  captureAndSend();
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

  try {
    // Create a canvas element to draw the current video frame
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    // Draw the current video frame onto the canvas
    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

    // Convert the canvas image to a base64-encoded JPEG image
    const imageData = canvas.toDataURL('image/jpeg');

    const formData = new FormData();
    formData.append('image', imageData); // Send the base64 image

    const response = await fetch('http://localhost:8000/api/encode', {
      method: 'POST',
      body: formData,
    });

    const data = await response.json();

    // Extract the success value and encoding array from the response
    const match = data.encoding.match(/'success':\s*(true|false)/i);
    const success = match ? match[1].toLowerCase() === 'true' : false;

    const encodingMatch = data.encoding.match(/\[([^\]]+)\]/); // Match the array inside square brackets
    let encodingArray = null;
    
    if (encodingMatch) {
      try {
        encodingArray = JSON.parse(`[${encodingMatch[1]}]`); // Parse the matched array
      } catch (error) {
        console.error("Failed to parse encoding array:", error);
        logs.value.push("Error parsing encoding array.");
      }
    } else {
      console.error("No encoding array found in response");
      logs.value.push("No encoding array found in response.");
    }

    // Ensure student number is valid
    if (!studentNumber.value.trim()) {
      logs.value.push("Student number is empty or invalid.");
      return;
    }

    if (success && encodingArray && encodingArray.length > 0) {
      // Store the encoding with the student number
      const stored = storeEncoding(studentNumber.value, encodingArray);

      if (stored) {
        logs.value.push(`Successfully stored encoding for student: ${studentNumber.value}`);
      } else {
        logs.value.push("Failed to store encoding.");
      }
    } else {
      logs.value.push("Failed to extract encoding or success value.");
    }
    
  } catch (error) {
    console.error("Error sending the image:", error);
    logs.value.push(`Error sending the image: ${error.message}`);
  } finally {
    isCapturing = false; // Reset the capturing flag
  }
};

// Initialize the webcam feed
const initializeWebcam = async () => {
  try {
    cameraStream = await navigator.mediaDevices.getUserMedia({ video: true });
    const video = videoElement.value;
    if (video) {
      video.srcObject = cameraStream; // Set the webcam stream as the video source
      video.play(); // Ensure the video starts playing
    }
  } catch (error) {
    console.error("Error accessing webcam:", error); // Log the error
    logs.value.push(`Error accessing webcam: ${error.message}`);
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

const onEnterIdClick = () => {
  enterIdMode.value = true; // Enable "Enter ID" mode
  has_id.value = false; // Update has_id to false
};

onMounted(() => {
  initializeWebcam(); // Start the webcam feed
  loadSavedEncodings(); // Load any previously saved encodings
  fetchRecentLog(); // Fetch recent logs from the backend
});

onBeforeUnmount(() => {
  stopWebcam(); // Stop the webcam feed when the component is unmounted
});
</script>

<style>
/* Additional styles for the logs summary */
.second-square {
  padding: 20px;
  box-sizing: border-box;
}

.loading-indicator {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 18px;
  color: #333;
  text-align: center;
  z-index: 1000;
}

</style>