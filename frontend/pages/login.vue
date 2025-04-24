<template>
  <div class="login-container">
    <div class="logo">
      <img src="@/assets/images/uplb_logo.png" alt="UPLB Logo">
    </div>

    <nav class="top-navigator">
      <a href="/">HOME</a>
      <a href="/about">ABOUT US</a>
      <a href="/contact">CONTACT</a>
      <a href="/login" class="active">LOGIN</a>
    </nav>

    <div class="login-content">
      <h1 class="login-title">LOG IN</h1>

      <div class="login-form">
        <div class="input-group">
          <span class="icon">
            <font-awesome-icon icon="envelope" />
          </span>
          <input type="email" placeholder="Email" v-model="email" />
        </div>

        <div class="input-group">
          <span class="icon">
            <font-awesome-icon icon="lock" />
          </span>
          <input type="password" placeholder="Password" v-model="password" />
        </div>

        <div class="form-options">
          <label class="remember-me">
            <input type="checkbox">
            <span>Remember Me</span>
          </label>
          <a href="/forgot-password" class="forgot-password">Forgot Password?</a>
        </div>

        <button class="login-button" @click="login">Log in</button>
        <p class="error" v-if="errorMessage">{{ errorMessage }}</p>
        <p class="signup-link">or <a href="/signup">Sign up</a></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { supabase } from '../lib/supabase'
import '~/assets/css/login.css'

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const router = useRouter()

const login = async () => {
  const { data, error } = await supabase.auth.signInWithPassword({
    email: email.value,
    password: password.value,
  })

  if (error) {
    errorMessage.value = error.message
  } else {
    // Redirect to /face-scanning after successful login
    router.push('/face-scanning')
  }
}
</script>
