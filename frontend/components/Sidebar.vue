<template>
  <div>
    <!-- Sidebar -->
    <div
      class="sidebar"
      :class="{ collapsed: isCollapsed }"
    >
      <!-- UPLB Logo -->
      <div style="position: absolute; left: 25.4px; top: 43.8px;">
        <img
          src="@/assets/images/uplb_logo.png"
          alt="UPLB Logo"
          style="width: 53.5px; height: 47.5px;"
        />
      </div>

      <!-- Face Recognition Title -->
      <div style="position: absolute; left: 90px; top: 45.4px;">
        <div
          class="face-title"
          style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 25px; color: #eead2b; font-weight: bold;"
        >
          Face
        </div>
        <div
          class="recognition-title"
          style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 15px; color: white; margin-top: 0.8px; font-weight: bold;"
        >
          Recognition
        </div>
      </div>

      <!-- Navigation Menu -->
      <div class="menu" style="margin-top: 120px; padding: 0;">
        <ul>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Dashboard' }"
              @click.prevent="$router.push('/face-scanning')"
            >
              <i class="fas fa-tachometer-alt"></i>
              Dashboard
            </a>
          </li>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Logs Summary' }"
            >
              <i class="fas fa-file-alt"></i>
              Logs Summary
            </a>
          </li>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Register Student' }"
              @click.prevent="$router.push('/register-student')"
            >
              <i class="fas fa-user-plus"></i>
              Register Student
            </a>
          </li>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Update Info' }"
            >
              <i class="fas fa-edit"></i>
              Update Info
            </a>
          </li>
        </ul>
      </div>

      <!-- Horizontal Line -->
      <hr style="border: 1px solid #444; margin: 16px 0; width: 90%; margin-left: auto; margin-right: auto;" />

      <!-- Account Section -->
      <div class="menu" style="margin-top: 0; padding: 0;">
        <ul>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Account' }"
            >
              <i class="fas fa-user"></i>
              Account
            </a>
          </li>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Saved Logs' }"
            >
              <i class="fas fa-save"></i>
              Saved Logs
            </a>
          </li>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Settings' }"
            >
              <i class="fas fa-cog"></i>
              Settings
            </a>
          </li>
        </ul>
      </div>

      <!-- Horizontal Line -->
      <hr style="border: 1px solid #444; margin: 16px 0; width: 90%; margin-left: auto; margin-right: auto;" />

      <!-- Footer Section -->
      <div class="menu" style="position: absolute; bottom: 16px; width: 100%; padding: 0;">
        <ul>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Admin' }"
            >
              <i class="fas fa-user-shield"></i>
              Admin
            </a>
          </li>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Help' }"
            >
              <i class="fas fa-question-circle"></i>
              Help
            </a>
          </li>
          <li>
            <a
              href="#"
              class="menu-item"
              :class="{ active: activeMenu === 'Log Out' }"
            >
              <i class="fas fa-sign-out-alt"></i>
              Log Out
            </a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Toggle Button for Small Screens -->
    <button
      class="toggle-button"
      @click="isCollapsed = !isCollapsed"
    >
      ☰
    </button>
  </div>
</template>

<script>
export default {
  name: 'Sidebar',
  props: {
    activeMenu: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      isCollapsed: false, // State to toggle sidebar
    };
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Zoika:wght@400;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;700&display=swap');

/* Sidebar styles */
.sidebar {
  font-family: 'Bricolage Grotesque', sans-serif;
  background-color: #252525;
  width: 280px;
  height: 100vh;
  position: absolute;
  left: 0;
  top: 0;
  overflow: hidden;
  transition: transform 0.3s ease;
}

.sidebar.collapsed {
  transform: translateX(-100%); /* Hide sidebar when collapsed */
}

.menu ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.menu-item {
  text-decoration: none;
  color: white;
  display: flex;
  align-items: center;
  padding: 8px 16px;
  border-radius: 4px;
  height: 45.5px;
  width: 100%;
  transition: background-color 0.3s ease;
}

.menu-item i {
  margin-right: 12px;
  font-size: 18px;
}

.menu-item:hover,
.menu-item.active {
  font-weight: bold;
  color: white;
  background-color: #eead2b;
}

.toggle-button {
  position: absolute;
  top: 20px;
  left: 300px;
  background-color: #252525;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 8px 12px;
  cursor: pointer;
  z-index: 1000;
}

/* Responsive Styles */
@media (max-width: 768px) {
  .sidebar {
    transform: translateX(-100%); /* Hide sidebar by default on small screens */
  }

  .sidebar.collapsed {
    transform: translateX(0); /* Show sidebar when toggled */
  }

  .toggle-button {
    left: 20px; /* Adjust position of toggle button on small screens */
  }
}

/* Hide toggle button on larger screens */
@media (min-width: 769px) {
  .toggle-button {
    display: none; /* Hide the toggle button */
  }
}
</style>