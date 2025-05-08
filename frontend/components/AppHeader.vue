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
  position: fixed;
  top: 0;
  left: 0;
  z-index: 100; /* Higher z-index to ensure it stays on top */
}

.logo-container {
  display: flex;
  align-items: center;
  gap: 10px;
}

.uplb-logo {
  height: 60px;
  width: auto;
}

.logo-text {
  display: flex;
  flex-direction: column;
}

.uplb-text {
  font-weight: bold;
  color: white;
  font-size: 1.5rem;
}

.nav-links {
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