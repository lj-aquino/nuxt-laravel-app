<template>
  <div class="barcode-scanner-modal" v-if="show">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Scan Student ID</h3>
        <button class="close-btn" @click="closeModal">×</button>
      </div>
      <div class="scanner-content">
        <div class="status-message">
          {{ scannerStatus }}
        </div>
        <input 
          ref="barcodeInput"
          type="text"
          v-model="scannedCode"
          @keyup.enter="handleBarcodeScan"
          placeholder="Waiting for barcode scan..."
          autocomplete="off"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  show: Boolean
});

const emit = defineEmits(['update:show', 'barcode-scanned']);

const barcodeInput = ref(null);
const scannedCode = ref('');
const scannerStatus = ref('Ready to scan...');

// Focus input when modal opens
watch(() => props.show, (newVal) => {
  if (newVal) {
    nextTick(() => {
      barcodeInput.value?.focus();
    });
  }
});

const handleBarcodeScan = () => {
  if (scannedCode.value) {
    emit('barcode-scanned', scannedCode.value);
    scannedCode.value = '';
    closeModal();
  }
};

const closeModal = () => {
  emit('update:show', false);
};

// Automatically focus the input field
onMounted(() => {
  if (props.show) {
    barcodeInput.value?.focus();
  }
});
</script>

<style scoped>
.barcode-scanner-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  font-family: 'Bricolage Grotesque', sans-serif;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  width: 400px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.close-btn {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
}

.scanner-content {
  text-align: center;
}

.status-message {
  margin-bottom: 15px;
  color: #666;
}

input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  text-align: center;
  font-family: 'Bricolage Grotesque', sans-serif;
}
</style>