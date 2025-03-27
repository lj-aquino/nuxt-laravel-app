<template>
    <div class="viewport">
      <div class="device-sim">
        <div class="rotated-wrapper">
          <iframe
            :src="droidCamUrl"
            class="stream"
            frameborder="0"
            allowfullscreen
          ></iframe>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  const droidCamUrl = 'http://192.168.1.113:4747/video';
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
  