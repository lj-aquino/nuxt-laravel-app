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
            <i class="fas fa-envelope"></i>
          </span>
          <input type="email" placeholder="Email">
        </div>

        <div class="input-group">
          <span class="icon">
            <i class="fas fa-lock"></i>
          </span>
          <input type="password" placeholder="Password">
        </div>

        <div class="form-options">
          <label class="remember-me">
            <input type="checkbox">
            <span>Remember Me</span>
          </label>
          <a href="/forgot-password" class="forgot-password">Forgot Password?</a>
        </div>

        <button class="login-button">Log in</button>
        <p class="signup-link">or <a href="/signup">Sign up</a></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import '~/assets/css/login.css'; // Import the CSS file for styles
</script>