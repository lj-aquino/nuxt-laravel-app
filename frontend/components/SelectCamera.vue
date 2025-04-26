<template>
    <div v-if="modelValue" class="camera-modal">
      <div class="camera-modal-content">
        <h3>Select Camera</h3>
        <select v-model="selectedCamera" @change="onCameraChange">
          <option value="">Choose camera</option>
          <option 
            v-for="device in videoDevices" 
            :key="device.deviceId" 
            :value="device.deviceId"
          >
            {{ device.label || `Camera ${device.deviceId}` }}
          </option>
        </select>
        <button class="close-btn" @click="$emit('update:modelValue', false)">Close</button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  
  const props = defineProps({
    modelValue: Boolean, // for v-model support
  });
  
  const emit = defineEmits(['update:modelValue', 'camera-change']);
  
  const videoDevices = ref([]);
  const selectedCamera = ref('');
  
  const getCameras = async () => {
    try {
      const devices = await navigator.mediaDevices.enumerateDevices();
      videoDevices.value = devices.filter(device => device.kind === 'videoinput');
    } catch (error) {
      console.error('Error getting cameras:', error);
    }
  };
  
  const onCameraChange = () => {
    emit('camera-change', selectedCamera.value);
  };
  
  onMounted(getCameras);
  </script>

<style scoped>
.camera-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  font-family: 'Bricolage Grotesque', sans-serif;
  border-radius: 8px;
}

.camera-modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  width: 300px;
}

.camera-modal-content h3 {
  margin-top: 0;
  margin-bottom: 15px;
  font-family: 'Bricolage Grotesque', sans-serif;
  font-weight: bold;
}

.camera-modal-content select {
  width: 100%;
  padding: 8px;
  margin-bottom: 15px;
  font-family: 'Bricolage Grotesque', sans-serif;
  border-radius: 8px;
}

.close-btn {
  background: #eead2b;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  width: 100%;
  font-family: 'Bricolage Grotesque', sans-serif;
}

.close-btn:hover {
  background: #d69b24;
}
</style>