<template>
  <div class="home-container">
    <!-- Background video - lower z-index to ensure it stays in the background -->
    <video autoplay muted loop class="background-video">
      <source src="~/assets/videos/bg_vid.mp4" type="video/mp4">
    </video>
    
    <!-- Content wrapper - will contain all UI elements that should be above the video -->
    <div class="content-wrapper">
      <!-- Logo -->
      <div class="logo-container">
        <img src="~/assets/images/uplb_logo.png" alt="UPLB Logo" class="uplb-logo">
        <div class="logo-text">
          <div class="uplb-text">UPLB</div>
          <div class="security-text">SECURITY</div>
        </div>
      </div>
      
      <!-- Navigation links -->
      <div class="nav-links">
        <a href="#" class="active">Home</a>
        <a href="#">Contact</a>
        <a href="#">Register</a>
        <a href="#">Login</a>
      </div>
      
      <!-- Content container -->
      <div class="content-container">
        <!-- Title and subtitle -->
        <div class="title-section">
          <h1 class="main-title">
            <span>FACE & ID</span>
            <span>AUTHENTICATION</span>
          </h1>
          <h2 class="subtitle">Enhancing UPLB's Curfew System with Face Recognition and School ID Authentication</h2>
        </div>
        
        <!-- Input field and scan button -->
        <div class="input-section">
          <div class="input-button-container">
            <input type="text" placeholder="Enter your student ID" class="student-id-input">
            <button class="scan-button" @click="openCameraSelect">SCAN FACE</button>
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
        <video ref="webcam" class="webcam-feed" autoplay playsinline></video>
      </div>
    </div>
    
    <!-- Camera selection modal -->
    <select-camera v-model="showCameraSelect" @camera-change="onCameraChange" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import SelectCamera from '~/components/SelectCamera.vue';

const webcam = ref(null);
const stream = ref(null);
const showCameraSelect = ref(false);

// Function to open camera selection modal
const openCameraSelect = () => {
  showCameraSelect.value = true;
};

// Function to handle camera change
const onCameraChange = async (deviceId) => {
  if (stream.value) {
    stopCamera();
  }

  if (!deviceId) return;

  try {
    stream.value = await navigator.mediaDevices.getUserMedia({
      video: { 
        deviceId: { exact: deviceId },
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

<style scoped>
/* Reset and base styles */
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body {
  height: 100%;
  width: 100%;
  overflow: hidden;
}

.home-container {
  position: relative;
  min-height: 100vh;
  min-width: 100vw;
  overflow: hidden;
  color: white;
  font-family: 'Bricolage Grotesque', sans-serif;
}

/* Background video styling */
.background-video {
  position: fixed; /* Changed from absolute to fixed */
  top: 0;
  left: 0;
  width: 100vw; /* Use viewport units */
  height: 100vh;
  object-fit: cover;
  z-index: -1; /* Keep video in background */
}

/* Content wrapper */
.content-wrapper {
  position: relative;
  width: 100%;
  height: 100vh;
  z-index: 2; /* Higher z-index than video */
  display: flex;
  flex-direction: column;
}

/* Logo styling */
.logo-container {
  position: absolute;
  top: 20px;
  right: 20px;
  z-index: 10;
  display: flex;
  align-items: center;
}

.uplb-logo {
  height: 60px;
  width: auto;
  margin-right: 10px;
}

.logo-text {
  display: flex;
  flex-direction: column;
  align-items: flex-start; /* Right align */
}

.uplb-text, .security-text {
  font-weight: bold;
  color: white;
}

.uplb-text {
  font-size: 1.5rem;
}

.security-text {
  font-size: 1.2rem;
}

/* Navigation links */
.nav-links {
  position: absolute;
  top: 50px;
  left: 50px;
  z-index: 10;
  display: flex;
  gap: 70px;
}

.nav-links a {
  color: white;
  text-decoration: none;
  font-size: 1.2rem;
  transition: all 0.2s ease;
}

.nav-links a:hover {
  opacity: 0.8;
}

.nav-links a.active {
  border-bottom: 2px solid white;
  font-weight: bold;
}

/* Content container */
.content-container {
  position: absolute;
  top: 50%;
  left: 32%;
  transform: translate(-50%, -50%);
  width: 80%;
  max-width: 800px;
  z-index: 10;
}

.title-section {
  margin-bottom: 40px;
  display: flex;
  flex-direction: column;
  align-items: flex-start; /* Right align */
}

.main-title {
  font-family: 'HK Gothic', sans-serif; /* Updated font family */
  font-weight: bold;
  margin-bottom: 15px;
  text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.main-title span {
  display: block;
  font-size: 3rem;
  line-height: 1.1;
}

.subtitle {
  font-family: 'Montserrat', sans-serif; /* Updated font family */
  font-size: 1.5rem;
  font-weight: normal;
  opacity: 0.9;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
  max-width: 600px; /* Limit width to control line breaks */
}

.input-section {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-bottom: 50px;
  width: 100%;
  max-width: 400px;
  margin-left: 15px;/* Right align */
}

.input-button-container {
  position: relative;
  width: 100%;
  margin-left: 10px;
}

.student-id-input {
  padding: 12px 15px;
  border-radius: 50px; /* Added 100 corner rounding */
  border: none;
  font-size: 1.1rem;
  font-family: inherit;
  width: 100%;
  padding-right: 120px; /* Make space for the button */
}

.scan-button {
  position: absolute;
  right: 5px;
  top: 5px;
  bottom: 5px;
  padding: 0 15px;
  border-radius: 50px;
  border: none;
  background-color: #eead2b;
  color: white;
  font-size: 1rem;
  cursor: pointer;
  font-family: inherit;
  font-weight: bold;
  transition: background-color 0.2s ease;
}

.scan-button:hover {
  background-color: #d69b24;
}

.bottom-info {
  position: absolute;
  bottom: 20px;
  left: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
  z-index: 10;
}

.bottom-subtitle {
  font-size: 1.1rem;
  opacity: 0.8;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

.camera-icon {
  font-size: 1.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  color: white;
}

.camera-icon:hover {
  transform: scale(1.1);
}

.camera-circle {
  position: absolute;
  bottom: -15vh; /* Adjust to position circle partly off screen */
  right: -10vh;
  width: 50vw; /* 30% of viewport width */
  height: 50vw; /* Keep it circular */
  border-radius: 50%;
  overflow: hidden;
  border: 5px solid #eead2b;
  z-index: 5;
}

.webcam-feed {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
</style>