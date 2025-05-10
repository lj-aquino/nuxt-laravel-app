<template>
  <div class="status-indicator">
    <!-- Loading spinner -->
    <div v-if="status === 'loading'" class="spinner"></div>
    
    <!-- Success checkmark -->
    <div v-else-if="status === 'success'" class="checkmark">
      <i class="fas fa-check"></i>
    </div>
    
    <!-- Error exclamation mark -->
    <div v-else-if="status === 'error'" class="error-mark">
      <i class="fas fa-exclamation"></i>
    </div>
  </div>
</template>

<script setup>
// Define props with validation
const props = defineProps({
  status: {
    type: String,
    default: 'loading',
    validator: (value) => ['loading', 'success', 'error', 'none'].includes(value)
  }
});
</script>

<style scoped>
.status-indicator {
  display: inline-flex;
  align-items: center;
  margin-left: 10px;
}

/* Loading spinner */
.spinner {
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: #eead2b;
  animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Success checkmark */
.checkmark {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgb(255, 255, 255);
  font-size: 12px;
}

/* Error exclamation mark */
.error-mark {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #fffbfa;
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgb(11, 10, 10);
  font-size: 12px;
  font-weight: bold;
}
</style>