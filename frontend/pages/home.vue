<template>
  <div class="home-container">
    <AppHeader />
    <!-- Background video - lower z-index to ensure it stays in the background -->
    <video autoplay muted loop class="background-video">
      <source src="~/assets/videos/bg_vid.mp4" type="video/mp4">
    </video>
    
    <!-- Content wrapper - will contain all UI elements that should be above the video -->
    <div class="content-wrapper">
      <!-- Logo -->
      
      <!-- Content container -->
      <div class="content-container">
        <!-- Title and subtitle -->
        <div class="title-section">
          <h1 class="main-title">
            <span>FACE & ID</span>
            <span>AUTHENTICATION</span>
          </h1>
          <h2 class="home-subtitle">Enhancing UPLB's Curfew System with Face Recognition and School ID Authentication</h2>
        </div>
        
        <!-- Input field and scan button -->
        <div class="input-section-home">
          <!-- Login form (default) -->
          <div v-if="!showRegistrationForm" class="input-button-container-home">
            <input 
              type="text" 
              placeholder="Enter your student no." 
              class="student-id-input"
              v-model="studentId"
              @input="checkInputMethod"
              @keydown="resetTypingTimer">
            <button class="scan-button" @click="checkIfStudentNoExists">SCAN FACE</button>
          
            <!-- Student verification feedback -->
            <div v-if="verificationAttempted" class="verification-feedback">
              <p v-if="studentNoExists" class="current-process">
                {{ processingMessage }}
                <span v-if="loadingStatus === 'error'" class="retry-links">
                  <a href="#" @click.prevent="retryFaceEncoding">Yes</a> / 
                  <a href="#" @click.prevent="logEntryAnyway">No</a>
                </span>
                <!-- Add LoadingSpinner component -->
                <LoadingSpinner :status="spinnerStatus" />
              </p>
              <p v-else class="verification-error">
                Student number not found...
                <span style="margin-left: 2%;">
                  <a href="#" class="register-link" @click.prevent="toggleRegistrationForm">Register</a>
                </span>
              </p>
            </div>
          </div>
          
          <!-- Registration form -->
          <div v-else class="registration-form">
            <input 
              type="text" 
              placeholder="Enter Full Name" 
              class="student-name-input"
              v-model="registrationName">
            <input 
              type="text" 
              placeholder="Enter Student no." 
              class="student-id-input"
              v-model="registrationStudentId">
            <div class="register-buttons">
              <button class="register-button" @click="registerStudent">REGISTER</button>
              <button class="cancel-button" @click="toggleRegistrationForm">CANCEL</button>
            </div>
            <p class="current-process">
              {{ processingMessage }}
              <span v-if="registrationSuccessful" class="scan-face-link">
                <a href="#" @click.prevent="toggleRegistrationForm">Scan face.</a>
              </span>
              <LoadingSpinner :status="spinnerStatus" />
            </p>
          </div>

          <div v-if="entryRecorded" class="enrollment-status">
            <img src="~/assets/images/enrolled_check.png" class="enrolled-check" alt="Enrolled">
            Enrolled
          </div>
        </div>

      </div>
      
      <!-- Bottom info section - moved to bottom right -->
      <div class="bottom-info">
        <p class="bottom-subtitle">Web app verifies students' identities using facial recognition.</p>
        <div class="camera-icon" @click="openCameraSelect">
          <i class="fas fa-camera"></i>
        </div>
      </div>
      
      <!-- Camera circle -->
      <div class="camera-circle">
        <div class="webcam-container">
          <video ref="webcam" class="webcam-feed" autoplay playsinline></video>
          <div class="face-square-overlay"><span></span></div>
        </div>
      </div>
    </div>
    
    <!-- Camera selection modal -->
    <select-camera v-model="showCameraSelect" @camera-change="onCameraChange" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import SelectCamera from '~/components/SelectCamera.vue';
import LoadingSpinner from '~/components/LoadingSpinner.vue'; // Import the loading spinner component
import AppHeader from '~/components/AppHeader.vue'; // Import the header component
import { compareFaceEncoding } from '~/utils/compareFaceEncoding.js'; // Import the face recognition utility
import { getFaceEncoding } from '~/utils/getFaceEncoding.js'; // Import the face encoding utility
import { registerStudent as registerStudentUtil } from '~/utils/registerStudent.js'; // Import the register student utility
import { recordEntry } from '~/utils/recordEntry.js'; // Import the record entry utility
import { fixStudentNumberFormat } from '~/utils/fixStudentNumberFormat.js'; // Import the student number formatting utility
import { checkStudentEnrollment } from '~/utils/studentApi.js';
import '~/assets/css/home.css'; // Import the external CSS file

const webcam = ref(null);
const stream = ref(null);
const showCameraSelect = ref(false);
const studentId = ref('');
const studentNoExists = ref(false);
const verificationAttempted = ref(false);
const processingMessage = ref('Student number found...');
const isProcessing = ref(false);
const loadingStatus = ref('none'); // Status for face matching
const faceMatched = ref(false); // Flag to indicate if face matching was successful
const registrationSuccessful = ref(false); // Flag to indicate if registration was successful
const entryRecorded = ref(false); // Flag to indicate if entry was recorded
const isEnrolled = ref(false);

// registering form variables
const showRegistrationForm = ref(false);
const registrationName = ref('');
const registrationStudentId = ref('');

// Add these variables to your script setup section
const wasScanned = ref(false);
const lastInputTime = ref(0);
const typingTimer = ref(null);
const typingSpeed = ref(0);

// Function to detect if input was scanned or typed
const checkInputMethod = () => {
  const now = Date.now();
  
  // Calculate typing speed (time between inputs in ms)
  if (lastInputTime.value > 0) {
    typingSpeed.value = now - lastInputTime.value;
  }
  
  // Update last input time
  lastInputTime.value = now;
  
  // If input is very fast (less than 50ms between characters) and length is significant,
  // it's likely from a scanner
  if (typingSpeed.value < 50 && studentId.value.length > 3) {
    wasScanned.value = true;
  }
};

// Reset typing timer on each keydown
const resetTypingTimer = () => {
  // Clear any existing timer
  if (typingTimer.value) clearTimeout(typingTimer.value);
  
  // Set a new timer that will reset the wasScanned flag after 2 seconds of inactivity
  typingTimer.value = setTimeout(() => {
    // If it's been 2 seconds since last input, reset for next input session
    lastInputTime.value = 0;
    // Don't reset wasScanned here, we want to preserve the detection
  }, 2000);
};

// Function to toggle between login and registration forms
const toggleRegistrationForm = () => {
  loadingStatus.value = 'none'; // Reset match status
  showRegistrationForm.value = !showRegistrationForm.value;
  processingMessage.value = ''; // Reset processing message
  studentNoExists.value = true; // Reset student number existence check
  studentNumber.value = ''; // Reset student ID input field
  // Reset form fields when toggling
  if (showRegistrationForm.value) {
    registrationName.value = '';
    registrationStudentId.value = '';
  }
};

// Compute spinner status based on processing state and match status
const spinnerStatus = computed(() => {
  if (isProcessing.value) return 'loading';
  if (loadingStatus.value === 'success') return 'success';
  if (loadingStatus.value === 'error') return 'error';
  return 'none';
});

// Modify your checkIfStudentNoExists function
const checkIfStudentNoExists = async () => {
  entryRecorded.value = false; // Reset entry recorded state
  // Reset previous state
  registrationSuccessful.value = false; // Reset registration success state
  loadingStatus.value = 'none';
  studentId.value = fixStudentNumberFormat(studentId.value);
  if (!studentId.value) {
    alert("Please enter your student no.");
    return;
  }
  
  // Log whether scanner was used for debugging
  console.log("Was scanner used:", wasScanned.value);
  
  try {
    const apiUrl = useRuntimeConfig().public.apiUrl;
    const mainApiKey = useRuntimeConfig().public.mainApiKey;
    
    const response = await fetch(`${apiUrl}/students/show`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-API-KEY':mainApiKey
      },
      body: JSON.stringify({
        student_number: studentId.value,
      }),
    });
    
    const data = await response.json();
    console.log('API Response:', data);
    verificationAttempted.value = true;
    
    if (response.ok && data.message === "Student found successfully") {
      studentNoExists.value = true;
      // Process face encoding after student is verified
      handleFaceEncoding();
    } else {
      studentNoExists.value = false;
    }
  } catch (error) {
    console.error('Error validating student ID:', error);
    verificationAttempted.value = true;
    studentNoExists.value = false;
  }
};

const handleFaceEncoding = async () => {
  faceMatched.value = false; // Reset face matched status
  if (!studentNoExists.value) {
    return;
  }
  
  try {
    isProcessing.value = true;
    loadingStatus.value = 'none'; // Reset match status
    
    const mainApiKey = useRuntimeConfig().public.mainApiKey;
    
    // Get the face encoding
    const faceEncodingResult = await getFaceEncoding({
      webcam: webcam.value,
      updateMessage: (message) => {
        processingMessage.value = message;
      }
    });
    
    // If we couldn't get a face encoding, exit early
    if (!faceEncodingResult || !faceEncodingResult.success) {
      loadingStatus.value = 'error';
      return;
    }
    
    // Now proceed with the comparison
    processingMessage.value = "Comparing face with database...";
    
    // Call compare function with the extracted encoding
    const isMatch = await compareFaceEncoding(
      studentId.value, 
      faceEncodingResult, 
      mainApiKey
    );
    
    if (isMatch) {
      processingMessage.value = "Face encoding matched!";
      loadingStatus.value = 'success';
      faceMatched.value = true; // Set the flag to true
      await recordStudentEntry(true);
    } else {
      processingMessage.value = "Face did not match. Try again?";
      loadingStatus.value = 'error';
      faceMatched.value = false; // Set the flag to false
    }
    
  } catch (error) {
    console.error('Error in face encoding process:', error);
    processingMessage.value = "Unexpected error occurred. Please try again.";
    loadingStatus.value = 'error';
  } finally {
    isProcessing.value = false;
  }
};

// Add these methods inside the script setup section
const retryFaceEncoding = () => {
  // Call handleFaceEncoding again
  handleFaceEncoding();
};

const recordStudentEntry = async (verificationSuccess = false) => {
  try {
    entryRecorded.value = false;
    isProcessing.value = true;
    loadingStatus.value = 'none';
    processingMessage.value = "Recording entry...";
    
    const mainApiKey = useRuntimeConfig().public.mainApiKey;

    isEnrolled.value = true; // Reset enrollment status
    
    // Check enrollment status from AMIS API
    // try {
    //   const enrollmentResult = await checkStudentEnrollment(studentId.value);
    //   isEnrolled.value = enrollmentResult.is_student === true;
    // } catch (error) {
    //   console.error('Failed to check enrollment status:', error);
    //   // Fallback to false if there's an error
    //   isEnrolled.value = false;
    // }
    
    // Continue with existing code to record entry
    const result = await recordEntry({
      studentNumber: studentId.value,
      hasId: wasScanned.value,
      remarks: verificationSuccess 
        ? (wasScanned.value ? "Face verified with ID scan" : "Face verified without ID scan") 
        : "Face verification bypassed",
      status: faceMatched.value ? "verified" : "unverified",
      apiKey: mainApiKey
    });
    
    if (result.success) {
      processingMessage.value = wasScanned.value 
        ? (faceMatched.value 
            ? "Entry recorded: Student is verified, presented ID."
            : "Entry recorded: Student unverified, presented ID.")
        : (faceMatched.value
            ? "Entry recorded: Student is verified, no ID presented."
            : "Entry recorded: Student unverified, no ID presented.");
      loadingStatus.value = 'success';
      entryRecorded.value = true;
      
      // Clear the student ID input field after successful entry recording
      studentId.value = '';
    } else {
      processingMessage.value = "Failed to record entry.";
      loadingStatus.value = 'error';
    }
  } catch (error) {
    console.error('Error recording entry:', error);
    processingMessage.value = "Error recording entry.";
    loadingStatus.value = 'error';
  } finally {
    isProcessing.value = false;
    // Reset scanner detection for next entry
    wasScanned.value = false;
  }
};

const logEntryAnyway = async () => {
  loadingStatus.value = 'none'; // Reset match status
  processingMessage.value = "Entry log will be added..";
  await recordStudentEntry(true);
  // Here you would add code to log the entry despite failed face recognition
  // For example, make an API call to log the entry
};

// Function to handle student registration via register button
const registerStudent = async () => {
  // Validate input fields
  registrationStudentId.value = fixStudentNumberFormat(registrationStudentId.value);
  if (!registrationName.value || !registrationStudentId.value) {
    processingMessage.value = "Please fill in all fields.";
    return;
  }
  
  // Show processing state
  isProcessing.value = true;
  processingMessage.value = "Starting registration process...";
  
  try {
    const apiConfig = useRuntimeConfig().public;
    
    // Call the utility function to handle face encoding and registration
    const result = await registerStudentUtil({
      webcam: webcam.value,
      studentId: registrationStudentId.value,
      studentName: registrationName.value,
      updateMessage: (message) => {
        processingMessage.value = message;
      },
      apiKey: apiConfig.mainApiKey
    });
    
    if (result.success) {
          processingMessage.value = "Registration successful!";
          loadingStatus.value = 'success';
          studentId.value = registrationStudentId.value;
          registrationSuccessful.value = true; // Set this flag to true
          studentNoExists.value = false; // Set this flag to true
        } else {
          processingMessage.value = "Registration failed.";
          loadingStatus.value = 'error';
        }
    
  } catch (error) {
    console.error('Registration error:', error);
    alert('An unexpected error occurred during registration.');
  } finally {
    isProcessing.value = false;
  }
};

// Initialize default camera on page load
onMounted(async () => {
  try {
    // Get available video devices
    const devices = await navigator.mediaDevices.enumerateDevices();
    const videoDevices = devices.filter(device => device.kind === 'videoinput');
    
    if (videoDevices.length > 0) {
      // Use the first available camera (usually the laptop's built-in camera)
      const defaultCamera = videoDevices[0].deviceId;
      
      // Start the camera
      await startCamera(defaultCamera);
    }
  } catch (error) {
    console.error('Error initializing default camera:', error);
  }
});

// Function to start camera with specified deviceId
const startCamera = async (deviceId) => {
  if (stream.value) {
    stopCamera();
  }

  try {
    stream.value = await navigator.mediaDevices.getUserMedia({
      video: { 
        deviceId: deviceId ? { exact: deviceId } : undefined,
        width: { ideal: 480 },
        height: { ideal: 480 }
      }
    });
    
    if (webcam.value) {
      webcam.value.srcObject = stream.value;
    }
  } catch (error) {
    console.error('Error accessing camera:', error);
  }
};

// Function to open camera selection modal
const openCameraSelect = () => {
  showCameraSelect.value = true;
};

// Function to handle camera change
const onCameraChange = async (deviceId) => {
  if (!deviceId) return;
  await startCamera(deviceId);
};

// Function to stop camera
const stopCamera = () => {
  if (stream.value) {
    stream.value.getTracks().forEach(track => track.stop());
    stream.value = null;
  }
};

// Clean up camera on component unmount
onUnmounted(() => {
  stopCamera();
});
</script>

<style>
/* Empty style block - all styles moved to home.css */
</style>