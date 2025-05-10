<template>
  <div class="login-container">
    <!-- Place AppHeader at the top of the structure -->
    <AppHeader />
    
    <!-- Background video - lower z-index to ensure it stays in the background -->
    <video autoplay muted loop class="background-video">
      <source src="~/assets/videos/bg_vid.mp4" type="video/mp4">
    </video>
    
    <!-- Content wrapper - will contain all UI elements that should be above the video -->
    <div class="content-login-wrapper">
      <!-- Login content - centered vertically and horizontally -->
      <div class="login-content">
        <div class="title">Welcome back!</div>
        <div class="subtitle">Login to your account</div>
        
        <form @submit.prevent="handleLogin" class="login-form">
          <div class="input-group">
            <span class="input-icon">
              <i class="fas fa-envelope"></i>
            </span>
            <input 
              type="email" 
              v-model="email" 
              placeholder="Email" 
              required 
              class="text-field"
            />
          </div>
          
          <div class="input-group">
            <span class="input-icon">
              <i class="fas fa-lock"></i>
            </span>
            <input 
              :type="showPassword ? 'text' : 'password'" 
              v-model="password" 
              placeholder="Password" 
              required 
              class="text-field"
            />
            <span class="toggle-password" @click="togglePassword">
              <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </span>
          </div>
          
          <div class="forgot-password">
            <NuxtLink to="/forgot-password">Forgot password?</NuxtLink>
          </div>
          
          <p class="error" v-if="errorMessage">{{ errorMessage }}</p>
        </form>
        
        <div class="action-section">
          <button type="submit" class="login-button" @click="handleLogin">Login</button>
          <button class="signup-button" @click="navigateToSignup">Sign Up</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { supabase } from '~/lib/supabase'; // Make sure this path is correct
import AppHeader from '~/components/AppHeader.vue';
import '~/assets/css/login.css'; // Import the external CSS file

// Form data
const email = ref('');
const password = ref('');
const showPassword = ref(false);
const errorMessage = ref('');
const router = useRouter();

// Toggle password visibility
const togglePassword = () => {
  showPassword.value = !showPassword.value;
};

// Handle login submission
const handleLogin = async () => {
  try {
    const { data, error } = await supabase.auth.signInWithPassword({
      email: email.value,
      password: password.value,
    });

    if (error) {
      errorMessage.value = error.message;
    } else {
      // Redirect to /face-scanning after successful login
      const { data: { session } } = await supabase.auth.getSession();
      router.push('/face-scanning');
    }
  } catch (e) {
    errorMessage.value = 'An error occurred during login';
    console.error(e);
  }
};

// Navigate to signup page
const navigateToSignup = () => {
  router.push('/signup');
};
</script>