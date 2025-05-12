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
      


          <!-- Loading Indicator - Shown when recognizing -->
          <div v-if="isRecognizing" class="loading-indicator">
            <i class="fas fa-spinner fa-spin"></i>
          </div>
      
          <!-- Content shown when not recognizing -->
          <template v-else>
            <!-- Ready for Scan State -->
            <div v-if="!hasRecordedEntry">
              <div class="verified-box ready-box">
                <div class="recording-indicator"></div>
                <span>Ready</span>
              </div>
              
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
              <div class="verified-box">
                <div v-if="isVerified" class="verified-circle">
                  <i class="fas fa-check"></i>
                </div>
                <div v-else class="unverified-circle">
                  <i class="fas fa-exclamation"></i>
                </div>
                <span>{{ isVerified ? 'Verified' : 'Unverified' }}</span>
              </div>
            
              <div class="student-name">
                {{ studentName ? formatStudentName(studentName) : 'N/A' }}
              </div>
            
              <div class="student-id">
                {{ studentNumber ? maskStudentNumber(studentNumber) : 'N/A' }}
              </div>
            
              <div class="enrolled">
                {{ isEnrolled ? 'Enrolled' : 'Not Enrolled' }}
              </div>
              
              <div class="entry-time">
                {{ entryTime || new Date().toLocaleString() }}
              </div>
              
              <div class="remarks">
                Remarks
              </div>
            
              <div class="remarks-note">
                {{ has_id ? 'Presented ID' : 'No ID Presented' }}
              </div>
            </div>
          </template>

      </div>

      <div class="scanned-number-display" v-if="showScannedInfo">
        <div class="scanned-info" v-if="showScannedInfo">
          <i class="fas fa-barcode"></i>
          <span>Scanned ID: {{ maskStudentNumber(studentNumber) }}</span>
        </div>
      </div>

      <!-- Buttons -->
      <div v-if="!enterIdMode && (showIdButtons || showScannedInfo)">
        <button
          class="enter-id-button animated-button"
          v-if="showIdButtons"
          @click="onEnterIdClick"
        >
          <i class="fas fa-id-card"></i>
          Enter ID
        </button>
        
        <button
          class="scan-id-button animated-button"
          v-if="showIdButtons"
          @click="onScanIdClick" 
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
          :disabled="isScanning"
        >
          {{ isScanning ? 'Scanning...' : 'Scan Face' }}
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

    <!-- ID Scan Notification Modal -->
    <div v-if="showIdScanNotification" class="notification-modal" @click.self="onIdScanOkay">
      <div class="notification-content">
        <button 
          type="button" 
          class="close-button" 
          @click.stop="onIdScanOkay"
        >×</button>
        <div class="modal-main-content">
          <div class="icon-circle success-icon">
            <i class="fas fa-check"></i>
          </div>
          <h2>ID Scanned Successfully</h2>
          <p class="subtitle">Student Number: {{ studentNumber }}</p>
        </div>
        <button class="green-button" @click="proceedToFaceScan">
          Proceed to Face Scan
        </button>
      </div>
    </div>

    <!-- Verification Notification Modal -->
    <div v-if="showVerificationNotification" class="notification-modal">
      <div class="notification-content">
        <button class="close-button" @click="onVerificationOkay">×</button>
        <div class="modal-main-content">
          <div class="icon-circle" :class="isVerified ? 'success-icon' : 'failure-icon'">
            <i :class="isVerified ? 'fas fa-check' : 'fas fa-times'"></i>
          </div>
          <h2>{{ isVerified ? 'Verification Successful' : 'Verification Failed' }}</h2>
          <p class="subtitle">
            {{ isVerified ? 'Face encodings matched.' : "Face encoding didn't match." }}
          </p>
        </div>
        <button
          :class="isVerified ? 'green-button' : 'red-button'"
          @click="handleVerificationButtonClick"
        >
          {{ isVerified ? 'Go Dashboard' : 'Try Again' }}
        </button>
      </div>
    </div>
    
    <BarcodeScanner
        v-model:show="showBarcodeScanner"
        @barcode-scanned="handleBarcodeScan"
      />
    <div v-if="showNotification" class="notification-modal">
      <div class="notification-content">
        <button class="close-button" @click="showNotification = false">×</button>
        <div class="modal-main-content">
          <div class="icon-circle warning-icon">
            <i class="fas fa-exclamation"></i>
          </div>
          <h2>{{ notificationTitle }}</h2>
          <p class="subtitle">{{ notificationMessage }}</p>
        </div>
        <button 
          v-if="notificationAction"
          class="green-button"
          @click="notificationAction.handler"
        >
          {{ notificationAction.label }}
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import LogsSummary from '~/components/LogsSummary.vue';
import CameraSelect from '~/components/SelectCamera.vue'; // Import the camera selection component
import BarcodeScanner from '~/components/BarcodeScanner.vue';
import { ref, onMounted, reactive } from 'vue';
import '~/assets/css/face-scanning.css'; // Import the CSS file for styles
import { useRouter } from 'vue-router'; // Import the router for navigation
import { maskStudentNumber } from '~/utils/maskStudentNumber';

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
const idScanSuccess = ref(false); // Track if ID scan was successful
const isScanning = ref(false); // Track if scanning is in progress

const showIdButtons = ref(true); // Track if ID buttons should be shown
const showIdScanNotification = ref(false);
const showVerificationNotification = ref(false);
const showScannedInfo = ref(false); // Track if the scanned info div should be shown
const studentName = ref(''); // Store the student's name
const isEnrolled = ref(false); // Track if the student is enrolled
const entryTime = ref(''); // Store the entry time
const mainApiKey = useRuntimeConfig().public.mainApiKey;

const resetStates = () => {
  showScannedInfo.value = false;
  studentNumber.value = '';
  showIdButtons.value = true;
  enterIdMode.value = false;
};

const getStudentDetails = async (studentNum) => {
  try {
    const response = await $fetch('https://sp-j16t.onrender.com/api/students/show', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-API-KEY': mainApiKey,
      },
      body: JSON.stringify({
        student_number: studentNum
      }),
    });

    if (response.message === 'Student found successfully') {
      studentName.value = response.data.student_name;
      isEnrolled.value = response.data.enrolled;
      return response.data;
    }
    return null;
  } catch (error) {
    console.error('Error fetching student details:', error);
    studentName.value = 'N/A';
    isEnrolled.value = false;
    return null;
  }
};

const handleVerificationButtonClick = () => {
  if (isVerified.value) {
    onVerificationOkay();
  } else {
    onRetry();
  }
};

definePageMeta({
  middleware: ['auth']
})

const onScanIdClick = () => {
  has_id.value = true;
  showBarcodeScanner.value = true;
  showIdButtons.value = false; // Hide the buttons while scanner is active
};

const formatStudentNumber = (studentNum) => {
  // Remove any leading zeros
  const cleanNum = studentNum.replace(/^0+/, '');
  
  // Check if the number has the correct length (9 digits)
  if (cleanNum.length !== 9) {
    console.error('Invalid student number length');
    return null;
  }
  
  // Split into year (4 digits) and sequence (5 digits)
  const year = cleanNum.slice(0, 4);
  const sequence = cleanNum.slice(4);
  
  // Return formatted string
  return `${year}-${sequence}`;
};

const handleBarcodeScan = async (scannedBarcode) => {
  const formattedStudentNumber = formatStudentNumber(scannedBarcode);
  studentNumber.value = formattedStudentNumber;
  has_id.value = true;
  idScanSuccess.value = true;
  
  // Get student details after successful scan
  await getStudentDetails(scannedBarcode);
  
  showIdScanNotification.value = true;
};

const onIdScanOkay = async () => {
  console.log('Close button clicked'); // Add this for debugging
  
  try {
    const encodingResult = await checkFaceEncoding(studentNumber.value);
    
    if (!encodingResult || !encodingResult.data) {
      notificationTitle.value = 'Face Not Registered';
      notificationMessage.value = 'Face still not registered.';
      notificationAction.value = {
        label: 'Register Face',
        handler: navigateToRegister
      };
      return;
    }
    
    showIdScanNotification.value = false;
    onScanFaceClick();
  } catch (error) {
    console.error('Error in onIdScanOkay:', error);
  }
};

const onVerificationOkay = async () => {
  console.log('Go Dashboard clicked');
  showVerificationNotification.value = false;
  showIdButtons.value = true;
  enterIdMode.value = false;
  showScannedInfo.value = false;
  await recordEntryAttempt();

  // After 5 seconds, reset to ready state
  setTimeout(() => {
    hasRecordedEntry.value = false;
    studentName.value = '';
    studentNumber.value = '';
    isEnrolled.value = false;
    has_id.value = true;
    entryTime.value = '';
  }, 5000);
};

const onRetry = () => {
  console.log('Try Again clicked');
  showVerificationNotification.value = false;
  showIdButtons.value = true;
  // Add any additional retry logic here
};

const proceedToFaceScan = async () => {
  showIdScanNotification.value = false;
  showScannedInfo.value = true; // Show the scanned info
  
  // Set a timeout to hide the scanned info after 5 seconds
  setTimeout(() => {
    showScannedInfo.value = false;
  }, 5000);
  
  onScanFaceClick();
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

    entryTime.value = new Date().toLocaleString(); // Get the current date and time

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
        'X-API-KEY': mainApiKey,
      },
      body: JSON.stringify(payload),
    });

    if (response && response.message === 'Log created successfully') {
      console.log('Log created successfully:', response.data);
      logs.value.push(`Log created successfully for student: ${studentNumber.value}`);
      hasRecordedEntry.value = true; // Set to true after successful record
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
  // Check if this is after ID scan
  if (notificationTitle.value === 'ID Scanned Successfully') {
    // Check if face encoding exists for the scanned student
    const encodingResult = await checkFaceEncoding(studentNumber.value);
    
    if (!encodingResult || !encodingResult.data) {
      // Show notification for unregistered face
      notificationTitle.value = 'Face Not Registered';
      notificationMessage.value = 'Face still not registered.';
      notificationAction.value = {
        label: 'Register Face',
        handler: navigateToRegister
      };
      return; // Don't close notification, show registration prompt instead
    }
    
    // If face encoding exists, proceed with face scanning
    showNotification.value = false;
    onScanFaceClick();
  } else {
    // Handle other notifications (verification results, etc.)
    showNotification.value = false;
    showIdButtons.value = true; // Restore the buttons
    enterIdMode.value = false; // Reset enter ID mode
    await recordEntryAttempt();
  }
};


const stopWebcam = () => {
  if (cameraStream) {
    cameraStream.getTracks().forEach((track) => track.stop()); // Stop all tracks
    cameraStream = null;
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
        'X-API-KEY': mainApiKey,
      },
      body: JSON.stringify(payload),
    });
    console.log("API call completed. Response received:", response);

    if (response) {
      const isMatch = response.match;
      isVerified.value = isMatch; // Update verification status
      
      // Show verification notification instead of generic notification
      showVerificationNotification.value = true;
      
      // Log the result
      console.log(`Comparison Result: ${isMatch ? "Match" : "No Match"}`);
      logs.value.push(`Comparison Result for ${studentNum}: ${isMatch ? "Match" : "No Match"}`);
      
      // Clear any previous notifications
      showIdScanNotification.value = false;
      showNotification.value = false;
    } else {
      console.error("Comparison failed or unexpected response:", response);
      logs.value.push("Error: Comparison failed or unexpected response.");
    }
  } catch (error) {
    console.error("Error during face encoding comparison:", error);
    logs.value.push(`Error during face encoding comparison: ${error.message}`);
    
    // Show error in verification notification
    isVerified.value = false;
    showVerificationNotification.value = true;
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
        'X-API-KEY': mainApiKey,
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

  isScanning.value = true; // Set scanning state to true

  try {
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
    await getStudentDetails(studentNumber.value);
    await captureAndSend();
  } finally {
    isScanning.value = false; // Reset scanning state
  }
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

// Function to get face encoding from API
const getFaceEncoding = async () => {
  if (!studentVerified.value || !stream.value) {
    return;
  }
  
  try {
    isProcessing.value = true;
    processingMessage.value = "Scanning face, please position your face in the square...";
    
    // Create a canvas to capture the current webcam frame
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    canvas.width = webcam.value.videoWidth;
    canvas.height = webcam.value.videoHeight;
    
    // Draw the current webcam frame on the canvas
    context.drawImage(webcam.value, 0, 0, canvas.width, canvas.height);
    
    // Convert the canvas to a base64 image
    const imageDataUrl = canvas.toDataURL('image/jpeg');
    const base64Image = imageDataUrl.split(',')[1]; // Remove the data URL prefix
    
    const apiUrl = useRuntimeConfig().public.apiUrl;
    const mainApiKey = useRuntimeConfig().public.mainApiKey;
    
    // Send the image to the API for encoding
    const response = await fetch(`${apiUrl}/encode`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-API-KEY': mainApiKey
      },
      body: JSON.stringify({
        student_number: studentId.value,
        image_data: base64Image
      }),
    });
    
    const data = await response.json();
    
    if (response.ok && data.success) {
      processingMessage.value = "Face encoding done...";
      // You might want to store the encoding or proceed with authentication
    } else {
      processingMessage.value = "Face encoding failed. Please try again.";
    }
  } catch (error) {
    console.error('Error processing face encoding:', error);
    processingMessage.value = "Error processing face. Please try again.";
  } finally {
    isProcessing.value = false;
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
  enterIdMode.value = true;
  has_id.value = false;
  showIdButtons.value = false; // Hide the buttons while in enter ID mode
};

onMounted(() => {
  resetStates(); // Reset states on component mount
  initializeWebcam(); // Start the webcam feed
  loadSavedEncodings(); // Load any previously saved encodings
});

onBeforeUnmount(() => {
  stopWebcam(); // Stop the webcam feed when the component is unmounted
});
</script>