<template>
  <header class="app-header">
    <div class="logo-container">
      <img src="~/assets/images/uplb_logo.png" alt="UPLB Logo" class="uplb-logo">
      <div class="logo-text">
        <div class="uplb-text">UPLB SSO</div>
      </div>
    </div>
    
    <div class="nav-links">
      <NuxtLink to="/home" class="nav-link" active-class="active">Home</NuxtLink>
      <NuxtLink to="/contact" class="nav-link" active-class="active">Contact</NuxtLink>
      <NuxtLink to="/about" class="nav-link" active-class="active">About</NuxtLink>
      <NuxtLink to="/login" class="nav-link" active-class="active">Login</NuxtLink>
    </div>
  </header>
</template>

<script setup>
// No script needed for basic functionality
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;700&display=swap');

.app-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 2rem;
  width: 100%;
  font-family: 'Bricolage Grotesque', sans-serif;
}

.logo-container {
  position: absolute;
  top: 20px;
  right: 20px;
  z-index: 10;
  display: flex;
  flex-direction: row-reverse; /* Reverse the order of elements */
  align-items: center;
  gap: 10px; /* Add gap between logo and text */
}

.uplb-logo {
  height: 60px;
  width: auto;
  margin-left: 10px; /* Change margin-right to margin-left */
  margin-right: 0; /* Reset margin-right if it exists */
}

.logo-text {
  display: flex;
  flex-direction: column;
  align-items: flex-end; /* Align text to right side since we're reversing order */
}

.uplb-text {
  font-weight: bold;
  color: white;
  font-size: 1.5rem;
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
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
}

.nav-links a:hover {
  opacity: 0.8;
}

.nav-links a.active {
  border-bottom: 2px solid white;
  font-weight: bold;
}

/* Responsive styles */
@media (max-width: 768px) {
  .app-header {
    flex-direction: column;
    padding: 1rem;
  }
  
  .nav-links {
    margin-top: 1rem;
    gap: 1rem;
  }
}
</style>